<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader; // OPTIONAL if you have league/csv; otherwise we’ll do native fgetcsv
use Throwable;

class ProductImporter
{
    public function import(string $absoluteCsvPath, string $duplicateStrategy = 'update'): object
    {
        $handle = fopen($absoluteCsvPath, 'r');
        if (!$handle) {
            throw new \RuntimeException('Could not open CSV.');
        }

        $headers = null;
        $rowNum  = 0;

        $summary = (object)[
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
            'errorReportStoragePath' => null
        ];

        // Map to detect duplicate SKUs inside the same file
        $seenSkus = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            if ($rowNum === 1) {
                $headers = $this->normalizeHeaders($row);
                continue;
            }
            $summary->total++;

            $data = $this->rowToAssoc($headers, $row);

            // Normalize & trim
            $data = array_map(fn($v) => is_string($v) ? trim($v) : $v, $data);

            // Validate one row
            $validator = Validator::make($data, [
                'sku'           => ['required', 'string', 'max:100'],
                'name'          => ['required', 'string', 'max:255'],
                'description'   => ['nullable', 'string'],
                'price'         => ['required', 'numeric', 'min:0'],
                'currency'      => ['nullable', 'string', 'max:10'],
                'category_path' => ['required', 'string', 'max:255'],
                'stock'         => ['nullable', 'integer', 'min:0'],
                'status'        => ['nullable', 'in:draft,active,inactive'],
                'images'        => ['nullable', 'string'],
                'attributes'    => ['nullable', 'string'], // JSON string
            ]);

            if ($validator->fails()) {
                $summary->failed++;
                $summary->errors[] = [
                    'row' => $rowNum,
                    'sku' => $data['sku'] ?? null,
                    'messages' => $validator->errors()->all(),
                ];
                continue;
            }

            // Duplicate SKU within the same file → count as failed duplicate-row to avoid ambiguity
            $sku = $data['sku'];
            if (isset($seenSkus[$sku])) {
                $summary->failed++;
                $summary->errors[] = [
                    'row' => $rowNum,
                    'sku' => $sku,
                    'messages' => ["Duplicate SKU '{$sku}' appears multiple times in the file."],
                ];
                continue;
            }
            $seenSkus[$sku] = true;

            try {
                DB::beginTransaction();

                $categoryId = $this->ensureCategoryPath($data['category_path']);

                $payload = [
                    'name'        => $data['name'],
                    'description' => $data['description'] ?? null,
                    'price'       => $data['price'],
                    'currency'    => $data['currency'] ?? 'INR',
                    'stock'       => isset($data['stock']) ? (int)$data['stock'] : 0,
                    'status'      => $data['status'] ?? 'active',
                    'category_id' => $categoryId,
                    // You may process images/attributes below
                ];

                // attributes JSON (optional)
                if (!empty($data['attributes'])) {
                    $decoded = json_decode($data['attributes'], true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $payload['attributes'] = $decoded;
                    } else {
                        throw new \InvalidArgumentException("Invalid JSON in attributes.");
                    }
                }

                // images (pipe-separated)
                if (!empty($data['images'])) {
                    $payload['images'] = explode('|', $data['images']);
                }

                $product = Product::where('sku', $sku)->first();

                if ($product) {
                    if ($duplicateStrategy === 'skip') {
                        $summary->skipped++;
                    } else { // update
                        $product->fill($payload)->save();
                        $summary->updated++;
                    }
                } else {
                    $product = new Product(array_merge($payload, ['sku' => $sku]));
                    $product->save();
                    $summary->created++;
                }

                DB::commit();
            } catch (Throwable $e) {
                DB::rollBack();
                $summary->failed++;
                $summary->errors[] = [
                    'row' => $rowNum,
                    'sku' => $sku ?? null,
                    'messages' => [$e->getMessage()],
                ];
                continue;
            }
        }
        fclose($handle);

        // Build a downloadable error CSV if there are failures
        if ($summary->failed > 0) {
            $storagePath = $this->writeErrorCsv($summary->errors);
            $summary->errorReportStoragePath = $storagePath;
        }

        return $summary;
    }

    private function normalizeHeaders(array $headers): array
    {
        return array_map(function ($h) {
            return strtolower(trim($h));
        }, $headers);
    }

    private function rowToAssoc(array $headers, array $row): array
    {
        $assoc = [];
        foreach ($headers as $i => $key) {
            $assoc[$key] = $row[$i] ?? null;
        }
        return $assoc;
    }

    /**
     * Ensure category hierarchy exists; return the final category_id.
     * Accepts "Parent > Child > Subchild".
     */
    private function ensureCategoryPath(string $path): int
    {
        $parts = array_map(fn($p) => trim($p), explode('>', $path));
        $parentId = null;
        foreach ($parts as $name) {
            if ($name === '') continue;
            $category = Category::firstOrCreate(
                ['name' => $name, 'parent_id' => $parentId],
                ['slug' => \Str::slug($name)]
            );
            $parentId = $category->id;
        }
        return $parentId;
    }

    private function writeErrorCsv(array $errors): string
    {
        $lines = [];
        $lines[] = ['row', 'sku', 'errors'];
        foreach ($errors as $e) {
            $lines[] = [
                $e['row'] ?? '',
                $e['sku'] ?? '',
                implode(' | ', $e['messages'] ?? []),
            ];
        }
        $tmp = tmpfile();
        $meta = stream_get_meta_data($tmp);
        $tmpPath = $meta['uri'];
        $out = fopen($tmpPath, 'w');
        foreach ($lines as $line) {
            fputcsv($out, $line);
        }
        fclose($out);

        $storePath = 'imports/error-reports/' . now()->format('Ymd_His') . '_product_import_errors.csv';
        \Illuminate\Support\Facades\Storage::put($storePath, file_get_contents($tmpPath));
        fclose($tmp);

        return $storePath; // storage path used by download route
    }
}
