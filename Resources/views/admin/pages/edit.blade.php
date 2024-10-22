@php use App\Helpers\ModuleHelper; @endphp@extends('layouts.admin.app')
@section('styles')
    <link href="{{ asset('admin/assets/css/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('admin/assets/css/multi-select.css') }}" rel="stylesheet"/>
    <link href="{{ asset('admin/plugins/foundation-datepicker/datepicker.css') }}" rel="stylesheet"/>
@endsection

@section('scripts')
    <script src="{{ asset('admin/assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/jquery.multi-select.js') }}"></script>
    <script src="{{ asset('admin/plugins/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('admin/plugins/foundation-datepicker/datepicker.js') }}"></script>
    <script src="{{ asset('admin/plugins/ckeditor/loadCustomCkEditorPlugins.js') }}"></script>
    <script>
        $(".select2").select2({language: "bg"});

        var focusedEditor;
        CKEDITOR.timestamp = new Date();
        CKEDITOR.on('instanceReady', function (evt) {
            var editor = evt.editor;
            editor.on('focus', function (e) {
                focusedEditor = e.editor.name;
            });
        });
        var nowTemp  = new Date();
        var now      = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);
        var checkin  = $('#dpd1').fdatepicker({
            onRender: function (date) {
                return '';
            },
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            if (ev.date.valueOf() > checkout.date.valueOf()) {
                var newDate = new Date(ev.date)
                newDate.setDate(newDate.getDate() + 1);
                checkout.update(newDate);
            }
            checkin.hide();
            $('#dpd2')[0].focus();
        }).data('datepicker');
        var checkout = $('#dpd2').fdatepicker({
            onRender: function (date) {
                return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
            },
            format: 'yyyy-mm-dd'
        }).on('changeDate', function (ev) {
            checkout.hide();
        }).data('datepicker');
        $('#oneDayEventPicker').fdatepicker({
            onRender: function (date) {
                return '';
            },
            format: 'yyyy-mm-dd'
        });
    </script>
@endsection

@section('content')
    @include('admin.pages.breadcrumbs')
    @include('admin.notify')
    
    <form class="my-form" action="{{ route('admin.pages.update', ['id' => $contentPage->id]) }}" method="POST" data-form-type="update" enctype="multipart/form-data" autocomplete="off">
        <div class="col-xs-12 p-0">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="position" value="{{(old('position')) ? old('position') : $contentPage->position}}">
            <div class="navigation-id-old hidden">{{old('category_page_id')}}</div>
            <div class="navigation-id-current hidden">{{$contentPage->category_page_id}}</div>
            <div class="one-day-event-old hidden">{{ old('one_day_event') }}</div>

            <div class="bg-grey top-search-bar">
                <div class="action-mass-buttons pull-right">
                    <button type="submit" name="submit" value="submit" class="btn btn-lg save-btn margin-bottom-10"><i class="fas fa-save"></i></button>
                    <a href="{{ url()->previous() }}" role="button" class="btn btn-lg back-btn margin-bottom-10"><i class="fa fa-reply"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-12 col-xs-12">
            <div class="form-group">
                <label class="control-label page-label col-md-3"><span class="text-purple">* </span>{{ __('admin.pages.category_page') }}:</label>
                <div class="col-md-4">
                    <select class="form-control select2 inner-page-contents-select navigation_type_select" name="category_page_id">
                        @foreach($pageCategories as $pageCategory)
                            <option value="{{ $pageCategory->id }}" {{ $pageCategory->id === $contentPage->categoryPage->id ? 'selected': '' }} visualization_type="{{ $pageCategory->visualization_type_id }}">{{ $pageCategory->title}}</option>
                        @endforeach
                    </select>
                    <span class="hidden old-category-page-id">{{ old('category_page_id') }}</span>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-warning"><strong>{{ __('admin.common.warning') }} </strong>{{ __('admin.pages.change_title_warning') }}</div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12 col-xs-12">
                <ul class="nav nav-tabs nav-tabs-first">
                    @foreach($languages as $language)
                        <li @if($language->code === config('default.app.language.code')) class="active" @endif><a data-toggle="tab" href="#{{$language->code}}">{{$language->code}} <span class="err-span-{{$language->code}} hidden text-purple"><i class="fas fa-exclamation"></i></span></a></li>
                    @endforeach
                </ul>
                <div class="tab-content m-b-0">
                    @foreach($languages as $language)
                        @php
                            $contentPageTranslate = is_null($contentPage->translate($language->code)) ? $contentPage : $contentPage->translate($language->code);
                        @endphp
                        <div id="{{$language->code}}" class="tab-pane fade in @if($language->code === config('default.app.language.code')) active @endif">
                            @include('admin.partials.on_edit.form_fields.input_text', ['model'=> $contentPageTranslate, 'fieldName' => 'title_' . $language->code, 'label' => trans('admin.title'), 'required' => true])
                            @include('admin.partials.on_edit.form_fields.textarea_without_ckeditor', ['model'=> $contentPageTranslate, 'fieldName' => 'announce_' . $language->code, 'rows' => 4, 'label' => trans('admin.announce'), 'required' => false])
                            @include('admin.partials.on_edit.form_fields.textarea', ['model'=> $contentPageTranslate, 'fieldName' => 'description_' . $language->code, 'rows' => 9, 'label' => trans('admin.description'), 'required' => false])
                            @include('admin.partials.on_edit.show_in_language_visibility_checkbox', ['model'=> $contentPage, 'fieldName' => 'visible_' . $language->code])

                            <div class="additional-textareas-wrapper">
                                <hr>
                                <h3>{{ __('admin.common.additional_texts') }}</h3>
                                <div class="panel-group" id="accordion-{{$language->id}}">
                                    @for($i=1; $i<7; $i++)
                                        @include('admin.partials.on_edit.additional_title_and_text', ['model' => $contentPageTranslate, 'language' => $language, 'i' => $i])
                                    @endfor
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <ul class="nav nav-tabs-second">
                    @foreach($languages as $language)
                        <li @if($language->code === config('default.app.language.code')) class="active" @endif><a langcode="{{$language->code}}">{{$language->code}}</a></li>
                    @endforeach
                </ul>
                @include('admin.partials.on_edit.seo', ['model' => $contentPage->seoFields])
                <div class="form form-horizontal">
                    <div class="form-body">
                        <div class="form-group @if($errors->has('price')) has-error @endif">
                            <label class="control-label col-md-3">{{ __('admin.common.price') }}:</label>
                            <div class="col-md-3">
                                <input class="form-control" type="number" step="0.01" name="price" value="{{ old('price') ? old('price') : $contentPage->price }}">
                                @if($errors->has('price'))
                                    <span class="help-block">{{ trans($errors->first('price')) }}</span>
                                @endif
                                <div class="col-md-12 m-t-10 p-l-0">
                                    <div class="pretty p-default p-square">
                                        <input type="checkbox" name="from_price" class="tooltips" data-toggle="tooltip" data-placement="right" data-original-title="{{ __('admin.common.activate_deactivate_from_price') }}" data-trigger="hover" {{ ($contentPage->from_price) ? 'checked' : '' }} />
                                        <div class="state p-primary">
                                            <label>{{ __('admin.pages.activate_from_price') }}</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(array_key_exists('OneHotel', ModuleHelper::getActiveModules()))
                            <div class="form-group @if($errors->has('discounted_price')) has-error @endif">
                                <label class="control-label col-md-3">{{ __('onehotel::admin.discounted_price') }}:</label>
                                <div class="col-md-3">
                                    <input class="form-control" type="number" step="0.01" name="discounted_price" value="{{ old('price') ? old('price') : $contentPage->discounted_price }}">
                                    @if($errors->has('discounted_price'))
                                        <span class="help-block">{{ trans($errors->first('discounted_price')) }}</span>
                                    @endif
                                    <div class="col-md-12 m-t-10 p-l-0">
                                        <div class="pretty p-default p-square">
                                            <input type="checkbox" name="from_discounted_price" class="tooltips" data-toggle="tooltip" data-placement="right" data-original-title="{{ __('admin.common.activate_deactivate_from_price') }}" data-trigger="hover" {{ ($contentPage->from_discounted_price) ? 'checked' : '' }} />
                                            <div class="state p-primary">
                                                <label>{{ __('admin.pages.activate_from_price') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <hr>
                        <div class="form-group @if($errors->has('one_day_event_date')) has-error @endif one-day-event hidden">
                            <label class="control-label col-md-3">{{ __('admin.pages.one_day_event') }}:</label>
                            <div class="col-md-3">
                                <div class="input-group m-b-10">
                                    <div class="input-group-addon">{{ __('admin.pages.date') }}</div>
                                    <input type="text" class="form-control" value="{{ $contentPage->one_day_event_date }}" name="one_day_event_date" id="oneDayEventPicker">
                                </div>
                                @if($errors->has('one_day_event_date'))
                                    <span class="help-block">{{ trans($errors->first('one_day_event_date')) }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group @if($errors->has('date_from_to')) has-error @endif period-event">
                            <label class="control-label col-md-3">{{ __('admin.pages.for_period') }}:</label>
                            <div class="col-md-3">
                                <div class="input-group m-b-10">
                                    <div class="input-group-addon">{{ __('admin.pages.from_date') }}</div>
                                    <input type="text" class="form-control" value="{{ $contentPage->from_date }}" name="from_date" id="dpd1">
                                </div>
                                <div class="input-group">
                                    <div class="input-group-addon">{{ __('admin.pages.to_date') }}</div>
                                    <input type="text" class="form-control" value="{{ $contentPage->to_date }}" name="to_date" id="dpd2">
                                </div>
                                @if($errors->has('date_from_to'))
                                    <span class="help-block">{{ trans($errors->first('date_from_to')) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-3">{{ __('admin.pages.type_event') }}:</label>
                            <div class="col-md-3 m-t-10 p-l-0">
                                <div class="hidden one-day-event-div">{{$contentPage->one_day_event}}</div>
                                <div class="pretty p-default p-square">
                                    <input type="checkbox" name="one_day_event" class="tooltips one-day-event-checkbox" data-toggle="tooltip" data-placement="right" data-original-title="{{ __('admin.pages.activate_deactivate_one_day_event') }}" data-trigger="hover" {{ old('one_day_event') ? old('one_day_event') : ($contentPage->one_day_event ? 'checked' : '') }} />
                                    <div class="state p-primary">
                                        <label>{{ __('admin.pages.one_day_event') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        @if(!$contentPage->categoryPage->isListTypeVisualization())
                            @include('admin.partials.on_edit.form_fields.upload_file', ['model' => $contentPage, 'deleteRoute' => route('admin.pages.delete-image', ['id'=>$contentPage->id])])
                            <hr>
                        @endif
                        @include('admin.partials.on_edit.active_checkbox', ['model' => $contentPage])
                        <hr>
                        @include('admin.partials.on_edit.position_in_site_button', ['model' => $contentPage, 'models' => $contentPage->categoryPage->pages])

                    </div>
                    @include('admin.partials.on_edit.form_actions_bottom')

                </div>
            </div>
        </div>
    </form>
@endsection
