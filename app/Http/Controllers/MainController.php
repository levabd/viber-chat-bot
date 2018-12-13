<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ViberUser;

class MainController extends Controller
{

    public function index(Request $request) {
    $viberUsers = ViberUser::whereNotNull('completed_session_id')->with('completedSession', 'completedSession.drug')->paginate(20);
        return view('main', ['viberUsers' => $viberUsers]);
    }
    
}
