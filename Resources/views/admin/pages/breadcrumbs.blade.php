<div class="breadcrumbs">
    <ul>
        <li>
            <a href="{{ route('admin.index') }}"><i class="fa fa-home"></i></a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="{{ route('admin.pages.index') }}" class="text-black">@lang('admin.pages.index')</a>
        </li>
        @if(url()->current() === route('admin.pages.index'))
        @elseif(url()->current() === route('admin.pages.create', ['categoryId' => $categoryPage->id]))
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.category-page.pages', ['id' => $categoryPage->id]) }}" class="text-black">{{ $categoryPage->title }}</a>
            </li>
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.pages.create') }}" class="text-purple">@lang('admin.pages.create')</a>
            </li>
        @elseif(Request::segment(3) !== null && url()->current() === route('admin.pages.edit', ['id' => Request::segment(3)]))
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.category-page.pages', ['id' => $categoryPage->id]) }}" class="text-black">{{ $categoryPage->title }}</a>
            </li>
            <li>
                <i class="fa fa-angle-right"></i>
                <a href="{{ route('admin.pages.edit', ['id' => Request::segment(3)]) }}" class="text-purple">@lang('admin.pages.edit')</a>
            </li>
        @endif
    </ul>
</div>
