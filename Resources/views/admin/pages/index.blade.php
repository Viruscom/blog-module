@extends('layouts.admin.app')
@section('styles')
    <link href="{{ asset('admin/assets/css/select2.min.css') }}" rel="stylesheet"/>
@endsection
@section('scripts')
    <script src="{{ asset('admin/assets/js/select2.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/bootstrap-confirmation.js') }}"></script>
    <script>
        $('[data-toggle=confirmation]').confirmation({
            rootSelector: '[data-toggle=confirmation]',
            container: 'body',
        });
        $(".select2").select2({language: "bg"});
    </script>
@endsection
@section('content')
    @include('admin.pages.breadcrumbs')
    @include('admin.notify')
    @include('admin.partials.index.top_search_with_mass_buttons', ['mainRoute' => Request::segment(2)])

    <div class="row">
        <div class="col-xs-12">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <th class="width-2-percent"></th>
                    <th class="width-2-percent">{{ __('admin.number') }}</th>
                    <th>{{ __('admin.title') }}</th>
                    <th>{{ __('admin.pages.to_category') }}</th>
                    <th class="width-220">{{ __('admin.actions') }}</th>
                    </thead>
                    <tbody>
                    <tbody>
                    @if(count($pages))
                            <?php $i = 1; ?>
                        @foreach($pages as $page)
                            <tr class="t-row row-{{$page->id}}">
                                <td class="width-2-percent">
                                    <div class="pretty p-default p-square">
                                        <input type="checkbox" class="checkbox-row" name="check[]" value="{{$page->id}}"/>
                                        <div class="state p-primary">
                                            <label></label>
                                        </div>
                                    </div>
                                </td>
                                <td class="width-2-percent">{{$i}}</td>
                                <td>{{ $page->title }}</td>
                                <td>{{ $page->categoryPage->title }}</td>
                                <td class="pull-right">
                                    @if(array_key_exists('OneHotel', $activeModules))
                                        @include('onehotel::admin.partials.pages_index_buttons', ['page' => $page])
                                    @endif
                                    @if(array_key_exists('AdBoxes', $activeModules))
                                        <a href="{{ route('admin.pages.make-ad-box', ['id' => $page->id]) }}" class="btn btn-info tooltips" role="button" data-toggle="tooltip" data-placement="auto" title="" data-original-title="Създай рекламно каре"><i class="fas fa-ad"></i></a>
                                    @endif

                                    @include('admin.partials.index.action_buttons', ['mainRoute' => Request::segment(2), 'models' => $pages, 'model' => $page, 'showInPublicModal' => false])

                                </td>
                            </tr>
                            <tr class="t-row-details row-{{$page->id}}-details hidden">
                                <td colspan="2"></td>
                                <td colspan="2">
                                    @include('admin.partials.index.table_details', ['model' => $page, 'moduleName' => 'Pages'])
                                </td>
                                <td class="width-220">
                                    <img class="thumbnail img-responsive" src="{{ $page->getFileUrl() }}"/>
                                </td>
                            </tr>
                                <?php $i++; ?>
                        @endforeach
                        <tr style="display: none;">
                            <td colspan="5" class="no-table-rows">{{ trans('admin.pages.no_records') }}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="5" class="no-table-rows">{{ trans('admin.pages.no_records') }}</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
