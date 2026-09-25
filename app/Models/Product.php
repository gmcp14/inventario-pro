<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable= [
        'category_id',
        'code',
        'name',
        'slug',
        'description',
        'cost_price',
        'sale_price',
        'stock',
        'min_stock',
        'image_path',
        'is_active',
    ];

    public function category(){
        return $this->belongsTo(Category::class);
    }
}
