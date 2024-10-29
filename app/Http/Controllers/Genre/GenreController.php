<?php

namespace App\Http\Controllers\Genre;

use App\Http\Controllers\Controller;
use App\Http\Requests\Genre\StoreGenreRequest;
use App\Http\Requests\Genre\UpdateGenreRequest;
use App\Models\Genre;
use Carbon\Carbon;

class GenreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $genres = Genre::whereNull('deleted_at')->get();

        return response()->json([
            'genres'    => $genres,
            'message'   => 'Show all genres'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGenreRequest $request)
    {
        $genre = Genre::create($request->validated());

        return response()->json([
            'genre'     => $genre,
            'message'   => 'Genre created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Genre $genre)
    {
        abort_if(!$genre || $genre->deleted_at != null, 404);

        return response()->json([
            'genre'     => $genre,
            'message'   => "Show detail of {$genre->name}"
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGenreRequest $request, Genre $genre)
    {
        abort_if(!$genre || $genre->deleted_at != null, 404);

        $genre->update($request->validated());

        return response()->json(status: 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Genre $genre)
    {
        abort_if(!$genre || $genre->deleted_at != null, 404);

        $genre->update([
            'deleted_at' => Carbon::now()
        ]);

        return response()->json(status: 204);
    }
}
