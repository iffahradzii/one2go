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
    
    public function faqs()
    {
        $faqs = \App\Models\Faq::where('is_published', true)
                   ->orderBy('display_order')
                   ->get();
                   
        return view('customer.faqs.index', compact('faqs'));
    }
}
