<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CategoryModel extends Model
{
    public function getCategory($p)
    {
        $query = DB::table('categories');
        if (isset($p['id'])) {
            $query->where('uniqueId', $p['id']);
            return $query->first();
        }
        if (isset($p['like']) && !empty($p['like'])) {
            $query->where(function ($q) use ($p) {
                $q->where('name', 'like', '%' . $p['like'] . '%')
                    ->orWhere('slug', 'like', '%' . $p['like'] . '%');
            });
        }
        $query->where('isTrashed', 0);
        return $query->orderBy('uniqueId', 'desc')->paginate(10);
    }
    public function checkCategory($p)
    {
        return DB::table('categories')->where('slug', $p)->where('isTrashed', 0)->select('slug')->get();
    }
    public function getRole()
    {
        $query = DB::table('tbl_roles as tr')->select('tr.roleId', 'tr.roleName');
        $query->where('tr.isTrashed', '=', 0);
        $result = $query->get();
        return $result;
    }
}
