<?php

namespace App\Http\Controllers;

use App\Models\AdminLogModel;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class AdminLogController extends Controller
{
    public function list(Request $request)
    {
        $p = [
            'q'        => $request->input('q'),            // free text (action/model/adminId/entityId/IP)
            'action'   => $request->input('action'),       // create|update|delete|hard_delete
            'model'    => $request->input('model'),        // e.g. products
            'adminId'  => $request->input('adminId'),      // numeric
            'entityId' => $request->input('entityId'),     // numeric
            'from'     => $request->input('from'),         // YYYY-MM-DD
            'to'       => $request->input('to'),           // YYYY-MM-DD
            'perPage'  => (int)($request->input('perPage', 20)),
        ];

        $m = new AdminLogModel();
        $logs = $m->getLogs($p);
        $logs->getCollection()->transform(function ($row) use ($m) {
            $row->pairs = $m->changedPairs($row); // array: field => [old, new]
            return $row;
        });
        Paginator::useBootstrap();
        $p['logs']   = $logs;
        $p['header'] = true;
        $p['footer'] = true;
        $p['sidebar'] = true;
        $p['body']   = 'audit.list';
        return view('layout.index', $p);
    }
}
