<?php 
	require_once 'datosdb.php';	
	require_once 'vectorForms.php';
	
	//Variables
	$crlf = "\n";
	
	//Datos de configuracion iniciales
	$config = new VectorForms($dbhost, $dbschema, $dbuser, $dbpass, $raiz, "Departamento de Cobranzas y Servicios", "img/logo.png", true);
	
	$_SESSION['imgCKEditor'] = '/VectorForms/admin/ckeditor/imgup';

	/**
	 * Items de menu adicionales 
	 */
	 /*
	$config->menuItems = ["InformeDiario" => array(
			"NumeCarg" => 1,
			"Titulo" => "Movimientos del día",
			"Icono" => "fa-tachometer",
			"Url" => "informeDiario.php"
		)
	];
	*/

	/**
	 * TABLAS
	 */
	
	/**
	 * USUARIOS
	 */
	$tabla = new Tabla("usuarios", "Usuarios", "el Usuario", true, "objeto/usuarios", "fa-users");
	$tabla->labelField = "NombPers";
	$tabla->numeCarg = 1;
	
	//Campos
	$tabla->addField("NumeUser", "number", 0, "Número", false, true, true);
	$tabla->addField("NombPers", "text", 200, "Nombre Completo");
	$tabla->addField("NombUser", "text", 0, "Usuario");
	$tabla->fields['NombUser']['classControl'] = "ucase";

	$tabla->addField("NombPass", "password", 0, "Contraseña", true, false, false, false);
	$tabla->fields["NombPass"]['isMD5'] = true;
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["usuarios"] = $tabla;
	$tabla = null;
	
?>