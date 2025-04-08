<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    //About Us Page
    public function aboutUs()
    {
        return view('about-us'); // Ensure the 'about_us.blade.php' file exists in your views folder
    }
}
