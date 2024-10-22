<?php

    namespace Modules\Blog\Models;

    use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
    use Astrotomic\Translatable\Translatable;
    use Illuminate\Database\Eloquent\Model;

    class BlogCategoryTranslation extends Model implements TranslatableContract
    {
        use Translatable;
        
        public    $timestamps = false;
        protected $fillable   = ['name', 'description'];
    }
