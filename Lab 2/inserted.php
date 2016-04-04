<?	
	session_start();
	$key = $_SESSION["key"];
	if ((isset($_POST["done"])) && ($_POST["key"] == $key))
	{
		$_SESSION["erorn"] = "";
		require_once "/forms_and_control/control_form.php";
		$name = clean ($_POST["newname"]);
		$alias_1 = clean ($_POST["newalias_1"]);
		$alias_2 = clean ($_POST["newalias_2"]);
		$alias_3 = clean ($_POST["newalias_3"]);
		$shelf_life = clean ($_POST["newshelf"]);
		$composition = clean ($_POST["newcomposition"]);
		if(!empty($name) && !empty($shelf_life) && !empty($composition))
		{
			if (check_length($name, 1, 50) && check_length($alias_1, 0, 50) && check_length($alias_2, 0, 50) && check_length($alias_3, 0, 50) && check_length($shelf_life, 1, 3) && check_length($composition, 1, 255)) 
			{
				require_once "/navigation_and_head/conect.php";
				if ($errorconect != 2) 
				{
					try{
						$STHI = $DBH->prepare("SELECT COUNT(id) FROM drug WHERE name = ?");
						$STHI->bindParam(1, $name);
						$STHI->execute();
						$STHI->setFetchMode(PDO::FETCH_ASSOC);
						$row = $STHI->fetch();
						if ($row["count"] == 0)
						{
							$STHIN = $DBH->prepare("INSERT INTO drug (name, alias_1, alias_2, alias_3, shelf_life, composition) VALUES (?, ?, ?, ?, ?, ?)");
							$STHIN->bindParam(1, $name);
							$STHIN->bindParam(2, $alias_1); 
							$STHIN->bindParam(3, $alias_2); 
							$STHIN->bindParam(4, $alias_3);
							$STHIN->bindParam(5, $shelf_life);
							$STHIN->bindParam(6, $composition); 
							$STHIN->execute();
							$DBH = null;
							header("Location: http://bd.lab");							
						}
						else
						{
							$erorn = "Такое имя уже есть";
							$_SESSION["dname"] = $name;
							$_SESSION["dalias_1"] = $alias_1;
							$_SESSION["dalias_2"] = $alias_2;
							$_SESSION["dalias_3"] = $alias_3;
							$_SESSION["dshelf_life"] = $shelf_life;
							$_SESSION["dcomposition"] = $composition;
							$_SESSION["erorn"] = $erorn;
							header("Location: http://bd.lab/insert.php");
						}
						
					}
					catch (PDOException $e) 
					{
						$_SESSION["dname"] = $name;
						$_SESSION["dalias_1"] = $alias_1;
						$_SESSION["dalias_2"] = $alias_2;
						$_SESSION["dalias_3"] = $alias_3;
						$_SESSION["dshelf_life"] = $shelf_life;
						$_SESSION["dcomposition"] = $composition;
						$erorn = "Опаньки, что-то пошло не так";
						file_put_contents ("errorlist.txt", $e, FILE_APPEND);
						$_SESSION["erorn"] = $erorn;
						header("Location: http://bd.lab/insert.php");
					}
				}
				else
				{
					$_SESSION["dname"] = $name;
					$_SESSION["dalias_1"] = $alias_1;
					$_SESSION["dalias_2"] = $alias_2;
					$_SESSION["dalias_3"] = $alias_3;
					$_SESSION["dshelf_life"] = $shelf_life;
					$_SESSION["dcomposition"] = $composition;
					$erorn = "Опаньки, что-то пошло не так";
					$_SESSION["erorn"] = $erorn;
					header("Location: http://bd.lab/upd.php");
				}
				$DBH = null;
			}
			else 
			{
				$erorn = "Превышено кол-во символов";
				$_SESSION["erorn"] = $erorn;
				$_SESSION["dname"] = $name;
				$_SESSION["dalias_1"] = $alias_1;
				$_SESSION["dalias_2"] = $alias_2;
				$_SESSION["dalias_3"] = $alias_3;
				$_SESSION["dshelf_life"] = $shelf_life;
				$_SESSION["dcomposition"] = $composition;
				header("Location: http://bd.lab/insert.php");
				
			}	
		}
		else 
		{
			$erorn = "Вы ввели пустые значения";
			$_SESSION["erorn"] = $erorn;
			$_SESSION["dname"] = $name;
			$_SESSION["dalias_1"] = $alias_1;
			$_SESSION["dalias_2"] = $alias_2;
			$_SESSION["dalias_3"] = $alias_3;
			$_SESSION["dshelf_life"] = $shelf_life;
			$_SESSION["dcomposition"] = $composition;
			header("Location: http://bd.lab/insert.php");

		}		
	}
	else
	{
		$erorn = "Опаньки, тот ли вы человечек";
		$_SESSION["erorn"] = $erorn;
		$_SESSION["dname"] = $_POST["newname"];
		$_SESSION["dalias_1"] = $_POST["newalias_1"];
		$_SESSION["dalias_2"] = $_POST["newalias_2"];
		$_SESSION["dalias_3"] = $_POST["newalias_3"];
		$_SESSION["dshelf_life"] = $_POST["newshelf_life"];
		$_SESSION["dcomposition"] = $_POST["newcomposition"];
		header("Location: http://bd.lab/insert.php");
	}
?>