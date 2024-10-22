<div class="breadcrumbs">
    <ul>
        <li>
            <a href="{{ route('admin.index') }}"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="{{ route('admin.category-page.index') }}" class="text-black">@lang('admin.category_pages.index')</a>
        </li>
        @if(url()->current() === route('admin.category-page.create'))
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.category-page.create') }}" class="text-purple">@lang('admin.category_pages.create')</a>
            </li>
        @elseif(Request::segment(3) !== null && url()->current() === route('admin.category-page.edit', ['id' => Request::segment(3)]))
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.category-page.edit', ['id' => Request::segment(3)]) }}" class="text-purple">@lang('admin.category_pages.edit')</a>
            </li>
        @endif
    </ul>
</div>
