//TODO: Verify token in params
<?php
session_start();
require('mysqli_connect.php');

$errors = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
    $new_password = mysqli_real_escape_string($dbc, trim($_POST['new_password']));
    $confirm_password = mysqli_real_escape_string($dbc, trim($_POST['confirm_password']));
    
    // Check if the passwords match
    if ($new_password != $confirm_password) {
        $errors[] = 'Your new passwords do not match.';
    } elseif (strlen($new_password) < 8) { 
        $errors[] = 'Your password must be at least 8 characters long.';
    } else {
        
        // Check if the email exists in the database
        $query = "SELECT id FROM user WHERE email='$email'";
        $result = mysqli_query($dbc, $query);
        if (mysqli_num_rows($result) == 1) {
            
            // Email exists, proceed with updating the password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $query = "UPDATE user SET password='$hashed_password' WHERE email='$email'";
            $result = mysqli_query($dbc, $query);
            
            if ($result && mysqli_affected_rows($dbc) == 1) { 
                
                // Success message
                $_SESSION['password_updated'] = 'Your password has been updated.';
                header("Location: Login.php"); 
                exit();
            } else {
                $errors[] = 'Your password could not be updated due to a system error or invalid email address.';
            }
        } else {
            $errors[] = 'No account found with that email address.';
        }
    }
}

if (!empty($errors)) {
    echo '<h1>Error!</h1>
    <p class="error">The following error(s) occurred:<br />';
    foreach ($errors as $msg) {
        echo " - $msg<br />\n";
    }
    echo '</p><p>Please try again.</p>';
}
?>

<h1>Reset Password</h1>
<?php
if (isset($_SESSION['password_updated'])) {
    echo '<p class="success">' . $_SESSION['password_updated'] . '</p>';
    unset($_SESSION['password_updated']);
}
?>
<form action="reset_password.php" method="post">
    <p>Email Address: <input type="email" name="email" required></p>
    <p>New Password: <input type="password" name="new_password" required></p>
    <p>Confirm New Password: <input type="password" name="confirm_password" required></p>
    <p><input type="submit" name="submit" value="Reset Password"></p>
</form>
