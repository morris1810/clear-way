<?php



require('mysqli_connect.php'); // Connect to the db.

$id = 0; // Initialize $id
$result_message = "";

if ((isset($_GET['id']) && is_numeric($_GET['id']))) { // From Admin_Page.php
    $id = $_GET['id'];
} elseif ((isset($_POST['id'])) && (is_numeric($_POST['id']))) { // Form submission.
    $id = $_POST['id'];
} else {
    echo '<p class="error">User not found.</p>';
    echo '<a href="admin_table_user.php">Go Back</a>';
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
        $gender = mysqli_real_escape_string($dbc, trim($_POST['gender']));
    }

    if (empty($_POST['driving_experience'])) {
        $errors[] = 'You forgot to enter your driving experience.';
    } else {
        $driving_experience = mysqli_real_escape_string($dbc, trim($_POST['driving_experience']));
    }

    if (empty($_POST['role'])) {
        $errors[] = 'You forgot to enter the role';
    } else {
        $role = mysqli_real_escape_string($dbc, trim($_POST['role']));
    }



    if (empty($errors)) {
        $query = "SELECT id FROM user WHERE email='$email' AND id != $id";
        $result = mysqli_query($dbc, $query);

        if (mysqli_num_rows($result) == 0) {
            $query = "UPDATE user SET name='$name', email='$email', phone='$phone', gender='$gender', driving_experience=$driving_experience, role='$role' WHERE id=$id LIMIT 1";
            $result = mysqli_query($dbc, $query);
            if (mysqli_affected_rows($dbc) == 1) {
                $result_message .= '<p style="color: var(--text-color); font-size: 1.2rem;">The user has been edited.</p>';
            } else {
                $result_message .= '<p style="color: var(--text-color); font-size: 1.2rem;">Try to assign different value to update data. </p>';
            }
        } else {
            $result_message .= '<p class="errorText">The email address has already been registered by another user.</p>';
        }
    } else {
        foreach ($errors as $msg) {
            $result_message .= '<p class="errorText">';
            $result_message .= $msg;
            $result_message .= '</p>';
        }
    }
}

$query = "SELECT name, email, phone, gender, driving_experience, role FROM user WHERE id=$id";
$result = mysqli_query($dbc, $query);


if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $name = $row['name'];
    $email = $row['email'];
    $phone = $row['phone'];
    $gender = $row['gender'];
    $driving_experience = $row['driving_experience'];
    $role = $row['role'];
} else {
    $result_message .= '<p class="errorText">User not found.</p>';
    $result_message .= '<a href="admin_table_user.php">Go Back</a>';
}

mysqli_close($dbc);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Editing</title>
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
        <form action="admin_edit_data.php" method="post">
            <div class="inputContainer">
                <label for="name">Name:</label>
                <input placeholder="Your Name" type="text" name="name" id="name" value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div class="inputContainer">
                <label for="name">Email:</label>
                <input required placeholder="example@gmail.com" type="text" name="email" id="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
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
                <input placeholder="0" type="text" name="driving_experience" id="driving_experience" value="<?php echo htmlspecialchars($driving_experience); ?>">
            </div>
            <div class="inputContainer">
                <label for="role">Role:</label>
                <input placeholder="user" type="text" name="role" id="role" value="<?php echo htmlspecialchars($role); ?>">
            </div>
            <br>
            <input type="hidden" name="id" value="<?php echo $id; ?>" />
            <button class="submitBtn" type="submit" name="submit">UPDATE</button>
            <a href="admin_table_user.php" class="cancelBtn">Cancel</a>
        </form>
    </main>
    <script src="../assets/script/displayMode.js"></script>
</body>

</html>