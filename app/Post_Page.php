<?php
session_start();
$page_title = 'Traffic Report Submission';

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    require ('mysqli_connect.php'); 
    
    $errors = array(); 
    
    // Check and validate the street (optional):
    $street = !empty($_POST['street']) ? mysqli_real_escape_string($dbc, trim($_POST['street'])) : NULL;
    
    // Check for a city:
    if (empty($_POST['city'])) {
        $errors[] = 'You forgot to enter your city.';
    } else {
        $city = mysqli_real_escape_string($dbc, trim($_POST['city']));
    }
    
    // Check for a state:
    if (empty($_POST['state'])) {
        $errors[] = 'You forgot to enter your state.';
    } else {
        $state = mysqli_real_escape_string($dbc, trim($_POST['state']));
    }
    
    // Check for a postcode:
    if (empty($_POST['postcode'])) {
        $errors[] = 'You forgot to enter your postcode.';
    } else {
        $postcode = mysqli_real_escape_string($dbc, trim($_POST['postcode']));
    }
    
    // Check for a country:
    if (empty($_POST['country'])) {
        $errors[] = 'You forgot to enter your country.';
    } else {
        $country = mysqli_real_escape_string($dbc, trim($_POST['country']));
    }
    
    // Check for the traffic jam value:
    if (empty($_POST['traffic_jam'])) {
        $errors[] = 'You forgot to specify the traffic jam level.';
    } else {
        $traffic_jam = mysqli_real_escape_string($dbc, trim($_POST['traffic_jam']));
    }
    
    // Check for the date:
    if (empty($_POST['date'])) {
        $errors[] = 'You forgot to enter the date of the traffic report.';
    } else {
        $date = mysqli_real_escape_string($dbc, trim($_POST['date']));
    }
    
    if (empty($errors)) { 
        
        // Retrieve the email from the session
        $email = isset($_SESSION['email']) ? mysqli_real_escape_string($dbc, $_SESSION['email']) : null;
        
        // Create the query:
        $query = "INSERT INTO post_data (user_email, street, city, state, postcode, country, traffic_jam, date)
              VALUES ('$email', '$street', '$city', '$state', '$postcode', '$country', '$traffic_jam', '$date')";
        
        $result = mysqli_query($dbc, $query); 
        
        if ($result) { 
            // Success message or redirection
            echo '<p>Your traffic report has been submitted successfully.</p>';
        } else { // If it did not run OK.
            // Public message:
            echo '<h1>System Error</h1>
        <p class="error">Your traffic report could not be submitted due to a system error. We apologize for any inconvenience.</p>';
            
            // Debugging message:
            echo '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $query . '</p>';
        }
        
        mysqli_close($dbc); // Close the database connection.
        
    } else { // Report the errors.
        echo '<h1>Error!</h1>
    <p class="error">The following error(s) occurred:<br />';
        foreach ($errors as $msg) {
            echo " - $msg<br />\n";
        }
        echo '</p><p>Please try again.</p><p><br /></p>';
    }
    
    
} 
?>

<h1>Submit Traffic Report</h1>
<form action="Post_Data.php" method="post">
    <input type="hidden" name="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>">
    <p>Street: <input type="text" name="street" size="30" maxlength="60" value="<?php if (isset($_POST['street'])) echo $_POST['street']; ?>" /></p>
    <p>City: <input type="text" name="city" size="30" maxlength="40" value="<?php if (isset($_POST['city'])) echo $_POST['city']; ?>" /></p>
    <p>State: <input type="text" name="state" size="30" maxlength="40"  value="<?php if (isset($_POST['state'])) echo $_POST['state']; ?>" /></p>
    <p>Postcode: <input type="text" name="postcode" size="10" maxlength="20"  value="<?php if (isset($_POST['postcode'])) echo $_POST['postcode']; ?>" /></p>
    <p>Country: <input type="text" name="country" size="30" maxlength="40"  value="<?php if (isset($_POST['country'])) echo $_POST['country']; ?>" /></p>
    <p>Traffic Jam:
        <input type="radio" name="traffic_jam" value="light" <?php if (isset($_POST['traffic_jam']) && $_POST['traffic_jam'] == 'light') echo 'checked="checked"'; ?> /> Light
        <input type="radio" name="traffic_jam" value="medium" <?php if (isset($_POST['traffic_jam']) && $_POST['traffic_jam'] == 'medium') echo 'checked="checked"'; ?> /> Medium
        <input type="radio" name="traffic_jam" value="heavy" <?php if (isset($_POST['traffic_jam']) && $_POST['traffic_jam'] == 'heavy') echo 'checked="checked"'; ?> /> Heavy
    </p>
    <p>Date: <input type="date" name="date"  value="<?php if (isset($_POST['date'])) echo $_POST['date']; ?>" /></p>
    <p><input type="submit" name="submit" value="Post" /></p>
</form>
