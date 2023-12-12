<?php
session_start();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require ('mysqli_connect.php'); 
    
    $errors = array(); 
    
    // Validate the email address:
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
    
    // Validate the password:
    if (empty($_POST['password'])) {
        $errors[] = 'You forgot to enter your password.';
    } else {
        $password = mysqli_real_escape_string($dbc, trim($_POST['password']));
    }
    
    if (empty($errors)) {
        
        // Retrieve the user's information:
        $query = "SELECT id, email, password, role FROM user WHERE email='$email'";
        $result = mysqli_query($dbc, $query);
        
        if (mysqli_num_rows($result) == 1) { // User found.
            $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
            
            // Check the password:
            $entered_hashed_password = sha1($password);
            if ($entered_hashed_password == $row['password']) {
                // Set the session data:
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['email'] = $row['email'];
                
                // Redirect to another page:
                if ($row['role'] == 'admin') {
                    header("Location: Admin_Page.php");
                } else {
                    // Redirect the user to a login page if not an admin:
                    header("Location: index.php");
                }
                exit();
            } else {
                $errors[] = 'The password entered does not match the one on file.';
            }
        } else {
            $errors[] = 'The email address entered does not match any account.';
        }
    }
    
    // Report the errors.
    if (!empty($errors)) {
        foreach ($errors as $e) {
            $error_message .= '<p class="errorText">'.$e.'</p>';
        }
        //scroll to bottom
        $error_message .= '<script>window.scrollTo(0, document.body.scrollHeight);</script>';
    }
    
    mysqli_close($dbc); // Close the database connection.
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClearWay: Login</title>
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

        <form action="login.php" method="post">
            <div class="inputContainer">
                <label for="email">Email:</label>
                <input required placeholder="example@gmail.com" type="text" name="email" id="email" value="<?php if (isset($_POST['email'])) echo $_POST['email']; ?>">
            </div>
            <div class="inputContainer">
                <label for="password">Password:</label>
                <input required placeholder="example12345" type="password" class="passwordInput" name="password" id="password">
            </div>
            <a class="forgotPasswordLink" href="forget-password.php">Forgot password?</a>
            <button class="submitBtn" type="submit" name="submit">LOGIN</button>
            <div class="textContainer">
                <p>Dont have an account? </p>
                <a href="register.php"> Sign up</a>
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

