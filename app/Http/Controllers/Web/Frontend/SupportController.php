<?php

namespace VanguardLTE\Http\Controllers\Web\Frontend;

use Illuminate\Http\Request;
use VanguardLTE\Http\Controllers\Controller;

class SupportController extends Controller
{
    //
    public function ticket(\Illuminate\Http\Request $request)
    {
        return view('frontend.Default.support.ticket');
    }

    public function about(\Illuminate\Http\Request $request)
    {
        return view('frontend.Default.support.about');
    }
}
