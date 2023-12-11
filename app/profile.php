<?php
session_start();

// Check if the user is trying to log out.
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $_SESSION = array(); // Clear the session array.

    if (isset($_COOKIE[session_name()])) { // If the session cookie exists, delete it.
        setcookie(session_name(), '', time() - 3600); // Set the cookie to expire in the past.
    }

    session_destroy(); // Destroy the session data on the server.
    header('Location: login.php'); // Redirect to the login page.
    exit();
}

// Check if the user is logged in by looking at session data.
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require('mysqli_connect.php');

// Prepare to retrieve user information from the database.
$user_id = $_SESSION['user_id'];
$query_user = "SELECT name, email, gender, phone, driving_experience FROM user WHERE id = $user_id";

$result_user = mysqli_query($dbc, $query_user); // Execute the query.

if ($result_user) {
    $row_user = mysqli_fetch_array($result_user, MYSQLI_ASSOC); // Fetch the result row.

    // Assign the results to variables.
    $name = $row_user['name'];
    $email = $row_user['email'];
    $gender = $row_user['gender'];
    $phone = $row_user['phone'];
    $driving_experience = $row_user['driving_experience'];

    mysqli_free_result($result_user);
} else {
    echo '<p class="error">There was an error in processing your request.</p>'; // Error handling.
}

// Now, let's retrieve the traffic report data for the user.
$query_traffic = "SELECT city AS location, traffic_jam AS status FROM post_data WHERE user_email = '$email' ORDER BY date DESC";
$result_traffic = mysqli_query($dbc, $query_traffic);

$state_status_table = '';
$state_status_table .=
    '<table class="dataTable">
		<tr class="dataHeaderRow">
			<th class="dataHeader">Location</th>
			<th class="dataHeader">Status</th>
			<th class="dataHeader"></th>
		</tr>';

if ($result_traffic) {
    while ($row_traffic = mysqli_fetch_array($result_traffic, MYSQLI_ASSOC)) {
        $state  =  $row_traffic['location'];
        $status =  $row_traffic['status'];
        $state_status_table .=
            '<tr class="dataContentRow">
                <td class="dataContent">' . $state . '</td>
                <td class="dataContent">' . $status . '</td>
                <td class="status status' . str_replace(' ', '', $status) . '"></td>
            </tr>';
    }
    mysqli_free_result($result_traffic);
} else {
    $state_status_table .= '
    <tr>
        <td colspan="3">
            <p class="noDataMessage">You haven\'t post anything yet.</p>
        </td>
    </tr>';
}
$state_status_table .= '</table>';


mysqli_close($dbc); // Close the database connection.

// Check if the profile is incomplete.
$profile_incomplete_message = '';
if (empty($name) || empty($gender) || empty($phone) || empty($driving_experience)) {
    $profile_incomplete_message = 'Some profile information is missing. <br/>Please update your profile.';
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>ClearWay: Profile</title>
	<link rel="icon" type="image/x-icon" href="../assets/imgs/logo.png">

    <!-- CSS Styling -->
    <link rel="stylesheet" href="../assets/style/profile.css">
</head>

<body>
    <header>
        <a class="leftContainer" href="../index.html" target="_self">
            <img src="../assets/imgs/logo.png" alt="logo image" class="logo">
            <h1 class="companyName">ClearWay</h1>
        </a>
        <div class="rightContainer">
            <nav class="navBar">
                <button class="switchDisplayModeBtn"></button>
                <a href="index.php" class="mapBtn">
                    <img src="../assets/imgs/map.png" alt="profile icon">
                </a>
            </nav>
        </div>
    </header>
    <main>
        <div class="personalInfoContainer">
            <div class="topContainer">
                <h3>Personal Profile</h3>
                <table>
                    <tr>
                        <td>Email</td>
                        <td>:</td>
                        <td><?php echo htmlspecialchars($email); ?></td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td>:</td>
                        <td><?php echo $name ? htmlspecialchars($name) : 'Not provided'; ?></td>
                    </tr>
                    <tr>
                        <td>Gender</td>
                        <td>:</td>
                        <td><?php echo $gender ? htmlspecialchars($gender) : 'Not provided'; ?></td>
                    </tr>
                    <tr>
                        <td>Phone</td>
                        <td>:</td>
                        <td><?php echo $phone ? htmlspecialchars($phone) : 'Not provided'; ?></td>
                    </tr>
                    <tr>
                        <td>Driving Experience</td>
                        <td>:</td>
                        <td><?php echo $driving_experience ? htmlspecialchars($driving_experience) . ' years' : 'Not provided'; ?></td>
                    </tr>
                </table>
            </div>
            <div class="bottomContainer">
                <p class="incompleteMessage"><?php echo $profile_incomplete_message; ?></p>
    
                <div class="buttonContainer">
                    <a href="Edit_User.php">Edit Profile</a> 
                    <a href="Forgot_Password.php">Reset Password</a> 
                    <a href="?action=logout" class="logoutBtn">Log out</a>
                </div>
            </div>
        </div>
        <div class="tableContainer">
            <h3>Post</h3>
            <?php echo $state_status_table ?>
            <div class="btnContainer">
                <button class="postBtn">
                    +
                </button>
            </div>
        </div>
    </main>
    <script src="../assets/script/displayMode.js"></script>
</body>

</html>