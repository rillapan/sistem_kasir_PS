<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PanduanController extends Controller
{
    /**
     * Display the panduan page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $active = 'panduan';
        $title = 'Panduan Penggunaan';
        return view('panduan.index', compact('active', 'title'));
    }
}
