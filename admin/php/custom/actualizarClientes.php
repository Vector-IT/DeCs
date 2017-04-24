<?php
	ini_set("log_errors", 1);
	ini_set("error_log", "php-error.log");

	require_once '../datos.php';
	
	$strSQL = "SELECT NumeClie, NumeSoli FROM clientes";

	$clientes = $config->cargarTabla($strSQL);

	$tabla = $config->getTabla("clientes");

	while ($cliente = $clientes->fetch_assoc()) {
		$datos = [
			"NumeClie" => $cliente["NumeClie"],
			"NumeSoli" => $cliente["NumeSoli"]
		];

		$tabla->editar($datos);
	}

	echo "Fin";
?>