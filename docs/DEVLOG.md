# Laravel movie reviewer server

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

## Data insertion using DataGrip
1. Open the project in the datagrip
2. Click on the **Data Source**
3. Select the database, for this project it should be `database.sqlite`
4. Navigate to drivers folder, and locate `SQLite`
5. Install appropriate driver by clicking *Download* button
6. You can test if the drivers were installed correctly by clicking on the *test connection* button in the `Data Sources` folder
7. Restart datagrip before inserting values in the database
8. It should work!

##