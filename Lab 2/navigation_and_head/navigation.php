<?
  require "conect.php"; 
  if ($errorconect != 2) 
  {
    try
    {
      $STHuser = $DBH->prepare("SELECT id, surname FROM patient WHERE delete = false ORDER BY surname");
      $STHuser->execute();
      $STHuser->setFetchMode(PDO::FETCH_ASSOC); 
      }
    catch (PDOException $e) {
      $errorconect = 1;
      file_put_contents ("errorlist.txt", $e, FILE_APPEND);
    }
    if ($errorconect != 1) 
    {
      if (isset($_SESSION["surname"])) 
      {

        require_once "user.html";
      }
      require_once "navigation.html";
    }
    else
      require "errprconect.html";
  }
  else
    require "errorconect.html";
  $DBH = null;  
?>