<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Inertia\Inertia;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index()
    {
        $books = Book::all()->map(function ($book) {
            return [
                'id' => $book->id,
                'title' => $book->title,
                'author' => $book->author,
                'is_available' => $book->isAvailable(),
            ];
        });

        return Inertia::render('Books/Index', [
            'books' => $books
        ]);
    }
}
