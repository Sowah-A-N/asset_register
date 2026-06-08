<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assets Locations</title>
</head>
<body>
    <label for="">From:</label>
    <input type="date" name="from" id="from-date"><br />

    <label for="">To:</label>
    <input type="date" name="to" id="to-date"><br />

    <button type="submit">Generate</button>

    <?php

        include "datacon.php";

        $query = "SELECT * FROM {}";

        $result = $conn->query($query);

        if($result->num_rows > 0){
            echo "";
            echo "";
            echo "";
            echo "";
            echo "";

            while($row = $result->fetch_assoc()){
                echo "";
                echo "";
                echo "";
                echo "";
                echo "";
                echo "";
            }

        }

    ?>
    
</body>

</html>