<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return redirect()
            ->route('pwa');

    }
    public function pwa()
    {
        return view('pwa.home');
    }
}
