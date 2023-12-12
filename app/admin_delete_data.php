<?php
// This page is for deleting a user record.

require('mysqli_connect.php');

// Check for a valid user ID, through GET or POST:
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) { // From Admin_Page.php
    $id = $_GET['id'];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
    $id = $_POST['id'];
} else { // No valid ID, kill the script.
    echo '<p class="error">This page has been accessed in error.</p>';
    exit();
}

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['sure'] == 'Yes') {
        $query = "DELETE FROM user WHERE id=$id LIMIT 1";
        $result = mysqli_query($dbc, $query);
        if (mysqli_affected_rows($dbc) == 1) {
            echo '<p>The user has been deleted.</p>';
        } else {
            echo '<p class="error">The user could not be deleted due to a system error.</p>';
            echo '<p>' . mysqli_error($dbc) . '<br />Query: ' . $query . '</p>';
        }
    } else { // No confirmation of deletion.
        echo '<p>The user has NOT been deleted.</p>';
    }
} else {

    // Retrieve the user's information:
    $query = "SELECT name FROM user WHERE id=$id";
    $result = mysqli_query($dbc, $query);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        echo "<h3>Name: {$row[0]}</h3>
        Are you sure you want to delete this user?";


        echo '<form action="Delete_Data.php" method="post">
            <input type="radio" name="sure" value="Yes" /> Yes
            <input type="radio" name="sure" value="No" checked="checked" /> No
            <input type="submit" name="submit" value="Submit" />
            <input type="hidden" name="id" value="' . $id . '" />
        </form>';
    } else {
        echo '<p class="error">This page has been accessed in error.</p>';
    }
}

mysqli_close($dbc);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Delete User</title>
    <link rel="icon" type="image/x-icon" href="../assets/imgs/logo.png">

    <!-- CSS Styling -->
    <link rel="stylesheet" href="../assets/style/form.css">

</head>

<body>
    <header>
        <button class="switchDisplayModeBtn"></button>
    </header>
    <main>
        
    </main>
</body>
</html>