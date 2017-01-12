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
	 * EMPRESAS
	 */
	$tabla = new Tabla('empresas', 'Empresas', 'la Empresa', true, 'objeto/empresas', 'fa-building');
	$tabla->allowDelete = false;
	$tabla->labelField = 'NombEmpr';
	
	$tabla->addField("NumeEmpr", "number", 0, "Número", false, true, true);
	$tabla->addField("NombEmpr", "text", 200, "Nombre");
	$tabla->addField("NumeBanc", "select", 200, "Banco", true, false, false, false, '', '', 'bancos', 'NumeBanc', 'NombBanc', '', 'NombBanc');
	$tabla->addField("NumeCuen", "text", 200, "Nro Cuenta");
	$tabla->addField("PorcComi", "checkbox", 100, "Comision a porcentaje?");
	$tabla->addField("ComiDecs", "number", 0, "Comisión (solo números)");
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');
	
	$config->tablas["empresas"] = $tabla;
	$tabla = null;
	
	/**
	 * BANCOS
	 */
	$tabla = new Tabla("bancos", "Bancos", "el Banco", true, "objeto/bancos", "fa-bank");
	$tabla->labelField = "NombBanc";
	
	$tabla->addField("NumeBanc", "number", 0, "Número", false, true, true);
	$tabla->addField("NombBanc", "text", 200, "Nombre");
	$tabla->fields["NombBanc"]["cssControl"] = "ucase";
	
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');
	
	$config->tablas["bancos"] = $tabla;
	$tabla = null;
	
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
	$tabla->addField("NumeCarg", "select", 0, "Cargo", true, false, false, true, '', '', 'cargos', 'NumeCarg', 'NombCarg', '', 'NombCarg');
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["usuarios"] = $tabla;
	$tabla = null;
	
?>