@extends('layouts.admin.app')
@section('styles')
    <link href="{{ asset('admin/assets/css/select2.min.css') }}" rel="stylesheet"/>
@endsection
@section('scripts')
    <script src="{{ asset('admin/assets/js/select2.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $(".select2").select2({language: "bg"});
            $('.select2').css('min-width', '100%');
        });
    </script>
@endsection
@section('content')
    @include('admin.pages.breadcrumbs')
    @include('admin.notify')
    <div class="col-md-12 m-b-10">
        <div class="form-group">
            <label class="control-label page-label col-md-3"><span class="text-purple">* </span>{{ __('admin.pages.category') }}:</label>
            <div class="col-md-4">
                <select class="form-control select2 products-select select2-hidden-accessible" name="category_id" data-select2-id="1" tabindex="-1" aria-hidden="true">
                    <option value="" data-select2-id="3">@lang('admin.common.please_select')</option>
                    @foreach($categories as $index => $category)
                        @include('admin.pages.categories_options', ['category' => $category, 'depth' => [$index + 1]])
                    @endforeach
                </select>

            </div>
        </div>
    </div>
@endsection
