<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        return response([
            'data' => Category::all()
        ]);
        //return Category::query()->get() ;
    }

    public function store(Request $request)
    {
        $category = new Category() ;
        $category->name = $request->get('name');
        $created =$category->save();
        return response([
            'data' => $created
        ]);
//        return response([
//            'message'=>$is_added == 1 ? 'Category is added successfully ' : 'An error occurred while adding the product'
//        ]);
    }




}
