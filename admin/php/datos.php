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
	$tabla->jsFiles = ["admin/js/empresas.js"];
	$tabla->btnList = [
			["Vendedores", "verVendedores", "btn-default"]
	];
	
	$tabla->addField("NumeEmpr", "number", 0, "Número", false, true, true);
	$tabla->addField("NombEmpr", "text", 200, "Nombre");
	$tabla->addField("NumeBanc", "select", 200, "Banco", true, false, false, true, '', '', 'bancos', 'NumeBanc', 'NombBanc', '', 'NombBanc');
	$tabla->addField("NumeCuen", "text", 200, "Nro Cuenta");
	$tabla->addField("PorcComi", "checkbox", 100, "Comision a porcentaje?");
	$tabla->fields["PorcComi"]['showOnList'] = false;
	$tabla->addField("ComiDecs", "number", 0, "Comisión (solo números)");
	$tabla->fields["ComiDecs"]['showOnList'] = false;
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');
	
	$config->tablas["empresas"] = $tabla;
	$tabla = null;
	
	/**
	 * VENDEDORES
	 */
	$tabla = new Tabla("vendedores", "Vendedores", "el Vendedor", true, "objeto/vendedores", "fa-male");
	$tabla->labelField = "NombVend";
	$tabla->masterField = "NumeEmpr";
	
	$tabla->addField("NumeVend", "number", 0, "Número", false, true, true);
	$tabla->addField("NombVend", "text", 200, "Nombre");
	$tabla->addField("NumeEmpr", "select", 200, "Empresa", true, false, false, true, '', '', 'empresas', 'NumeEmpr', 'NombEmpr', '', 'NombEmpr');
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');
	
	$config->tablas["vendedores"] = $tabla;
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
	 * CLIENTES
	 */
	$tabla = new Tabla("clientes", "Clientes", "el Cliente", true, "objeto/clientes", "fa-address-card");
	$tabla->labelField = "NombClie";
	
	$tabla->addField("NumeClie", "number", 0, "Numero", false, true, true);
	$tabla->addField("NombClie", "text", 200, "Nombre");
	$tabla->addField("NumeEmpr", "select", 200, "Empresa", true, false, false, false, '', '', 'empresas', 'NumeEmpr', 'NombEmpr', '', 'NombEmpr');
	$tabla->addField("NumeTele", "text", 100, "Teléfono");
	$tabla->fields["NumeTele"]["cssGroup"] = "form-group2";
	
	$tabla->addField("NumeCelu", "text", 100, "Celular", true, false, false);
	$tabla->fields["NumeCelu"]["cssGroup"] = "form-group2";
	
	$tabla->addField("MailClie", "email", 200, "E-mail", true, false, false);
	$tabla->addField("FechIngr", "date", 0, "Fecha ingreso");
	/*
	$tabla->fields["FechIngr"]['formatDb'] = "DATE_FORMAT(FechIngr, '%d-%m-%Y %H:%i') FechIngr";
	$tabla->fields["FechIngr"]['mirrorField'] = 'hdnFechIngr';
	$tabla->fields["FechIngr"]['mirrorFormat'] = 'yyyy-mm-dd hh:ii';
	*/
	
	$tabla->addField("DireClie", "text", 200, "Dirección", true, false, false);
	$tabla->addField("NombLoca", "text", 200, "Localidad", true, false, false);
	$tabla->fields["NombLoca"]["cssGroup"] = "form-group2";
	
	$tabla->addField("NombProv", "select", 200, "Provincia", true, false, false, false, '', '', 'provincias', 'NumeProv', 'NombProv', '', 'NombProv');
	$tabla->fields["NombProv"]["cssGroup"] = "form-group2";
	
	$tabla->addField("CodiPost", "text", 0, "Código postal", true, false, false);
	$tabla->addField("NumeVend", "select", 200, "Vendedor", true, false, false, false, '', '', 'vendedores', 'NumeVend', 'NombVend', '', 'NombVend');
	$tabla->addField("ObseClie", "textarea", 200, "Observaciones", true, false, false);
	$tabla->addField("NumeEstaClie", "select", 80, "Estado", true, false, false, false, '', '', 'estadosclientes', 'NumeEstaClie', 'NombEstaClie', '', 'NombEstaClie');
	$tabla->addField("ValoMovi", "number", 0, "Valor móvil");
	$tabla->addField("FechEntr", "text", 100, "Fecha de entrega", true, false, false, false);
	
	$config->tablas["clientes"] = $tabla;
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