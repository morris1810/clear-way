<?php
// This page is for deleting a user record.

require('mysqli_connect.php');
$result_message = '';

// Check for a valid user ID, through GET or POST:
if ((isset($_GET['id'])) && (is_numeric($_GET['id']))) { // From Admin_Page.php
    $id = $_GET['id'];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) {
    $id = $_POST['id'];
} else { // No valid ID, kill the script.
    $result_message .= '<p class="error">User not found.</p>';
    $result_message .= '<a href="admin_table_user.php">Go Back</a>';
    exit();
}

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['sure'] == 'Yes') {
        $query = "DELETE FROM user WHERE id=$id LIMIT 1";
        $result = mysqli_query($dbc, $query);
        if (mysqli_affected_rows($dbc) == 1) {
            $result_message .= '<p style="color: var(--text-color); font-size: 1.2rem;">The user has been deleted.</p>';
            $result_message .= '<a href="admin_table_user.php">Go Back</a>';

        } else {
            $result_message .= '<p class="error">The user could not be deleted due to a system error.</p>';
            $result_message .= '<p>' . mysqli_error($dbc) . '<br />Query: ' . $query . '</p>';
        }
    } else { // No confirmation of deletion.
        $result_message .= '<p>The user has NOT been deleted.</p>';
        $result_message .= '<a href="admin_table_user.php">Go Back</a>';

    }
} else {

    // Retrieve the user's information:
    $query = "SELECT name FROM user WHERE id=$id";
    $result = mysqli_query($dbc, $query);
    $display = '';
    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_array($result, MYSQLI_NUM);
        
        $display .=  "<h5>Are you sure you want to delete {$row[0]}</h5>";

        $display .= '<form action="admin_delete_data.php" method="post">
        <div class="radioContainer">
            <input type="radio" id="yes" name="sure" value="Yes" /> 
            <label id="yesLabel" for="yes">Yes</label>
            <input type="radio" id="no" name="sure" value="No" checked /> 
            <label id="noLabel" for="no">No</label>
        </div>
            <button class="submitBtn deleteBtn" type="submit" name="submit">DELETE</button>
            <input type="hidden" name="id" value="' . $id . '" />
        </form>';

    } else {
        $result_message .= '<p class="error">This page has been accessed in error.</p>';
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
        <?php echo $result_message; ?>
        <?php echo $display; ?>
    </main>
    <script src="../assets/script/displayMode.js"></script>
</body>

</html>