<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ViberUser;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
                $viberUsers = ViberUser::with('session')->paginate(20);
                return view('home', ['viberUsers' => $viberUsers]);
    }
}
