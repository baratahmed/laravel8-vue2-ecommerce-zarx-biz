<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    public $table = 'carts';

    // protected $guarded = [];
    protected $fillable = ['user_id','product_id','quantity','price'];

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
