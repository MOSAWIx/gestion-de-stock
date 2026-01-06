<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
        // Removed parent::__construct() as the parent class does not define a constructor
        $this->middleware('auth');
    }

    public function index()
    {
        return view('home');
    }
}
