<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Slider;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    public function index() {
        //Slider
        $slider = Slider::all();
        return view('cozastore.home',compact('slider'));
    }

    public function shop() {
        $d = Product::all();
        return view('cozastore.shop',compact('d'));
    }

    public function cart() {
        return view('cozastore.cart');
    }

    public function blog() {
        return view('cozastore.blog');
    }

    public function about() {
        return view('cozastore.about');
    }

    public function contact() {
        return view('cozastore.contact');
    }

    public function bdetails() {
        return view('cozastore.blogdetail');
    }

    public function game() {
        return view('cozastore.components.game');
    }

    public function ProductQuickView($id){
        $p = Product::with('tags', 'colors', 'sizes', 'images', 'category')->find($id);
        return response()->json(['data' => $p, 'message' => 'User fatched successfully']);
    }
}
