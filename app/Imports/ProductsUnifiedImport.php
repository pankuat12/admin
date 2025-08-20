<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductsUnifiedImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows, WithChunkReading
{
    private string $duplicateStrategy;
    private array $seenSkus = [];
    private object $summary;

    public function __construct(string $duplicateStrategy = 'update')
    {
        $this->duplicateStrategy = $duplicateStrategy;

        $this->summary = (object)[
            'total' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
            'errorReportStoragePath' => null,
        ];
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function rules(): array
    {
        // column names must match headers (case-insensitive); ensure your file has these
        return [
            'sku'           => ['required', 'string', 'max:100'],
            'name'          => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'price'         => ['required', 'numeric', 'min:0'],
            'currency'      => ['nullable', 'string', 'max:10'],
            'category_path' => ['required', 'string', 'max:255'], // single category name
            'stock'         => ['nullable', 'integer', 'min:0'],
            'status'        => ['nullable', 'in:draft,active,inactive'],
            'images'        => ['nullable', 'string'], // pipe-separated URLs
        ];
    }

    public function onFailure(...$failures)
    {
        // Each $failure represents validation errors for a row
        foreach ($failures as $failure) {
            $this->summary->failed++;
            $this->summary->errors[] = [
                'row' => $failure->row(), // 1-based including heading row
                'sku' => $failure->values()['sku'] ?? null,
                'messages' => $failure->errors(),
            ];
        }
    }

    public function onRow(Row $row)
    {
        // HeadingRow is enabled; $row->toArray() maps by header
        $r = $row->toArray();
        // We count all non-empty rows; Laravel-Excel fires validation before this when WithValidation is used.
        $this->summary->total++;

        // Normalize/trim strings
        foreach ($r as $k => $v) {
            if (is_string($v)) $r[$k] = trim($v);
        }

        $sku = $r['sku'] ?? null;
        if (!$sku) {
            $this->summary->failed++;
            $this->summary->errors[] = [
                'row' => $row->getIndex(),
                'sku' => null,
                'messages' => ['SKU is missing after normalization.'],
            ];
            return;
        }

        // Duplicate within the same file
        if (isset($this->seenSkus[$sku])) {
            $this->summary->failed++;
            $this->summary->errors[] = [
                'row' => $row->getIndex(),
                'sku' => $sku,
                'messages' => ["Duplicate SKU '{$sku}' appears multiple times in the file."],
            ];
            return;
        }
        $this->seenSkus[$sku] = true;

        try {
            DB::beginTransaction();

            // Ensure single-name category exists
            $categoryName = $r['category_path'];
            $category = Category::firstOrCreate(
                ['name' => $categoryName, 'parent_id' => null],
                ['slug' => Str::slug($categoryName)]
            );

            $payload = [
                'name'        => $r['name'],
                'description' => $r['description'] ?? null,
                'price'       => $r['price'],
                'currency'    => $r['currency'] ?? 'INR',
                'stock'       => isset($r['stock']) ? (int)$r['stock'] : 0,
                'status'      => $r['status'] ?? 'active',
                'category_id' => $category->id,
            ];

            // images: pipe-separated
            if (!empty($r['images'])) {
                $payload['images'] = explode('|', $r['images']);
            }

            $existing = Product::where('sku', $sku)->first();

            if ($existing) {
                if ($this->duplicateStrategy === 'skip') {
                    $this->summary->skipped++;
                } else {
                    $existing->fill($payload)->save();
                    $this->summary->updated++;
                }
            } else {
                Product::create(array_merge($payload, ['sku' => $sku]));
                $this->summary->created++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->summary->failed++;
            $this->summary->errors[] = [
                'row' => $row->getIndex(),
                'sku' => $sku,
                'messages' => [$e->getMessage()],
            ];
        }
    }

    public function __destruct()
    {
        // When import finishes, if there are failures, write error CSV
        if ($this->summary->failed > 0) {
            $this->summary->errorReportStoragePath = $this->writeErrorCsv($this->summary->errors);
        }
    }

    public function summary(): object
    {
        return $this->summary;
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
        Storage::put($storePath, file_get_contents($tmpPath));
        fclose($tmp);

        return $storePath;
    }
}
