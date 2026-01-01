<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SocialPlannerController extends Controller
{
    public function index()
    {
        return view('social-planner.index');
    }
}
