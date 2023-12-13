<?php
    // TODO: Send reset password email. Email validation
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClearWay: Forget Password</title>
    <link rel="icon" type="image/x-icon" href="../assets/imgs/logo.png">

    <!-- CSS Styling -->
    <link rel="stylesheet" href="../assets/style/form.css">
</head>

<body>
    <header>
        <button class="switchDisplayModeBtn"></button>
    </header>
    <main>
        <form method="post">
            <h3>Please key in your email to reset password.</h3>
            <div class="inputContainer">
                <label for="email">Email:</label>
                <input required placeholder="example@gmail.com" type="text" name="email" id="email">
            </div>
            <button class="submitBtn" type="submit">SUBMIT</button>
            <div class="textContainer">
                <p>Already have an account? </p>
                <a href="login.php"> Login</a>
            </div>
        </form>    
    </main>
    <script src="../assets/script/displayMode.js"></script>
</body>

</html>