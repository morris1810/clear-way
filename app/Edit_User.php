<?php
session_start(); 

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
            header('Location: Profile.php');
            exit();
        } else {
            echo '<p class="error">No changes were made.</p>';
        }
    } else { // Report the errors.
        echo '<p class="error">The following error(s) occurred:<br />';
        foreach ($errors as $msg) {
            echo " - $msg<br />\n";
        }
        echo '</p><p>Please try again.</p>';
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
    <title>Edit Profile</title>
    
</head>
<body>
    <h1>Edit Your Profile</h1>
    <form action="Edit_User.php" method="post">
        <p>Name: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" ></p>
        <p>Email Address: <input type="email" name="email" value="<?php echo htmlspecialchars($_SESSION['email']); ?>" readonly></p>
        <p>Phone: <input type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" ></p>
        <p>Gender: <input type="text" name="gender" value="<?php echo htmlspecialchars($gender); ?>" ></p>
        <p>Driving Experience: <input type="text" name="driving_experience" value="<?php echo htmlspecialchars($driving_experience); ?>" ></p>
        <p><input type="submit" name="submit" value="Save Changes"></p>
    </form>
</body>
</html>
