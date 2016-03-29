<?php
	class Functions
	{
		public static function secure($var, $level=1)
		{
			$var = htmlspecialchars($var, ENT_QUOTES, "UTF-8");
			if($level<1) $var = mysqli_real_escape_string($var);
			if($level<2) $var = addslashes($var);
			return $var;
		}
		
		public static function testDb($host, $login, $pass, $db=null)
		{
			try
			{
				$db = new PDO('mysql:host='.$host.';dbname='.$db, $login, $pass);
				$db->query('SET NAMES UTF8');
			}
			catch (Exception $e)
			{
				return false;
			}
			return true;
		}
	}
?>