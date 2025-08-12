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
