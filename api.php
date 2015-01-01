<?php
	//DownloadMii (API) v0.1-///
	/*Documentation
		This api.php is for parse the request URL and get the data from the db

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
	
	function getJSONFromSQLQuery($conn, $sql, $name, $bindParamTypes = null, $bindParamVarsArr = null) {
		//TODO: Category ID -> name, Rating ID -> integer, add error checks
		
		$stmt = $conn->prepare($sql);
		if (isset($bindParamTypes, $bindParamVarsArr)) {
			$callUserArgs = $bindParamVarsArr;
			array_unshift($callUserArgs, $bindParamTypes);
			
			//Create references for call_user_func_array
			$callUserArgsRefs = array();
			foreach($callUserArgs as $key => $value) {
				$callUserArgsRefs[$key] = &$callUserArgs[$key];
			}
			
			call_user_func_array(array($stmt, 'bind_param'), $callUserArgsRefs); //Safe SQL binding
		}
		$stmt->execute(); //Perform query
		$mysqlResult = $stmt->get_result(); //Get results
		
		$arr = array();
		while ($mysqlRow = $mysqlResult->fetch_object()) {
			array_push($arr, $mysqlRow); //Push all rows to the array
		}
		
		$stmt->close();
		$jsonResultObj = (object)array($name => $arr); //Create an enclosing object
		return json_encode($jsonResultObj); //Return JSON
	}

	$origKey = ''; //Key to verify if the app that is accessing the API is valid
	$param = explode('/', strtok(getenv('REQUEST_URI'), '?'));
	$pad = 0; //nº of folders from the root where is located the api folder ej <domain>/<1st>/<2nd>/api

	//get POST parameters
	$appKey = !empty($_POST['appKey']) ? $_POST['appKey'] : null;

	//Syntax/security checks
	if (count($param) < 5 + $pad) {
		http_response_code(400);
		exit();
	}
	if ($origKey != $appKey) {
		http_response_code(403);
		exit();
	}
	
	$mysqlConn = new mysqli('localhost', 'root', '', 'dwnmii'); //TODO: Use consts from external php file instead of these strings
	$topLevelRequest = $param[2 + $pad];
	
	switch ($topLevelRequest) {
		case 'bydev':
			$mysqlQuery = 'SELECT app.* FROM apps app JOIN users usr ON usr.userId = app.creator WHERE usr.nick = ?'; //Select rows from apps table by queried developer
			print(getJSONFromSQLQuery($mysqlConn, $mysqlQuery, $param[3 + $pad], 's', [$param[3 + $pad]]));
			break;
		
		case 'apps':
			if (count($param) < 5 + $pad) {
				http_response_code(400);
				exit();
			}
			$secondLevelRequest = $param[3 + $pad];
			
			switch ($secondLevelRequest) {
				case 'TopDownloadedApps':
				case 'TopDownloadedGames':
					$mysqlQuery = 'SELECT app.* FROM apps app JOIN categories maincat ON maincat.categoryId = app.category'; //Select top 10 downloaded apps/games
					
					//Ask for only apps/games depending on request
					if ($secondLevelRequest == 'TopDownloadedApps') {
						$mysqlQuery .= ' WHERE maincat.name != "Games"';
					}
					else {
						$mysqlQuery .= ' WHERE maincat.name = "Games"';
					}
					
					$mysqlQuery .= ' ORDER BY app.downloads DESC LIMIT 10';
					print(getJSONFromSQLQuery($mysqlConn, $mysqlQuery, $secondLevelRequest));
					break;
					
				case 'StaffPicks':
					# code...
					break;
				
				case 'Applications':
					$mysqlQuery = 'SELECT app.* FROM apps app';
					$bindParamTypes = null;
					$bindParamArgs = null;
					
					//Category query appending
					if (count($param) > 5 + $pad) {
						$bindParamTypes = 's';
						$bindParamArgs = array($param[4 + $pad]);
						
						$mysqlQueryEnd = ' WHERE maincat.name = ?';
						$mysqlQuery .= ' JOIN categories maincat ON maincat.categoryId = app.category';
						
						if (count($param) > 6 + $pad) {
							$bindParamTypes .= 's';
							array_push($bindParamArgs, $param[5 + $pad]);
							
							$mysqlQueryEnd .= ' AND subcat.name = ?';
							$mysqlQuery .= ' JOIN categories subcat ON subcat.categoryId = app.subCategory';
						
							if (count($param) > 7 + $pad) {
								$bindParamTypes .= 's';
								array_push($bindParamArgs, $param[6 + $pad]);
								
								$mysqlQueryEnd .= ' AND othercat.name = ?';
								$mysqlQuery .= ' JOIN categories othercat ON othercat.categoryId = app.otherCategory';
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
			if (count($param) > 4) {
				//get the banner for the current application
			}
			else{
				//get the current main banner
			}
			break;
	}
	$mysqlConn->close();
?>
