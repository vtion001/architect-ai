<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContentCreatorController extends Controller
{
    public function index()
    {
        return view('content-creator.index');
    }
}
