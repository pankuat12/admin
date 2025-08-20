<?php

namespace App\Http\Controllers;

use App\Models\ProductModel;
use App\Models\CrudModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function list(Request $request)
    {
        // dd($request->all());
        // If POST, normalize to GET so pagination keeps filters in the URL
        if ($request->isMethod('post')) {
            $filters = [
                'q'           => $request->input('q'),
                'category_id' => $request->input('category_id'),
                'featured'    => $request->input('featured'),     // '' | '1' | '0'
                'status'      => $request->input('status'), // multiple
                'min_price'   => $request->input('min_price'),
                'max_price'   => $request->input('max_price'),
            ];
            // Remove empties for a clean querystring
            $filters = array_filter($filters, function ($v) {
                return !($v === null || $v === '' || (is_array($v) && count($v) === 0));
            });
            return Redirect::route('products.list', $filters);
        }
        $p = [
            'like'        => $request->query('q'),
            'category_id' => $request->query('category_id'),
            'featured'    => $request->query('featured'),
            'status'      => $request->query('status', []),
            'min_price'   => $request->query('min_price'),
            'max_price'   => $request->query('max_price'),
        ];

        $modal   = new ProductModel();
        $product = $modal->getProduct($p); // now uses filters + appends internally

        Paginator::useBootstrap();
        // Category list for filter dropdown
        $categories = $modal->getCategory();
        $bounds = $modal->getPriceBounds();
        return view('layout.index', [
            'like'       => $p['like'],
            'bounds' => $bounds,
            'product'    => $product,
            'filters'    => $p,
            'categories' => $categories,
            'header'     => true,
            'footer'     => true,
            'sidebar'    => true,
            'body'       => 'product.list',
        ]);
    }
    public function handle(Request $req)
    {
        $ids = $req->input('ids', []);
        if (!is_array($ids) || !count($ids)) {
            return response()->json(['status' => 0, 'message' => 'Please select at least one product.'], 422);
        }
        $action = $req->input('action');
        try {
            $affected = 0;

            if ($action === 'price') {
                $mode  = $req->input('price_mode');      // set|add_amount|add_percent
                $value = (float) $req->input('price_value');

                $q = DB::table('products')->whereIn('uniqueId', $ids);
                if ($mode === 'set') {
                    $affected = $q->update(['sale_price' => $value, 'updatedOn' => Carbon::now()->timestamp]);
                } elseif ($mode === 'add_amount') {
                    $affected = $q->update([
                        'sale_price' => DB::raw("GREATEST(0, sale_price + {$value})"),
                        'updatedOn'  => Carbon::now()->timestamp
                    ]);
                } elseif ($mode === 'add_percent') {
                    $pct = 1 + ($value / 100);
                    $affected = $q->update([
                        'sale_price' => DB::raw("GREATEST(0, sale_price * {$pct})"),
                        'updatedOn'  => Carbon::now()->timestamp
                    ]);
                } else {
                    return response()->json(['status' => 0, 'message' => 'Invalid price mode.'], 422);
                }
            } elseif ($action === 'status') {
                $status = $req->input('status_value'); // active|inactive|draft|out_of_stock
                if (!$status) return response()->json(['status' => 0, 'message' => 'Choose a status.'], 422);

                $affected = DB::table('products')->whereIn('uniqueId', $ids)
                    ->update(['status' => $status, 'updatedOn' => Carbon::now()->timestamp]);
            } elseif ($action === 'stock') {
                $mode  = $req->input('stock_mode');  // set|add|sub
                $value = (int) $req->input('stock_value');

                $q = DB::table('products')->whereIn('uniqueId', $ids);
                if ($mode === 'set') {
                    $affected = $q->update(['stock_count' => $value, 'updatedOn' => Carbon::now()->timestamp]);
                } elseif ($mode === 'add') {
                    $affected = $q->update([
                        'stock_count' => DB::raw("GREATEST(0, stock_count + {$value})"),
                        'updatedOn'   => Carbon::now()->timestamp
                    ]);
                } elseif ($mode === 'sub') {
                    $affected = $q->update([
                        'stock_count' => DB::raw("GREATEST(0, stock_count - {$value})"),
                        'updatedOn'   => Carbon::now()->timestamp
                    ]);
                } else {
                    return response()->json(['status' => 0, 'message' => 'Invalid stock mode.'], 422);
                }
            } else {
                return response()->json(['status' => 0, 'message' => 'Unknown action.'], 422);
            }
            return response()->json(['status' => 1, 'message' => "Updated {$affected} products."]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Bulk update failed.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 422);
        }
    }
    public function add()
    {
        $p[] = '';
        $modal = new ProductModel();
        $category = $modal->getCategory($p); //print_r($category);die;
        if (count($category) != 0) {
            $p['category'] = $category;
        }
        $p['header'] = true;
        $p['footer'] = true;
        $p['sidebar'] = true;
        $p['body'] = 'product.add';
        return view('layout.index', $p);
    }
    public function edit($id)
    {
        $modal = new ProductModel();
        $p['id'] = base64_decode($id);
        $product = $modal->getProduct($p);
        $p['product'] = $product;
        $category = $modal->getCategory($p); //print_r($category);die;
        if (count($category) != 0) {
            $p['category'] = $category;
        }
        // print_r($p);die;
        $p['header'] = true;
        $p['footer'] = true;
        $p['sidebar'] = true;
        $p['body'] = 'product.add';
        return view('layout.index', $p);
    }
    public function copy($id)
    {
        $modal = new ProductModel();
        $p['id'] = base64_decode($id);
        $product = $modal->getProduct($p);
        $p['product'] = $product;
        $category = $modal->getCategory($p); //print_r($category);die;
        if (count($category) != 0) {
            $p['category'] = $category;
        }
        // print_r($p);die;
        $p['header'] = true;
        $p['footer'] = true;
        $p['sidebar'] = true;
        $p['body'] = 'product.copy';
        return view('layout.index', $p);
    }
    public function update(Request $request)
    {
        //    print_r($_FILES);
        //    print_r($_POST);die;
        $product = new ProductModel;
        $modal = new CrudModel;
        $id = $request->integer('hidden_id', 0);
        $isUpdate = $id > 0;
        $validator = Validator::make($request->all(), [
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|integer',
            'mrp'           => 'required|numeric|min:1',
            'sale_price' => 'required|numeric|lte:mrp',
            'sku'               => 'required|string|' . ($isUpdate
                ? 'unique:products,sku,' . $id . ',uniqueId'
                : 'unique:products,sku'),
            'stock_count'   => 'required|integer|min:0',
            'meta_title'    => 'required|string|max:255',
            'meta_description' => 'required|string|max:500',
            'images.*'      => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:5120',
            'keep_images'       => 'nullable|string',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first()
            ]);
        }
        // ---- Fetch existing product (for update + cleanup)
        $existing = null;
        if ($isUpdate) {
            $existing = $product->getProduct(['id' => $id]);
            if (!$existing) {
                return response()->json(['status' => 0, 'message' => 'Product not found.']);
            }
        }
        // ---- Slug & ensure folder for new uploads
        $slug = Str::slug($request->name);
        $dir  = public_path("upload/product/{$slug}");
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0775, true);
        }
        // ---- Upload NEW files → relative paths
        $upload = $this->saveProductImages($request, 'name', 'images');
        $newRel = $upload['rel'] ?? [];
        // ---- Keep images from UI (they are URLs) → convert to rel
        $keepUrls = json_decode($request->input('keep_images', '[]'), true);
        $keepUrls = is_array($keepUrls) ? $keepUrls : [];
        $keepRel  = [];
        foreach ($keepUrls as $u) {
            $rel = $this->urlToRelative($u);
            if ($rel) $keepRel[] = $rel;
        }
        // ---- Merge order: kept (as arranged) + new uploads; cap at 8
        $finalRel = array_values(array_slice(array_merge($keepRel, $newRel), 0, 8));
        // ---- Cleanup removed files (compare rel paths)
        if ($isUpdate && !empty($existing->images)) {
            $oldRel = json_decode($existing->images, true) ?: [];
            // Compare by filenames to avoid accidental deletes on slug change
            $oldNames   = $this->basenameSet($oldRel);
            $finalNames = $this->basenameSet($finalRel);

            foreach ($oldRel as $rel) {
                $name = basename($rel);
                if (!isset($finalNames[$name])) {
                    $fs = public_path(str_replace('public/', '', $rel));
                    if (File::exists($fs)) {
                        @File::delete($fs);
                    }
                }
            }
        }
        // ---- Build payload
        $meta = [
            'name'             => $request->name,
            'slug'             => $slug,
            'category_id'      => (int)$request->category_id,
            'status'           => (int)$request->status,
            'mrp'              => (float)$request->mrp,
            'sale_price'       => (float)$request->sale_price,
            'sku'              => $request->sku,
            'stock_count'      => (int)$request->stock_count,
            'featured'         => (int)($request->featured ?? 0),
            'meta_title'       => $request->meta_title,
            'meta_description' => $request->meta_description,
            'description'      => $request->description,
            'images'           => json_encode($finalRel),
        ];
        // print_r($meta);die;
        if ($isUpdate) {
            $clientVersion  = (int) $request->input('concurrency_version', 0);
            $currentVersion = (int) $existing->version;
            if ($clientVersion !== $currentVersion) {
                return response()->json([
                    'status'  => 0,
                    'message' => 'This product was updated by someone else. Please reload.',
                    'your_version'    => $clientVersion,
                    'current_version' => $currentVersion,
                ]);
            }
            // 1) Build AFTER payload (normalized)
            $after = $this->normalizeProductPayload($meta);
            $after['images'] = $finalRel; // ensure array form for diff
            // 2) Build BEFORE payload from DB row (normalized)
            $beforeRow = (array) $existing;
            // existing images in DB are JSON; decode
            if (!empty($beforeRow['images'])) {
                $decoded = json_decode($beforeRow['images'], true);
                $beforeRow['images'] = is_array($decoded) ? $decoded : [];
            } else {
                $beforeRow['images'] = [];
            }
            // unify `version` presence
            if (!isset($beforeRow['version'])) $beforeRow['version'] = 1;

            $before = $this->normalizeProductPayload($beforeRow);
            // 3) Compute diff
            $diff = $this->array_diff_assoc_deep($before, $after);
            // 4) Short‑circuit if nothing changed
            if (empty($diff)) {
                return response()->json([
                    'status'  => 1,
                    'message' => 'No changes detected.'
                ]);
            }
            // 5) Determine next version
            // If you store current version in products.version, use that; else query product_versions
            $currentVersion = (int) ($existing->version ?? 1);
            // Or: $currentVersion = (int) \DB::table('product_versions')->where('product_id', $id)->max('version') ?: 1;
            $nextVersion = $currentVersion + 1;
            // 6) Persist the product with incremented version
            $meta['version']   = $nextVersion;
            $meta['updatedOn'] = Carbon::now()->timestamp;
            $meta['updatedBy'] = session('usid');
            $ok = $modal->updateData('products', $meta, 'products.uniqueId', $id);
            if ($ok) {
                // 7) Insert audit log
                $auditPayload = [
                    'product_id' => $id,
                    'version'    => $nextVersion,
                    'before'     => $before,
                    'after'      => $after,
                    'diff'       => $diff,
                    'meta'       => ['slug' => $meta['slug'], 'sku' => $meta['sku']],
                ];
                $auditData = [
                    'adminId'      => session('usid'),
                    'modelChanged' => 'products',
                    'action'       => 'update',
                    'changes'      => json_encode($auditPayload),
                    'createdAt'    => now(),
                    'entityId'     => $id,
                    'ip'           => $request->ip(),
                ];
                $modal->insertData('audit_logs', $auditData);
                // 8) Version snapshot
                $versionData = [
                    'product_id'  => $id,
                    'version'     => $nextVersion,
                    'data_json'   => json_encode(array_merge($after, ['version' => $nextVersion])),
                    'created_by'  => session('usid'),
                    'created_at'  => now(),
                ];
                $modal->insertData('product_versions', $versionData);
                return response()->json(['status' => 1, 'message' => 'Product Update Successfully.']);
            }
            return response()->json(['status' => 0, 'message' => 'Something Went Wrong.']);
        } else {
            $meta['createdOn'] = Carbon::now()->timestamp;
            $meta['createdBy'] = session('usid');
            $table = 'products';
            $resp = $modal->insertDataWithid($table, $meta);
            // print_r($resp);die;
            if ($resp != 0) {
                // Audit log (create)
                $auditPayload = [
                    'product_id' => $resp,
                    'version'    => 1,
                    'before'     => (object)[],
                    'after'      => $meta,
                    'diff'     => $meta,
                    'meta'       => ['slug' => $slug, 'sku' => $request->sku],
                ];
                $auditData = [
                    'adminId'      => session('usid'),
                    'modelChanged' => 'products',
                    'action'       => 'create',
                    'changes'      => json_encode($auditPayload),
                    'createdAt'    => now(),
                    'entityId'     => $resp,
                    'ip'           => $request->ip(),
                ];
                $modal->insertData('audit_logs', $auditData);
                // Audit log (create)
                // Version snapshot (v1)
                $versionsData = [
                    'product_id'  => $resp,
                    'version'     => 1,
                    'data_json'   => json_encode($meta),
                    // 'images_json' => json_encode($savedRelPaths),
                    'created_by'  => session('usid'),
                    'created_at'  => now(),
                ];
                $modal->insertData('product_versions', $versionsData);
                // Version snapshot (v1)
                $data['status'] = 1;
                $data['message'] = 'Product Add Successfully.';
            } else {
                $data['status'] = 0;
                $data['message'] = 'Something Went Wrong.';
            }
        }
        return json_encode($data);
    }
    public function saveProductImages($request, $nameField = 'name', $fileField = 'images')
    {
        $slug = Str::slug($request->$nameField) ?: 'product-' . Str::random(6);
        $dir  = public_path("upload/product/{$slug}");
        if (!File::isDirectory($dir)) {
            File::makeDirectory($dir, 0775, true);
        }
        // Dedupe by file content
        $existingHashes = [];
        foreach (glob($dir . '/*') as $path) {
            if (is_file($path)) {
                $existingHashes[sha1_file($path)] = basename($path);
            }
        }
        $relPaths = [];
        if ($request->hasFile($fileField)) {
            $seenBatch = [];
            foreach ($request->file($fileField) as $file) {
                if (!$file->isValid()) continue;
                $hash = sha1_file($file->getRealPath());
                if (isset($existingHashes[$hash]) || isset($seenBatch[$hash])) continue;
                $ext  = strtolower($file->extension() ?: 'jpg');
                $name = "{$slug}-" . uniqid() . ".{$ext}";
                $file->move($dir, $name);
                $rel = "public/upload/product/{$slug}/{$name}";
                $relPaths[] = $rel;
                $existingHashes[$hash] = $name;
                $seenBatch[$hash] = true;
            }
        }
        return [
            'rel' => $relPaths,   // <-- store these in DB
        ];
    }
    private function urlToRelative(?string $url): ?string
    {
        if (!$url) return null;
        $path = parse_url($url, PHP_URL_PATH); // e.g. /admin/public/upload/...
        if (!$path) return null;
        // Figure out your app's public URL subpath (e.g. "/admin/public")
        $publicSub = rtrim(parse_url(asset('/'), PHP_URL_PATH) ?? '', '/');
        // Strip "/admin/public" prefix if present → "/upload/..."
        if ($publicSub && Str::startsWith($path, $publicSub)) {
            $path = substr($path, strlen($publicSub));
        }
        return ltrim($path, '/'); // "upload/..."
    }
    private function basenameSet(array $rels): array
    {
        $set = [];
        foreach ($rels as $rel) {
            $set[basename($rel)] = true;
        }
        return $set;
    }
    private function normalizeProductPayload(array $data): array
    {
        // Keep only the fields you care to audit/version (exclude volatile fields)
        $keep = [
            'name',
            'slug',
            'category_id',
            'status',
            'mrp',
            'sale_price',
            'sku',
            'stock_count',
            'featured',
            'meta_title',
            'meta_description',
            'description',
            'images',
            'version'
        ];
        $out = [];
        foreach ($keep as $k) {
            if (array_key_exists($k, $data)) $out[$k] = $data[$k];
        }
        // Ensure consistent types
        $out['category_id'] = isset($out['category_id']) ? (int)$out['category_id'] : null;
        $out['status']      = isset($out['status']) ? (int)$out['status'] : null;
        $out['mrp']         = isset($out['mrp']) ? (float)$out['mrp'] : null;
        $out['sale_price']  = isset($out['sale_price']) ? (float)$out['sale_price'] : null;
        $out['stock_count'] = isset($out['stock_count']) ? (int)$out['stock_count'] : null;
        $out['featured']    = isset($out['featured']) ? (int)$out['featured'] : 0;

        // Decode images JSON if string
        if (isset($out['images']) && is_string($out['images'])) {
            $tmp = json_decode($out['images'], true);
            if (is_array($tmp)) $out['images'] = $tmp;
        }
        return $out;
    }
    private function array_diff_assoc_deep($old, $new)
    {
        // returns only changed keys with [old => ..., new => ...] or nested arrays
        $diff = [];
        $allKeys = array_unique(array_merge(array_keys((array)$old), array_keys((array)$new)));
        foreach ($allKeys as $k) {
            $ov = $old[$k] ?? null;
            $nv = $new[$k] ?? null;

            $bothArrays = is_array($ov) && is_array($nv);
            if ($bothArrays) {
                // for images arrays, compare by value+order
                if ($k === 'images') {
                    if ($ov !== $nv) {
                        $diff[$k] = ['old' => $ov, 'new' => $nv];
                    }
                } else {
                    $sub = $this->array_diff_assoc_deep($ov, $nv);
                    if (!empty($sub)) $diff[$k] = $sub;
                }
            } else {
                if ($ov !== $nv) {
                    $diff[$k] = ['old' => $ov, 'new' => $nv];
                }
            }
        }
        return $diff;
    }
    public function delete(Request $request)
    {

        $id   = (int) base64_decode($request->input('id'));
        $type = $request->input('type');
        // dd($id,$type);
        if ($id <= 0) {
            return response()->json(['status' => 0, 'message' => 'Invalid product id.'], 422);
        }

        $row = DB::table('products')->where('uniqueId', $id)->first();
        if (!$row) {
            return response()->json(['status' => 0, 'message' => 'Product not found.'], 404);
        }
        // Build BEFORE snapshot for audit
        $beforeArr = (array) $row;
        $beforeArr['images'] = json_decode($beforeArr['images'] ?? '[]', true) ?: [];
        $before = $this->normalizeProductPayload($beforeArr);

        // Decide next version
        $currentVersion = (int) ($row->version ?? 1);
        $nextVersion    = $currentVersion + 1;

        if ($type == 'soft') {
            // ---------- SOFT DELETE ----------
            $update = [
                'isTrashed' => 1,
                'trashedOn' => now()->timestamp,
                'trashedBy' => session('usid'),
                'version'   => $nextVersion,
                'updatedOn' => now()->timestamp,
                'updatedBy' => session('usid'),
            ];
            DB::beginTransaction();
            try {
                DB::table('products')->where('uniqueId', $id)->update($update);

                // AFTER snapshot for version row (mark trashed)
                $after = $before;
                $after['isTrashed'] = 1;
                $after['version']   = $nextVersion;

                // Audit row
                $auditPayload = [
                    'product_id' => $id,
                    'version'    => $nextVersion,
                    'before'     => $before,
                    'after'      => $after,
                    'diff'       => ['isTrashed' => ['old' => 0, 'new' => 1]],
                    'meta'       => ['slug' => $row->slug, 'sku' => $row->sku],
                ];
                DB::table('audit_logs')->insert([
                    'adminId'      => session('usid'),
                    'modelChanged' => 'products',
                    'action'       => 'delete',     // or 'soft_delete'
                    'changes'      => json_encode($auditPayload),
                    'createdAt'    => now(),
                    'entityId'     => $id,
                    'ip'           => $request->ip(),
                ]);

                // Version snapshot (tombstone state)
                DB::table('product_versions')->insert([
                    'product_id' => $id,
                    'version'    => $nextVersion,
                    'data_json'  => json_encode($after),
                    'created_by' => session('usid'),
                    'created_at' => now(),
                ]);

                DB::commit();
                return response()->json(['status' => 1, 'message' => 'Product moved to trash.']);
            } catch (\Throwable $e) {
                DB::rollBack();
                report($e);
                return response()->json(['status' => 0, 'message' => 'Failed to trash product.'], 500);
            }
        } else {
            // ---------- HARD DELETE ----------
            // Move images to .trash (or delete permanently)
            $images = $before['images'] ?? [];
            foreach ($images as $rel) {
                // move to trash:
                $this->moveRelToTrash($rel);
                // OR: @File::delete($this->relToPublicPath($rel));
            }

            DB::beginTransaction();
            try {
                DB::table('products')->where('uniqueId', $id)->delete();
                // Audit row (hard delete)
                $auditPayload = [
                    'product_id' => $id,
                    'version'    => $currentVersion, // last known
                    'before'     => $before,
                    'after'      => (object)[],      // empty
                    'diff'       => $before,         // all removed
                    'meta'       => ['slug' => $row->slug, 'sku' => $row->sku],
                ];
                DB::table('audit_logs')->insert([
                    'adminId'      => session('usid'),
                    'modelChanged' => 'products',
                    'action'       => 'hard_delete',
                    'changes'      => json_encode($auditPayload),
                    'createdAt'    => now(),
                    'entityId'     => $id,
                    'ip'           => $request->ip(),
                ]);

                DB::commit();
                return response()->json(['status' => 1, 'message' => 'Product permanently deleted.']);
            } catch (\Throwable $e) {
                DB::rollBack();
                report($e);
                return response()->json(['status' => 0, 'message' => 'Failed to delete product.'], 500);
            }
        }
    }
    private function relToPublicPath(string $rel): string
    {
        return public_path(ltrim(str_replace('public/', '', $rel), '/'));
    }

    // Move a file to trash instead of deleting
    private function moveRelToTrash(string $rel): ?string
    {
        $src = $this->relToPublicPath($rel);
        if (!File::exists($src)) return null;

        $base = basename($rel);
        $trashRelDir = 'public/upload/.trash/' . date('Ymd');
        $trashDir    = $this->relToPublicPath($trashRelDir);

        if (!File::isDirectory($trashDir)) File::makeDirectory($trashDir, 0775, true);

        $dstRel = $trashRelDir . '/' . $base;
        $dst    = $this->relToPublicPath($dstRel);

        // avoid collision
        if (File::exists($dst)) {
            $pi = pathinfo($base);
            $dstRel = $trashRelDir . '/' . $pi['filename'] . '-' . uniqid() . '.' . $pi['extension'];
            $dst    = $this->relToPublicPath($dstRel);
        }

        return @File::move($src, $dst) ? $dstRel : null;
    }
}
