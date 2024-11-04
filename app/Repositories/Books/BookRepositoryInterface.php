<?php

namespace App\Repositories\Books;

use App\Http\Requests\Book\StoreBookRequest;
use App\Http\Requests\Book\UpdateBookRequest;
use App\Models\Book;
use Illuminate\Http\Request;

interface BookRepositoryInterface
{
    public function getAll();
    public function getById(Book $book);
    public function create(StoreBookRequest $request);
    public function update(UpdateBookRequest $request, Book $book);
    public function delete(Book $book);
    public function searchBooks(Request $request);
}
