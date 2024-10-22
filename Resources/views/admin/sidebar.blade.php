@php use App\Helpers\WebsiteHelper; @endphp
<div class="panel panel-default">
    <div class="panel-heading collapsed" role="tab" id="blog" data-toggle="collapse" data-parent="#accordionMenu" href="#collapseBlog" aria-expanded="false" aria-controls="collapseBlog">
        <h4 class="panel-title">
            <a>
                <i class="far fa-file-alt"></i> <span>@lang('blog::admin.sidebar.blog')</span>
            </a>
        </h4>
    </div>
    <div id="collapseBlog" class="panel-collapse collapse" role="tabpanel" aria-labelledby="blog">
        <div class="panel-body">
            <ul class="nav">
                <li><a href="{{ route('admin.category-page.index') }}" class="{{ WebsiteHelper::isActiveRoute('admin.category-page.*') ? 'active' : '' }}"><i class="fas fa-indent"></i> <span>{!! trans('admin.category_pages.index') !!}</span></a></li>
                <li><a href="{{ route('admin.pages.index') }}" class="{{ WebsiteHelper::isActiveRoute('admin.pages.*') ? 'active' : '' }}"><i class="far fa-file-alt"></i> <span>{!! trans('admin.pages.index') !!}</span></a></li>
            </ul>
        </div>
    </div>
</div>
