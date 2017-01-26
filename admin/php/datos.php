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
	$config->menuItems = ["Salir" => array(
			"NumeCarg" => 1,
			"Titulo" => "Salir del sistema",
			"Icono" => "fa-sign-out",
			"Url" => "logout.php"
		)
	];


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
	$tabla->fields["PorcComi"]['isHiddenInList'] = true;

	$tabla->addField("ComiDecs", "number", 0, "Comisión (solo números)");
	$tabla->fields["ComiDecs"]['isHiddenInList'] = true;
	
	$tabla->addFieldFileImage("ImagEmpr", "image", 100, "Logo", 'imgEmpresas', false);	
	
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["empresas"] = $tabla;

	/**
	 * VENDEDORES
	 */
	$tabla = new Tabla("vendedores", "Vendedores", "el Vendedor", false, "objeto/vendedores", "fa-male");
	$tabla->labelField = "NombVend";
	$tabla->masterTable = "empresas";
	$tabla->masterFieldId = "NumeEmpr";
	$tabla->masterFieldName = "NombEmpr";

	$tabla->addField("NumeVend", "number", 0, "Número", false, true, true);
	$tabla->addField("NombVend", "text", 200, "Nombre");
	$tabla->addField("NumeEmpr", "select", 200, "Empresa", true, false, false, true, '', '', 'empresas', 'NumeEmpr', 'NombEmpr', '', 'NombEmpr');
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["vendedores"] = $tabla;

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


	/**
	 * CLIENTES
	 */
	$tabla = new Tabla("clientes", "Clientes", "el Cliente", true, "objeto/clientes", "fa-address-card");
	$tabla->labelField = "NombClie";

	$tabla->addField("NumeClie", "number", 0, "Numero", false, true, true);
	$tabla->addField("NombClie", "text", 200, "Nombre");
	$tabla->addField("NumeEmpr", "select", 200, "Empresa", true, false, false, true, '', '', 'empresas', 'NumeEmpr', 'NombEmpr', '', 'NombEmpr');
	$tabla->fields["NumeEmpr"]["isHiddenInList"] = true;

	$tabla->addField("NumeTele", "text", 100, "Teléfono");
	$tabla->fields["NumeTele"]["cssGroup"] = "form-group2";

	$tabla->addField("NumeCelu", "text", 100, "Celular");
	$tabla->fields["NumeCelu"]["cssGroup"] = "form-group2";

	$tabla->addField("MailClie", "email", 200, "E-mail");
	$tabla->addField("FechIngr", "date", 0, "Fecha ingreso");

	$tabla->addField("DireClie", "text", 200, "Dirección");
	$tabla->fields["DireClie"]["isHiddenInList"] = true;

	$tabla->addField("NombLoca", "text", 200, "Localidad");
	$tabla->fields["NombLoca"]["cssGroup"] = "form-group2";

	$tabla->addField("NumeProv", "select", 200, "Provincia", true, false, false, true, '', '', 'provincias', 'NumeProv', 'NombProv', '', 'NombProv');
	$tabla->fields["NumeProv"]["cssGroup"] = "form-group2";
	$tabla->fields["NumeProv"]["isHiddenInList"] = true;

	$tabla->addField("CodiPost", "text", 0, "Código postal");
	$tabla->fields["CodiPost"]["isHiddenInList"] = true;

	$tabla->addField("NumeVend", "select", 80, "Vendedor", true, false, false, true, '', '', 'vendedores', 'NumeVend', 'NombVend', '', 'NombVend');
	$tabla->fields["NumeVend"]["isHiddenInList"] = true;

	$tabla->addField("ObseClie", "textarea", 200, "Observaciones");
	$tabla->fields["ObseClie"]["isHiddenInList"] = true;

	$tabla->addField("NumeEstaClie", "select", 80, "Estado", true, false, false, true, '', '', 'estadosclientes', 'NumeEstaClie', 'NombEstaClie', '', 'NombEstaClie');

	$tabla->addField("ValoMovi", "number", 0, "Valor móvil");
	$tabla->fields["ValoMovi"]["cssGroup"] = "form-group2";

	$tabla->addField("FechEntr", "text", 0, "Fecha de entrega");
	$tabla->fields["FechEntr"]["cssGroup"] = "form-group2";
	$tabla->fields["FechEntr"]["isHiddenInList"] = true;

	$tabla->addField("ValoCuot", "number", 0, "Valor cuota");
	$tabla->fields["ValoCuot"]["cssGroup"] = "form-group2";

	$tabla->addField("CantCuot", "number", 0, "Cuotas restantes");
	$tabla->fields["CantCuot"]["cssGroup"] = "form-group2";

	$config->tablas["clientes"] = $tabla;

	/**
	 * ESTADOS CLIENTES
	 */
	$tabla = new Tabla("estadosclientes", "Estados de clientes", "el Estado", true, "objeto/estadosclientes", "fa-cogs");
	$tabla->labelField = "NombEstaClie";

	$tabla->addField("NumeEstaClie", "number", 0, "Numero", false, true, true);
	$tabla->addField("NombEstaClie", "text", 100, "Estado");
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["estadosclientes"] = $tabla;

	/**
	 * TIPOS DE PAGOS
	 */
	$tabla = new Tabla("tipospagos", "Formas de Pago", "la Forma de Pago", true, "objeto/tipospagos", "fa-credit-card");
	$tabla->labelField = "NombTipoPago";

	$tabla->addField("NumeTipoPago", "number", 0, "Número", false, true, true);
	$tabla->addField("NombTipoPago", "text", 100, "Nombre");
	$tabla->fields["NombTipoPago"]["cssControl"] = "ucase";

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["tipospagos"] = $tabla;

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

?>