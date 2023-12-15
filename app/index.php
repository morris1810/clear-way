<?php

//================================
// Query Data
//================================

function get_status_by_state($state) {
	require('mysqli_connect.php');

	$query_status = "SELECT traffic_jam AS status FROM post_data WHERE state = '" . $state . "' ORDER BY date DESC LIMIT 1";
	$query_result = mysqli_query($dbc, $query_status);

	if ($query_result && mysqli_num_rows($query_result) > 0) {
		$status = mysqli_fetch_row($query_result)[0];
	} else {
		$status = "No Data Today";
	}

	mysqli_free_result($query_result);
	mysqli_close($dbc);

	return $status;
}

$state_list = array(
	"Johor",
	"Kedah",
	"Kelantan",
	"Melaka",
	"Negeri Sembilan",
	"Pahang",
	"Perak",
	"Perlis",
	"Penang",
	"Sabah",
	"Sarawak",
	"Selangor",
	"Terengganu"
);

$state_status_list = array();

$state_status_table = '';
$state_option = '';
$state_status_table .=
	'<table class="dataTable">
		<tr class="dataHeaderRow">
			<th class="dataHeader">Location</th>
			<th class="dataHeader">Status</th>
			<th class="dataHeader"></th>
		</tr>';

foreach ($state_list as $state) {
	//get all status for each state
	$status = get_status_by_state($state);
	$state_status_list[$state] = $status;
	$state_status_table .=
		'<tr class="dataContentRow">
			<td class="dataContent">' . $state . '</td>
			<td class="dataContent">' . $status . '</td>
			<td class="status status' . str_replace(' ', '', $status) . '"></td>
		</tr>';
	//generate the option with all state
	$state_option .= '<option value="' . $state . '">' . $state . '</option>';
}



$state_status_table .= '</table>';

//================================================================
// For pop up post data and handle post data form submission
//================================================================

session_start();

$post_data_pop_up_css = "display: none;";
if (isset($_GET['action']) && $_GET['action'] == 'post_data') {


	// Session timeout duration in seconds
	$timeout_duration = 1800;

	// Check if the last activity timestamp is set
	if (isset($_SESSION['last_activity'])) {
		// Calculate the session's age
		$session_age = time() - $_SESSION['last_activity'];

		// Check if the session has expired
		if ($session_age > $timeout_duration) {
			unset($_SESSION['user_id']);
			unset($_SESSION['email']);
			// Destroy the session and redirect to login page or a timeout page
			session_unset();
			session_destroy();
			header('Location: login.php?timeout=1');
			exit();
		}
	}


	// Check if the user is logged in by looking at session data.
	if (!isset($_SESSION['user_id'])) {
		header('Location: login.php');
		exit();
	}

	$post_data_pop_up_css = "display: auto;";
}


$error_message = '';

// Check for form submission:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	// Check if the user is logged in.
	if (!isset($_SESSION['user_id'])) {
		// Not logged in, redirect to login page.
		header('Location: login.php');
		exit();
	}

	require('mysqli_connect.php');

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

	if (!is_numeric($_POST['postcode'])) {
		$errors[] = 'Only numeric value for postcode.';
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
		$query = "INSERT INTO post_data (user_email, street, city, state, postcode, country, traffic_jam )
              VALUES ('$email', '$street', '$city', '$state', '$postcode', '$country', '$traffic_jam')";

		$result = mysqli_query($dbc, $query);

		if ($result) {
			// Success message or redirection
			$result_message =  'Your traffic report has been submitted successfully.';
		} else { // If it did not run OK.
			// Public message:
			$result_message = 'Something went wrong';

			// Debugging message:
			echo '<script> console.log("' . mysqli_error($dbc) . '\nQuery: ' . $query . '");</script>';
		}
		echo '<script>alert("' . $result_message . '")</script>';


		mysqli_close($dbc); // Close the database connection.
		header('Location: index.php');
	} else {
		// Report the errors.
		foreach ($errors as $e) {
			$error_message .= '<p class="errorText">' . $e . '</p>';
		}
		//scroll to bottom
		$error_message .= '<script>window.scrollTo(0, document.body.scrollHeight);</script>';
	}
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ClearWay: Home</title>
	<link rel="icon" type="image/x-icon" href="../assets/imgs/logo.png">

	<!-- CSS Styling -->
	<link rel="stylesheet" href="../assets/style/app.css">
	<link rel="stylesheet" href="../assets/style/popup.css">
</head>

<body>
	<!-- ================================
     Navigation Bar 
    ================================= -->
	<header>
		<a class="leftContainer" href="../index.html" target="_self">
			<img src="../assets/imgs/logo.png" alt="logo image" class="logo">
			<h1 class="companyName">ClearWay</h1>
		</a>
		<div class="rightContainer">
			<nav class="navBar">
				<button class="switchDisplayModeBtn"></button>
				<a href="profile.php" class="profileBtn">
					<img src="../assets/imgs/user.png" alt="profile icon">
				</a>
			</nav>
		</div>
	</header>
	<main>
		<div class="tableContainer">
			<?php echo $state_status_table ?>
		</div>
		<div id="map"></div>

	</main>
	<div class="btnContainer">
		<a href="?action=post_data" class="postBtn">
			+
		</a>
	</div>
	<div class="newPostPopUp" style="<?php echo $post_data_pop_up_css; ?>">
		<div class="displayMessageContainer">
			<?php
			echo $error_message;
			?>
		</div>
		<form method="post" class="newPostForm" action="index.php?action=post_data">
			<input type="hidden" name="email" value="<?php echo isset($_SESSION['email']) ? $_SESSION['email'] : ''; ?>">
			<a href="?action=none" class="closeBtn">
				<img src="../assets/imgs/close.png" alt="close button">
			</a>
			<label>Location:</label>
			<input name="street" type="text" placeholder="Street(Optional)" value="<?php if (isset($_POST['street'])) echo $_POST['street']; ?>">
			<input name="city" type="text" placeholder="City*" value="<?php if (isset($_POST['city'])) echo $_POST['city']; ?>">
			<span class="row">
				<!-- <input type="text" placeholder="State*"> -->
				<select id="state" name="state">
					<option disabled selected value="">State*</option>
					<?php
					if (isset($_POST['state'])) {
						echo '<option value="' . $_POST['state'] . '" selected>' . $_POST['state'] . '</option>';
					}

					echo $state_option;
					?>
				</select>
				<input name="postcode" type="text" placeholder="Postcode*" value="<?php if (isset($_POST['postcode'])) echo $_POST['postcode']; ?>">
			</span>
			<input name="country" type="text" placeholder="Country*" value="<?php if (isset($_POST['country'])) echo $_POST['country']; ?>">
			<label for="">Traffic Jam:</label>
			<div class="row radioContainer">
				<input type="radio" name="traffic_jam" id="trafficJamLow" value="light" <?php if (isset($_POST['traffic_jam']) && $_POST['traffic_jam'] == 'light') echo 'checked="checked"'; ?>>
				<label id="trafficJamLowLabel" for="trafficJamLow">Light</label>
				<input type="radio" name="traffic_jam" id="trafficJamMedium" value="medium" <?php if (isset($_POST['traffic_jam']) && $_POST['traffic_jam'] == 'medium') echo 'checked="checked"'; ?>>
				<label id="trafficJamMediumLabel" for="trafficJamMedium">Medium</label>
				<input type="radio" name="traffic_jam" id="trafficJamHigh" value="heavy" <?php if (isset($_POST['traffic_jam']) && $_POST['traffic_jam'] == 'heavy') echo 'checked="checked"'; ?>>
				<label id="trafficJamHighLabel" for="trafficJamHigh">Heavy</label>
			</div>
			<span class="row dateContainer">
				<label>Date:</label>
				<input class="readonly" name="date" type="text" readonly value="<?php echo date('Y-m-d H:i:s'); ?>">
				<p> <?php echo date('d-M-Y'); ?></p>
			</span>
			<button class="submitBtn" type="submit" name="submit">Post</button>
		</form>
	</div>
	<script>
		(g => {
			var h, a, k, p = "The Google Maps JavaScript API",
				c = "google",
				l = "importLibrary",
				q = "__ib__",
				m = document,
				b = window;
			b = b[c] || (b[c] = {});
			var d = b.maps || (b.maps = {}),
				r = new Set,
				e = new URLSearchParams,
				u = () => h || (h = new Promise(async (f, n) => {
					await (a = m.createElement("script"));
					e.set("libraries", [...r] + "");
					for (k in g) e.set(k.replace(/[A-Z]/g, t => "_" + t[0].toLowerCase()), g[k]);
					e.set("callback", c + ".maps." + q);
					a.src = `https://maps.${c}apis.com/maps/api/js?` + e;
					d[q] = f;
					a.onerror = () => h = n(Error(p + " could not load."));
					a.nonce = m.querySelector("script[nonce]")?.nonce || "";
					m.head.append(a)
				}));
			d[l] ? console.warn(p + " only loads once. Ignoring:", g) : d[l] = (f, ...n) => r.add(f) && u().then(() => d[l](f, ...n))
		})({
			key: "AIzaSyBhpDP_0rLSw1eS8_fr7JfHrcTicYtDFEQ",
			v: "weekly",
			// Use the 'v' parameter to indicate the version to use (weekly, beta, alpha, etc.).
			// Add other bootstrap parameters as needed, using camel case.
		});

		// Location
		const malaysia = {
			position: {
				lat: 4.0,
				lng: 108.5
			},
			label: "Malaysia"
		}
		const johor = {
			position: {
				lat: 1.4927,
				lng: 103.7414
			},
			label: "<?php echo $state_status_list['Johor']; ?>"
		};

		const kedah = {
			position: {
				lat: 6.1210,
				lng: 100.3600
			},
			label: "<?php echo $state_status_list['Kedah']; ?>"
		};

		const kelantan = {
			position: {
				lat: 6.1254,
				lng: 102.2386
			},
			label: "<?php echo $state_status_list['Kelantan']; ?>"
		};

		const melaka = {
			position: {
				lat: 2.1896,
				lng: 102.2501
			},
			label: "<?php echo $state_status_list['Melaka']; ?>"
		};

		const negeriSembilan = {
			position: {
				lat: 2.7297,
				lng: 101.9381
			},
			label: "<?php echo $state_status_list['Negeri Sembilan']; ?>"
		};

		const pahang = {
			position: {
				lat: 3.8077,
				lng: 103.3260
			},
			label: "<?php echo $state_status_list['Pahang']; ?>"
		};

		const perak = {
			position: {
				lat: 4.5975,
				lng: 101.0901
			},
			label: "<?php echo $state_status_list['Perak']; ?>"
		};

		const perlis = {
			position: {
				lat: 6.4414,
				lng: 100.1986
			},
			label: "<?php echo $state_status_list['Perlis']; ?>"
		};

		const penang = {
			position: {
				lat: 5.4141,
				lng: 100.3288
			},
			label: "<?php echo $state_status_list['Penang']; ?>"
		};

		const sabah = {
			position: {
				lat: 5.9804,
				lng: 116.0735
			},
			label: "<?php echo $state_status_list['Sabah']; ?>"
		};

		const sarawak = {
			position: {
				lat: 1.5533,
				lng: 110.3593
			},
			label: "<?php echo $state_status_list['Sarawak']; ?>"
		};

		const selangor = {
			position: {
				lat: 3.0738,
				lng: 101.5183
			},
			label: "<?php echo $state_status_list['Selangor']; ?>"
		};

		const terengganu = {
			position: {
				lat: 5.3302,
				lng: 103.1408
			},
			label: "<?php echo $state_status_list['Terengganu']; ?>"
		};

		const stateList = [
			johor,
			kedah,
			kelantan,
			melaka,
			negeriSembilan,
			pahang,
			perak,
			perlis,
			penang,
			sabah,
			sarawak,
			selangor,
			terengganu
		];

		let map;

		const shape = {
			coords: [1, 1, 1, 20, 18, 20, 18, 1],
			type: "poly",
		};

		async function initMap() {
			const {
				Map
			} = await google.maps.importLibrary("maps");

			map = new Map(document.getElementById("map"), {
				center: malaysia.position,
				zoom: 5.2,
				mapTypeControl: false

			});



			for (let i = 0; i < stateList.length; i++) {
				const state = stateList[i]

				let markerColor = "";
				switch (state.label) {
					case 'Heavy':
						markerColor = "#ff3a3a";
						break;
					case 'Medium':
						markerColor = "#fff53a";
						break;
					case 'Light':
						markerColor = "#51ff3a";
						break;
					default:
						markerColor = "#aaaaaa";
						break;
				}

				const svgMarker = {
					path: google.maps.SymbolPath.CIRCLE,
					fillColor: markerColor,
					fillOpacity: 1,
					strokeWeight: 2,
					strokeColor: "black",
					rotation: 0,
					scale: 8,
				};

				new google.maps.Marker({
					map,
					position: state.position,
					title: state.label,
					icon: svgMarker
				});
			}
		}

		initMap();
	</script>
	<script src="../assets/script/displayMode.js"></script>
</body>

</html>