<? 
	try {
		$host = "localhost";
		$dbname = "BD";
		$user = "postgres";
		$pass = "postgres";
	  	$DBH = new PDO("pgsql:host=$host;dbname=$dbname", $user, $pass);  
	}  
	catch(PDOException $e) { 
	    file_put_contents ("errorlist.txt", $e, FILE_APPEND);
	    $errorconect = 2;
	}
?>