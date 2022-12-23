<?php

namespace App\Models;

use App\Models\Region;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;
    protected $fillable = ["street", "buliding", "floor", "flat", "notes",'status', "latitude", "longitude", "region_id", "customer_id"];

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
