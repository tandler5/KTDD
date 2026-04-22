<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author',
        'isbn',
    ];

    public function rentals()
    {
        return $this->hasMany(Rental::class);
    }

    public function isAvailable(): bool
    {
        return !$this->rentals()->whereNull('returned_at')->exists();
    }
}
