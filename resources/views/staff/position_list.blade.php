@extends('layout.master')
@section('heading_title', trans('position.heading_title'))
@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h2>{{ trans('main.text_list') }}</h2>
                    <div class="pull-right">
                        <a class="btn btn-primary btn-sm" href="{{ url('position/add') }}"><i class="fa fa-plus"></i> {{ trans('main.text_add') }}
                        </a>
                        <a class="btn btn-warning btn-sm" onclick="return confirm('{{ trans('main.text_confirm_delete') }}') ? $('#form').submit() : false;"><i class="fa fa-times"></i> {{ trans('main.text_delete') }}
                        </a>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <form id="form" action="{{ url('position/delete') }}" class="form-horizontal form-label-left" method="post">
                        <table id="datatable-checkbox" class="table table-striped table-bordered bulk_action">
                            <thead>
                            <tr>
                                <th><input type="checkbox" id="check-all" class="flat"></th>
                                <th>{{ trans('position.text_name') }}</th>
                                <th class="text-center">{{ trans('main.text_sort_order') }}</th>
                                <th class="text-center">{{ trans('main.text_sort_permission') }}</th>
                                <th class="text-center">{{ trans('main.text_status') }}</th>
                                <th class="text-right">{{ trans('main.text_action') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                </td>
                                <td><input type="text" class="form-control" value="{{ $filter_name }}" name="filter_name"></td>
                                <td class="text-center"></td>
                                <td class="text-center"></td>
                                <td class="text-center">
                                    <select name="filter_status" id="status" class="form-control col-md-7 col-xs-12" >
                                        <option value=""></option>
                                        <option value="0" @if($filter_status == '0') selected @endif>{{ trans('main.text_disabled') }}</option>
                                        <option value="1" @if($filter_status == '1') selected @endif>{{ trans('main.text_enabled') }}</option>
                                    </select>
                                </td>
                                <td class="text-right">
                                    <a class="btn btn-sm btn-primary filter">{{ trans('main.text_filter') }}</a>
                                </td>
                            </tr>
                            @if($positions)
                                @foreach($positions as $position)
                                    <tr>
                                        <td>
                                            <input type="checkbox" value="{{ $position->id }}" class="flat check">
                                        </td>
                                        <td>{{ $position->name }}</td>
                                        <td class="text-center">{{ $position->sort_order }}</td>
                                        <td class="text-center">{{ $position->sort_permission }}</td>
                                        <td class="text-center">
                                            @if($position->status)
                                                <i class="fa fa-check-circle text-primary"></i>
                                            @else
                                                <i class="fa fa-times-circle text-danger"></i>
                                            @endif
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ url('position/edit/' . $position->id) }}" class="btn btn-sm btn-primary">{{ trans('main.text_edit') }}</a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">
                                        {{ trans('main.text_no_result') }}
                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                        <div>
                            {{ $positions->links() }}
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script_footer')
    <script>
        $('.filter').bind('click', function (e) {
            e.preventDefault();
            href = '{{ url('position') }}?filter';
            filter_name = $('input[name="filter_name"]').val();
            filter_status = $('select[name="filter_status"]').find('option:selected').val();
            filter = false;
            if (filter_name !== ''){
                href += '&filter_name=' + filter_name;
                filter = true;
            }
            if (filter_status !== ''){
                href += '&filter_status=' + filter_status;
                filter = true;
            }
            if (filter === true){
                location = href;
            }else{
                location = '{{ url('position') }}';
            }
        })
    </script>
@endpush