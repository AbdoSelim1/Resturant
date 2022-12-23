<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model implements HasMedia
{
    use HasFactory , InteractsWithMedia;

    protected $fillable = ['name','email','phone','password','email_verified_at','status'];
}
