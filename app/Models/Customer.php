<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable =[
        'document_type',
        'document_number',
        'name',
        'email',
        'phone',
        'addres',
        'is_active',
    ];
  
    public function getFullDocumentAttribute(): string
    {
        return "{$this->document_type}-{$this->document_number}";
    }
    
}
