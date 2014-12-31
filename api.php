<?php
	//DownloadMii (API) v0.1-///
	/*Documentation
		This api.php is for parse the request URL and ger the data from the db

		The URL request will be formated like this:
		To retrieve JSON
		App list by developer
		<domain>/api/bydev/[developerId]

		App list by category/sub/other
		<domain>/api/apps/[category]/[subCategory]/[otherCategory]

		To rate an APP
		<domain>/api/rate/[securetoken]/[appguid]/[rating]
		
		To get banner
		<domain>/api/banner
		
		To get APP banner
		<domain>/api/banner/[appguid]

	*/
	
	function queryResultToJSON($conn, $sql, $name) {
		//TODO: Category ID -> name, Rating ID -> integer, add error checks
		
		$mysqlResult = $conn->query($sql); //Perform query
		$arr = array();
		
		while ($mysqlRow = $mysqlResult->fetch_object()) {
			array_push($arr, $mysqlRow); //Push all rows to the array
		}
		
		$mysqlResult->close();
		$jsonResultObj = (object)array($name => $arr); //Create an enclosing object
		return json_encode($jsonResultObj); //Return JSON
	}

	$origKey = ""; //Key to verify if the app that is accessing the API is valid
	$param = explode('/', strtok(getenv('REQUEST_URI'), '?'));
	$pad = -1; //nº of folders from the root where is located the api folder ej <domain>/<1st>/<2nd>/api

	//get POST parameters
	$appKey = !empty($_POST['appKey']) ? $_POST['appKey'] : null;

	if ($origKey == $appKey) {
		$mysqlConn = new mysqli('localhost', 'root', '', 'dwnmii'); //TODO: Use consts from external php file instead of these strings
		//TODO: Prevent SQL injection
		
		switch ($param[3 + $pad]) {
			case 'bydev':
				$mysqlQuery = 'SELECT app.* FROM apps app JOIN users usr ON usr.userId = app.creator WHERE usr.nick = "' . $param[4 + $pad] . '"'; //Select rows from apps table by queried developer
				print(queryResultToJSON($mysqlConn, $mysqlQuery, $param[4 + $pad]));
				break;
			
			case 'apps':
				switch ($param[4 + $pad]) {
					case 'TopDownloadedApps':
						$mysqlQuery = 'SELECT app.* FROM apps app ORDER BY downloads DESC LIMIT 10'; //Select top 10 downloaded apps
						print(queryResultToJSON($mysqlConn, $mysqlQuery, "TopDownloadedApps"));
						break;

					case 'TopDownloadedGames':
						# code..
						break;

					case 'StaffPicks':
						# code...
						break;

					case 'Applications':
						$mysqlQuery = 'SELECT app.* FROM apps app';
						
						//Category query appending
						if (count($param) > 5) {
							$mysqlQueryEnd = ' WHERE maincat.name = "' . $param[5 + $pad] . '"';
							$mysqlQuery .= ' JOIN categories maincat ON maincat.categoryId = app.category';
							
							if (count($param) > 6) {
								$mysqlQueryEnd .= ' AND subcat.name = "' . $param[6 + $pad] . '"';
								$mysqlQuery .= ' JOIN categories subcat ON subcat.categoryId = app.subCategory';
							
								if (count($param) > 7) {
									$mysqlQueryEnd .= ' AND othercat.name = "' . $param[7 + $pad] . '"';
									$mysqlQuery .= ' JOIN categories othercat ON othercat.categoryId = app.otherCategory';
								}
							}
							
							$mysqlQuery .= $mysqlQueryEnd;
						}
						
						print(queryResultToJSON($mysqlConn, $mysqlQuery, "Apps"));
						break;

					case 'Games':
						if (count($param) > 5) {
							//get from the games data for the selected categorys/sub categorys
						}
						else {
							//get all the games ignoring categorys/sub categorys
						}
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
				if (count($param) > 4) {
					//get the banner for the current application
				}
				else{
					//get the current main banner
				}
				break;
		}
		$mysqlConn->close();
	}
	else
	{
		http_response_code(403);
		exit();
	}
?>
