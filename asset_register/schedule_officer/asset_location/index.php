<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Location</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2>Add Location</h2>

<form action="process_add_location.php" method="post">
    <label for="location">Location:</label>
    <input type="text" id="location" name="location" required>
    <br>
    <input type="submit" value="Add Location">
</form>

</body>
</html>