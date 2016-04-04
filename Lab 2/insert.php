<?	
	session_start();
	require_once "/navigation_and_head/head.html";
	require_once "/navigation_and_head/navigation.php";
	require_once "/navigation_and_head/foot.html";
	if ($_SESSION["dname"] != "") 
	{
		$name = $_SESSION["dname"];
		$alias_1 = $_SESSION["dalias_1"];
		$alias_2 = $_SESSION["dalias_2"];
		$alias_3 = $_SESSION["dalias_3"];
		$shelf_life = $_SESSION["dshelf_life"];
		$composition = $_SESSION["dcomposition"];
		$erorn = $_SESSION["erorn"];
		$_SESSION["dname"] = "";
		$_SESSION["dalias_1"] = "";
		$_SESSION["dalias_2"] = "";
		$_SESSION["dalias_3"] = "";
		$_SESSION["dshelf_life"] = "";
		$_SESSION["dcomposition"] = "";
		$_SESSION["erorn"] = "";
	}
	$key = $_SESSION["key"];
	require_once "insert.html";
?>