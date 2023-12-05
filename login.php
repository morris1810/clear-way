<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ClearWay: Login</title>
    <link rel="icon" type="image/x-icon" href="assets/imgs/logo.png">

    <!-- CSS Styling -->
    <link rel="stylesheet" href="assets/style/form.css">
</head>

<body>
    <header>
        <button class="switchDisplayModeBtn"></button>
    </header>
    <main>
        <a href="index.html" target="_self" class="imgContainer">
            <img src="assets/imgs/logo.png" alt="logo icon">
        </a>

        <form method="post">
            <div class="inputContainer">
                <label for="email">Email:</label>
                <input required placeholder="example@gmail.com" type="text" name="email" id="email">
            </div>
            <div class="inputContainer">
                <label for="password">Password:</label>
                <input required placeholder="example12345" type="password" class="passwordInput" name="password" id="password">
            </div>
            <a class="forgotPasswordLink" href="forget-password.php">Forgot password?</a>
            <button class="submitBtn" type="submit">LOGIN</button>
            <div class="textContainer">
                <p>Dont have an account? </p>
                <a href="sign-up.php"> Sign up</a>
            </div>
        </form>
    <script src="assets/script/displayMode.js"></script>

    </main>
</body>

</html>