<?
	function clean($value) 
	{
	    $value = trim($value);
	    $value = stripslashes($value);
	    $value = strip_tags($value);
	    $value = htmlspecialchars($value);	    
	    return $value;
	}

	function check_length($value, $min, $max) 
	{
	    $result = (mb_strlen($value) < $min || mb_strlen($value) > $max);
	    return !$result;
	}
?>