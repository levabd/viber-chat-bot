<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ViberUser;

class SessionController extends Controller
{

    public function index($user_id, Request $request) {
        try {
    $viberUser = ViberUser::with('sessions')->where('id', $user_id)->firstOrFail();
        } catch(\Exception $e) {
        //return back();
    }//catch
        return view('session', ['viberUser' => $viberUser]);
    }
    
}
