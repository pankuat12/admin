<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserModel extends Model
{
    public function getUser($p)
    {
        $query = DB::table('tbl_people as tp')->select('tp.*');
        if (isset($p['id'])) {
            $query->where('tp.uniqueId', $p['id']);
            return $query->first();
        }
        if (isset($p['like']) && !empty($p['like'])) {
            $query->where(function ($q) use ($p) {
                $q->where('tp.fName', 'like', '%' . $p['like'] . '%')
                    ->orWhere('tp.lName', 'like', '%' . $p['like'] . '%')
                    ->orWhere('tp.mail', 'like', '%' . $p['like'] . '%')
                    ->orWhere('tp.number', 'like', '%' . $p['like'] . '%');
            });
        }
        $query->where('tp.isTrashed', 0);
        return $query->orderBy('tp.uniqueId', 'desc')->paginate(10);
    }
    public function checkUser($p)
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
    public function getRole()
    {
        $query = DB::table('tbl_roles as tr')->select('tr.roleId', 'tr.roleName');
        $query->where('tr.isTrashed', '=', 0);
        $result = $query->get();
        return $result;
    }
}
