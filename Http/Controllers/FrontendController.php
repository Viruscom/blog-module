<?php

namespace Modules\Blog\Http\Controllers;

use App\Http\Controllers\Controller;

class FrontendController extends Controller
{
    public function index()
    {
        return view('blog::frontend.index');
    }
}
