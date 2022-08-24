<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_url',
        'price',
        'quantity',
        'phone_number',
        'description',
        'exp_date',
        'views',
        'category_id',
        'user_id'
    ];

    protected $appends = ['new_price', 'is_editable', 'liked', 'likes_count'];

    public $with = ['reviews'];
    protected $withCount=['reviews'];

    public function getNewPriceAttribute()
    {
        $new_price = $this->price;
        $discounts = $this->discounts;
        foreach ($discounts as $discount) {
            if (now() >= $discount['date']) {
                $new_price = $this->price - ($this->price * $discount['discount_percentage'] / 100);
            }
        }
        return $new_price;
    }

    public function getLikedAttribute(): bool
    {
        return Like::query()
            ->where('user_id', '=', Auth::id())
            ->where('product_id', '=', $this->id)
            ->exists();
    }

    public function getLikesCountAttribute(): int
    {
        return Like::query()
            ->where('product_id', '=', $this->id)
            ->count();
    }

    /*
     * used to return if the current product is editable
     * by the user which has sent the request or not
     * */
    public function getIsEditableAttribute(): bool
    {
        return $this->user_id == Auth::id();
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class)->orderBy('date');
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes');
    }


}
