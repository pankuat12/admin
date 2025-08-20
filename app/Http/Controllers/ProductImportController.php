<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Rap2hpoutre\FastExcel\FastExcel;

class ProductImportController extends Controller
{
    /**
     * Map to your manual schema.
     */
    protected array $statusMap = [
        'active'        => 1,
        'inactive'      => 0,
        'draft'         => 2,
        'out of stock'  => 3,
    ];

    protected array $map = [
        'tables' => [
            'products'   => 'products',
            'categories' => 'categories',
            'audit_logs' => 'audit_logs',
            'versions'   => 'product_versions',
        ],
        'product_columns' => [
            'pk'           => 'uniqueId',
            'sku'          => 'sku',
            'name'         => 'name',
            'slug'         => 'slug',
            'category_fk'  => 'category_id',
            'mrp'          => 'mrp',
            'sale_price'   => 'sale_price',
            'qty'          => 'stock_count',     // stock column
            'description'  => 'description',
            'status'       => 'status',
            'images'       => 'images',
            'featured'     => 'featured',
            'meta_title'   => 'meta_title',
            'meta_desc'    => 'meta_description',
            'version'      => 'version',
            // audit / timestamps (UNIX epoch ints)
            'created_by'   => 'createdBy',
            'updated_by'   => 'updatedBy',
            'created_at'   => 'createdOn',
            'updated_at'   => 'updatedOn',
            // soft delete
            'is_trashed'   => 'isTrashed',
            'trashed_on'   => 'trashedOn',
            'trashed_by'   => 'trashedBy',
        ],
        'category_columns' => [
            'pk'         => 'uniqueId',
            'name'       => 'name',
            'slug'       => 'slug',
            // audit / timestamps (UNIX epoch ints)
            'created_by' => 'createdBy',
            'updated_by' => 'updatedBy',
            'created_at' => 'createdOn',
            'updated_at' => 'updatedOn',
            // soft delete
            'is_trashed' => 'isTrashed',
            'trashed_on' => 'trashedOn',
            'trashed_by' => 'trashedBy',
        ],
    ];

    // ---------- Helpers ----------

    protected function productAuditSubset(array $row): array
    {
        $keep = [
            'uniqueId',
            'name',
            'slug',
            'description',
            'mrp',
            'sale_price',
            'sku',
            'category_id',
            'images',
            'status',
            'stock_count',
            'featured',
            'meta_title',
            'meta_description',
            'version',
            'createdOn',
            'createdBy',
            'updatedOn',
            'updatedBy',
            'isTrashed',
            'trashedOn',
            'trashedBy',
        ];
        $out = [];
        foreach ($keep as $k) {
            if (array_key_exists($k, $row)) $out[$k] = $row[$k];
        }
        return $out;
    }

    protected function diffAssoc(array $before, array $after): array
    {
        $changed = [];
        $allKeys = array_unique(array_merge(array_keys($before), array_keys($after)));
        foreach ($allKeys as $k) {
            $b = $before[$k] ?? null;
            $a = $after[$k] ?? null;
            if ($b !== $a) $changed[$k] = ['before' => $b, 'after' => $a];
        }
        return $changed;
    }

    // ---------- Actions ----------

    public function store(Request $request)
    {
        // 1) Validate upload + checkbox
        $request->validate([
            'file' => ['required', 'file', 'max:20480', 'mimes:csv,txt,xls,xlsx'],
            'auto_create_categories' => ['nullable', 'in:on,1,true'],
        ]);
        $autoCreate = $request->has('auto_create_categories');

        // 2) Save temp file
        $path = $request->file('file')->store('imports/tmp'); // storage/app/imports/tmp/...
        $full = Storage::path($path);

        // 3) Counters + context
        $created = $updated = $failed = $createdCategories = $skipped = 0;
        $processed = 0;
        $failRows = [];
        $now = time(); // your schema uses epoch ints
        $userId = (int) (session('usid') ?? 0);

        // 4) Table/column maps
        $T_PRODUCTS   = $this->map['tables']['products'];
        $T_CATEGORIES = $this->map['tables']['categories'];
        $T_AUDIT      = $this->map['tables']['audit_logs'];
        $T_VERSIONS   = $this->map['tables']['versions'];
        $PC = $this->map['product_columns'];
        $CC = $this->map['category_columns'];

        // 5) Import loop
        (new FastExcel())->import($full, function (array $row) use (
            &$created,
            &$updated,
            &$failed,
            &$createdCategories,
            &$skipped,
            &$processed,
            &$failRows,
            $autoCreate,
            $now,
            $userId,
            $T_PRODUCTS,
            $T_CATEGORIES,
            $T_AUDIT,
            $T_VERSIONS,
            $PC,
            $CC,
            $request
        ) {
            $processed++;
            $row = array_change_key_case($row, CASE_LOWER);

            // Validate row
            $v = Validator::make($row, [
                'sku'         => ['required', 'string', 'max:64'],
                'name'        => ['required', 'string', 'max:255'],
                'category'    => ['required', 'string', 'max:255'],
                'mrp'         => ['nullable', 'numeric', 'min:0'],
                'sale_price'  => ['nullable', 'numeric', 'min:0', 'lte:mrp'],
                'stock'       => ['nullable', 'integer', 'min:0'],
                'description' => ['nullable', 'string'],
                'status'      => ['nullable', 'in:active,inactive,draft,out of stock'],
            ]);

            if ($v->fails()) {
                $failed++;
                $failRows[] = ['row_values' => $row, 'errors' => $v->errors()->all()];
                return;
            }

            // Normalize
            $statusRaw = strtolower(trim((string)($row['status'] ?? '')));
            $statusVal = $this->statusMap[$statusRaw] ?? 0;

            $sku        = trim((string)$row['sku']);
            $name       = trim((string)$row['name']);
            $catName    = trim((string)$row['category']);
            $mrp        = isset($row['mrp']) ? (float)$row['mrp'] : null;
            $salePrice  = isset($row['sale_price']) ? (float)$row['sale_price'] : null;
            $stock      = isset($row['stock']) ? (int)$row['stock'] : null;
            $desc       = (string)($row['description'] ?? '');

            try {
                $result = DB::transaction(function () use (
                    $autoCreate,
                    $now,
                    $userId,
                    $T_PRODUCTS,
                    $T_CATEGORIES,
                    $T_AUDIT,
                    $T_VERSIONS,
                    $PC,
                    $CC,
                    $request,
                    $sku,
                    $name,
                    $catName,
                    $mrp,
                    $salePrice,
                    $stock,
                    $desc,
                    $statusVal
                ) {
                    $out = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'cat' => 0];

                    // Category resolve/create
                    $categoryId = null;
                    if ($catName !== '') {
                        $existingCat = DB::table($T_CATEGORIES)->where($CC['name'], $catName)->first();
                        if (!$existingCat && $autoCreate) {
                            $catInsert = [
                                $CC['name']       => $catName,
                                $CC['slug']       => Str::slug($catName),
                                $CC['created_by'] => $userId,
                                $CC['updated_by'] => $userId,
                                $CC['created_at'] => $now,
                                $CC['updated_at'] => $now,
                                $CC['is_trashed'] => 0,
                            ];
                            $categoryId = DB::table($T_CATEGORIES)->insertGetId($catInsert);
                            $out['cat'] = 1;
                        } else {
                            $categoryId = $existingCat?->{$CC['pk']};
                        }
                    }

                    // Upsert by SKU
                    $product = DB::table($T_PRODUCTS)->where($PC['sku'], $sku)->first();

                    if ($product) {
                        // Build intended changes (only keys that might change)
                        $update = [
                            $PC['name']        => $name,
                            $PC['description'] => ($desc !== '') ? $desc : $product->{$PC['description']},
                            $PC['status']      => $statusVal,
                            $PC['updated_by']  => $userId,
                            $PC['updated_at']  => $now,
                            $PC['version']     => ((int)($product->{$PC['version']} ?? 0)) + 1,
                        ];
                        if ($categoryId !== null) $update[$PC['category_fk']] = $categoryId;
                        if ($mrp !== null)        $update[$PC['mrp']] = $mrp;
                        if ($salePrice !== null)  $update[$PC['sale_price']] = $salePrice;
                        if ($stock !== null)      $update[$PC['qty']] = $stock;

                        // Compute diff vs current DB row — ignore meta fields below
                        $beforeSlim = (array) $product;

                        $compareKeys = [
                            $PC['name'],
                            $PC['description'],
                            $PC['status'],
                            $PC['mrp'],
                            $PC['sale_price'],
                            $PC['qty'],
                            $PC['category_fk'],
                        ];
                        $hasChange = false;
                        foreach ($compareKeys as $k) {
                            if (array_key_exists($k, $update)) {
                                $beforeVal = $product->{$k} ?? null;
                                $afterVal  = $update[$k];
                                if ($beforeVal !== $afterVal) {
                                    $hasChange = true;
                                    break;
                                }
                            }
                        }

                        if (!$hasChange) {
                            // No material change — skip
                            $out['skipped'] = 1;
                            return $out;
                        }

                        // Apply update
                        DB::table($T_PRODUCTS)->where($PC['sku'], $sku)->update($update);

                        $afterFull = (array) DB::table($T_PRODUCTS)->where($PC['sku'], $sku)->first();
                        $afterSlim = $this->productAuditSubset($afterFull);
                        $diffFields = $this->diffAssoc($this->productAuditSubset($beforeSlim), $afterSlim);

                        // Audit (update)
                        DB::table($T_AUDIT)->insert([
                            'adminId'      => $userId,
                            'modelChanged' => 'products',
                            'action'       => 'update',
                            'changes'      => json_encode([
                                'product_id' => $afterFull[$PC['pk']] ?? null,
                                'version'    => $afterFull[$PC['version']] ?? null,
                                'before'     => $this->productAuditSubset($beforeSlim),
                                'after'      => $afterSlim,
                                'diff'       => $diffFields,
                                'meta'       => ['slug' => $afterSlim['slug'] ?? null, 'sku' => $sku],
                            ], JSON_UNESCAPED_UNICODE),
                            'createdAt'    => now(),
                            'entityId'     => $afterFull[$PC['pk']] ?? null,
                            'ip'           => $request->ip(),
                        ]);

                        // Version snapshot
                        DB::table($T_VERSIONS)->insert([
                            'product_id' => $afterFull[$PC['pk']] ?? null,
                            'version'    => $afterFull[$PC['version']] ?? null,
                            'data_json'  => json_encode($afterSlim, JSON_UNESCAPED_UNICODE),
                            'created_by' => $userId,
                            'created_at' => now(),
                        ]);

                        $out['updated'] = 1;
                        return $out;
                    }

                    // CREATE
                    $insert = [
                        $PC['sku']         => $sku,
                        $PC['name']        => $name,
                        $PC['slug']        => Str::slug($name . '-' . $sku),
                        $PC['description'] => $desc,
                        $PC['status']      => $statusVal,
                        $PC['featured']    => 0,
                        $PC['version']     => 1,
                        $PC['mrp']         => $mrp ?? 0,
                        $PC['sale_price']  => $salePrice ?? ($mrp ?? 0),
                        $PC['qty']         => $stock ?? 0,
                        $PC['created_by']  => $userId,
                        $PC['updated_by']  => $userId,
                        $PC['created_at']  => $now,
                        $PC['updated_at']  => $now,
                        $PC['is_trashed']  => 0,
                    ];
                    if ($categoryId !== null) $insert[$PC['category_fk']] = $categoryId;

                    $id = DB::table($T_PRODUCTS)->insertGetId($insert);

                    $afterFull = (array) DB::table($T_PRODUCTS)->where($PC['pk'], $id)->first();
                    $afterSlim = $this->productAuditSubset($afterFull);

                    // Audit (create)
                    DB::table($T_AUDIT)->insert([
                        'adminId'      => $userId,
                        'modelChanged' => 'products',
                        'action'       => 'create',
                        'changes'      => json_encode([
                            'product_id' => $id,
                            'version'    => 1,
                            'before'     => (object)[],
                            'after'      => $afterSlim,
                            'diff'       => $afterSlim,
                            'meta'       => ['slug' => $afterSlim['slug'] ?? null, 'sku' => $sku],
                        ], JSON_UNESCAPED_UNICODE),
                        'createdAt'    => now(),
                        'entityId'     => $id,
                        'ip'           => $request->ip(),
                    ]);

                    // Version snapshot
                    DB::table($T_VERSIONS)->insert([
                        'product_id' => $id,
                        'version'    => 1,
                        'data_json'  => json_encode($afterSlim, JSON_UNESCAPED_UNICODE),
                        'created_by' => $userId,
                        'created_at' => now(),
                    ]);

                    $out['created'] = 1;
                    return $out;
                });

                // Accumulate totals
                $created           += $result['created'];
                $updated           += $result['updated'];
                $createdCategories += $result['cat'];
                $skipped           += $result['skipped'];
            } catch (\Throwable $e) {
                $failed++;
                $failRows[] = ['row_values' => $row, 'errors' => [$e->getMessage()]];
                // optional: \Log::error($e);
            }
        });

        // 6) Cleanup temp file
        Storage::delete($path);

        // 7) Build a clear, numeric summary message
        $message = sprintf(
            "Import complete. Processed: %d • Created: %d • Updated: %d • Skipped (no change): %d • Failed: %d • New categories: %d",
            $processed,
            $created,
            $updated,
            $skipped,
            $failed,
            $createdCategories
        );

        // 8) Flash session data (your Blade can print both message + numbers)
        return back()->with([
            'success' => $message,
            'import_summary' => [
                'processed'          => $processed,
                'created_count'      => $created,
                'updated_count'      => $updated,
                'skipped_count'      => $skipped,
                'failed_count'       => $failed,
                'created_categories' => $createdCategories,
                'failed_examples'    => array_slice($failRows, 0, 5),
            ],
        ]);
    }
}
