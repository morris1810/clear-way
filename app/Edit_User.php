<?php
session_start();
$error_message = '';

// Check if the user is logged in.
if (!isset($_SESSION['user_id'])) {
    // Not logged in, redirect to login page.
    header('Location: login.php');
    exit();
}

require('mysqli_connect.php');
$user_id = $_SESSION['user_id']; // Get the user ID from the session.

// Initialize variables to hold user information
$name = $email = $gender = $phone = $driving_experience = '';

// Retrieve the user's email from the session or database
if (!isset($_SESSION['email'])) {

    $query = "SELECT email FROM user WHERE id = $user_id";
    $result = mysqli_query($dbc, $query);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $_SESSION['email'] = $row['email'];
    mysqli_free_result($result);
}

$errors = array(); // Initialize an error array.

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate the name
    if (empty($_POST['name'])) {
        $errors[] = 'You forgot to enter your name.';
    } else {
        $name = mysqli_real_escape_string($dbc, trim($_POST['name']));
    }

    if (!is_numeric($_POST['phone'])) {
        $errors[] = 'Only numeric value for phone';
    }
    if (!is_numeric($_POST['driving_experience'])) {
        $errors[] = 'Only numeric value for Driving Experience';
    }

    // Assume other fields are filled out
    $phone = mysqli_real_escape_string($dbc, trim($_POST['phone']));
    $gender = mysqli_real_escape_string($dbc, trim($_POST['gender']));
    $driving_experience = mysqli_real_escape_string($dbc, trim($_POST['driving_experience']));

    if (empty($errors)) {

        // Update the user's info
        $query = "UPDATE user SET name='$name', phone='$phone', gender='$gender', driving_experience='$driving_experience' WHERE id=$user_id";
        $result = mysqli_query($dbc, $query);

        if (mysqli_affected_rows($dbc) == 1) {
            // Redirect to the profile page.
            header('Location: profile.php');
            exit();
        } 
    } else { // Report the errors.
        foreach ($errors as $e) {
            $error_message .= '<p class="errorText">'.$e.'</p>';
        }
        //scroll to bottom
        $error_message .= '<script>window.scrollTo(0, document.body.scrollHeight);</script>';
    } 
} else {
    // Fetch the user's current data to populate the form.
    $query = "SELECT name, phone, gender, driving_experience FROM user WHERE id=$user_id";
    $result = mysqli_query($dbc, $query);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $name = $row['name'];
    $phone = $row['phone'];
    $gender = $row['gender'];
    $driving_experience = $row['driving_experience'];
    mysqli_free_result($result);
}

mysqli_close($dbc);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClearWay: Edit Profile</title>
    <link rel="icon" type="image/x-icon" href="../assets/imgs/logo.png">

    <!-- CSS Styling -->
    <link rel="stylesheet" href="../assets/style/form.css">
</head>

<body>
    <header>
        <button class="switchDisplayModeBtn"></button>
    </header>
    <main>
        <a href="../edit_user.php" target="_self" class="imgContainer">
            <img src="../assets/imgs/logo.png" alt="logo icon">
        </a>

        <form action="edit_user.php" method="post">
            <div class="inputContainer">
                <label for="name">Name:</label>
                <input placeholder="Your Name" type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div class="inputContainer">
                <label for="name">Email:</label>
                <input required placeholder="example@gmail.com" type="text" name="email" id="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly>
            </div>
            <div class="inputContainer">
                <label for="phone">Phone:</label>
                <input placeholder="+6012-345 6789" type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($phone); ?>">
            </div>
            <div class="inputContainer">
                <label for="gender">Gender:</label>
                <!-- <input placeholder="example@gmail.com" type="text" name="gender" id="gender" value="<?php echo htmlspecialchars($gender); ?>"> -->
                <select name="gender" id="gender">
                    <option value=""></option>
                    <option <?php if (htmlspecialchars($gender) == 'Male') echo 'selected'; ?> value="Male">Male</option>
                    <option <?php if (htmlspecialchars($gender) == 'Female') echo 'selected'; ?> value="Female">Female</option>
                </select>
            </div>
            <div class="inputContainer">
                <label for="driving_experience">Driving Experience (Year):</label>
                <input placeholder="example@gmail.com" type="text" name="driving_experience" id="driving_experience" value="<?php echo htmlspecialchars($driving_experience); ?>">
            </div>
            <br>
            <button class="submitBtn" type="submit" name="submit">UPDATE PROFILE</button>
        </form>
    </main>
    <div class="displayMessageContainer">
        <?php
        echo $error_message;
        ?>
    </div>
    <script src="../assets/script/displayMode.js"></script>
</body>

</html>