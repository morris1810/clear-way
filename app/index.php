<?php
function get_status_by_state($state)
{
	require('mysqli_connect.php');

	$query_status = "SELECT traffic_jam AS status FROM post_data WHERE state = '" . $state . "' AND date = CURDATE()";
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
$state_status_table .=
	'<table class="dataTable">
		<tr class="dataHeaderRow">
			<th class="dataHeader">Location</th>
			<th class="dataHeader">Status</th>
			<th class="dataHeader"></th>
		</tr>';

foreach ($state_list as $state) {
	$status = get_status_by_state($state);
	$state_status_list[$state] = $status;
	$state_status_table .=
		'<tr class="dataContentRow">
			<td class="dataContent">' . $state . '</td>
			<td class="dataContent">' . $status . '</td>
			<td class="status status' . str_replace(' ', '', $status) . '"></td>
		</tr>';
}



$state_status_table .= '</table>';



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
		<button class="postBtn">
			+
		</button>
	</div>
	<!-- <div class="postPopUp">
		<form method="post" class="newPostForm">
			<label>Location</label>
			<input type="text" placeholder="Street(Optional)">
			<input type="text" placeholder="City*">
			<span class="row">
				<input type="text" placeholder="State*">
				<input type="text" placeholder="Postcode*">
			</span>
			<input type="text" placeholder="Country*">
			<label for="">Traffic Jam:</label>
			<div class="row radioConatiner">
				<input type="radio" name="trafficJam" id="trafficJamLow" value="low">
				<input type="radio" name="trafficJam" id="trafficJamMedium" value="medium">
				<input type="radio" name="trafficJam" id="trafficJamHigh" value="high">
				<label id="trafficJamLowLabel" for="trafficJamLow">Low</label>
				<label id="trafficJamMediumLabel" for="trafficJamMedium">Medium</label>
				<label id="trafficJamHighLabel" for="trafficJamHigh">High</label>
			</div>
			<label>Date</label> <input type="text" readonly>
			<button type="submit">Post</button>
		</form>
	</div> -->
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
				lat: 3.952412936963896,
				lng: 109.43101984204212
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
					scale: 12,
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