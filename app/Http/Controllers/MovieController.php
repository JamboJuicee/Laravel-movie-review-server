<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function getAllMovies() {
        return Movie::all();
    }

    public function getReviews(Movie $movie) {
        return $movie->reviews;
    }

    public function addReview(Request $request) {
        $review = new Review($request->all());
        $review->save();

        return response()->json($review, 201);
    }
}
