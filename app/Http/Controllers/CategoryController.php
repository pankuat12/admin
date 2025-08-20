<?php

namespace App\Http\Controllers;

use App\Models\CrudModel;
use App\Models\CategoryModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class CategoryController extends Controller
{
    public function list(Request $request)
    {
        // dd($request->all());
        $p['like'] = $request->input('q');
        $category = new CategoryModel();
        $category = $category->getCategory($p);
        Paginator::useBootstrap();
        $p['category'] = $category;
        $p['header'] = true;
        $p['footer'] = true;
        $p['sidebar'] = true;
        $p['body'] = 'category.list';

        return view('layout.index', $p);
    }
    public function add()
    {
        $p[] = '';
        $category = new CategoryModel();
        $role = $category->getRole($p); //print_r($role);die;
        if (count($role) != 0) {
            $p['role'] = $role;
        }
        $p['header'] = true;
        $p['footer'] = true;
        $p['sidebar'] = true;
        $p['body'] = 'category.list';
        return view('layout.index', $p);
    }
    public function edit($id)
    {
        $category = new CategoryModel();
        $p['id'] = base64_decode($id);
        $data = $category->getCategory($p); //print_r($data);die;
        if (count($data) != 0) {
            $p['data'] = $data[0];
        }
        // print_r($p['state']);die;
        $p['header'] = true;
        $p['footer'] = true;
        $p['sidebar'] = true;
        $p['body'] = 'category.list';
        return view('layout.index', $p);
    }
    public function update(Request $request)
    {
        // print_r($request->all());die;
        $category = new CategoryModel;
        $modal = new CrudModel;
        $slug = Str::slug($request->categoryName);
        if ($request->hiddenId == 0) {
            $check = $category->checkCategory($slug);
            if (count($check) > 0) {
                $data['status'] = 0;
                $data['message'] = 'Category already exists!';
                return response()->json($data);
            }
        }
        if ($request->categoryName) {
            $p['name'] = $request->categoryName;
            $p['slug'] = $slug;
        }
        // print_r($p);die;
        if ($request->hiddenId != 0) {
            $p['updatedOn'] = Carbon::now()->timestamp;
            $p['UpdatedBy'] = session('usid');
            $table_name = 'categories';
            $where1 = 'categories.uniqueId';
            $where2 = $request->hiddenId;
            $updateResp = $modal->updateData($table_name, $p, $where1, $where2);
            if ($updateResp != 0) {
                $data['status'] = 1;
                $data['message'] = 'Category Update Successfully.';
            } else {
                $data['status'] = 0;
                $data['message'] = 'Something Went Wrong.';
            }
        } else {
            $p['createdOn'] = Carbon::now()->timestamp;
            $p['createdBy'] = session('usid');
            $table = 'categories';
            $resp = $modal->insertData($table, $p); //print_r($resp);die;
            if ($resp != 0) {
                $data['status'] = 1;
                $data['message'] = 'Category Add Successfully.';
            } else {
                $data['status'] = 0;
                $data['message'] = 'Something Went Wrong.';
            }
        }
        echo json_encode($data);
    }
    public function get(Request $request)
    {
        $category = new CategoryModel();
        $p['id'] = $request->id;
        $data = $category->getCategory($p);
        if (!$data) {
            return response()->json(['status' => 0, 'message' => 'Category not found!']);
        }
        $html = '
            <input type="hidden" id="editCategoryId" value="' . $data->uniqueId . '">
            <div class="mb-2">
                <label>Category Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control editCategoryName" value="' . $data->name . '">
            </div>
        ';
        return response()->json(['status' => 1, 'html' => $html]);
    }
}
