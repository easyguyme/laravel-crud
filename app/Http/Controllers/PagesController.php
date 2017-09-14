<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    //
    public function index(){
        $title ='Welcome to Mitch.io';

        return view('pages.index') ->with('title',$title);

    }
    
    public function about(){
        $title ='ABOUT US';
        
        return view('pages.about')->with('title',$title);
    }
    
    public function  services(){
        $data =array(
            'title'=>"Our Services",
            'services'=>['Web design', 'Programming', 'SEO']

        );
        
        return view('pages.services')->with($data);




    }
}
