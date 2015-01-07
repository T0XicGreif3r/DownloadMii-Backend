<?php
	//DownloadMii (API) v0.1-///
	/*Documentation
		This api.php is for parse the request URL and get the data from the db

		The URL request will be formated like this:
		To retrieve JSON
		App list by developer
		<domain>/api/bydev/[developerId]

		App list by category/sub/other
		<domain>/api/apps/[category]/[subcategory]/[othercategory]

		To rate an APP
		<domain>/api/rate/[securetoken]/[appguid]/[rating]
		
		To get main banner
		<domain>/api/banner
		
		To get APP banner
		<domain>/api/banner/[appguid]
		
		To get a list of all the categories
		<domain>/api/categories
		
		To get a list of subcategories
		<domain>/api/categories/[category_name]
		
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
	
	$mysqlConn = connectToDatabase();
	$topLevelRequest = $param[0];
	
	//TODO: Error check
	switch ($topLevelRequest) {
		case 'apps':
			sendResponseCodeAndExitIfTrue(count($param) < 2, 400);
			$secondLevelRequest = $param[1];
			
			//Base query
			$mysqlQuery = 'SELECT app.*, user.nick AS publisher, appver.number AS version, appver.3dsx AS 3dsx, appver.smdh AS smdh, maincat.name AS category, subcat.name AS subcategory, othercat.name AS othercategory FROM apps app
							LEFT JOIN users user ON user.userId = app.publisher
							LEFT JOIN appversions appver ON appver.versionId = app.version
							LEFT JOIN categories maincat ON maincat.categoryId = app.category
							LEFT JOIN categories subcat ON subcat.categoryId = app.subcategory
							LEFT JOIN categories othercat ON othercat.categoryId = app.othercategory WHERE app.publishstate = 1';
			
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
					print(getJSONFromSQLQuery($mysqlConn, $mysqlQuery, 'Apps'));
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
						
							if (count($param) > 4) {
								$bindParamTypes .= 's';
								array_push($bindParamArgs, $param[4]);
								$mysqlQueryEnd .= ' AND othercat.name = ?';
							}
						}
						
						$mysqlQuery .= $mysqlQueryEnd;
					}
					
					print(getJSONFromSQLQuery($mysqlConn, $mysqlQuery, 'Apps', $bindParamTypes, $bindParamArgs));
					break;
			}
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
				switch ($secondLevelRequest) {
					case 'version':
						echo "0.0.0.0"; //Example, todo: get 'version' from 'DownloadMii'
						break;
					case 'data':
						echo "todo";
						break;
					case 'default':
						echo "Error: incorrect use of API!";
						break;
				}
			}
			else{
				echo "Error: incorrect use of API!";
			}
		default:
			echo "Error: incorrect use of API!";
	}
	$mysqlConn->close();
?>
