<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	require_once 'datos.php';
	
	$user = strtoupper(str_replace("'", "", $_POST["usuario"]));
	$pass = md5(str_replace("'", "", $_POST["password"]));
	
	$tabla = $config->cargarTabla("SELECT NumeUser, NombPers FROM {$config->tbLogin} WHERE NumeEsta = 1 AND UPPER(NombUser) = '{$user}' AND NombPass = '{$pass}'");
	
	$strSalida = "";
	
	if ($tabla)
	{
		session_start();
		$fila = $tabla->fetch_array();
		$_SESSION['is_logged_in'] = 1;
		$_SESSION['NumeUser'] = $fila['NumeUser'];
		$_SESSION['NombUsua'] = $fila['NombPers'];
		$_SESSION['DarkTheme'] = $_POST['theme'];
	
		$tabla->free();
	}
	else {
		//Error
		if ($_POST["returnUrl"] == "-1") {
			echo "ERROR";
		}
		else {
			header("Location:../login.php?error=1");
			die();
		}
	}
}

if ($_POST["returnUrl"] == "-1") {
	echo "Ok";
}
else if ($_POST["returnUrl"] == "")
	header("Location:../");
else
	header("Location:".$_POST["returnUrl"]);
//die();

?>
