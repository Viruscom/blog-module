<?php

    namespace App\Http\Controllers\Admin;

    use App\Actions\CommonControllerAction;
    use App\Helpers\CacheKeysHelper;
    use App\Helpers\FileDimensionHelper;
    use App\Helpers\LanguageHelper;
    use App\Helpers\MainHelper;
    use App\Helpers\ModuleHelper;
    use App\Http\Controllers\Controller;
    use App\Http\Requests\CategoryPageStoreRequest;
    use App\Http\Requests\CategoryPageUpdateRequest;
    use App\Interfaces\Controllers\CategoryPageInterface;
    use App\Interfaces\PositionInterface;
    use App\Models\CategoryPage\CategoryPage;
    use App\Models\CategoryPage\CategoryPageTranslation;
    use Cache;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;

    class CategoryPageController extends Controller implements CategoryPageInterface, PositionInterface
    {
        public function index()
        {
            if (is_null(Cache::get(CacheKeysHelper::$CATEGORY_PAGE_ADMIN))) {
                CategoryPage::cacheUpdate();
            }

            return view('admin.category_pages.index', ['categoryPages' => Cache::get(CacheKeysHelper::$CATEGORY_PAGE_ADMIN)]);
        }
        public function store(CategoryPageStoreRequest $request, CommonControllerAction $action): RedirectResponse
        {
            if ($request->has('image')) {
                $request->validate(['image' => FileDimensionHelper::getRules('CategoryPage', 1)], FileDimensionHelper::messages('CategoryPage', 1));
            }
            $categoryPage = $action->doSimpleCreate(CategoryPage::class, $request);
            $action->updateUrlCache($categoryPage, CategoryPageTranslation::class);
            $action->storeSeo($request, $categoryPage, 'CategoryPage');
            CategoryPage::cacheUpdate();

            if ($request->has('submitaddnew')) {
                return redirect()->back()->with('success-message', 'admin.common.successful_create');
            }

            return redirect()->route('admin.category-page.index')->with('success-message', trans('admin.common.successful_create'));
        }
        public function create()
        {
            return view('admin.category_pages.create', [
                'languages'     => LanguageHelper::getActiveLanguages(),
                'fileRulesInfo' => CategoryPage::getUserInfoMessage()
            ]);
        }
        public function edit($id)
        {
            $categoryPage = CategoryPage::whereId($id)->with('translations')->first();
            MainHelper::goBackIfNull($categoryPage);

            return view('admin.category_pages.edit', [
                'categoryPage'  => $categoryPage,
                'languages'     => LanguageHelper::getActiveLanguages(),
                'fileRulesInfo' => CategoryPage::getUserInfoMessage(),
                'categoryPages' => Cache::get(CacheKeysHelper::$CATEGORY_PAGE_ADMIN)
            ]);
        }
        public function deleteMultiple(Request $request, CommonControllerAction $action): RedirectResponse
        {
            if (!is_null($request->ids[0])) {
                $action->deleteMultiple($request, CategoryPage::class);

                return redirect()->back()->with('success-message', 'admin.common.successful_delete');
            }

            return redirect()->back()->withErrors(['admin.common.no_checked_checkboxes']);
        }
        public function delete($id, CommonControllerAction $action): RedirectResponse
        {
            $categoryPage = CategoryPage::where('id', $id)->with('pages')->first();
            MainHelper::goBackIfNull($categoryPage);

            if ($categoryPage->pages->isNotEmpty()) {
                return redirect()->back()->withErrors(['admin.category_pages.cant_delete_has_records']);
            }

            $action->deleteFromUrlCache($categoryPage);
            $action->delete(CategoryPage::class, $categoryPage);

            return redirect()->back()->with('success-message', 'admin.common.successful_delete');
        }
        public function activeMultiple($active, Request $request, CommonControllerAction $action): RedirectResponse
        {
            $action->activeMultiple(CategoryPage::class, $request, $active);
            CategoryPage::cacheUpdate();

            return redirect()->back()->with('success-message', 'admin.common.successful_edit');
        }
        public function active($id, $active): RedirectResponse
        {
            $categoryPage = CategoryPage::find($id);
            MainHelper::goBackIfNull($categoryPage);

            $categoryPage->update(['active' => $active]);
            CategoryPage::cacheUpdate();

            return redirect()->back()->with('success-message', 'admin.common.successful_edit');
        }
        public function update($id, CategoryPageUpdateRequest $request, CommonControllerAction $action): RedirectResponse
        {
            $categoryPage = CategoryPage::whereId($id)->with('translations')->first();
            MainHelper::goBackIfNull($categoryPage);

            $request['visualization_type_id'] = $request->visualization_type_id;
            $action->doSimpleUpdate(CategoryPage::class, CategoryPageTranslation::class, $categoryPage, $request);
            $action->updateUrlCache($categoryPage, CategoryPageTranslation::class);

            if ($request->has('image')) {
                $request->validate(['image' => FileDimensionHelper::getRules('CategoryPage', 1)], FileDimensionHelper::messages('CategoryPage', 1));
                $categoryPage->saveFile($request->image);
            }
            $action->updateSeo($request, $categoryPage, 'CategoryPage');
            CategoryPage::cacheUpdate();

            if (array_key_exists('OneHotel', ModuleHelper::getActiveModules()) && !$categoryPage->with_tour_btn) {
                $categoryPage->pages()->update(['tour_active' => false]);
            }

            return redirect()->route('admin.category-page.index')->with('success-message', 'admin.common.successful_edit');
        }
        public function positionUp($id, CommonControllerAction $action): RedirectResponse
        {
            $categoryPage = CategoryPage::whereId($id)->with('translations')->first();
            MainHelper::goBackIfNull($categoryPage);

            $action->positionUp(CategoryPage::class, $categoryPage);
            CategoryPage::cacheUpdate();

            return redirect()->back()->with('success-message', 'admin.common.successful_edit');
        }

        public function positionDown($id, CommonControllerAction $action): RedirectResponse
        {
            $categoryPage = CategoryPage::whereId($id)->with('translations')->first();
            MainHelper::goBackIfNull($categoryPage);

            $action->positionDown(CategoryPage::class, $categoryPage);
            CategoryPage::cacheUpdate();

            return redirect()->back()->with('success-message', 'admin.common.successful_edit');
        }

        public function deleteImage($id, CommonControllerAction $action): RedirectResponse
        {
            $categoryPage = CategoryPage::find($id);
            MainHelper::goBackIfNull($categoryPage);

            if ($action->imageDelete($categoryPage, CategoryPage::class)) {
                return redirect()->back()->with('success-message', 'admin.common.successful_delete');
            }

            return redirect()->back()->withErrors(['admin.image_not_found']);
        }

        public function getCategoryPages($id)
        {
            $categoryPage = CategoryPage::where('id', $id)->with(['pages' => function ($query) {
                $query->with('translations')->orderBy('position');
            }])->first();
            MainHelper::goBackIfNull($categoryPage);

            return view('admin.category_pages.assigned_pages', [
                'categoryPage' => $categoryPage,
                'pages'        => $categoryPage->pages
            ]);
        }
    }
