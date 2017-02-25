<?php
	require_once 'datosdb.php';
	require_once 'vectorForms.php';

	require_once 'custom/clientes.php';
	require_once 'custom/cuotas.php';
	require_once 'custom/seguimientos.php';

	//Variables
	$crlf = "\n";

	//Datos de configuracion iniciales
	$config = new VectorForms($dbhost, $dbschema, $dbuser, $dbpass, $raiz, "Departamento de Cobranzas y Servicios", "img/logo.png", true);
	$config->cssFiles = ["admin/css/custom.css"];

	$_SESSION['imgCKEditor'] = '/VectorForms/admin/ckeditor/imgup';

	/**
	 * Items de menu adicionales
	 */
	$config->menuItems = [
			new MenuItem("Configuraciones", '', '', 'fa-cogs', 1, true, false),
			new MenuItem("Salir del Sistema", 'logout.php', '', 'fa-sign-out', '', false, false)
	];


	/**
	 * TABLAS
	 */

	/**
	 * USUARIOS
	 */
	$tabla = new Tabla("usuarios", "usuarios", "Usuarios", "el Usuario", true, "objeto/usuarios", "fa-users");
	$tabla->labelField = "NombPers";
	$tabla->numeCarg = 1;
	$tabla->isSubItem = true;

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

	/**
	 * BANCOS
	 */
	$tabla = new Tabla("bancos", "bancos", "Bancos", "el Banco", true, "objeto/bancos", "fa-bank");
	$tabla->labelField = "NombBanc";
	$tabla->isSubItem = true;
	
	$tabla->addField("NumeBanc", "number", 0, "Número", false, true, true);
	$tabla->addField("NombBanc", "text", 200, "Nombre");
	$tabla->fields["NombBanc"]["cssControl"] = "ucase";

	$tabla->addField("ComiBanc", "number", 0, "Porcentaje de Comisión");
	$tabla->fields["ComiBanc"]["step"] = "0.01";

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["bancos"] = $tabla;

	/**
	 * ESTADOS CLIENTES
	 */
	$tabla = new Tabla("estadosclientes", "estadosclientes", "Estados de clientes", "el Estado", true, "objeto/estadosclientes", "fa-cogs");
	$tabla->labelField = "NombEstaClie";
	$tabla->isSubItem = true;

	$tabla->addField("NumeEstaClie", "number", 0, "Numero", false, true, true);
	$tabla->addField("NombEstaClie", "text", 100, "Estado");
	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["estadosclientes"] = $tabla;

	/**
	 * TIPOS DE PAGOS
	 */
	$tabla = new Tabla("tipospagos", "tipospagos", "Formas de Pago", "la Forma de Pago", true, "objeto/tipospagos", "fa-credit-card");
	$tabla->labelField = "NombTipoPago";
	$tabla->isSubItem = true;

	$tabla->addField("NumeTipoPago", "number", 0, "Número", false, true, true);
	$tabla->addField("NombTipoPago", "text", 100, "Nombre");
	$tabla->fields["NombTipoPago"]["cssControl"] = "ucase";

	$tabla->addField("ComiTipoPago", "number", 0, "Porcentaje de Comisión");
	$tabla->fields["ComiTipoPago"]["step"] = "0.01";

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["tipospagos"] = $tabla;

	/**
	 * TIPOS DE CONTACTOS
	 */
	$tabla = new Tabla("tiposcontactos", "tiposcontactos", "Tipos de Contacto", "el Tipo de contacto", true, "objeto/tiposcontactos", "fa-headphones");
	$tabla->labelField = "NombTipoCont";
	$tabla->isSubItem = true;

	$tabla->addField("NumeTipoCont", "number", 0, "Número", false, true, true);
	$tabla->addField("NombTipoCont", "text", 200, "Nombre");
	$tabla->fields["NombTipoCont"]["cssControl"] = "ucase";

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["tiposcontactos"] = $tabla;

	/**
	 * TIPOS DE RESPUESTAS
	 */
	$tabla = new Tabla("tiposrespuestas", "tiposrespuestas", "Tipos de Repuesta", "el Tipo de respuesta", true, "objeto/tiposrespuestas", "fa-question-circle");
	$tabla->labelField = "NombTipoResp";
	$tabla->isSubItem = true;

	$tabla->addField("NumeTipoResp", "number", 0, "Número", false, true, true);
	$tabla->addField("NombTipoResp", "text", 200, "Nombre");
	$tabla->fields["NombTipoResp"]["cssControl"] = "ucase";

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["tiposrespuestas"] = $tabla;

	/**
	 * EMPRESAS
	 */
	$tabla = new Tabla('empresas', 'empresas', 'Empresas', 'la Empresa', true, 'objeto/empresas', 'fa-industry');
	$tabla->allowDelete = false;
	$tabla->labelField = 'NombEmpr';
	$tabla->jsFiles = ["admin/js/custom/empresas.js"];
	$tabla->btnList = [
			array('titulo'=>"Vendedores", 'onclick'=>"verVendedores", 'class'=>"btn-default")
	];

	$tabla->addField("NumeEmpr", "number", 0, "Número", false, true, true);
	$tabla->addField("NombEmpr", "text", 200, "Nombre");
	$tabla->addField("NumeBanc", "select", 200, "Banco", true, false, false, true, '', '', 'bancos', 'NumeBanc', 'NombBanc', '', 'NombBanc');
	$tabla->addField("NumeCuen", "text", 200, "Nro Cuenta");

	$tabla->addFieldFileImage("ImagEmpr", "image", 100, "Logo", 'imgEmpresas', false);

	$tabla->addField("ImpoAdmi", "number", 80, "Gastos administrativos");
	$tabla->fields["ImpoAdmi"]["cssGroup"] = "form-group2";
	$tabla->fields["ImpoAdmi"]["step"] = "0.01";

	$tabla->addField("PorcAdmi", "checkbox", 100, "Es porcentaje?", true, false, false, true, '1');
	$tabla->fields["PorcAdmi"]['isHiddenInList'] = true;
	$tabla->fields["PorcAdmi"]["cssGroup"] = "form-group2";

	$tabla->addField("ImpoGest", "number", 80, "Gestión de cobranza");
	$tabla->fields["ImpoGest"]["cssGroup"] = "form-group2";
	$tabla->fields["ImpoGest"]["step"] = "0.01";

	$tabla->addField("PorcGest", "checkbox", 100, "Es porcentaje?", true, false, false, true, '1');
	$tabla->fields["PorcGest"]['isHiddenInList'] = true;
	$tabla->fields["PorcGest"]["cssGroup"] = "form-group2";

	$tabla->addField("ImpoOtro", "number", 80, "Otros gastos");
	$tabla->fields["ImpoOtro"]["cssGroup"] = "form-group2";
	$tabla->fields["ImpoOtro"]["step"] = "0.01";

	$tabla->addField("PorcOtro", "checkbox", 100, "Es porcentaje?", true, false, false, true, '1');
	$tabla->fields["PorcOtro"]['isHiddenInList'] = true;
	$tabla->fields["PorcOtro"]["cssGroup"] = "form-group2";

	$tabla->addField("FechVenc1", "number", 0, "1er Vencimiento");
	$tabla->fields["FechVenc1"]['isHiddenInList'] = true;

	$tabla->addField("FechVenc2", "number", 0, "2do Vencimiento");
	$tabla->fields["FechVenc2"]["cssGroup"] = "form-group2";
	$tabla->fields["FechVenc2"]['isHiddenInList'] = true;

	$tabla->addField("FechVenc3", "number", 0, "3er Vencimiento");
	$tabla->fields["FechVenc3"]["cssGroup"] = "form-group2";
	$tabla->fields["FechVenc3"]['isHiddenInList'] = true;

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["empresas"] = $tabla;

	/**
	 * VENDEDORES
	 */
	$tabla = new Tabla("vendedores", "vendedores", "Vendedores", "el Vendedor", false, "objeto/vendedores", "fa-male");
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
	 * CLIENTES
	 */
	$tabla = new Clientes("clientes", "clientes", "Clientes", "el Cliente", true, "objeto/clientes", "fa-id-card-o");
	$tabla->labelField = "NombClie";
	$tabla->listarOnLoad = false;

	$tabla->searchFields = array("NumeSoli", "NombClie");

	$tabla->btnForm = [
			array('titulo'=>'<i class="fa fa-fw fa-money" aria-hidden="true"></i> Generar cuotas', 
					'onclick'=>"generarCuotas()", 
					'class'=>"btn-success")
	];

	$tabla->btnList = [
			array("titulo"=> 'Ver cuotas',
					"onclick"=> "verCuotas",
					"class"=> "btn-default"),
			array("titulo"=> "Crear Seguimiento",
					"onclick"=>"crearSeguimiento",
					"class"=>"btn-default")
	];

	$tabla->jsFiles = ['admin/js/custom/clientes.js'];

	$tabla->addField("NumeClie", "number", 0, "Numero", false, true, true);
	$tabla->fields["NumeClie"]["isHiddenInForm"] = true;
	$tabla->fields["NumeClie"]["isHiddenInList"] = true;

	$tabla->addField("NumeSoli", "number", 0, "Nro Solicitud");

	$tabla->addField("NombClie", "text", 200, "Nombre");
	$tabla->addField("NumeEmpr", "select", 200, "Empresa", true, false, false, true, '', '', 'empresas', 'NumeEmpr', 'NombEmpr', '', 'NombEmpr');
	$tabla->fields["NumeEmpr"]["isHiddenInList"] = true;

	$tabla->addField("NumeTele", "text", 100, "Teléfono", false);
	$tabla->fields["NumeTele"]["cssGroup"] = "form-group2";

	$tabla->addField("NumeCelu", "text", 100, "Celular", false);
	$tabla->fields["NumeCelu"]["cssGroup"] = "form-group2";

	$tabla->addField("MailClie", "email", 200, "E-mail", false);
	$tabla->addField("FechIngr", "date", 0, "Fecha ingreso");

	$tabla->addField("DireClie", "text", 200, "Dirección");
	$tabla->fields["DireClie"]["cssGroup"] = "form-group2";
	$tabla->fields["DireClie"]["isHiddenInList"] = true;

	$tabla->addField("NombBarr", "text", 200, "Barrio", false);
	$tabla->fields["NombBarr"]["cssGroup"] = "form-group2";
	$tabla->fields["NombBarr"]["isHiddenInList"] = true;

	$tabla->addField("NombLoca", "text", 200, "Localidad");
	$tabla->fields["NombLoca"]["cssGroup"] = "form-group2";

	$tabla->addField("NumeProv", "select", 200, "Provincia", true, false, false, true, '', '', 'provincias', 'NumeProv', 'NombProv', '', 'NombProv');
	$tabla->fields["NumeProv"]["cssGroup"] = "form-group2";
	$tabla->fields["NumeProv"]["isHiddenInList"] = true;

	$tabla->addField("CodiPost", "text", 0, "Código postal", false);
	$tabla->fields["CodiPost"]["isHiddenInList"] = true;

	$tabla->addField("NumeVend", "select", 80, "Vendedor", true, false, false, true, '', '', 'vendedores', 'NumeVend', 'NombVend', '', 'NombVend');
	$tabla->fields["NumeVend"]["isHiddenInList"] = true;

	$tabla->addField("ObseClie", "textarea", 201, "Observaciones", false);
	$tabla->fields["ObseClie"]["isHiddenInList"] = true;

	$tabla->addField("NumeEstaClie", "select", 80, "Estado", true, false, false, true, '', '', 'estadosclientes', 'NumeEstaClie', 'NombEstaClie', '', 'NombEstaClie');

	$tabla->addField("FechPagoDesd", "number", 0, "Fecha de pago desde el");
	$tabla->fields["FechPagoDesd"]["cssGroup"] = "form-group2";
	$tabla->fields["FechPagoDesd"]["isHiddenInList"] = true;

	$tabla->addField("FechPagoHast", "number", 0, "hasta el");
	$tabla->fields["FechPagoHast"]["cssGroup"] = "form-group2";
	$tabla->fields["FechPagoHast"]["isHiddenInList"] = true;

	$tabla->addField("ValoMovi", "text", 100, "Producto");
	$tabla->fields["ValoMovi"]["cssGroup"] = "form-group2";
	$tabla->fields["ValoMovi"]["isHiddenInList"] = true;

	$tabla->addField("ValoCuot", "number", 0, "Valor cuota");
	$tabla->fields["ValoCuot"]["cssGroup"] = "form-group2";
	$tabla->fields["ValoCuot"]["isHiddenInList"] = true;
	$tabla->fields["ValoCuot"]["step"] = "0.01";

	$tabla->addField("CodiBarr", "text", 0, "Codigo de barras");
	$tabla->fields["CodiBarr"]["isHiddenInForm"] = true;
	$tabla->fields["CodiBarr"]["isHiddenInList"] = true;

	$tabla->addField("CodiPagoElec", "text", 0, "Codigo de pago electrónico");
	$tabla->fields["CodiPagoElec"]["isHiddenInForm"] = true;
	$tabla->fields["CodiPagoElec"]["isHiddenInList"] = true;
	// $tabla->addField("CantCuot", "number", 0, "Cuotas restantes");
	// $tabla->fields["CantCuot"]["cssGroup"] = "form-group2";

	$config->tablas["clientes"] = $tabla;

	/**
	 * PAGOS
	 */
	$tabla = new Cuota("cuotas", "pagos", "Cuotas", "Cuota", "true", "cuotas.php", "fa-money", "FechCuot DESC, NumeClie", false, false, true);
	$tabla->jsFiles = ['admin/js/custom/cuotas.js'];
	$tabla->btnList = [
			array('titulo'=>'<i class="fa fa-fw fa-eye" aria-hidden="true"></i> Ver', 'onclick'=>"verCuota", 'class'=>"btn-primary"),
	];

	$tabla->addField("NumePago", "number", 0, "Número", false, true, true);
	$tabla->fields["NumePago"]["isHiddenInForm"] = true;
	$tabla->fields["NumePago"]["isHiddenInList"] = true;

	$tabla->addField("NumeCuot", "number", 0, "Anticipo Nº");
	$tabla->fields["NumeCuot"]["cssGroup"] = "form-group2";

	$tabla->addField("FechCuot", "datetime", 0, "Fecha Emisión", true, true);
	$tabla->fields["FechCuot"]["cssGroup"] = "form-group2";
	
	$tabla->addField("NumeClie", "select", 80, "Cliente", true, false, false, true, '', '', 'clientes', 'NumeClie', 'NombClie', '', 'NombClie');

	$tabla->addField("ObsePago", "textarea", 200, "Observaciones", false);
	$tabla->fields["ObsePago"]["isHiddenInList"] = true;

	$tabla->addField("NumeTipoPago", "select", 80, "Tipo pago", true, false, false, true, '', '', 'tipospagos', 'NumeTipoPago', 'NombTipoPago', '', 'NombTipoPago');

	$tabla->addField("NumeEstaPago", "select", 80, "Estado", true, false, false, true, '', '', 'estadospagos', 'NumeEstaPago', 'NombEstaPago', '', 'NombEstaPago');
	$tabla->addField("FechVenc1", "date", 80, "Vencimiento");
	$tabla->addField("ImpoTota", "number", 0, "Total");
	$tabla->fields["ImpoTota"]["formatDb"] = "CONCAT('$ ', (ImpoPura + ImpoAdmi + ImpoGest + ImpoOtro)) ImpoTota";
	$tabla->fields["ImpoTota"]["showOnForm"] = false;
	$tabla->fields["ImpoTota"]["txtAlign"] = 'right';

	$tabla->addField("FechPago", "date", 80, "Fecha de Pago", false, true);
	$tabla->fields["FechPago"]["isHiddenInList"] = true;
	$tabla->fields["FechPago"]["cssGroup"] = "form-group2";

	$tabla->addField("FechAcre", "date", 80, "Fecha de Pago", false, true);
	$tabla->fields["FechAcre"]["isHiddenInList"] = true;
	$tabla->fields["FechAcre"]["cssGroup"] = "form-group2";

	$config->tablas["cuotas"] = $tabla;

	/**
	 * SEGUIMIENTOS
	 */
	$tabla = new Seguimiento("seguimientos", "seguimientos", "Seguimientos", "Seguimiento", true, "objeto/seguimientos", "fa-bookmark-o", "FechSegu DESC", true, false);

	$tabla->addField("NumeSegu", "number", 0, "Número", false, true, true);
	$tabla->addField("NumeClie", "select", 80, "Cliente", true, false, false, true, '', '', 'clientes', 'NumeClie', 'NombClie', '', 'NombClie');
	$tabla->addField("FechSegu", "datetime", 80, "Fecha");
	$tabla->fields["FechSegu"]["showOnForm"] = false;

	$tabla->addField("FlagCont", "checkbox", 0, "Estableció contacto?");
	$tabla->addField("NumeTipoCont", "select", 80, "Tipo de contacto", true, false, false, true, '', '', 'tiposcontactos', 'NumeTipoCont', 'NombTipoCont', '', 'NombTipoCont');
	$tabla->addField("NumeTipoResp", "select", 80, "Tipo de respuesta", true, false, false, true, '', '', 'tiposrespuestas', 'NumeTipoResp', 'NombTipoResp', '', 'NombTipoResp');
	$tabla->addField("ObseSegu", "textarea", 1000, "Observaciones", false);
	$tabla->fields["ObseSegu"]["isHiddenInList"] = true;

	$tabla->addField("NumeEsta", "select", 0, "Estado", true, false, false, true, '1', '', 'estados', 'NumeEsta', 'NombEsta', '', 'NombEsta');

	$config->tablas["seguimientos"] = $tabla;
	
	?>