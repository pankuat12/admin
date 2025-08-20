<?php

namespace App\Http\Controllers;

use App\Models\AuthModel;
use Illuminate\Support\Facades\Cookie;
use App\Helpers\login;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        $p['body'] = 'auth.login';
        return view('layout.index', $p);
    }
    public function adminLogin()
    {
        $auth = new AuthModel;
        if (isset($_POST['userMail'])) {
            $d['mail'] = $_POST['userMail'];
            $UD = $auth->logByPass($d);
            if (count($UD) == 1) {
                if ($UD[0]->active == 1) {
                    if (Hash::check($_POST['userPassword'], $UD[0]->password)) {
                        $data['status'] = 1;
                        $data['message'] = 'Login Successfully';
                        session([
                            'usid' => $UD[0]->uniqueId,
                        ]);
                    } else {
                        $data['status'] = 2;
                        $data['message'] = 'Password Not Match !';
                        $data['type'] = 'password';
                    }
                } else {
                    $data['status'] = 0;
                    $data['message'] = 'User Not Active  | Try Sometime Later !';
                }
            } else {
                $data['status'] = 2;
                $data['message'] = 'Mail Not Found | Try With Different Mail !';
                $data['type'] = 'mail';
            }
        } else {
            $data['status'] = 0;
            $data['message'] = 'Missing parameter | Try Sometime Later !';
        }
        return json_encode($data);
    }
    public function logout()
    {
        session()->forget(['usid']);
        return redirect('/')->with('pass', 'Logout Successful !');
    }
}
