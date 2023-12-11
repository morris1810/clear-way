<?php

session_start();
$page_title = 'View the Current Posts';

require('mysqli_connect.php');



$query = "SELECT COUNT(post_id) FROM post_data";
$result = mysqli_query($dbc, $query);
$row = mysqli_fetch_array($result, MYSQLI_NUM);
$records = $row[0];

// Calculate the number of pages
$display = 5; // Number of records to show per page
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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $page_title; ?></title>
   <style>
        body {
            font-family: sans-serif;
        }
        .dropdown {
            position: relative;
            display: inline-block;
        }
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        .dropdown-content a:hover {background-color: #f1f1f1;}
        .dropdown:hover .dropdown-content {display: block;}
        .dropbtn {
            background-color: #4CAF50;
            color: white;
            padding: 16px;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }
        .dropbtn:hover, .dropbtn:focus {
            background-color: #3e8e41;
        }
        table {
            width: 75%;
            margin-top: 20px;
            align: center;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {background-color: #f2f2f2;}
        tr:hover {background-color: #ddd;}
    </style>
</head>
</head>
<body>

<div class="dropdown">
    <button class="dropbtn">Posted Data</button>
    <div class="dropdown-content">
        <a href="Admin_Page.php">Accounts</a>
        <a href="post_table.php">Posted Data</a>
    </div>
</div>

<?php

// Begin the table
echo '<table align="center" cellspacing="0" cellpadding="5" width="75%">
<tr>
    <td align="left"><b>User Email</b></td>
    <td align="left"><b>Street</b></td>
    <td align="left"><b>City</b></td>
    <td align="left"><b>State</b></td>
    <td align="left"><b>Postcode</b></td>
    <td align="left"><b>Country</b></td>
    <td align="left"><b>Traffic Jam</b></td>
    <td align="left"><b>Date</b></td>
</tr>';
echo '<div style="position: fixed; bottom: 10px; right: 10px;">';
echo '<a href="Post_Table.php?sort=' . $next_sort . '"><button>Sort by Date</button></a>';
echo '</div>';

$bg = '#eeeeee';
// Fetch and display records from the `post_data` table
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
    echo '<tr bgcolor="' . $bg . '">
        <td align="left">' . $row['user_email'] . '</td>
        <td align="left">' . $row['street'] . '</td>
        <td align="left">' . $row['city'] . '</td>
        <td align="left">' . $row['state'] . '</td>
        <td align="left">' . $row['postcode'] . '</td>
        <td align="left">' . $row['country'] . '</td>
        <td align="left">' . $row['traffic_jam'] . '</td>
        <td align="left">' . $row['date'] . '</td>


    </tr>';
}

echo '</table>'; 
mysqli_free_result($result); 
mysqli_close($dbc);

// Pagination and sorting links:
if ($pages > 1) {
    echo '<br /><p>';
    $current_page = ($start/$display) + 1;
    
    // Show previous button when next page is displayed
    if ($current_page != 1) {
        echo '<a href="Admin_Page.php?s=' . ($start - $display) . '&p=' . $pages . '&sort=' . $sort . '">Previous</a> ';
    }
    
    for ($i = 1; $i <= $pages; $i++) {
        if ($i != $current_page) {
            echo '<a href="Admin_Page.php?s=' . (($display * ($i - 1))) . '&p=' . $pages . '&sort=' . $sort . '">' . $i . '</a> ';
        } else {
            echo $i . ' ';
        }
    }
    
    // if its not the end of the page, then display next button:
    if ($current_page != $pages) {
        echo '<a href="Admin_Page.php?s=' . ($start + $display) . '&p=' . $pages . '&sort=' . $sort . '">Next</a>';
    }
    
    echo '</p>'; 
}
?>

</body>
</html>