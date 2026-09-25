<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_type',
        'document_number',
        'name',
        'email',
        'phone',
        'address',
        'contact_person',
        'is_active',
    ];

    public function getFullDocumentAttribute(): string
    {
        return "{$this->document_type}-{$this->document_number}";
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}