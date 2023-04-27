<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/reserve.css">
    <title>Hotel Reservation</title>
</head>
<body>
    <header>
        <h1>Hotel Reservation</h1>
    </header>
    <main>
        <form action="backend/reserve.php" method="POST" id="reservation-form">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="checkin">Check-in:</label>
            <input type="date" name="checkin" id="checkin" required>

            <label for="checkout">Check-out:</label>
            <input type="date" name="checkout" id="checkout" required>

            <label for="room">Room type:</label>
            <select name="room" id="room" required>
                <option value="">Select a room type</option>
                <option value="single">Single</option>
                <option value="double">Double</option>
                <option value="suite">Suite</option>
            </select>

            <input type="submit" value="Reserve">
        </form>
    </main>
</body>
</html>
