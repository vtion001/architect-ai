<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResearchEngineController extends Controller
{
    public function index()
    {
        return view('research-engine.index');
    }
}
