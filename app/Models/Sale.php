<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
    'voucher_number',
    'customer_id',
    'user_id',
    'subtotal',
    'tax',
    'discount',
    'total',
    'payment_method',
    'paid_amount',
    'change_amount',
    'sale_type',
    'due_date',
    'pending_amount',
    'status',
    'notes',
];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }
}
