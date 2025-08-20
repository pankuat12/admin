<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AuthModel extends Model
{
    public function logByPass($p)
    {
        $query = DB::table('tbl_people')->select('*');
        $query->where('mail', '=', $p['mail']);
        $query->where('isTrashed', '=', 0);
        $result = $query->get();
        return $result;
    }
}
