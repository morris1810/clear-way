<?php

$state_option = "";

$state_list = array(
    "Johor",
    "Kedah",
    "Kelantan",
    "Melaka",
    "Negeri Sembilan",
    "Pahang",
    "Perak",
    "Perlis",
    "Penang",
    "Sabah",
    "Sarawak",
    "Selangor",
    "Terengganu"
);


foreach ($state_list as $state) {
    //generate the option with all state
    $state_option .= '<option value="' . $state . '">' . $state . '</option>';
}


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
$query_traffic = "SELECT CONCAT(city, ', ', state) AS location, traffic_jam AS status, date FROM post_data WHERE user_email = '$email' ORDER BY date DESC";
$result_traffic = mysqli_query($dbc, $query_traffic);

$state_status_table = '';
$state_status_table .=
    '<table class="dataTable">
		<tr class="dataHeaderRow">
			<th class="dataHeader">Location</th>
			<th class="dataHeader">Status</th>
			<th class="dataHeader">DateTime</th>
		</tr>';

if ($result_traffic) {
    while ($row_traffic = mysqli_fetch_array($result_traffic, MYSQLI_ASSOC)) {





        // Print the formatted date and time
        echo $newDateTime; // Output: 12-Dec 11:02

        $state  =  $row_traffic['location'];
        $status =  $row_traffic['status'];

        $dateTimeObj = new DateTime($row_traffic['date']);
        $datetime = $dateTimeObj->format("d-M H:i");

        $state_status_table .=
            '<tr class="dataContentRow">
                <td class="dataContent">' . $state . '</td>
                <td class="status status' . str_replace(' ', '', $status) . '"></td>
                <td class="dataContent">' . $datetime . '</td>
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

//================================================================
// For pop up post data and handle post data form submission
//================================================================

session_start();

$post_data_pop_up_css = "display: none;";
if (isset($_GET['action']) && $_GET['action'] == 'post_data') {

    // Check if the user is logged in by looking at session data.
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    $post_data_pop_up_css = "display: auto;";
}


$error_message = '';

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Check if the user is logged in.
    if (!isset($_SESSION['user_id'])) {
        // Not logged in, redirect to login page.
        header('Location: login.php');
        exit();
    }

    require('mysqli_connect.php');

    $errors = array();

    // Check and validate the street (optional):
    $street = !empty($_POST['street']) ? mysqli_real_escape_string($dbc, trim($_POST['street'])) : NULL;

    // Check for a city:
    if (empty($_POST['city'])) {
        $errors[] = 'You forgot to enter your city.';
    } else {
        $city = mysqli_real_escape_string($dbc, trim($_POST['city']));
    }

    // Check for a state:
    if (empty($_POST['state'])) {
        $errors[] = 'You forgot to enter your state.';
    } else {
        $state = mysqli_real_escape_string($dbc, trim($_POST['state']));
    }

    // Check for a postcode:
    if (empty($_POST['postcode'])) {
        $errors[] = 'You forgot to enter your postcode.';
    } else {
        $postcode = mysqli_real_escape_string($dbc, trim($_POST['postcode']));
    }

    // Check for a country:
    if (empty($_POST['country'])) {
        $errors[] = 'You forgot to enter your country.';
    } else {
        $country = mysqli_real_escape_string($dbc, trim($_POST['country']));
    }

    // Check for the traffic jam value:
    if (empty($_POST['traffic_jam'])) {
        $errors[] = 'You forgot to specify the traffic jam level.';
    } else {
        $traffic_jam = mysqli_real_escape_string($dbc, trim($_POST['traffic_jam']));
    }

    // Check for the date:
    if (empty($_POST['date'])) {
        $errors[] = 'You forgot to enter the date of the traffic report.';
    } else {
        $date = mysqli_real_escape_string($dbc, trim($_POST['date']));
    }

    if (empty($errors)) {

        // Retrieve the email from the session
        $email = isset($_SESSION['email']) ? mysqli_real_escape_string($dbc, $_SESSION['email']) : null;

        // Create the query:
        $query = "INSERT INTO post_data (user_email, street, city, state, postcode, country, traffic_jam, date)
              VALUES ('$email', '$street', '$city', '$state', '$postcode', '$country', '$traffic_jam', '$date')";

        $result = mysqli_query($dbc, $query);

        if ($result) {
            // Success message or redirection
            $result_message =  'Your traffic report has been submitted successfully.';
        } else { // If it did not run OK.
            // Public message:
            $result_message = 'Something went wrong';

            // Debugging message:
            echo '<script> console.log("' . mysqli_error($dbc) . '\nQuery: ' . $query . '");</script>';
        }
        echo '<script>alert("' . $result_message . '")</script>';


        mysqli_close($dbc); // Close the database connection.
        header('Location: profile.php');
    } else {
        // Report the errors.
        foreach ($errors as $e) {
            $error_message .= '<p class="errorText">' . $e . '</p>';
        }
        //scroll to bottom
        $error_message .= '<script>window.scrollTo(0, document.body.scrollHeight);</script>';
    }
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
    <link rel="stylesheet" href="../assets/style/popup.css">
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
                    <a href="edit_user.php">Edit Profile</a>
                    <a href="forget-password.php">Reset Password</a>
                    <a href="?action=logout" class="logoutBtn">Log out</a>
                </div>
            </div>
        </div>
        <div class="tableContainer">
            <h3>Your Post</h3>
            <?php echo $state_status_table ?>

        </div>
        <div class="btnContainer">
                <a href="?action=post_data" class="postBtn">
                    +
                </a>
            </div>
    </main>
    <div class="newPostPopUp" style="<?php echo $post_data_pop_up_css; ?>">
        <div class="displayMessageContainer">
            <?php
            echo $error_message;
            ?>
        </div>
        <form method="post" class="newPostForm" action="profile.php?action=post_data">
            <input type="hidden" name="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>">
            <a href="?action=none" class="closeBtn">
                <img src="../assets/imgs/close.png" alt="close button">
            </a>
            <label>Location:</label>
            <input name="street" type="text" placeholder="Street(Optional)" value="<?php if (isset($_POST['street'])) echo $_POST['street']; ?>">
            <input name="city" type="text" placeholder="City*" value="<?php if (isset($_POST['city'])) echo $_POST['city']; ?>">
            <span class="row">
                <!-- <input type="text" placeholder="State*"> -->
                <select id="state" name="state">
                    <option disabled selected value="">State*</option>
                    <?php
                    if (isset($_POST['state'])) {
                        echo '<option value="' . $_POST['state'] . '" selected>' . $_POST['state'] . '</option>';
                    }

                    echo $state_option;
                    ?>
                </select>
                <input name="postcode" type="text" placeholder="Postcode*" value="<?php if (isset($_POST['postcode'])) echo $_POST['postcode']; ?>">
            </span>
            <input name="country" type="text" placeholder="Country*" value="<?php if (isset($_POST['country'])) echo $_POST['country']; ?>">
            <label for="">Traffic Jam:</label>
            <div class="row radioContainer">
                <input type="radio" name="traffic_jam" id="trafficJamLow" value="light" <?php if (isset($_POST['traffic_jam']) && $_POST['traffic_jam'] == 'light') echo 'checked="checked"'; ?>>
                <label id="trafficJamLowLabel" for="trafficJamLow">Light</label>
                <input type="radio" name="traffic_jam" id="trafficJamMedium" value="medium" <?php if (isset($_POST['traffic_jam']) && $_POST['traffic_jam'] == 'medium') echo 'checked="checked"'; ?>>
                <label id="trafficJamMediumLabel" for="trafficJamMedium">Medium</label>
                <input type="radio" name="traffic_jam" id="trafficJamHigh" value="heavy" <?php if (isset($_POST['traffic_jam']) && $_POST['traffic_jam'] == 'heavy') echo 'checked="checked"'; ?>>
                <label id="trafficJamHighLabel" for="trafficJamHigh">Heavy</label>
            </div>
            <span class="row dateContainer">
                <label>Date:</label>
                <input class="readonly" name="date" type="text" readonly value="<?php echo date('Y-m-d H:i:s'); ?>">
                <p> <?php echo date('d-M-Y H:i'); ?></p>
            </span>
            <button class="submitBtn" type="submit" name="submit">Post</button>
        </form>
    </div>
    <script src="../assets/script/displayMode.js"></script>
</body>

</html>