<?php
$page_title = 'Edit a User';
echo '<h1>Edit a User</h1>';

require ('mysqli_connect.php'); // Connect to the db.
$id = 0; // Initialize $id
if ((isset($_GET['id']) && is_numeric($_GET['id']))) { // From Admin_Page.php
    $id = $_GET['id'];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) { // Form submission.
    $id = $_POST['id'];
} else {
    echo '<p class="error">This page has been accessed in error.</p>';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = array();
    
    if (empty($_POST['name'])) {
        $errors[] = 'You forgot to enter the name.';
    } else {
        $name = mysqli_real_escape_string($dbc, trim($_POST['name']));
    }
    
    if (empty($_POST['email'])) {
        $errors[] = 'You forgot to enter the email address.';
    } else {
        $email = mysqli_real_escape_string($dbc, trim($_POST['email']));
    }
    
    if (empty($_POST['phone'])) {
        $errors[] = 'You forgot to enter the phone number.';
    } else {
        $phone = mysqli_real_escape_string($dbc, trim($_POST['phone']));
    }
    
    if (empty($_POST['gender'])) {
        $errors[] = 'You forgot to enter your gender.';
    } else {
        $phone = mysqli_real_escape_string($dbc, trim($_POST['gender']));
    }
    
    if (empty($_POST['drivinng_experience'])) {
        $errors[] = 'You forgot to enter your driving experience.';
    } else {
        $phone = mysqli_real_escape_string($dbc, trim($_POST['drivinng_experience']));
    }
    
    if (empty($_POST['role'])) {
        $errors[] = 'You forgot to enter the role';
    } else {
        $phone = mysqli_real_escape_string($dbc, trim($_POST['role']));
    }
    
  
    
    if (empty($errors)) {
        $query = "SELECT id FROM user WHERE email='$email' AND id != $id";
        $result = mysqli_query($dbc, $query);
        
        if (mysqli_num_rows($result) == 0) {
            $query = "UPDATE user SET name='$name', email='$email', phone='$phone', gender='$gender', driving_experience='$driving_experience', role='$role' WHERE id=$id LIMIT 1";
            $result = mysqli_query($dbc, $query);
            if (mysqli_affected_rows($dbc) == 1) {
                echo '<p>The user has been edited.</p>';
            } else {
                echo '<p class="error">The user could not be edited due to a system error. We apologize for any inconvenience.</p>';
            }
        } else {
            echo '<p class="error">The email address has already been registered by another user.</p>';
        }
    } else {
        echo '<p class="error">The following error(s) occurred:<br />';
        foreach ($errors as $msg) {
            echo " - $msg<br />\n";
        }
        echo '</p><p>Please try again.</p>';
    }
}

$query = "SELECT name, email, phone, gender, driving_experience, role FROM user WHERE id=$id";
$result = mysqli_query($dbc, $query);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    
echo '<form action="Change_Data.php" method="post">
<p>Name: <input type="text" name="name" size="15" maxlength="15" value="' . $row['name'] . '" /></p>
<p>Email: <input type="text" name="email" size="20" maxlength="60" value="' . $row['email'] . '"  /></p>
<p>Phone: <input type="text" name="phone" size="20" maxlength="30" value="' . $row['phone'] . '"  /></p>
<p>Gender: <input type="text" name="gender" size="10" maxlength="10" value="' . $row['gender'] . '"  /></p>
<p>Driving Experience: <input type="text" name="driving_experience" size="20" maxlength="30" value="' . $row['driving_experience'] . '"  /></p>
<p>Role: <input type="text" name="role" size="15" maxlength="15" value="' . $row['role'] . '"  /></p>
<p><input type="submit" name="submit" value="Save Changes" /></p>
<input type="hidden" name="id" value="' . $id . '" />
</form>';
} else {
    echo '<p class="error">This page has been accessed in error.</p>';
}

mysqli_close($dbc);
?>
