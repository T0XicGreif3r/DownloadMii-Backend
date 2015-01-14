<?php
	//DownloadMii (API) v0.1-///
	/*Documentation
		This api.php is for parse the request URL and get the data from the db

		The URL request will be formated like this:
		To retrieve JSON
		App list by developer
		<domain>/api/apps/ByDev/[developerId]

		Top 10 non-game app list
		<domain>/api/apps/TopDownloadedApps

		Top 10 game list
		<domain>/api/apps/TopDownloadedGames

		App list by category/sub/other
		<domain>/api/apps/Applications/[category]/[subcategory]

		To rate an APP (not implemented, won't work like this)
		<domain>/api/rate/[securetoken]/[appguid]/[rating]
		
		To get main banner (not implemented)
		<domain>/api/banner
		
		To get APP banner (not implemented)
		<domain>/api/banner/[appguid]
		
		To get a list of all the categories
		<domain>/api/categories
		
		To get a list of subcategories
		<domain>/api/categories/[category_name]
		
		To get 3dsx download URL for app
		<domain>/api/dl/3dsx/[appguid]
		
		To get smdh download URL for app
		<domain>/api/dl/smdh/[appguid]
		
		To get DownloadMii version string (eg "1.0.0.0")
		<domain>/api/dmii/version
		
		To get a list of apps that only includes the app with the name "DownloadMii"
		<domain>/api/dmii/data
		
		To get API version string (eg "1.0.0.0")
		<domain>/api/version
	*/
	
	require_once('common/user.php');
	require_once('common/functions.php');
	
	sendResponseCodeAndExitIfTrue(strpos(getenv('REQUEST_URI'), '/api/') != 0, 400);
	
	$origKey = ''; //Key to verify if the app that is accessing the API is valid
	$requestUri = strtok(getenv('REQUEST_URI'), '?');
	$param = explode('/', rtrim(substr($requestUri, strlen('/api/')), '/')); //All URL "directories" after /api/ -> array

	//get POST parameters
	$appKey = !empty($_POST['appKey']) ? $_POST['appKey'] : null;

	//Syntax/security checks
	sendResponseCodeAndExitIfTrue(count($param) < 1, 400);
	sendResponseCodeAndExitIfTrue($origKey != $appKey, 403);
	
	$topLevelRequest = $param[0];
			
	//Base app query
	$baseAppQuery = 'SELECT app.guid, app.name, app.publisher, app.version, app.description, app.category, app.subcategory, app.rating, app.downloads, user.nick AS publisher,
					appver.number AS version, maincat.name AS category, subcat.name AS subcategory, appver.largeIcon AS largeicon, appver.3dsx_md5 AS 3dsx_md5, appver.smdh_md5 AS smdh_md5 FROM apps app
					LEFT JOIN users user ON user.userId = app.publisher
					LEFT JOIN appversions appver ON appver.versionId = app.version
					LEFT JOIN categories maincat ON maincat.categoryId = app.category
					LEFT JOIN categories subcat ON subcat.categoryId = app.subcategory
					WHERE app.publishstate = 1';
	
	//TODO: Error check
	switch ($topLevelRequest) {
		case 'apps':
			sendResponseCodeAndExitIfTrue(count($param) < 2, 400);
			$secondLevelRequest = $param[1];
			
			$mysqlConn = connectToDatabase();
			$mysqlQuery = $baseAppQuery;
			
			switch ($secondLevelRequest) {
				case 'ByDev':
					$mysqlQuery .= ' AND user.nick = ?';
					print(getJSONFromSQLQuery($mysqlConn, $mysqlQuery, $param[2], 's', [$param[2]]));
					break;
				
				case 'TopDownloadedApps':
				case 'TopDownloadedGames':
					//Ask for only apps/games depending on request
					if ($secondLevelRequest == 'TopDownloadedApps') {
						$mysqlQuery .= ' AND maincat.name != "Games"';
					}
					else {
						$mysqlQuery .= ' AND maincat.name = "Games"';
					}
					
					$mysqlQuery .= ' ORDER BY app.downloads DESC LIMIT 10'; //Select top 10 downloaded apps/games
					$data = getJSONFromSQLQuery($mysqlConn, $mysqlQuery, 'Apps');
					header('Content-Length: '.strlen($data));
					print($data);
					break;
					
				case 'StaffPicks':
					# code...
					echo "Error: Not implemented!";
					break;
				
				case 'Applications':
					$bindParamTypes = null;
					$bindParamArgs = null;
					
					//Category query appending
					if (count($param) > 2) {
						$bindParamTypes = 's';
						$bindParamArgs = array($param[2]);
						$mysqlQueryEnd = ' AND maincat.name = ?';
						
						if (count($param) > 3) {
							$bindParamTypes .= 's';
							array_push($bindParamArgs, $param[3]);
							$mysqlQueryEnd .= ' AND subcat.name = ?';
						}
						
						$mysqlQuery .= $mysqlQueryEnd;
					}
					$data = getJSONFromSQLQuery($mysqlConn, $mysqlQuery, 'Apps', $bindParamTypes, $bindParamArgs);
					header('Content-Length: '.strlen($data));
					print($data);
					break;
			}
			$mysqlConn->close();
			break;
		
		case 'dl':
			if (count($param) > 2) {
				$mysqlConn = connectToDatabase();
				$secondLevelRequest = $param[1];
				$guid = $param[2];
				
				switch ($secondLevelRequest) {
					case '3dsx':
						//Select 3dsx field from current app version
						$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT appver.3dsx FROM appversions appver
																			LEFT JOIN apps app ON appver.versionId = app.version
																			WHERE app.guid = ? LIMIT 1', 's', [$guid]);
						
						printAndExitIfTrue(count($matchingApps) != 1, 'Invalid GUID.'); //Check if GUID is valid
						
						//Update download count if IP not downloaded app already
						$ipHash = md5($_SERVER['REMOTE_ADDR']);
						$matchingDownloadIPs = getArrayFromSQLQuery($mysqlConn, 'SELECT downloadId FROM downloads WHERE appGuid = ? AND ipHash = ? LIMIT 1', 'ss', [$guid, $ipHash]);
						if (count($matchingDownloadIPs) == 0) {
							executePreparedSQLQuery($mysqlConn, 'INSERT INTO downloads (appGuid, ipHash) VALUES (?, ?)', 'ss', [$guid, $ipHash]);
							executePreparedSQLQuery($mysqlConn, 'UPDATE apps SET downloads = downloads + 1 WHERE guid = ? LIMIT 1', 's', [$guid]);
						}
						
						//Redirect to file
						header('Content-Length: '.strlen($matchingApps[0]['3dsx']));
						echo $matchingApps[0]['3dsx'];
						break;
					
					case 'smdh':
						//Select smdh field from current app version
						$matchingApps = getArrayFromSQLQuery($mysqlConn, 'SELECT appver.smdh AS smdh FROM apps app
																			LEFT JOIN appversions appver ON appver.versionId = app.version
																			WHERE app.guid = ? LIMIT 1', 's', [$guid]);
						
						printAndExitIfTrue(count($matchingApps) != 1, 'Invalid GUID.'); //Check if GUID is valid
						
						//Redirect to file
						header('Content-Length: '.strlen($matchingApps[0]['smdh']));
						echo $matchingApps[0]['smdh'];
						break;
					
					default:
						echo 'Error: incorrect use of API!';
						break;
				}
				$mysqlConn->close();
			}
			else {
				echo 'Error: incorrect use of API!';
			}
			break;
		
		case 'categories':
			$mysqlQuery = 'SELECT cat.categoryId, cat.name FROM categories cat';
			$bindParamTypes = null;
			$bindParamArgs = null;
			
			if (count($param) > 1) {
				$maincat = $param[1];
				$bindParamTypes = 's';
				$bindParamArgs = array($maincat);
				$mysqlQuery .= ' LEFT JOIN categories parentcat ON cat.parent = parentcat.categoryId WHERE parentcat.name = ?';
			}
			else {
				$mysqlQuery .= ' WHERE cat.parent IS NULL';
			}
			
			$mysqlConn = connectToDatabase();
			$data = getJSONFromSQLQuery($mysqlConn, $mysqlQuery, count($param) < 2 ? 'Categories' : 'Subcategories', $bindParamTypes, $bindParamArgs);
			header('Content-Length: '.strlen($data));
			print($data);
			$mysqlConn->close();
			
			break;
		
		case 'rate':
			#if (/*Check secure Token*/) {
				# code...
			#}
			#else 
				#error invalid user.
			# code...
			break;
		
		case 'banner':
			if (count($param) > 1) {
				//get the banner for the current application
			}
			else{
				//get the current main banner
			}
			break;
		
		case 'dmii':
			if (count($param) > 1) {
				$secondLevelRequest = $param[1];
				switch ($secondLevelRequest) {
					case 'version':
						$mysqlConn = connectToDatabase();
						$mysqlQuery = 'SELECT appver.number AS version FROM apps app
										LEFT JOIN appversions appver ON appver.versionId = app.version
										WHERE app.publishstate = 1 AND app.guid = ? LIMIT 1';
						
						$matchingApps = getArrayFromSQLQuery($mysqlConn, $mysqlQuery, 's', [getConfigValue('downloadmii_app_guid')]);
						printAndExitIfTrue(count($matchingApps) !== 1, 'Invalid DownloadMii app GUID in config.');
						header('Content-Length: '.strlen($matchingApps[0]['version']));
						print($matchingApps[0]['version']);
						$mysqlConn->close();
						
						break;
					
					case 'data':
						$mysqlConn = connectToDatabase();
						$mysqlQuery = $baseAppQuery . ' AND app.guid = ? LIMIT 1';
						
						$data = getJSONFromSQLQuery($mysqlConn, $mysqlQuery, 'DownloadMii', 's', [getConfigValue('downloadmii_app_guid')]);
						header('Content-Length: '.strlen($data));
						print($data);
						$mysqlConn->close();
						
						break;
					
					default:
						echo 'Error: incorrect use of API!';
						break;
				}
			}
			else {
				echo 'Error: incorrect use of API!';
			}
			break;
		
		case 'version':
			echo '0.0.0.0';
			break;
		
		default:
			echo 'Error: incorrect use of API!';
	}
?>
