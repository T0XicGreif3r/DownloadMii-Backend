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

	$origKey = "" //Key to verify if the app that is accessing the API is valid
	$param = explode('/', strtok(getenv('REQUEST_URI'), '?'));
	$pad = 1; //nº of folders from the root where is located the api folder ej <domain>/<1st>/<2nd>/api

	//get POST parameters
	$appKey = !empty($_POST['appKey']) ? $_POST['appKey'] : null;

	if ($origKey == $appKey) {
		switch ($param[3 + $pad]) {
			case 'bydev':
				# code...
				break;
			
			case 'apps':
				switch ($param[4 + $pad]) {
					case 'TopDownloadedApps':
						# code...
						break;

					case 'TopDownloadedGames':
						# code..
						break;

					case 'StaffPicks':
						# code...
						break;

					case 'Applications':
						if (count($param) > 5) {
							//get from the applications for the selected categorys/sub categorys
						}
						else {
							//get all the applications ignoring categorys/sub categorys
						}
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
				if (/*Check secure Token*/) {
					# code...
				}
				else 
					#error invalid user.
				# code...
				break;
			
			case 'banner'
				if (count($param) > 4) {
					//get the banner for the current application
				}
				else{
					//get the current main banner
				}
				break;
		}
	}
	else
	{
		http_response_code(403);
		exit();
	}
?>
