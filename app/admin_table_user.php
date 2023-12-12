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


require('mysqli_connect.php');

$display = 10;

// Determine how many pages there are...
if (isset($_GET['p']) && is_numeric($_GET['p'])) {
    $pages = $_GET['p'];
} else {
    $query = "SELECT COUNT(id) FROM user";
    $result = mysqli_query($dbc, $query);
    $row = mysqli_fetch_array($result, MYSQLI_NUM);
    $records = $row[0];
    // Calculate the number of pages...
    $pages = ($records > $display) ? ceil($records / $display) : 1;
}

$start = (isset($_GET['s']) && is_numeric($_GET['s'])) ? $_GET['s'] : 0;

// Determine the sorting order...
$sort = (isset($_GET['sort'])) ? $_GET['sort'] : 'de_asc';
switch ($sort) {
    case 'de_asc':
        $order_by = 'driving_experience ASC';
        $next_sort = 'de_desc';
        break;
    case 'de_desc':
        $order_by = 'driving_experience DESC';
        $next_sort = 'de_asc';
        break;
    default:
        $order_by = 'driving_experience ASC';
        $next_sort = 'de_desc';
        break;
}

$query = "SELECT id, name, email, phone, gender, driving_experience, role FROM user ORDER BY $order_by LIMIT $start, $display";
$result = mysqli_query($dbc, $query);


$user_table = '';
$user_table .=
    '<table class="dataTable">
		<tr class="dataHeaderRow">
			<th class="dataHeader">Edit</th>
			<th class="dataHeader">Delete</th>
			<th class="dataHeader">Name</th>
			<th class="dataHeader">Email</th>
			<th class="dataHeader">Phone</th>
			<th class="dataHeader">Gender</th>
			<th class="dataHeader">Driving Experience</th>
			<th class="dataHeader">Role</th>
		</tr>';

while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $user_table .= '
    <tr class="dataContentRow">
        <td class="dataContent"><a href="admin_edit_data.php?id=' . $row['id'] . '">
            <img src="../assets/imgs/edit.png" alt="edit">
        </a></td>
        <td class="dataContent"><a href="admin_delete_data.php?id=' . $row['id'] . '">
            <img src="../assets/imgs/delete.png" alt="delete">
        </a></td>
        <td class="dataContent">' . $row['name'] . '</td>
        <td class="dataContent">' . $row['email'] . '</td>
        <td class="dataContent">' . $row['phone'] . '</td>
        <td class="dataContent">' . $row['gender'] . '</td>
        <td class="dataContent">' . $row['driving_experience'] . ' years</td>
        <td class="dataContent">' . $row['role'] . '</td>
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
    <title>Admin: User Table</title>
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
            <h2 class="currentTable">User</h2>
            <div class="optionContainer">
                <a href="admin_table_post.php" class="otherTable">Post</a>
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
                <a href="?sort=<?php echo $next_sort; ?>" class="sortBtn">Sort by Driving Experience</a>
            </div>
        </div>
    </main>
    <script src="../assets/script/displayMode.js"></script>
</body>

</html>