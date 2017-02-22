<?php
	require_once '../datos.php';
	
	$strSQL = "SELECT NumeClie FROM clientes";

	$clientes = $config->cargarTabla($strSQL);

	$tabla = $config->getTabla("clientes");

	while ($cliente = $clientes->fetch_assoc()) {
		$datos = ["NumeClie" => $cliente["NumeClie"]];

		$tabla->editar($datos);
	}

?>