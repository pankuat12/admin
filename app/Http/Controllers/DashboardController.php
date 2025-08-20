<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function Dashboard()
    {
        $p['tu'] = db::table('tbl_people')->where('active',1)->where('isTrashed',0)->count();
        $p['tc'] = db::table('categories')->where('isTrashed',0)->count();
        $p['tp'] = db::table('products')->where('isTrashed',0)->count();
        $p['recentProduct'] = db::table('products')->where('isTrashed',0)->orderBy('uniqueId')->limit(6)->get();
        $p['lowStock'] = db::table('products')->where('isTrashed',0)->orderBy('stock_count','asc')->limit(6)->get();
        $p['name'] =db::table('tbl_people')->where('active',1)->where('isTrashed',0)->value('fname');
        $p['header'] = true;
        $p['sidebar'] = true;
        $p['footer'] = true;
        $p['body'] = 'dashboard';
        return view('layout.index', $p);
    }
}
