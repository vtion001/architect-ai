<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        return view('knowledge-base.knowledge-hub');
    }
}
