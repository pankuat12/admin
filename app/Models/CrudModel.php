<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CrudModel extends Model
{
    public function insertDataWithid($table_name, $data)
    {
        $resp = DB::table($table_name)->insertGetId($data);
        return $resp;
    }
    public function insertData($table_name, $data)
    {
        // DB::enableQueryLog();
        $resp = DB::table($table_name)->insert($data);
        // dd(DB::getQueryLog());
        return $resp;
    }
    public function updateData($table_name, $data, $where1, $where2)
    {
        // DB::enableQuerylog();
        $resp = DB::table($table_name)
            ->where($where1, $where2)
            ->update($data);
        // dd(DB::getQuerylog());
        return $resp;
    }
    public function sendMail($p)
    {
        $data["appointment"] = $p['appointment'];
        $data["franchise"] = $p['franchise'];
        $data["mail"] = $p['franchise']->mail;
        $data["title"] = $p['title'];
        $data["subject"] = $p['subject'];
        // print_r($data);die;
        try {
            Mail::send('tpls.notification', ['data' => $data], function ($message) use ($data) {
                $message->to($data["mail"], $data["mail"])
                    ->subject($data["title"]);
            });
            $resp['status'] = 1;
        } catch (Exception $e) {
            $resp['status'] = 0;
        }
        return $resp;
    }
}
