<?php
session_start();
$page_title = 'Register';
$error_message = '';
// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    require('mysqli_connect.php');

    $errors = array();

    // Check and validate the email address & password
    if (empty($_POST['email'])) {
        $errors[] = 'You forgot to enter your email address.';
    } else {
        $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
        // Email validation pattern
        $email_pattern = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
        if (!preg_match($email_pattern, $email)) {
            $errors[] = 'Your email address is invalid.';
        }
    }


    if (!empty($_POST['pass1'])) {
        $password = mysqli_real_escape_string($dbc, trim($_POST['pass1']));
        // Password validation pattern (minimum 8 characters)
        $password_pattern = "/^.{8,}$/";
        if (!preg_match($password_pattern, $password)) {
            $errors[] = 'Your password must be at least 8 characters long.';
        } elseif ($_POST['pass1'] != $_POST['pass2']) {
            $errors[] = 'Your new password did not match the confirmed password.';
        }
    } else {
        $errors[] = 'You forgot to enter your new password.';
    }
    if (empty($errors)) {

        // Register the user in the database...


        $hashed_password = sha1($password);

        $query = "INSERT INTO user (email, password) VALUES ('$email', '$hashed_password')";

        $result = @mysqli_query($dbc, $query);
        if ($result) {

            // Retrieve the user_id of the newly registered user
            $user_id = mysqli_insert_id($dbc);

            // Set the session data with user_id and email
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;

            // Redirect the user to the profile page:
            header("Location: Profile.php");
            exit();
        } else {

            // Public message:
            echo '<h1>System Error</h1>
            <p class="error">You could not be registered due to a system error. We apologize for any inconvenience.</p>';

            // Debugging message:
            echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>';
        }
    } else {
        // Report the errors.
        if (!empty($errors)) {
            foreach ($errors as $e) {
                $error_message .= '<p class="errorText">' . $e . '</p>';
            }
            //scroll to bottom
            $error_message .= '<script>window.scrollTo(0, document.body.scrollHeight);</script>';
            
        }

    }

    // Close the database connection if it's still open.
    if (isset($dbc) && is_resource($dbc)) {
        mysqli_close($dbc);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClearWay: Register</title>
    <link rel="icon" type="image/x-icon" href="../assets/imgs/logo.png">

    <!-- CSS Styling -->
    <link rel="stylesheet" href="../assets/style/form.css">
</head>

<body>
    <header>
        <button class="switchDisplayModeBtn"></button>
    </header>
    <main>
        <a href="../index.html" target="_self" class="imgContainer">
            <img src="../assets/imgs/logo.png" alt="logo icon">
        </a>

        <form action="register.php" method="post">
            <div class="inputContainer">
                <label for="email">Email:</label>
                <input required placeholder="example@gmail.com" type="text" name="email" id="email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>">
            </div>
            <div class="inputContainer">
                <label for="password">Password:</label>
                <input required placeholder="example12345" type="password" class="passwordInput" name="pass1" id="password">
            </div>
            <div class="inputContainer">
                <label for="confirmPassword">Confirm Password:</label>
                <input required placeholder="example12345" type="password" class="passwordInput" name="pass2" id="confirmPassword">
            </div>
            <a class="forgotPasswordLink" href="forget-password.php">Forgot password?</a>
            <button class="submitBtn" type="submit" name="submit">REGISTER</button>
            <div class="textContainer">
                <p>Dont have an account? </p>
                <a href="login.php"> Login</a>
            </div>
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