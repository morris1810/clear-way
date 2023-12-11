<?php

$page_title = 'View the Current Users & Admin';

require ('mysqli_connect.php'); 

$display = 5;

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
<body>

<div class="dropdown">
    <button class="dropbtn">Accounts ^</button>
    <div class="dropdown-content">
        <a href="Admin_Page.php">Accounts</a>
        <a href="post_table.php">Posted Data</a>
    </div>
</div>


<table align="center" cellspacing="0" cellpadding="5" width="75%">
<tr>
    <td align="left"><b>Edit</b></td>
    <td align="left"><b>Delete</b></td>
    <td align="left"><b>Name</b></td>
    <td align="left"><b>Email</b></td>
    <td align="left"><b>Phone</b></td>
    <td align="left"><b>Gender</b></td>
    <td align="left"><b>Driving Experience</b></td>
    <td align="left"><b>Role</b></td>
</tr>

<?php

echo '<div style="position: fixed; bottom: 10px; right: 10px;">';
echo '<a href="Admin_Page.php?sort=' . $next_sort . '"><button>Sort by Driving Experience</button></a>';
echo '</div>';

$bg = '#eeeeee'; // Initialize $bg variable for row color alternation
while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
    $bg = ($bg=='#eeeeee' ? '#ffffff' : '#eeeeee');
    echo '<tr bgcolor="' . $bg . '">
        <td align="left"><a href="Change_Data.php?id='. $row['id'] . '">Edit</a></td>
        <td align="left"><a href="Delete_Data.php?id='. $row['id'] . '">Delete</a></td>
        <td align="left">' . $row['name'] . '</td>
        <td align="left">' . $row['email'] . '</td>
        <td align="left">' . $row['phone'] . '</td>
        <td align="left">' . $row['gender'] . '</td>
        <td align="left">' . $row['driving_experience'] . '</td>
        <td align="left">' . $row['role'] . '</td>
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
