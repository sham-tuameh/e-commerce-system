<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    //we don't need it in the project
    // get all comments
    public function index()
    {
        return response([
            'data' => Review::all(),
            'message' => 'data retrieved successfully'
        ]);
      //  return Comment::query()->get() ;
    }

    //create a comment
    public function store(Request $request)
    {
        $review=Review::query()->create([
            'user_id'=>$request->user()->id,
            'product_id'=>$request->get('product_id'),
            'content'=>$request->get('content')
        ]);
        return response($review);

//        return response([
//            'message'=> 'comment is created successfully'
//        ]);
    }


}
