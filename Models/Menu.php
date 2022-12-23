<?php

namespace App\Models;

use App\Traits\MenuImages;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia , MenuImages;
    protected $fillable = ['category_id','name', 'slug', 'description', 'status', 'quantity', 'prices_sizes','default_price','default_size'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
