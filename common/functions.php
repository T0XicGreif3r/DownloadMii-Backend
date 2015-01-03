<?php
	/*
		DownloadMii Internal Functions
	*/
	
	function generateRandomString() {
		return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', 24)), 0, 24);
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
	
	function connectToDatabase() {
		$config = include($_SERVER['DOCUMENT_ROOT'] . '\config.php');
		$mysqlConn = new mysqli($config['mysql_host'], $config['mysql_user'], $config['mysql_pass'], $config['mysql_db']); //Connect
		printAndExitIfTrue($mysqlConn->connect_errno, 'Error connecting to database.'); //Check for connection errors
		return $mysqlConn;
	}
	
	function executeSafeSQLQuery($conn, $sql, $bindParamTypes = null, $bindParamVarsArr = null, $returnStmt = false) {
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
		printAndExitIfTrue(!$stmt->execute(), 'Error executing database query.'); //Perform query
		
		if ($returnStmt) {
			return $stmt;
		}
		else {
			$stmt->close();
		}
	}
	
	function getArrayFromSQLQuery($conn, $sql, $bindParamTypes = null, $bindParamVarsArr = null) {
		$stmt = executeSafeSQLQuery($conn, $sql, $bindParamTypes, $bindParamVarsArr, true);
		$mysqlResult = $stmt->get_result(); //Get results
		
		$arr = array();
		while ($mysqlRow = $mysqlResult->fetch_assoc()) {
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
?>
