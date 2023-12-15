<?php

$display_msg = "";

if (isset($_GET['purpose']) && isset($_GET['key'])) {
    if ($_GET['purpose'] == "register") {

        if (isset($_GET['email']) && isset($_GET['password'])) {

            require('mysqli_connect.php');

            // Register the user in the database...
            $email = $_GET['email'];
            $password = $_GET['password'];

            $query_user = "SELECT * FROM user WHERE email = '" . $email . "'";
            $query_result = mysqli_query($dbc, $query_user);

            if ($query_result && mysqli_num_rows($query_result) == 1) {
                //If user already exist.
                $display_msg .= "Account has been registered. <br><a class='loginBtn' href='login.php'>Login now</a>";
            } else {

                $hashed_password = sha1($password);

                $query = "INSERT INTO user (email, password) VALUES ('$email', '$hashed_password')";

                $result = @mysqli_query($dbc, $query);
                if ($result) {
                    $display_msg .= "Account register SUCCESSFUL. <br><a class='loginBtn' href='login.php'>Login now</a>";
                } else {
                    $display_msg .= "Accounnt register FAILED. <br> please contact admin(syehrran@gmail.com) to register an account.";
                }
            }

            // Close the database connection if it's still open.
            if (isset($dbc) && is_resource($dbc)) {
                mysqli_close($dbc);
            }
        } else {
            //Redirect to app home page.
            header("Location: index.php");
        }
    } elseif ($_GET['purpose'] == "reset-password") {
        if (isset($_GET['email']) && isset($_GET['password'])) {

            require('mysqli_connect.php');

            // Register the user in the database...
            $email = $_GET['email'];
            $password = $_GET['password'];

            $hashed_password = sha1($password);
            $query = "UPDATE user SET password='$hashed_password' WHERE email='$email' LIMIT 1";

            $result = @mysqli_query($dbc, $query);
            if ($result) {
                // Redirect the user to the profile page:
                $display_msg .= "Reset password SUCCESSFUL. <br><a class='loginBtn' href='login.php'>Login now</a>";
            } else {
                $display_msg .= "Reset password FAILED. <br> please contact admin(syehrran@gmail.com) to reset your password.";
            }

            // Close the database connection if it's still open.
            if (isset($dbc) && is_resource($dbc)) {
                mysqli_close($dbc);
            }
        } else {
            //Redirect to app home page.
            header("Location: index.php");
        }
    } else {
        //Redirect to app home page.
        header("Location: index.php");
    }
} else {
    //Redirect to app home page.
    header("Location: index.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification</title>
    <link rel="icon" type="image/x-icon" href="../assets/imgs/logo.png">

    <!-- CSS Styling -->
    <link rel="stylesheet" href="../assets/style/verification.css">
</head>

<body>
    <img class="bgLogoImg" src="../assets/imgs/logo.png" alt="">
    <!-- ================================
     Navigation Bar 
    ================================= -->
    <header>
        <div class="leftContainer">
            <img src="../assets/imgs/logo.png" alt="logo image" class="logo">
            <h1 class="companyName">ClearWay</h1>
        </div>
        <div class="rightContainer">
            <nav class="navBar">
                <button class="switchDisplayModeBtn"></button>
            </nav>
        </div>
    </header>
    <main>
        <div class="displayMsgContainer">
            <?php echo "<p class='normalText'>Result: " . $display_msg . "</p>"; ?></p>
        </div>
    </main>
    <script src="../assets/script/displayMode.js"></script>
</body>

</html>