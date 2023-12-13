<?php

require("send_email.php");

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

        $verify_purpose = "register";   

        $veruft_url_link = DOMAIN."/app/verification.php?key=" . VERIFICATION_KEY . "&purpose=". $verify_purpose . "&email=" . $email . "&password=" . $password;
        $email_title = "Clear Way: Register Verification";
        $email_body = '
        <h3>To verify the email for <b>REGISTRATION</b>registration, please click the link below:</h3>
        <p>'. $veruft_url_link .'</p>';

        if(send_email_verification($email, $email_title, $email_body)) {
            $error_message .= "<p class='normalText'>The verification was send to your mailbox, <br>please follow the instruction to get verified.";
        } else {
            $error_message .= "<p class='normalText'>Verification email sent FAILED, please contact admin(syehrran@gmail.com)<br> to register an account. </p>";
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