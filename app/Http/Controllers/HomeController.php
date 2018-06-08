<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return view.
     */
    public function index()
    {
        $user = \Auth::user();
         //dd($user->hasRole('agency'));
        // dd($user->hasRole('admin')); //false
        // dd($user->can('edit-profile')); //true

        if($user->hasRole('admin') == true){
            return view('admin/response/index');
        }
        elseif($user->hasRole('agency')== true){
            return view('admin/response/index');
        }
    }
}
