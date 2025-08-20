<?php

namespace App\Http\Controllers;

use App\Models\CrudModel;
use App\Models\UserModel;
use App\Models\LocalModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function list(Request $request)
    {
        // dd($request->all());
        $p['like'] = $request->input('q');
        $modal = new UserModel();
        $user = $modal->getUser($p);
        Paginator::useBootstrap();
        $p['user'] = $user;
        $p['header'] = true;
        $p['footer'] = true;
        $p['sidebar'] = true;
        $p['body'] = 'user.list';

        return view('layout.index', $p);
    }
    public function update(Request $request)
    {
        // print_r($request->all());die;
        $user = new UserModel;
        $modal = new CrudModel;
        if ($_POST['hiddenId'] == 0) {
            if (isset($_POST['email']) && isset($_POST['contactNumber'])) {
                $c['mail'] = $_POST['email'];
                $check = $user->checkUser($c);
                if (count($check) > 0) {
                    $data['status'] = 00;
                    $data['type'] = 'mail';
                    $data['message'] = 'Mail already exists';
                    return response()->json($data);
                }
                $g['number'] = $_POST['contactNumber'];
                $check = $user->checkUser($g);
                if (count($check) > 0) {
                    $data['status'] = 00;
                    $data['type'] = 'number';
                    $data['message'] = 'Number already exists';
                    return response()->json($data);
                }
            }
        }
        if (isset($_POST['userRole'])) {
            $p['userRole'] = $_POST['userRole'];
        }
        if (isset($_POST['firstName'])) {
            $p['fName'] = $_POST['firstName'];
        }
        if (isset($_POST['lastName'])) {
            $p['lName'] = $_POST['lastName'];
        }
        if (isset($_POST['email'])) {
            $p['mail'] = $_POST['email'];
        }
        if ($_POST['password']) {
            $p['password'] = Hash::make($_POST['password']);
        }
        if (isset($_POST['contactNumber'])) {
            $p['number'] = $_POST['contactNumber'];
        }
        if ($request->file('profilePic')) {
            $file = $request->file('profilePic');
            $filename = $file->getClientOriginalName();
            $directory = public_path('upload/user');
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0777, true);
            }
            $oldImagePath = $directory . '/' . $filename;
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
            $file->move($directory, $filename);
            $p['photo'] = url('/public/upload/user/' . $filename);
        }
        // print_r($p);die;
        if ($_POST['hiddenId'] != 0) {
            $p['updatedOn'] = Carbon::now()->timestamp;
            $p['UpdatedBy'] = session('usid');
            $table_name = 'tbl_people';
            $where1 = 'tbl_people.uniqueId';
            $where2 = $_POST['hiddenId'];
            $updateResp = $modal->updateData($table_name, $p, $where1, $where2);
            if ($updateResp != 0) {
                $data['status'] = 1;
                $data['message'] = 'User Update Successfully.';
            } else {
                $data['status'] = 0;
                $data['message'] = 'Something Went Wrong.';
            }
        } else {
            $p['active'] = '1';
            $p['createdOn'] = Carbon::now()->timestamp;
            $p['createdBy'] = session('usid');

            $table = 'tbl_people';
            $resp = $modal->insertData($table, $p); //print_r($resp);die;
            if ($resp != 0) {
                $data['status'] = 1;
                $data['message'] = 'User Add Successfully.';
            } else {
                $data['status'] = 0;
                $data['message'] = 'Something Went Wrong.';
            }
        }
        return json_encode($data);
    }
    public function get()
    {
        $p['id'] = $_POST['id'];
        $user = new UserModel;
        $data = $user->getUser($p);
        if (!$data) {
            return response()->json(['status' => 0, 'message' => 'User not found!']);
        }
        if ($data->photo) {
            $img = '<img width="70" height="70" class="objBImg" src="' . $data->photo . '" />';
        } else {
            $img = '<img width="70" height="70" class="objBImg d-none" src="" />';
        }
        $html['html'] = '<div class="row mb-2">
                                <div class="col-lg-6 col-6 ">
                                    <label for="name">First Name <span class="danger">*</span></label>
                                    <input type="text" class="form-control editfirstName" value="' . $data->fName . '" placeholder="Enter Your First Name">
                                </div>
                                <div class="col-lg-6 col-6">
                                    <label for="name">Last Name</label>
                                    <input type="text" class="form-control editlastName" value="' . $data->lName . '" placeholder="Enter Your Last Name">
                                </div>
                            </div>
                            <div class="col mb-2">
                                <label for="email">E-mail <span class="danger">*</span></label>
                                <input type="email" class="form-control" disabled value="' . $data->mail . '" placeholder="Enter Your email">
                            </div>

                            <div class="row mb-3">
                                <div class="col-lg-6 col-6">
                                    <label for="number"> Contact Number <span class="danger">*</span></label>
                                    <input type="number" class="form-control" disabled value="' . $data->number . '" placeholder="Enter Your contact number">
                                </div>

                                <div class="col-lg-6 col-6">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control editpassword" placeholder="Enter Your Password">
                                </div>
                            </div>
                            <input type="hidden" id="edit_hiddenId" value="' . $data->uniqueId . '" />
                            <div class="align-items-center d-flex gap-2" >
                                <div class="file-drop-area">
                                    <span class="">
                                        <img src="' . asset('/public/assets/img/theme/cloud-upload.svg') . '"
                                            width="20" alt="">
                                    </span>
                                    <span>Profile pic</span>
                                    <input type="file" multiple="" class="editprofilePic subBImg" name="editprofilePic[]">

                                </div>
                                <div class="border" >
                                    ' . $img . '
                                </div>
                            </div>
                            ';
        return json_encode($html);
    }
}
