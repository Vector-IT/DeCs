<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	require_once 'datos.php';
	
	$user = strtoupper(str_replace("'", "", $_POST["usuario"]));
	$pass = md5(str_replace("'", "", $_POST["password"]));
	
	$tabla = $config->cargarTabla("SELECT NumeUser, NombPers FROM {$config->tbLogin} WHERE NumeEsta = 1 AND UPPER(NombUser) = '{$user}' AND NombPass = '{$pass}'");
	
	$strSalida = "";
	
	if ($tabla->num_rows == 1)
	{
		session_start();
		$fila = $tabla->fetch_array();
		$_SESSION['is_logged_in'] = 1;
		$_SESSION['NumeUser'] = $fila['NumeUser'];
		$_SESSION['NombUsua'] = $fila['NombPers'];
		if (isset($_POST['theme'])) {
			$_SESSION['DarkTheme'] = $_POST['theme'];
		}
	
		$tabla->free();
	}
	else {
		//Error
		header("Location:../login.php?returnUrl={$_POST["returnUrl"]}&error=1");
		die();
	}

	if ($_POST["returnUrl"] == "") {
		header("Location:../");
	}
	else {
		header("Location:".$_POST["returnUrl"]);
	}
}
?>
