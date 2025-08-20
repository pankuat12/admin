<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductModel extends Model
{
    public function getProduct($p)
    {
        if (isset($p['id'])) {
            $query = DB::table('products as tp')->select('tp.*');
            $query->where('tp.uniqueId', $p['id']);
            return $query->first();
        }

        $query = DB::table('products as tp')
            ->select('tp.*', 'c.name as category_name')
            ->leftJoin('categories as c', 'c.uniqueId', '=', 'tp.category_id')
            ->where('tp.isTrashed', 0);

        if (!empty($p['like'])) {
            $like = $p['like'];
            $query->where(function ($q) use ($like) {
                $q->where('tp.name', 'like', "%{$like}%")
                    ->orWhere('tp.slug', 'like', "%{$like}%");
            });
        }
        // Category
        if (!empty($p['category_id'])) {
            $query->where('tp.category_id', $p['category_id']);
        }

        // Featured (expects column tp.isFeatured TINYINT(1) 0/1). If you use other name, tweak here.
        if (isset($p['featured']) && $p['featured'] !== '') {
            $query->where('tp.featured', (int) $p['featured']);
        }

        // Status (expects tp.status VARCHAR) – if you use isActive, see note below
        if (!empty($p['status'])) {
            $query->where('tp.status', $p['status']); // e.g. ['active','inactive','draft','out_of_stock']
        }

        // Price range (using sale_price)
        if (!empty($p['min_price'])) {
            $query->where('tp.sale_price', '>=', (float) $p['min_price']);
        }
        if (!empty($p['max_price'])) {
            $query->where('tp.sale_price', '<=', (float) $p['max_price']);
        }

        // Pagination with state
        $perPage = 10;

        return $query->orderBy('tp.uniqueId', 'desc')
            ->paginate($perPage)
            ->appends([
                'q'           => $p['like'],
                'category_id' => $p['category_id'] ?? null,
                'featured'    => $p['featured'] ?? null,
                'status'      => $p['status'] ?? [],
                'min_price'   => $p['min_price'] ?? null,
                'max_price'   => $p['max_price'] ?? null,
            ]);
    }
    public function checkProduct($p)
    {
        $query = DB::table('tbl_people as tp')->select('tp.uniqueId');
        if (isset($p['mail'])) {
            $query->where('tp.mail', '=', $p['mail']);
        }
        if (isset($p['number'])) {
            $query->where('tp.number', '=', $p['number']);
        }
        $query->where('tp.isTrashed', '=', 0);
        $result = $query->get();
        return $result;
    }
    public function getCategory()
    {
        $result = DB::table('categories')->select('uniqueId', 'name')->where('isTrashed', '=', 0)->get();
        return $result;
    }
    public function getPriceBounds()
    {
        $q = DB::table('products')->where('isTrashed', 0);
        return (object)[
            'min' => (float) ($q->min('sale_price') ?? 0),
            'max' => (float) ($q->max('sale_price') ?? 0),
        ];
    }
}
