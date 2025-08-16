# Laravel movie reviewer server: Part 1

## 1. Install api
- `herd php artisan instal:api`

## 2. Create 2 migrations
- `herd php artisan make migration create_movies_table`
- `herd php artisan make migration create_reviews_table`

**Structure for migrations**:
1. movies:
```
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string("title", 255);
            $table->timestamps();
        });
    }
```
2. reviews:
```
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('movie_id')->constrained('movies')->cascadeOnDelete();
            $table->string('email', 255);
            $table->string('message', 255);
            $table->integer('rating');
            $table->timestamps();
        });
    }
```

## 3. Data insertion using DataGrip
1. Open the project in the datagrip
2. Click on the **Data Source**
3. Select the database, for this project it should be `database.sqlite`
4. Navigate to drivers folder, and locate `SQLite`
5. Install appropriate driver by clicking *Download* button
6. You can test if the drivers were installed correctly by clicking on the *test connection* button in the `Data Sources` folder
7. Restart datagrip before inserting values in the database
8. It should work!

## 4. Creating the model
- `herd php artisan make:model {NAME}`

Import this module to both models:
```
use Illuminate\Database\Eloquent\Concerns\HasUuids;
```

1. This structure for the Movie model honors one-many relationship between movie and reviews.
```
    class Movie extends Model
    {
        use HasUuids;

        public function reviews() {
            return $this -> hasMany(Review::class);
        }
    }
```

2. This structure for the Review model honors belongsTo relationship between review and movie.
```
    class Review extends Model
    {
        use HasUuids;

        protected $fillable = ['movie_id', 'email', 'message', 'rating'];

        public function Movie() {
            return $this -> belongsTo(Movie::class);
        }
    }
```

## 5. Create controller
- `herd php artisan make:controller MovieController`
- Functions implemented in the MovieController:
```
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
```

Also don't forget to import both models to the controller:
```
use App\Models\Movie
use App\Models\Review
```

# 6. Create 3 api routes:
- Import controller: `use App\Http\Controllers\MovieController;`
```
Route::get('/movies', [MovieController::class, 'getAllMovies']);
Route::get('/movies/{movie}/reviews', [MovieController::class, 'getReviews']);
Route::post('/movies/{movie}/reviews', [MovieController::class, 'addReview']);
```

## 7. CORS
1. Publish cors: `herd php artisan config:publish cors`
2. Goto /config/cors.php and add the client address (should be localhost:5500) in the allowed origins field.

# Server-side: Part 2
- The program worked from the very first test and w/o any bugs, now it's time to add other functionality.

1. Add server-side validation
Of course this should feature single-responsibility

We will need to import 5 modules:
```
    use Illuminate\Http\Response;
    use Illuminate\Support\Facades\Validator;
    use Illuminate\Support\Facades\Log;
    use Illuminate\Support\Facades\Mail;
    use App\Mail\ThankYou;
```

The 3 methods in the Movie Controller were re-designed
```
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
```

- To make the mail work, we should run: `herd php artisan make:mail ThankYou`
- This  creates `resources/views/thank-you-mail.blade.php` in which the following structure should be inserted:
```
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank you for your review</title>
</head>
<body>
    <h1>Thank you for your review</h1>

    <h2>Review details</h2>

    <ul>
        <li>Movie title: {{ $movie -> title }}</li>
        <li>Message: {{ $review -> message }}</li>
        <li>Rating: {{ $review -> rating }} stars</li>
    </ul>

    <p>This message was sent to {{ $review -> email }}.</p>
</body>
</html>
```

- Now this should work