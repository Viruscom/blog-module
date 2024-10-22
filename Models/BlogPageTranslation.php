<?php

    namespace Modules\Blog\Models;

    use Illuminate\Database\Eloquent\Model;

    class BlogPageTranslation extends Model
    {
        public    $timestamps = false;
        protected $fillable   = ['title', 'content'];
    }
