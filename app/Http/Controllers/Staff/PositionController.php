<?php

namespace App\Http\Controllers\Staff;

use App\Models\PositionModel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use App\Facades\Staff;
use Illuminate\Support\Facades\Validator;

class PositionController extends Controller
{
    private $positionModel;

    public function __construct()
    {
        parent::__construct();
        $this->positionModel = new PositionModel();
    }

    public function index(){
        if (!Staff::isRoot()){
            flash_error(trans('position.text_permission'));
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

        $data['positions'] = $this->positionModel->getList($filter);

        $data['filter_name'] = $filter_name;
        $data['filter_status'] = $filter_status;

        return view('staff.position_list', $data);
    }

    public function getForm($id = null){

        if (isset($id)){
            $info = $this->positionModel->getById((int)$id);
        }

        if (isset($id)){
            $data['action'] = url('position/edit/' . (int)$id);
        }else{
            $data['action'] = url('position/add');
        }

        $data['cancel'] = url('position');

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

        if (Request::has('sort_permission')){
            $data['sort_permission'] = Request::old('sort_permission');
        } elseif (!empty($info)){
            $data['sort_permission'] = $info->sort_permission;
        } else {
            $data['sort_permission'] = 0;
        }

        if (Request::has('status')){
            $data['status'] = Request::old('status');
        } elseif (!empty($info)){
            $data['status'] = $info->name;
        } else {
            $data['status'] = 1;
        }

        $data['sort_permission_exist'] = [];
        if ($sort_permissions = $this->positionModel->getSortPermissions()){
            foreach ($sort_permissions as $sort_permission) {
                $data['sort_permission_exist'][] = $sort_permission->sort_permission;
            }
        }

        $data['text_modified'] = !empty($info) ? trans('main.text_edit') : trans('main.text_add');

        return view('staff.position_form', $data);
    }

    public function add(){
        $validator = $this->validateForm();
        if ($validator->fails()){
            flash_error(trans('main.text_error_form'));
            return Redirect::back()->withErrors($validator)->withInput();
        }else{
            $id = $this->positionModel->add(Request::all());
            flash_success(trans('main.text_success_form'));

            switch (Request::input('_redirect')){
                case 'add':
                    return redirect('position/add');
                case 'edit':
                    return redirect('position/edit/' . $id);
                default:
                    return redirect('position');
            }
        }
    }

    public function edit($id = null){
        $validator = $this->validateForm();
        if ($validator->fails()){
            flash_error(trans('main.text_error_form'));
            return Redirect::back()->withErrors($validator)->withInput();
        }else{
            $this->positionModel->edit((int)$id, Request::all());
            flash_success(trans('main.text_success_form'));

            switch (Request::input('_redirect')){
                case 'add':
                    return redirect('position/add');
                case 'edit':
                    return redirect('position/edit/' . (int)$id);
                default:
                    return redirect('position');
            }
        }
    }

    protected function validateForm($id = null){
        $rules = [
            'name' => 'required|between:5,95',
            'sort_permission' => 'required|min:1|max:120|exist',
        ];

        $messages = [
            'name.required' => trans('position.error_name'),
            'name.between' => trans('position.error_name'),
            'sort_permission.required' => trans('position.error_sort_permission'),
            'sort_permission.max' => trans('position.error_sort_permission'),
            'sort_permission.min' => trans('position.error_sort_permission'),
            'sort_permission.exist' => trans('position.error_sort_permission_exit'),
        ];

        $validator = Validator::make(Request::all(), $rules, $messages);

        $validator->addExtension('exist', function ($attribute, $value, $parameters, $validator) {
            if ($sort_permissions = $this->positionModel->getSortPermissions()){
                foreach ($sort_permissions as $sort_permission) {
                    if ($value == $sort_permission->sort_permission){
                        return false;
                        break;
                    }
                }
            }
            return true;
        });

        return $validator;
    }
}
