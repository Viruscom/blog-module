@php use App\Models\CategoryPage\CategoryPage; @endphp@extends('layouts.admin.app')
@section('styles')
    <link href="{{ asset('admin/assets/css/select2.min.css') }}" rel="stylesheet"/>
@endsection

@section('scripts')
    <script src="{{ asset('admin/assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('admin/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('admin/plugins/ckeditor/loadCustomCkEditorPlugins.js') }}"></script>
    <script>
        try {
            CKEDITOR.timestamp = new Date();
        } catch {
        }
        $(".select2").select2({language: "bg"});
    </script>
@endsection

@section('content')
    @include('admin.category_pages.breadcrumbs')
    @include('admin.notify')
    <form action="{{ route('admin.category-page.update', ['id'=> $categoryPage->id]) }}" method="POST" enctype="multipart/form-data">
        <div class="col-xs-12 p-0">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="position" value="{{old('position')}}">
            <div class="bg-grey top-search-bar">
                <div class="action-mass-buttons pull-right">
                    <button type="submit" name="submit" value="submit" class="btn btn-lg save-btn margin-bottom-10"><i class="fas fa-save"></i></button>
                    <a href="{{ route('admin.category-page.index') }}" role="button" class="btn btn-lg back-btn margin-bottom-10"><i class="fa fa-reply"></i></a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <ul class="nav nav-tabs">
                    @foreach($languages as $language)
                        <li @if($language->code === config('default.app.language.code')) class="active" @endif><a data-toggle="tab" href="#{{$language->code}}">{{$language->code}} <span class="err-span-{{$language->code}} hidden text-purple"><i class="fas fa-exclamation"></i></span></a></li>
                    @endforeach
                </ul>
                <div class="tab-content">
                    @foreach($languages as $language)
                            <?php
                            $langTitle            = 'title_' . $language->code;
                            $langFirstText        = 'announce_' . $language->code;
                            $langTextUnderAdboxes = 'description_' . $language->code;
                            ?>
                        @php
                            $categoryPageTranslate = is_null($categoryPage->translate($language->code)) ? $categoryPage : $categoryPage->translate($language->code);
                        @endphp

                        <div id="{{$language->code}}" class="tab-pane fade in @if($language->code === config('default.app.language.code')) active @endif">
                            <div class="form-group @if($errors->has($langTitle)) has-error @endif">
                                <label class="control-label p-b-10" for="{{$langTitle}}"><span class="text-purple">* </span>@lang('admin.common.title') / link (<span class="text-uppercase">{{$language->code}}</span>):</label>
                                <input class="form-control" id="{{$langTitle}}" type="text" name="{{$langTitle}}" value="{{ old($langTitle) ?: $categoryPageTranslate->title }}">
                                @if($errors->has($langTitle))
                                    <span class="help-block">{{ trans($errors->first($langTitle)) }}</span>
                                @endif
                            </div>
                            <div class="form-group m-b-0 @if($errors->has($langFirstText)) has-error @endif">
                                <label class="control-label p-b-10"><span class="text-purple">* </span>{{ __('admin.category_pages.text_above_boxes') }} ({{$language->code}}):</label>
                                <textarea name="{{$langFirstText}}" class="ckeditor col-xs-12" rows="9">
                                    {{ old($langFirstText) ?: $categoryPageTranslate->announce }}
                                </textarea>
                                @if($errors->has($langFirstText))
                                    <span class="help-block">{{ trans($errors->first($langFirstText)) }}</span>
                                @endif
                            </div>
                            <div class="form-group m-b-0 m-t-10 @if($errors->has($langTextUnderAdboxes)) has-error @endif">
                                <label class="control-label p-b-10"><span class="text-purple">* </span>{{ __('admin.category_pages.text_bottom_boxes') }} ({{$language->code}}):</label>
                                <textarea name="{{$langTextUnderAdboxes}}" class="ckeditor col-xs-12" rows="9">
                                    {{ old($langTextUnderAdboxes) ?: $categoryPageTranslate->description }}
                                </textarea>
                                @if($errors->has($langTextUnderAdboxes))
                                    <span class="help-block">{{ trans($errors->first($langTextUnderAdboxes)) }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @include('admin.partials.on_edit.seo', ['model' => $categoryPage->seoFields])
                <div class="form form-horizontal">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="control-label col-md-3 p-t-0">{{ __('admin.common.visualization') }}:</label>
                            <div class="col-md-9">
                                <span class="module">
                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="visualization_type_id" value="{{ CategoryPage::$VISUALIZATION_TYPE_FIRST }}" {{old('visualization_type_id') == CategoryPage::$VISUALIZATION_TYPE_FIRST || $categoryPage->visualization_type_id == CategoryPage::$VISUALIZATION_TYPE_FIRST ? 'checked': ''}}>
                                        <div class="state p-primary-o">
                                            <label>{{ trans('admin.category_pages.visualisation_type_1') }}</label>
                                        </div>
                                    </div>
                                </span>

                                <span class="module">
                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="visualization_type_id" value="{{ CategoryPage::$VISUALIZATION_TYPE_SECOND }}" {{old('visualization_type_id') == CategoryPage::$VISUALIZATION_TYPE_SECOND || $categoryPage->visualization_type_id == CategoryPage::$VISUALIZATION_TYPE_SECOND ? 'checked': ''}}>
                                        <div class="state p-primary-o">
                                            <label>{{ trans('admin.category_pages.visualisation_type_2') }}</label>
                                        </div>
                                    </div>
                                </span>

                                <span class="module">
                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="visualization_type_id" value="{{ CategoryPage::$VISUALIZATION_TYPE_THIRD }}" {{old('visualization_type_id') == CategoryPage::$VISUALIZATION_TYPE_THIRD || $categoryPage->visualization_type_id == CategoryPage::$VISUALIZATION_TYPE_THIRD ? 'checked': ''}}>
                                        <div class="state p-primary-o">
                                            <label>{{ trans('admin.category_pages.visualisation_type_3') }}</label>
                                        </div>
                                    </div>
                                </span>

                                <span class="module">
                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="visualization_type_id" value="{{ CategoryPage::$VISUALIZATION_TYPE_LIST }}" {{old('visualization_type_id') == CategoryPage::$VISUALIZATION_TYPE_LIST || $categoryPage->visualization_type_id == CategoryPage::$VISUALIZATION_TYPE_LIST ? 'checked': ''}}>
                                        <div class="state p-primary-o">
                                            <label>{{ trans('admin.category_pages.visualisation_type_4') }}</label>
                                        </div>
                                    </div>
                                </span>

                                @if(array_key_exists('OneHotel', $activeModules))
                                    <span class="module">
                                    <div class="pretty p-default p-round">
                                        <input type="radio" name="visualization_type_id" value="{{ CategoryPage::$VISUALIZATION_ONE_HOTEL_ROOMS }}" {{old('visualization_type_id') == CategoryPage::$VISUALIZATION_ONE_HOTEL_ROOMS || $categoryPage->visualization_type_id == CategoryPage::$VISUALIZATION_ONE_HOTEL_ROOMS ? 'checked': ''}}>
                                        <div class="state p-primary-o">
                                            <label>{{ trans('admin.category_pages.visualisation_type_5') }}</label>
                                        </div>
                                    </div>
                                </span>
                                @endif
                            </div>
                        </div>
                        <hr>
                        @include('admin.partials.on_edit.form_fields.upload_file', ['model' => $categoryPage, 'deleteRoute' => route('admin.category-page.delete-image', ['id'=>$categoryPage->id])])
                        @if(array_key_exists('OneHotel', $activeModules))
                            @include('onehotel::admin.partials.category_page_checkboxes_edit', ['model' => $categoryPage])
                        @endif
                        <hr>
                        @include('admin.partials.on_edit.active_checkbox', ['model' => $categoryPage])
                        <hr>
                        @include('admin.partials.on_edit.position_in_site_button', ['model' => $categoryPage, 'models' => $categoryPages])
                    </div>
                    @include('admin.partials.on_edit.form_actions_bottom')
                </div>
            </div>
        </div>
    </form>
@endsection
