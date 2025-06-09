<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Display the FAQ page for customers.
     */
    public function index()
    {
        $faqs = Faq::where('is_published', true)
                   ->orderBy('display_order')
                   ->get();
                   
        return view('customer.faqs.index', compact('faqs'));
    }

    
}