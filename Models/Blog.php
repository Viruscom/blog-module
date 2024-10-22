<?php

namespace Modules\Blog\Models;

use App\Helpers\AdminHelper;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model implements TranslatableContract
{
    use Translatable;

    public static $IMAGES_PATH        = "images/blog";
    public static $BLOG_SYSTEM_IMAGE  = 'blog_img.png';
    public static $BLOG_RATIO         = '1/1';
    public static $BLOG_MIMES         = 'jpg,jpeg,png,gif';
    public static $BLOG_MAX_FILE_SIZE = '3000';


    public    $translatedAttributes = ['title', 'short_description', 'visible'];
    protected $fillable             = ['url', 'external_url', 'active', 'position', 'created_by', 'filename', 'date', 'from_date', 'to_date'];


    public function setKeys($array): array
    {
        $array[1]['sys_image_name']      = trans('blog::admin.blog.index');
        $array[1]['sys_image']     = self::$BLOG_SYSTEM_IMAGE;
        $array[1]['sys_image_path'] = AdminHelper::getSystemImage(self::$BLOG_SYSTEM_IMAGE);
        $array[1]['ratio']         = self::$BLOG_RATIO;
        $array[1]['mimes']         = self::$BLOG_MIMES;
        $array[1]['max_file_size'] = self::$BLOG_MAX_FILE_SIZE;

        return $array;
    }

}
