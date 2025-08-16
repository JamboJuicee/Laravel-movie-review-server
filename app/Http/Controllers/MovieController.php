<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\ThankYou;

class MovieController extends Controller
{
    public function getAllMovies() {
        Log::info("Retrieving all movies");
        return response()->json(Movie::all());
    }

    public function getReviews(Movie $movie) {
        Log::info("Retrieving all reviews for a movie with id: " . $movie->id);
        return response()->json($movie->reviews);
    }

    public function addReview(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:5|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'movie_id' => 'required|exists:movies,id'
        ]);

        if ($validator->fails()) {
            Log::warning("Review validation failed");
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $review = Review::create($validator->validated());
        Log::info("Review saved successfully");

        $movie = Movie::find($request->movie_id);
        Mail::to($review->email)->send(new ThankYou($movie, $review));
        Log::info("Email sent");
        
        return response()->json($review, 201);
    }
}
