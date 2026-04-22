<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{

    protected $fillable = [
        'book_id',
        'user_id',
        'rented_at',
        'due_date',
        'returned_at',
    ];
}
