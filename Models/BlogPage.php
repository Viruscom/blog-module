<?php

    namespace Modules\Blog\Models;

    use App\Helpers\AdminHelper;
    use App\Helpers\CacheKeysHelper;
    use App\Helpers\FileDimensionHelper;
    use App\Helpers\LanguageHelper;
    use App\Helpers\SeoHelper;
    use App\Interfaces\Models\CommonGalleryRelationInterface;
    use App\Interfaces\Models\CommonModelInterface;
    use App\Models\CategoryPage\CategoryPage;
    use App\Models\Seo;
    use App\Traits\CommonActions;
    use App\Traits\HasGallery;
    use App\Traits\HasModelRatios;
    use App\Traits\Scopes;
    use App\Traits\StorageActions;
    use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
    use Astrotomic\Translatable\Translatable;
    use Carbon\Carbon;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Str;
    use Modules\AdBoxes\Models\AdBox;

    class BlogPage extends Model implements TranslatableContract, CommonModelInterface, CommonGalleryRelationInterface
    {
        use Translatable, Scopes, StorageActions, CommonActions, HasGallery, HasModelRatios;

        public const FILES_PATH                   = "images/blog/pages";
        const        ALLOW_CATALOGS               = true;
        const        ALLOW_ICONS                  = true;
        const        ALLOW_LOGOS                  = true;
        public const CURRENCY_DECIMALS            = 2;
        public const CURRENCY_SEPARATOR           = '.';
        public const CURRENCY_THOUSANDS_SEPARATOR = '';

        public static int    $FIRST_TYPE                 = 1;
        public static int    $SECOND_TYPE                = 2;
        public static int    $THIRD_TYPE                 = 3;
        public static int    $FOURTH_TYPE                = 4;
        public static int    $FIFTH_TYPE                 = 5;
        public static string $CONTENT_BOX_1_SYSTEM_IMAGE = "blog_page_1_image.png";

        public static string $CONTENT_BOX_2_SYSTEM_IMAGE  = "blog_page_2_image.png";
        public static string $CONTENT_BOX_2_RATIO         = '3/2';
        public static string $CONTENT_BOX_2_MIMES         = 'jpg,jpeg,png,gif,webp';
        public static string $CONTENT_BOX_2_MAX_FILE_SIZE = '3000';

        public static string $CONTENT_BOX_3_SYSTEM_IMAGE  = "blog_page_3_image.png";
        public static string $CONTENT_BOX_3_RATIO         = '3/2';
        public static string $CONTENT_BOX_3_MIMES         = 'jpg,jpeg,png,gif,webp';
        public static string $CONTENT_BOX_3_MAX_FILE_SIZE = '3000';
        public static string $CONTENT_BOX_4_SYSTEM_IMAGE  = "blog_page_4_image.png";

        public static string $CONTENT_BOX_5_SYSTEM_IMAGE  = "blog_page_5_image.png";
        public static string $CONTENT_BOX_5_RATIO         = '3/2';
        public static string $CONTENT_BOX_5_MIMES         = 'jpg,jpeg,png,gif,webp';
        public static string $CONTENT_BOX_5_MAX_FILE_SIZE = '3000';

        public array $translatedAttributes = ['title', 'url', 'announce', 'description', 'visible', 'title_additional_1', 'title_additional_2', 'title_additional_3',
                                              'title_additional_4', 'title_additional_5', 'title_additional_6', 'text_additional_1', 'text_additional_2',
                                              'text_additional_3', 'text_additional_4', 'text_additional_5', 'text_additional_6'];
        protected    $table                = "pages";
        protected    $fillable             = ['category_page_id', 'from_price', 'price', 'from_date', 'to_date', 'show_in_homepage', 'active', 'position', 'creator_user_id', 'show_in_homepage_type', 'one_day_event', 'one_day_event_date', 'filename', 'filename_box2', 'filename_box3', 'in_ad_box', 'product_id', 'tour_active', 'tour_path', 'from_discounted_price', 'discounted_price'];

        public static function cacheUpdate(): void
        {
            cache()->forget(CacheKeysHelper::$PAGE_ADMIN);
            cache()->forget('frontHomePage');
            cache()->forget('frontContactsPage');
            cache()->forget('frontGalleryPage');
            cache()->rememberForever(CacheKeysHelper::$PAGE_ADMIN, function () {
                return self::with('categoryPage', 'translations', 'categoryPage.translations')->orderBy('position')->get();
            });
        }

        public static function getFileRule($pageVisualizationType): string
        {
            return FileDimensionHelper::getRules('Page', self::getFileDimensionKey($pageVisualizationType));
        }

        public static function getFileDimensionKey($pageVisualizationType): string
        {
            switch ($pageVisualizationType) {
                case 1:
                    return self::$FIRST_TYPE;
                case 2:
                    return self::$SECOND_TYPE;
                case 3:
                    return self::$THIRD_TYPE;
                case 4:
                    return self::$FOURTH_TYPE;
                case 5:
                    return self::$FIFTH_TYPE;
            }
        }

        public static function getFileRulesForView(): array
        {
            return [
                self::$FIRST_TYPE  => FileDimensionHelper::getUserInfoMessage('Page', self::$FIRST_TYPE),
                self::$SECOND_TYPE => FileDimensionHelper::getUserInfoMessage('Page', self::$SECOND_TYPE),
                self::$THIRD_TYPE  => FileDimensionHelper::getUserInfoMessage('Page', self::$THIRD_TYPE),
                self::$FOURTH_TYPE => [],
                self::$FIFTH_TYPE  => FileDimensionHelper::getUserInfoMessage('Page', self::$FIFTH_TYPE),
            ];
        }

        public static function getUserInfoMessage($key): string
        {
            return FileDimensionHelper::getUserInfoMessage('Page', $key);
        }

        public static function getFileRuleMessage($pageVisualizationType): string
        {
            return FileDimensionHelper::getUserInfoMessage('Page', self::getFileDimensionKey($pageVisualizationType));
        }

        public static function getLangArraysOnStore($data, $request, $languages, $modelId, $isUpdate)
        {
            foreach ($languages as $language) {
                $data[$language->code] = PageTranslation::getLanguageArray($language, $request, $modelId, $isUpdate);
            }

            return $data;
        }

        public static function getFileRules($key): string
        {
            return FileDimensionHelper::getRules('Page', $key);
        }

        public function categoryPage(): BelongsTo
        {
            return $this->belongsTo(CategoryPage::class);
        }

        public function getToDate($format)
        {
            if (is_null($this->to_date)) {
                return '';
            }

            return Carbon::parse($this->to_date)->format($format);
        }

        public function setKeys($array): array
        {
            $array[1]['sys_image_name'] = trans('page::admin.page.index') . ': ' . trans('page::admin.page.content_box_first');
            $array[1]['sys_image']      = self::$CONTENT_BOX_1_SYSTEM_IMAGE;
            $array[1]['sys_image_path'] = AdminHelper::getSystemImage(self::$CONTENT_BOX_1_SYSTEM_IMAGE);
            $array[1]['ratio']          = self::getModelRatio('page_box_first');
            $array[1]['mimes']          = self::getModelRatio('page_box_first');
            $array[1]['max_file_size']  = self::getModelMaxFileSize('page_box_first');

            $array[2]['sys_image_name'] = trans('page::admin.page.index') . ': ' . trans('page::admin.page.content_box_second');
            $array[2]['sys_image']      = self::$CONTENT_BOX_2_SYSTEM_IMAGE;
            $array[2]['sys_image_path'] = AdminHelper::getSystemImage(self::$CONTENT_BOX_2_SYSTEM_IMAGE);
            $array[2]['ratio']          = self::getModelRatio('page_box_second');
            $array[2]['mimes']          = self::getModelRatio('page_box_second');
            $array[2]['max_file_size']  = self::getModelMaxFileSize('page_box_second');

            $array[3]['sys_image_name'] = trans('page::admin.page.index') . ': ' . trans('page::admin.page.content_box_third');
            $array[3]['sys_image']      = self::$CONTENT_BOX_3_SYSTEM_IMAGE;
            $array[3]['sys_image_path'] = AdminHelper::getSystemImage(self::$CONTENT_BOX_3_SYSTEM_IMAGE);
            $array[3]['ratio']          = self::getModelRatio('page_box_third');
            $array[3]['mimes']          = self::getModelRatio('page_box_third');
            $array[3]['max_file_size']  = self::getModelMaxFileSize('page_box_third');

            return $array;
        }

        public function getSystemImage(): string
        {
            return AdminHelper::getSystemImage(self::${'CONTENT_BOX_' . $this->categoryPage->visualization_type_id . '_SYSTEM_IMAGE'});
        }

        public function makeAdBox()
        {
            $languages = LanguageHelper::getActiveLanguages();
            $data      = new Request();
            foreach ($languages as $language) {
                $contentPageTranslation                       = $this->translations()->where('language_id', $language->id)->first();
                $navigation                                   = $this->navigation->translations()->where('language_id', $language->id)->first();
                $data['visible_' . $language->code]           = true;
                $data['title_' . $language->code]             = $contentPageTranslation->title;
                $data['short_description_' . $language->code] = $contentPageTranslation->short_description;
                $data['url_' . $language->code]               = $language->code . '/page/' . $navigation->slug . '/' . $contentPageTranslation->slug;
            }
            $data['price']     = $this->price;
            $data['from_date'] = Carbon::parse($this->created_at)->format('Y-m-d');
            $data['position']  = AdBox::generatePosition($data, 0);

            $adBox = AdBox::create(AdBox::getCreateData($data));
            foreach ($languages as $language) {
                $adBox->translations()->create(AdBoxTranslation::getCreateData($language, $data));
            }
        }

        public static function generatePosition($request)
        {
            $models = self::where('category_page_id', $request->category_page_id)->orderBy('position', 'desc')->get();
            if (count($models) < 1) {
                return 1;
            }
            if (!$request->has('position') || is_null($request['position'])) {
                return $models->first()->position + 1;
            }

            if ($request['position'] > $models->first()->position) {
                return $models->first()->position + 1;
            }
            $modelsToUpdate = self::where('category_page_id', $request->category_page_id)->where('position', '>=', $request['position'])->get();
            foreach ($modelsToUpdate as $modelToUpdate) {
                $modelToUpdate->update(['position' => $modelToUpdate->position + 1]);
            }

            return $request['position'];
        }

        public static function getCreateData($request)
        {
            $data                    = self::getRequestData($request);
            $data['creator_user_id'] = Auth::user()->id;

            return $data;
        }

        public static function getRequestData($request)
        {
            $data = [
                'category_page_id' => $request->category_page_id,
                'creator_user_id'  => Auth::user()->id
            ];
            if ($request->has('price')) {
                $data['price'] = $request->price;
            }

            if ($request->has('one_day_event_date') && $request->one_day_event_date != "" && ($request->has('one_day_event') && $request->has('one_day_event') == "on")) {
                $data['one_day_event_date'] = Carbon::parse($request->one_day_event_date)->format('Y-m-d');
            } else {
                $data['from_date'] = null;
                $data['to_date']   = null;

                if ($request->has('from_date') && $request->from_date != "") {
                    $data['from_date'] = Carbon::parse($request->from_date)->format('Y-m-d');
                }

                if ($request->has('to_date') && $request->to_date != "") {
                    $data['to_date'] = Carbon::parse($request->to_date)->format('Y-m-d');
                }

                $data['one_day_event_date'] = null;
            }

            $data['from_price'] = 0;
            if ($request->has('from_price')) {
                $data['from_price'] = 1;
            }

            $data['one_day_event'] = 0;
            if ($request->has('one_day_event')) {
                $data['one_day_event'] = 1;
                $data['from_date']     = null;
                $data['to_date']       = null;
            }

            $data['active'] = false;
            if ($request->has('active')) {
                $data['active'] = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
            }

            if ($request->hasFile('image')) {
                $data['filename'] = pathinfo(CommonActions::getValidFilenameStatic($request->image->getClientOriginalName()), PATHINFO_FILENAME) . '.' . $request->image->getClientOriginalExtension();
            }

            return $data;
        }

        public function getAnnounce(): string
        {
            if (is_null($this->announce)) {
                return '';
            }

            return Str::limit($this->announce, 255, ' ...');
        }

        public function getFilepath($filename): string
        {
            return $this->getFilesPath() . $filename;
        }

        public function getFilesPath(): string
        {
            return self::FILES_PATH . '/' . $this->id . '/';
        }

        public function seoFields()
        {
            return $this->hasOne(Seo::class, 'model_id')->where('model', get_class($this));
        }

        public function seo($languageSlug)
        {
            $seo = $this->seoFields;
            if (is_null($seo)) {
                return null;
            }
            SeoHelper::setSeoFields($this, $seo->translate($languageSlug));
        }

        public function getEncryptedPath($moduleName): string
        {
            return encrypt($moduleName . '-' . get_class($this) . '-' . $this->id);
        }

        public function headerGallery()
        {
            return $this->getHeaderGalleryRelation(get_class($this));
        }

        public function mainGallery()
        {
            return $this->getMainGalleryRelation(get_class($this));
        }

        public function additionalGalleryOne()
        {
            return $this->getAdditionalGalleryOneRelation(get_class($this));
        }

        public function additionalGalleryTwo()
        {
            return $this->getAdditionalGalleryTwoRelation(get_class($this));
        }

        public function additionalGalleryThree()
        {
            return $this->getAdditionalGalleryThreeRelation(get_class($this));
        }

        public function additionalGalleryFour()
        {
            return $this->getAdditionalGalleryFourRelation(get_class($this));
        }

        public function additionalGalleryFive()
        {
            return $this->getAdditionalGalleryFiveRelation(get_class($this));
        }

        public function additionalGallerySix()
        {
            return $this->getAdditionalGallerySixRelation(get_class($this));
        }

        public function getPreviousPageUrl($languageSlug)
        {
            if ($this->position == 1) {
                return null;
            }
            $previousPage = $this->categoryPage->pages()->whereActive(true)->where('position', '<', $this->position)->orderBy('position', 'desc')->first();
            if (is_null($previousPage)) {
                return null;
            }

            return $previousPage->getUrl($languageSlug);
        }

        public function getUrl($languageSlug)
        {
            return url($languageSlug . '/' . $this->url);
        }

        public function getNextPageUrl($languageSlug)
        {
            $nextPage = $this->categoryPage->pages()->whereActive(true)->where('position', '>', $this->position)->orderBy('position', 'asc')->first();

            if (is_null($nextPage)) {
                return null;
            }

            return $nextPage->getUrl($languageSlug);
        }

        public function updatedPosition($request)
        {
            if (!$request->has('position') || is_null($request->position) || $request->position == $this->position) {
                return $this->position;
            }

            $maxPosition = self::where('category_page_id', $this->category_page_id)->max('position');
            $minPosition = 1;

            $newPosition = max($minPosition, min($request->position, $maxPosition));
            $query       = self::where('category_page_id', $this->category_page_id)->where('id', '<>', $this->id);

            if ($newPosition > $this->position) {
                $query->whereBetween('position', [$this->position + 1, $newPosition])->decrement('position');
            } elseif ($newPosition < $this->position) {
                $query->whereBetween('position', [$newPosition, $this->position - 1])->increment('position');
            }

            $this->position = $newPosition;
            $this->save();

            return $this->position;
        }

        public function getPrice(): string
        {
            if (is_null($this->price) || $this->price == '') {
                return '';
            }

            return number_format($this->price, self::CURRENCY_DECIMALS, self::CURRENCY_SEPARATOR, self::CURRENCY_THOUSANDS_SEPARATOR);
        }

        public function getDiscountedPrice(): string
        {
            if (is_null($this->discounted_price) || $this->discounted_price == '') {
                return '';
            }

            return number_format($this->discounted_price, self::CURRENCY_DECIMALS, self::CURRENCY_SEPARATOR, self::CURRENCY_THOUSANDS_SEPARATOR);
        }

        public function isOneDayEvent()
        {
            return $this->one_day_event;
        }

        public function getOneDayEventDate($format): string
        {
            if (is_null($this->one_day_event_date)) {
                return '';
            }

            return Carbon::parse($this->one_day_event_date)->format($format);
        }

        public function getFromDate($format)
        {
            if (is_null($this->from_date)) {
                return '';
            }

            return Carbon::parse($this->from_date)->format($format);
        }
    }
