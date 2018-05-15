<?php

namespace App\Http\Controllers\Staff;

use App\Facades\Staff;
use App\Http\Controllers\Controller;
use App\Models\PartModel;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

class PartController extends Controller
{
    private $partModel;

    public function __construct()
    {
        parent::__construct();
        $this->partModel = new PartModel();
    }

    public function index(){
        if (!Staff::isRoot()){
            flash_error(trans('part.text_permission'));
            return redirect(route('_dashboard'));
        }

        $filter_name = Request::get('filter_name') ? Request::get('filter_name') : '';
        $filter_status = Request::has('filter_status') ? Request::get('filter_status') : '';

        $filter = [
            'filter_name' => $filter_name,
            'filter_status' => $filter_status,
            'sort' => 'name',
            'order' => 'asc',
        ];

        $data['parts'] = $this->partModel->getList($filter);

        $data['filter_name'] = $filter_name;
        $data['filter_status'] = $filter_status;

        return view('staff.part_list', $data);
    }

    public function getForm($id = null){

        if (isset($id)){
            $info = $this->partModel->getById((int)$id);
        }

        if (isset($id)){
            $data['action'] = url('part/edit/' . (int)$id);
        }else{
            $data['action'] = url('part/add');
        }

        $data['cancel'] = url('part');

        if (Request::has('name')){
            $data['name'] = Request::old('name');
        } elseif (!empty($info)){
            $data['name'] = $info->name;
        } else {
            $data['name'] = '';
        }

        if (Request::has('sort_order')){
            $data['sort_order'] = Request::old('sort_order');
        } elseif (!empty($info)){
            $data['sort_order'] = $info->sort_order;
        } else {
            $data['sort_order'] = 0;
        }

        if (Request::has('status')){
            $data['status'] = Request::old('status');
        } elseif (!empty($info)){
            $data['status'] = $info->name;
        } else {
            $data['status'] = 1;
        }

        $data['text_modified'] = !empty($info) ? trans('main.text_edit') : trans('main.text_add');

        return view('staff.part_form', $data);
    }

    public function add(){
        $validator = $this->validateForm();
        if ($validator->fails()){
            flash_error(trans('main.text_error_form'));
            return Redirect::back()->withErrors($validator)->withInput();
        }else{
            $id = $this->partModel->add(Request::all());
            flash_success(trans('main.text_success_form'));

            switch (Request::input('_redirect')){
                case 'add':
                    return redirect('part/add');
                case 'edit':
                    return redirect('part/edit/' . $id);
                default:
                    return redirect('part');
            }
        }
    }

    public function edit($id = null){
        $validator = $this->validateForm();
        if ($validator->fails()){
            flash_error(trans('main.text_error_form'));
            return Redirect::back()->withErrors($validator)->withInput();
        }else{
            $this->partModel->edit((int)$id, Request::all());
            flash_success(trans('main.text_success_form'));

            switch (Request::input('_redirect')){
                case 'add':
                    return redirect('part/add');
                case 'edit':
                    return redirect('part/edit/' . (int)$id);
                default:
                    return redirect('part');
            }
        }
    }

    protected function validateForm($id = null){
        $rules = [
            'name' => 'required|between:5,95'
        ];

        $messages = [
            'name.required' => trans('part.error_name'),
            'name.between' => trans('part.error_name'),
        ];

        return Validator::make(Request::all(), $rules, $messages);
    }
}
