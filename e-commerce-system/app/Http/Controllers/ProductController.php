<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Like;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Boolean;

class ProductController extends Controller
{
    // get all products , specific product , sort
    // Get all products that matches the query parameters
    //      ex: get all products that 'contains' name
    public function index()
    {
        //Delete expired products
        //Product::query()->where('exp_date','<',now())->delete();

        $products = Product::query();
        //get info from the url
        $name = request('name');
        $category_id = request('category_id');
        $exp_date = request('exp_date');
        $price = request('price');
        $start_price = request('start_price');
        $end_price = request('end_price');
        $start_exp = request('start_exp');
        $end_exp = request('end_exp');
        $user_id = request('user_id');
        $sort_by = request('sort_by');

        if ($name) {
            $products->where('name', 'LIKE', '%' . $name . '%');
        }
        if ($user_id) {
            $products->where('user_id', '=', $user_id);
        }
        if ($price) {
            $products->where('price', '=', $price);
        }
        if ($start_price) {
            $products->where('price', '>=', $start_price);
        }
        if ($end_price) {
            $products->where('price', '<=', $end_price);
        }
        if ($category_id) {
            $products->where('category_id', '=', $category_id);
        }
        if ($exp_date) {
            $products->where('exp_date', '=', $exp_date);
        }
        if ($start_exp) {
            $products->where('exp_date', '>=', $start_exp);
        }
        if ($end_exp) {
            $products->where('exp_date', '<=', $end_exp);
        }
        if ($sort_by) {
            $products->orderBy($sort_by);
        }
        return response([
            'data' => $products->get()
        ]);

    }

    // crete a new product and save it
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'max:40'],
            'image_url' => ['required'],
            'phone_number' => ['required' , 'max:15'],
            'price' => ['required'],
            'exp_date' => ['required'],
            'category_id' => ['required'],
            'quantity'=> ['required']
        ]);


        $new_product = new Product();
        // get details from the body
        $new_product->name = $request->get('name');
        $new_product->image_url = $request->get('image_url');
        $new_product->phone_number = $request->get('phone_number');
        $new_product->price = $request->get('price');
        // $new_product->exp_date = Carbon::parse($request->get('exp_date'))->format('Y-m-d H:i:s');
        $new_product->exp_date = $request->get('exp_date');
        $new_product->quantity = $request->get('quantity');
        $new_product->user_id = $request->user()->id;
        $new_product->category_id = $request->get('category_id');
        $new_product->description = $request->get('description');
        $new_product->save();


        //todo
        // store discounts
         $discount_list = json_decode($request->get('discount_list'));
        //to test this in post man
        //$discount_list = $request->get('discount_list');
        foreach ($discount_list as $discount) {
            Discount::query()->create([
                'date' => $discount->date,
                'discount_percentage' => $discount->discount_percentage,
                'product_id' => $new_product->id,
            ]);
        }
        return response($new_product);

    }

    // get product from database using 'implicit data binding' way
    public function show(Product $product)
    {
        Product::query()->where('exp_date','<',now())->delete();

        return response($product);
    }

    public function increaseViews(Product $product)
    {
        $product->views += 1;
        $product->save();
        return response([
            'message' => 'increased'
        ]);
    }

    public function update(Request $request, Product $product)
    {
        if ($product->user_id != Auth::id())
            return response([
                'message' => 'you can\'t update a product you don\'t own'
            ], 401);

        $name = $request->input('name');
        $image_url = $request->input('image_url');
        $phone_number = $request->input('phone_number');
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        $description = $request->input('description');

        if ($name) {
            $product->name = $name;
        }
        if ($image_url) {
            $product->image_url = $image_url;
        }
        if ($phone_number) {
            $product->phone_number = $phone_number;
        }
        if ($price) {
            $product->price = $price;
        }
        if ($quantity) {
            $product->quantity = $quantity;
        }
        if ($description) {
            $product->description = $description;
        }

        $updated = $product->save();
        return response([
            'message' => $updated ? 'updated successfully' : 'validate your data'
        ]);
    }

    public function destroy(Product $product)
    {
        if (Auth::id() != $product->user_id)
            return response([
                'message' => 'you can\'t delete a product you don\'t own'
            ], 401);

        return response([
            'message' => $product->delete() . ' product deleted'
        ]);
    }

    public function like(Product $product)
    {
        $existing_like = Like::withTrashed()
            ->where('product_id', '=', $product->id)
            ->where('user_id', '=', Auth::id())
            ->first();

        // if there is no relation between products and user yet
        if (is_null($existing_like)) {
            Like::query()->create([
                'product_id' => $product->id ,
                'user_id' => Auth::id()
            ]);
        } else {
            //if 'like' is existed => move it to the trash
            if (is_null($existing_like->deleted_at)) {
                $existing_like->delete();
            } else {
                $existing_like->restore();
            }
        }
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image:jpeg,png,jpg,gif,svg,bmp'
        ]);
        $image_path = $request->file('image')->store('public');
        return response([
            'image_name' => pathinfo($image_path)['basename']
        ]);

    }
}
