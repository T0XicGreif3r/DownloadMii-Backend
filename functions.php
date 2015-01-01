<?php
	/*
		DownloadMii Internal Functions
	*/
	
	function connectToDatabase() {
		$mysqlConnStr = getenv('MYSQLAZURECONNSTR_mysqlstring');
		$mysqlConnStrPairs = explode(';', $mysqlConnStr);
		$mysqlConnStrArr = array();
		
		//Pair strings -> array
		foreach ($mysqlConnStrPairs as $strPair) {
			$splitStrPair = explode('=', $strPair);
			$mysqlConnStrArr[$splitStrPair[0]] = $splitStrPair[1];
		}
		
		printAndExitIfTrue(!(isset($mysqlConnStrArr['Data Source'], $mysqlConnStrArr['User Id'], $mysqlConnStrArr['Password'], $mysqlConnStrArr['Database'])), 'Error getting database information.');
		$mysqlConn = new mysqli($mysqlConnStrArr['Data Source'], $mysqlConnStrArr['User Id'], $mysqlConnStrArr['Password'], $mysqlConnStrArr['Database']); //Connect
		printAndExitIfTrue($mysqlConn->connect_errno, 'Error connecting to database.'); //Check for connection errors
		return $mysqlConn;
	}
	
	function getArrayFromSQLQuery($conn, $sql, $bindParamTypes = null, $bindParamVarsArr = null) {
		$stmt = $conn->prepare($sql);
		if (isset($bindParamTypes, $bindParamVarsArr)) {
			$callUserArgs = $bindParamVarsArr;
			array_unshift($callUserArgs, $bindParamTypes);
			
			//Create references for call_user_func_array
			$callUserArgsRefs = array();
			foreach ($callUserArgs as $key => $value) {
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
		return $arr;
	}
	
	function getJSONFromSQLQuery($conn, $sql, $name, $bindParamTypes = null, $bindParamVarsArr = null) {
		$arr = getArrayFromSQLQuery($conn, $sql, $bindParamTypes, $bindParamVarsArr);
		$jsonResultObj = (object)array($name => $arr); //Create an enclosing object
		return json_encode($jsonResultObj); //Return JSON
	}

	function sendResponseCodeAndExitIfTrue($condition, $responseCode) {
		if ($condition) {
			http_response_code($responseCode);
			print($responseCode);
			exit();
		}
	}

	function printAndExitIfTrue($condition, $str) {
		if ($condition) {
			print($str);
			exit();
		}
	}
	
	function generateRandomString() {
		return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', 24)), 0, 24);
	}
?>