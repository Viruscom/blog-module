<?php

    namespace App\Http\Controllers\Admin;

    use App\Actions\CommonControllerAction;
    use App\Actions\PageAction;
    use App\Helpers\CacheKeysHelper;
    use App\Helpers\LanguageHelper;
    use App\Helpers\MainHelper;
    use App\Helpers\ModuleHelper;
    use App\Helpers\WebsiteHelper;
    use App\Http\Requests\Pages\PageStoreRequest;
    use App\Http\Requests\Pages\PageUpdateRequest;
    use App\Interfaces\Controllers\PageInterface;
    use App\Interfaces\MultipleActionsInterface;
    use App\Models\CategoryPage\CategoryPage;
    use App\Models\Files\File;
    use App\Models\Pages\Page;
    use App\Models\Pages\PageTranslation;
    use Cache;
    use Illuminate\Contracts\Support\Renderable;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Routing\Controller;
    use Modules\Catalogs\Models\MainCatalog;
    use Modules\OneHotel\Models\OneHotel;

    class PageController extends Controller implements PageInterface, MultipleActionsInterface
    {
        public function index(PageAction $action)
        {
            if (is_null(Cache::get(CacheKeysHelper::$CATEGORY_PAGE_ADMIN))) {
                CategoryPage::cacheUpdate();
            }

            return view('admin.pages.categories', ['categories' => Cache::get(CacheKeysHelper::$CATEGORY_PAGE_ADMIN)]);
        }

        public function getCategoryPages($category_id)
        {
            $categoryPage = CategoryPage::where('id', $category_id)->with(['pages' => function ($query) {
                $query->with('translations')->orderBy('position');
            }])->first();
            MainHelper::goBackIfNull($categoryPage);

            return view('admin.pages.index', [
                'categoryPage' => $categoryPage,
                'pages'        => $categoryPage->pages
            ]);
        }

        public function deleteImage($id, CommonControllerAction $action): RedirectResponse
        {
            $page = Page::find($id);
            MainHelper::goBackIfNull($page);

            if ($action->imageDelete($page, Page::class)) {
                return redirect()->back()->with('success-message', 'admin.common.successful_delete');
            }

            return redirect()->back()->withErrors(['admin.image_not_found']);
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return Renderable
         */
        public function create(PageAction $action, $categoryId = null): Renderable
        {
            $action->checkForFilesCache();
            $action->checkForPageCategoriesAdminCache();

            $categoryPage = CategoryPage::where('id', $categoryId)->first();
            WebsiteHelper::redirectBackIfNull($categoryPage);

            $activeModules = ModuleHelper::getActiveModules();
            $data          = [
                'languages'      => LanguageHelper::getActiveLanguages(),
                'files'          => Cache::get(CacheKeysHelper::$FILES),
                'filesPathUrl'   => File::getFilesPathUrl(),
                'fileRulesInfo'  => Page::getFileRulesForView(),
                'pageCategories' => Cache::get(CacheKeysHelper::$CATEGORY_PAGE_ADMIN),
                'categoryPage'   => $categoryPage,
                'categoryId'     => $categoryPage->id
            ];

            if (array_key_exists('Catalogs', $activeModules)) {
                if (is_null(CacheKeysHelper::$CATALOGS_MAIN_FRONT)) {
                    MainCatalog::cacheUpdate();
                }
                $data['mainCatalogs'] = cache()->get(CacheKeysHelper::$CATALOGS_MAIN_FRONT);
            }

            return view('admin.pages.create', $data);
        }

        public function store(PageStoreRequest $request, CommonControllerAction $action, PageAction $pageAction): RedirectResponse
        {
            $categoryPage = CategoryPage::where('id', $request->category_page_id)->first();
            if (is_null($categoryPage)) {
                return back()->withErrors([trans('admin.pages.category_page_not_found')]);
            }

            $pageAction->validateImage($request, $categoryPage);

            $page = $action->doSimpleCreate(Page::class, $request);
            $action->updateUrlCache($page, PageTranslation::class);
            $action->storeSeo($request, $page, 'Page');
            Page::cacheUpdate();

            if (array_key_exists('OneHotel', ModuleHelper::getActiveModules())) {
                $tourPath = OneHotel::getTourPath($page->id);
                $page->makeDirectory($tourPath);
                if ($categoryPage->with_tour_btn) {
                    $page->update([
                                      'tour_active' => true,
                                      'tour_path'   => $tourPath
                                  ]);
                }
            }

            if ($request->has('submitaddnew')) {
                return redirect()->back()->with('success-message', 'admin.common.successful_create');
            }

            return redirect()->route('admin.category-page.pages', ['id' => $page->categoryPage->id])->with('success-message', trans('admin.common.successful_create'));
        }

        public function update($id, PageUpdateRequest $request, CommonControllerAction $action, PageAction $pageAction): RedirectResponse
        {
            $page = Page::whereId($id)->with('translations', 'seoFields')->first();
            MainHelper::goBackIfNull($page);

            $categoryPage = CategoryPage::where('id', $request->category_page_id)->first();
            if (is_null($categoryPage)) {
                return back()->withErrors([trans('admin.pages.category_page_not_found')]);
            }

            $pageAction->validateImage($request, $categoryPage);
            $pageAction->doPageSimpleUpdate(Page::class, PageTranslation::class, $page, $categoryPage, $request);
            $action->updateUrlCache($page, PageTranslation::class);

            if ($request->has('image')) {
                $request->validate(['image' => Page::getFileRules($page->categoryPage->visualization_type_id)], [Page::getUserInfoMessage($page->categoryPage->visualization_type_id)]);
                $page->saveFile($request->image);
            }

            $action->updateSeo($request, $page, 'Page');
            Page::cacheUpdate();

            return redirect()->route('admin.category-page.pages', ['id' => $page->categoryPage->id])->with('success-message', 'admin.common.successful_edit');
        }

        public function edit($id, PageAction $action)
        {
            $page = Page::whereId($id)->with('translations', 'categoryPage', 'categoryPage.translations')->with('categoryPage.pages', function ($q) {
                return $q->orderBy('position');
            })->first();
            MainHelper::goBackIfNull($page);

            $action->checkForFilesCache();
            $action->checkForPageCategoriesAdminCache();
            $data = [
                'contentPage'    => $page,
                'categoryPage'   => $page->categoryPage,
                'languages'      => LanguageHelper::getActiveLanguages(),
                'files'          => Cache::get(CacheKeysHelper::$FILES),
                'filesPathUrl'   => File::getFilesPathUrl(),
                'fileRulesInfo'  => $page->categoryPage->isListTypeVisualization() ? '' : Page::getFileRuleMessage($page->categoryPage->visualization_type_id),
                'pageCategories' => Cache::get(CacheKeysHelper::$CATEGORY_PAGE_ADMIN)
            ];

            $activeModules = ModuleHelper::getActiveModules();
            if (array_key_exists('Catalogs', $activeModules)) {
                if (is_null(CacheKeysHelper::$CATALOGS_MAIN_FRONT)) {
                    MainCatalog::cacheUpdate();
                }
                $data['mainCatalogs'] = cache()->get(CacheKeysHelper::$CATALOGS_MAIN_FRONT);
            }

            return view('admin.pages.edit', $data);
        }

        public function deleteMultiple(Request $request, CommonControllerAction $action): RedirectResponse
        {
            if (!is_null($request->ids[0])) {
                $action->deleteMultiple($request, Page::class);

                return redirect()->back()->with('success-message', 'admin.common.successful_delete');
            }

            return redirect()->back()->withErrors(['admin.common.no_checked_checkboxes']);
        }

        public function active($id, $active): RedirectResponse
        {
            $page = Page::find($id);
            MainHelper::goBackIfNull($page);

            $page->update(['active' => $active]);
            Page::cacheUpdate();

            return redirect()->back()->with('success-message', 'admin.common.successful_edit');
        }

        public function positionUp($id, PageAction $pageAction): RedirectResponse
        {
            $categoryPage = Page::whereId($id)->with('translations')->first();
            MainHelper::goBackIfNull($categoryPage);

            $pageAction->positionUp(Page::class, $categoryPage);
            Page::cacheUpdate();

            return redirect()->back()->with('success-message', 'admin.common.successful_edit');
        }

        public function positionDown($id, PageAction $pageAction): RedirectResponse
        {
            $categoryPage = Page::whereId($id)->with('translations')->first();
            MainHelper::goBackIfNull($categoryPage);

            $pageAction->positionDown(Page::class, $categoryPage);
            Page::cacheUpdate();

            return redirect()->back()->with('success-message', 'admin.common.successful_edit');
        }

        public function delete($id, CommonControllerAction $action): RedirectResponse
        {
            $page = Page::find($id);
            MainHelper::goBackIfNull($page);

            $action->deleteFromUrlCache($page);
            $action->delete(Page::class, $page);

            return redirect()->back()->with('success-message', 'admin.common.successful_delete');
        }

        public function activeMultiple($active, Request $request, CommonControllerAction $action): RedirectResponse
        {
            $action->activeMultiple(Page::class, $request, $active);
            Page::cacheUpdate();

            return redirect()->back()->with('success-message', 'admin.common.successful_edit');
        }

        public function makeAd($id, PageAction $pageAction): RedirectResponse
        {
            $page = Page::find($id);
            MainHelper::goBackIfNull($page);

            $pageAction->makeAdBox($page);

            return redirect()->back()->with('success-message', trans('admin.common.successful_create'));
        }
    }
