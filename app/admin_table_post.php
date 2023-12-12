<?php


// Check if the user is trying to log out.
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    $_SESSION = array(); // Clear the session array.

    if (isset($_COOKIE[session_name()])) { // If the session cookie exists, delete it.
        setcookie(session_name(), '', time() - 3600); // Set the cookie to expire in the past.
    }

    session_destroy(); // Destroy the session data on the server.
    header('Location: login.php'); // Redirect to the login page.
    exit();
}

session_start();
$page_title = 'View the Current Posts';

require('mysqli_connect.php');



$query = "SELECT COUNT(post_id) FROM post_data";
$result = mysqli_query($dbc, $query);
$row = mysqli_fetch_array($result, MYSQLI_NUM);
$records = $row[0];

// Calculate the number of pages
$display = 10; // Number of records to show per page
$pages = ($records > $display) ? ceil($records / $display) : 1;

$start = (isset($_GET['s']) && is_numeric($_GET['s'])) ? $_GET['s'] : 0;

// sorting order of `post_data` table
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'dt_desc';
switch ($sort) {
    case 'dt_asc':
        $order_by = 'date ASC';
        $next_sort = 'dt_desc';
        break;
    case 'dt_desc':
        $order_by = 'date DESC';
        $next_sort = 'dt_asc';
        break;
    default:
        $order_by = 'date DESC';
        $next_sort = 'dt_asc';
        break;
}

// Query to retrieve all posts, adjust fields as per your `post_data` table
$query = "SELECT post_id, user_email, street, city, state, postcode, country, traffic_jam, date FROM post_data ORDER BY $order_by LIMIT $start, $display";
$result = mysqli_query($dbc, $query);



$user_table = '';
$user_table .=
    '<table class="dataTable">
		<tr class="dataHeaderRow">
			<th class="dataHeader">Email</th>
			<th class="dataHeader">Street</th>
			<th class="dataHeader">City</th>
			<th class="dataHeader">State</th>
			<th class="dataHeader">Postcode</th>
			<th class="dataHeader">Country</th>
			<th class="dataHeader">Traffic</th>
			<th class="dataHeader">Date</th>
		</tr>';

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $user_table .= '
    <tr class="dataContentRow">

        <td class="dataContent">' . $row['user_email'] . '</td>
        <td class="dataContent longContent">' . $row['street'] . '</td>
        <td class="dataContent">' . $row['city'] . '</td>
        <td class="dataContent">' . $row['state'] . '</td>
        <td class="dataContent">' . $row['postcode'] . '</td>
        <td class="dataContent">' . $row['country'] . '</td>
        <td class="dataContent">' . $row['traffic_jam'] . '</td>
        <td class="dataContent aBitLongContent">' . $row['date'] . '</td>
    </tr>';
}

$user_table .= '</table>';

mysqli_free_result($result);
mysqli_close($dbc);

$pagination = '';

if ($pages > 0) {
    $pagination .= '    <div class="pageContainer">';
    $current_page = ($start / $display) + 1;

    // Show previous button when next page is displayed
    if ($current_page != 1) {
        $pagination .=  '<a href="?s=' . ($start - $display) . '&p=' . $pages . '&sort=' . $sort . '"><-</a> ';
    }

    for ($i = 1; $i <= $pages; $i++) {
        if ($i != $current_page) {
            $pagination .=  '<a href="?s=' . (($display * ($i - 1))) . '&p=' . $pages . '&sort=' . $sort . '">' . $i . '</a> ';
        } else {
            $pagination .= '<p class="active">'. $i . ' </p>';
        }
    }

    // if its not the end of the page, then display next button:
    if ($current_page != $pages) {
        $pagination .=  '<a href="?s=' . ($start + $display) . '&p=' . $pages . '&sort=' . $sort . '">-></a>';
    }

    $pagination .= '</div>';
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Post Table</title>
    <link rel="icon" type="image/x-icon" href="../assets/imgs/logo.png">

    <!-- CSS Styling -->
    <link rel="stylesheet" href="../assets/style/adminTable.css">
</head>

<body>
    <img class="bgLogoImg" src="../assets/imgs/logo.png" alt="">
    <!-- ================================
     Navigation Bar 
    ================================= -->
    <div class="flexContainer">
        <button class="dropDownContainer">
            <h2 class="currentTable">Post</h2>
            <div class="optionContainer">
                <a href="admin_table_user.php" class="otherTable">User</a>
            </div>
        </button>
        <header>
            <div class="rightContainer">
                <nav class="navBar">
                    <button class="switchDisplayModeBtn"></button>
                    <a href="?action=logout" class="logoutBtn">
                        <img src="../assets/imgs/logout.png" alt="profile icon">
                    </a>
                </nav>
            </div>
        </header>
    </div>
    <main>
        <div class="tableContainer">
            <?php echo $user_table; ?>
        </div>
        <div class="bottomContainer">
            <span></span>
            <?php echo $pagination; ?>
            <div class="sortBtnContainer <?php echo $next_sort; ?>">
                <a href="?sort=<?php echo $next_sort; ?>" class="sortBtn">Sort by Date</a>
            </div>
        </div>
    </main>
    <script src="../assets/script/displayMode.js"></script>
</body>

</html>



<body>



</body>
</html>