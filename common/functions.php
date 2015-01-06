<?php
	/*
		DownloadMii Internal Functions
	*/
	
	/**
	* Generate a random alphanumreric string
	*
	* @param int $len The desired length of the string to be returned
	* @return string The generated string
	*/
	function generateRandomString($len = 48) {
		$str = implode(range(0, 9)) . implode(range('A', 'Z')) . implode(range('a', 'z'));
		return substr(str_shuffle(str_repeat($str, ceil($len / strlen($str)))), 0, $len);
	}
	
	/**
	* Generate a random GUID
	*
	* @return string The generated GUID
	*/
	function generateGUID() {
		return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
	}

	/**
	* Evaluate a condition and, if it is true, exit with a HTTP response code
	*
	* @param bool $condition The condition to be evaluated
	* @param bool $responseCode The response code to return to the client
	*/
	function sendResponseCodeAndExitIfTrue($condition, $responseCode) {
		if ($condition) {
			http_response_code($responseCode);
			print($responseCode);
			exit();
		}
	}

	/**
	* Evaluate a condition and, if it is true, print a string and exit
	*
	* @param bool $condition The condition to be evaluated
	* @param bool $responseCode The string to return to the client
	*/
	function printAndExitIfTrue($condition, $str) {
		if ($condition) {
			print($str);
			exit();
		}
	}
	
	/**
	* Find out whether the client is logged in
	*
	* @return bool Whether the client is logged in
	*/
	function clientLoggedIn() {
		return isset($_SESSION['user_id'], $_SESSION['user_nick'], $_SESSION['user_role'], $_SESSION['user_token']);
	}
	
	/**
	* Get a value from the config.php file
	*
	* @param string $key The configuration key to get a value from
	* @return mixed The configuration value of the key
	*/
	function getConfigValue($key) {
		static $config;
		if (!isset($config)) {
			$config = require($_SERVER['DOCUMENT_ROOT'] . '\config.php'); //Load config if not loaded already
		}
		return $config[$key];
	}
	
	/**
	* Connect to the MySQL database through MySQLi
	*
	* @return mysqli The MySQLi connection object
	*/
	function connectToDatabase() {
		$mysqlConn = new mysqli(getConfigValue('mysql_host'), getConfigValue('mysql_user'), getConfigValue('mysql_pass'), getConfigValue('mysql_db')); //Connect
		printAndExitIfTrue($mysqlConn->connect_errno, 'Error connecting to database.'); //Check for connection errors
		return $mysqlConn;
	}
	
	/**
	* Execute an SQL query with prepared statements
	*
	* @param mysqli $conn The MySQLi connection to execute the query on
	* @param string $sql The SQL statement to be prepared
	* @param string $bindParamTypes A string that contains one or more characters that specify the types of the corresponding bind variables (corresponds to $types in mysqli_stmt::bind_param)
	* @param array $bindParamVarsArr An array of variables to bind to the SQL query (corresponds to $var1 in mysqli_stmt::bind_param, however an array here)
	* @param bool $returnStmt If true, return the mysqli_stmt object for the prepared statement, otherwise close it and return null
	* @return mixed If $returnStmt is true, the mysqli_stmt object for the prepared statement, otherwise null
	*/
	function executePreparedSQLQuery($conn, $sql, $bindParamTypes = null, $bindParamVarsArr = null, $returnStmt = false) {
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
			return null;
		}
	}
	
	/**
	* Get an array of rows from an SQL query with prepared statements
	*
	* @param mysqli $conn The MySQLi connection to execute the query on
	* @param string $sql The SQL statement to be prepared
	* @param string $bindParamTypes A string that contains one or more characters that specify the types of the corresponding bind variables (corresponds to $types in mysqli_stmt::bind_param)
	* @param array $bindParamVarsArr An array of variables to bind to the SQL query (corresponds to $var1 in mysqli_stmt::bind_param, however an array here)
	* @return array The returned rows from the SQL query
	*/
	function getArrayFromSQLQuery($conn, $sql, $bindParamTypes = null, $bindParamVarsArr = null) {
		$stmt = executePreparedSQLQuery($conn, $sql, $bindParamTypes, $bindParamVarsArr, true);
		$mysqlResult = $stmt->get_result(); //Get results
		
		$arr = array();
		while ($mysqlRow = $mysqlResult->fetch_assoc()) {
			array_push($arr, $mysqlRow); //Push all rows to the array
		}
		
		$stmt->close();
		return $arr;
	}
	
	/**
	* Get a JSON string of an array of rows from an SQL query with prepared statements
	*
	* @param mysqli $conn The MySQLi connection to execute the query on
	* @param string $sql The SQL statement to be prepared
	* @param string $name The desired name of the enclosing object around the rows
	* @param string $bindParamTypes A string that contains one or more characters that specify the types of the corresponding bind variables (corresponds to $types in mysqli_stmt::bind_param)
	* @param array $bindParamVarsArr An array of variables to bind to the SQL query (corresponds to $var1 in mysqli_stmt::bind_param, however an array here)
	* @return string The JSON string with the enclosing object
	*/
	function getJSONFromSQLQuery($conn, $sql, $name, $bindParamTypes = null, $bindParamVarsArr = null) {
		$arr = getArrayFromSQLQuery($conn, $sql, $bindParamTypes, $bindParamVarsArr);
		$jsonResultObj = (object)array($name => $arr); //Create an enclosing object
		return json_encode($jsonResultObj); //Return JSON
	}
?>
