<?php

    namespace Modules\Blog\Models;

    use App\Traits\StorageActions;
    use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
    use Astrotomic\Translatable\Translatable;
    use Illuminate\Database\Eloquent\Model;

    class BlogCategory extends Model implements TranslatableContract
    {
        use Translatable, StorageActions;

        public    $translatedAttributes = ['name', 'description'];
        protected $fillable             = ['parent_id'];
    }
