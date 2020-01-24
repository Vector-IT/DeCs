<?php
	session_start();
	require_once 'php/datos.php';

	$urlLogin = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/login.php?returnUrl=" . $_SERVER['REQUEST_URI'];
	$urlIndex = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/";

	if (!isset($_SESSION['is_logged_in'])){
		header($urlLogin);
		die();
	}

	$clientes = $config->tablas['clientes'];

	if ($clientes->numeCarg != '') {
		if (intval($clientes->numeCarg) < intval($config->buscarDato("SELECT NumeCarg FROM usuarios WHERE NumeUser = ". $_SESSION["NumeUser"]))) {
			header($urlIndex);
			die();
		}
	}

	(isset($_REQUEST["id"]))? $item = $_REQUEST["id"]: $item = "";

	if ($item == '') {
		header($urlIndex);
	}

	$pagos = $config->tablas['cuotas'];
	$seguimientos = $config->tablas["seguimientos"];

	$strSQL = "SELECT c.NumeClie,";
	$strSQL.= " c.NumeSoli,";
	$strSQL.= " c.NombClie,";
	$strSQL.= " e.NombEmpr,";
	$strSQL.= " c.NumeTele,";
	$strSQL.= " c.NumeCelu,";
	$strSQL.= " c.MailClie,";
	$strSQL.= " c.DireClie,";
	$strSQL.= " c.NombBarr,";
	$strSQL.= " c.NombLoca,";
	$strSQL.= " p.NombProv,";
	$strSQL.= " c.CodiPost,";
	$strSQL.= " v.NombVend,";
	$strSQL.= " c.ObseClie,";
	$strSQL.= " ec.NombEstaClie,";
	$strSQL.= " c.ValoMovi,";
	$strSQL.= " c.ValoCuot,";
	$strSQL.= " DATE_FORMAT(c.FechIngr, '%d/%m/%Y') FechIngr,";
	$strSQL.= " c.FechPagoDesd,";
	$strSQL.= " c.FechPagoHast,";
	$strSQL.= " c.CantCuot,";
	$strSQL.= " c.CodiBarr,";
	$strSQL.= " c.CodiPagoElec,";
	$strSQL.= " DATE_FORMAT(c.FechImpr, '%d/%m/%Y') FechImpr";
	$strSQL.= " FROM clientes c";
	$strSQL.= " INNER JOIN empresas e ON c.NumeEmpr = e.NumeEmpr";
	$strSQL.= " INNER JOIN provincias p ON c.NumeProv = p.NumeProv";
	$strSQL.= " LEFT JOIN vendedores v ON c.NumeVend = v.NumeVend";
	$strSQL.= " INNER JOIN estadosclientes ec ON c.NumeEstaClie = ec.NumeEstaClie";
	$strSQL.= " WHERE c.NumeClie = {$item}";

	$tabla = $config->cargarTabla($strSQL);

	if ($tabla === false) {
		header($urlIndex);
	}

	$cliente = $tabla->fetch_assoc();

	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
	header("Pragma: no-cache"); // HTTP 1.0.
	header("Expires: 0"); // Proxies.	
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		require_once 'php/linksHeader.php';
	?>

	<script>
		function verCuota(strID) {
			location.href = "verCuota.php?id=" + strID;
		}

		function verSeguimiento(strID) {
			location.href = "objeto/seguimientos&id=" + strID;
		}
	</script>
</head>
<body>
	<?php
		$config->crearMenu();

		require_once 'php/header.php';
	?>

	<div class="container-fluid">
		<div class="page-header">
			<h2><?php echo 'Ficha de ' .$cliente["NombClie"]?></h2>
		</div>
		<button class="btn btn-sm btn-info clickable" data-js="history.go(-1);"><i class="fa fa-chevron-circle-left fa-fw" aria-hidden="true"></i> Volver</button>

		<form id="frmclientes" class="form-horizontal marginTop20 frmObjeto" method="post" onsubmit="return false;" style="display: block;">
			<div class="form-group form-group-sm ">
				<label for="NumeSoli" class="control-label col-md-2">Nro Solicitud:</label>
				<div class="col-md-2">
					<input type="number" step="1" class="form-control input-sm " id="NumeSoli" readonly value="<?php echo $cliente['NumeSoli']?>">
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="NombClie" class="control-label col-md-2">Nombre:</label>
				<div class="col-md-6">
					<input type="text" class="form-control input-sm " id="NombClie" readonly value="<?php echo $cliente['NombClie']?>">
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="NumeEmpr" class="control-label col-md-2">Empresa:</label>
				<div class="col-md-6">
					<input type="text" class="form-control input-sm " id="NumeEmpr" readonly value="<?php echo $cliente['NombEmpr']?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="NumeTele" class="control-label col-md-2">Teléfono:</label>
				<div class="col-md-5">
					<input type="text" class="form-control input-sm " id="NumeTele" readonly value="<?php echo $cliente['NumeTele']?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="NumeCelu" class="control-label col-md-2">Celular:</label>
				<div class="col-md-5">
					<input type="text" class="form-control input-sm " id="NumeCelu" readonly value="<?php echo $cliente['NumeCelu']?>">
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="MailClie" class="control-label col-md-2">E-mail:</label>
				<div class="col-md-6">
					<input type="email" class="form-control input-sm " id="MailClie" readonly value="<?php echo $cliente['MailClie']?>">
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="FechIngr" class="control-label col-md-2">Fecha ingreso:</label>
				<div class="col-md-2">
					<div class="input-group date margin-bottom-sm inpFechIngr">
						<input type="text" class="form-control input-sm " id="FechIngr" readonly value="<?php echo $cliente['FechIngr']?>">
						<span class="input-group-addon add-on"><i class="fa fa-calendar fa-fw"></i></span>
					</div>
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="DireClie" class="control-label col-md-2">Dirección:</label>
				<div class="col-md-6">
					<input type="text" class="form-control input-sm " id="DireClie" readonly value="<?php echo $cliente['DireClie']?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="NombBarr" class="control-label col-md-2">Barrio:</label>
				<div class="col-md-6">
					<input type="text" class="form-control input-sm " id="NombBarr" readonly value="<?php echo $cliente['NombBarr']?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="NombLoca" class="control-label col-md-2">Localidad:</label>
				<div class="col-md-6">
					<input type="text" class="form-control input-sm " id="NombLoca" readonly value="<?php echo $cliente['NombLoca']?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="NumeProv" class="control-label col-md-2">Provincia:</label>
				<div class="col-md-6">
					<input type="text" class="form-control input-sm ucase " id="NumeProv" readonly value="<?php echo $cliente['NombProv']?>">
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="CodiPost" class="control-label col-md-2">Código postal:</label>
				<div class="col-md-2">
					<input type="text" class="form-control input-sm " id="CodiPost" readonly value="<?php echo $cliente['CodiPost']?>">
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="NumeVend" class="control-label col-md-2">Vendedor:</label>
				<div class="col-md-4">
					<input type="text" class="form-control input-sm ucase " id="NumeVend" readonly value="<?php echo $cliente['NombVend']?>">
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="ObseClie" class="control-label col-md-2">Observaciones:</label>
				<div class="col-md-10">
					<textarea class="form-control input-sm autogrow " id="ObseClie" readonly value="<?php echo $cliente['ObseClie']?>"></textarea>
					<script type="text/javascript">
						$("#ObseClie").autogrow({vertical: true, horizontal: false, minHeight: 36});
					</script>
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="NumeEstaClie" class="control-label col-md-2">Estado:</label>
				<div class="col-md-4">
					<input type="text" class="form-control input-sm ucase " id="NumeEstaClie" readonly value="<?php echo $cliente['NombEstaClie']?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="FechPagoDesd" class="control-label col-md-2">Fecha de pago desde el:</label>
				<div class="col-md-2">
					<input type="number" step="1" class="form-control input-sm " id="FechPagoDesd" readonly value="<?php echo $cliente['FechPagoDesd']?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="FechPagoHast" class="control-label col-md-2">hasta el:</label>
				<div class="col-md-2">
					<input type="number" step="1" class="form-control input-sm " id="FechPagoHast" readonly value="<?php echo $cliente['FechPagoHast']?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="ValoMovi" class="control-label col-md-2">Producto:</label>
				<div class="col-md-5">
					<input type="text" class="form-control input-sm " id="ValoMovi" readonly value="<?php echo $cliente['ValoMovi']?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="ValoCuot" class="control-label col-md-2">Valor cuota:</label>
				<div class="col-md-2">
					<input type="number" step="0.01" class="form-control input-sm " id="ValoCuot" readonly value="<?php echo $cliente['ValoCuot']?>">
				</div>
			</div>

			<div class="form-group">
				<label for="CodiPagoElec" class="control-label col-md-2">Código de pago electrónico:</label>
				<div class="col-md-4">
					<input type="text" class="form-control input-sm " id="CodiPagoElec" readonly value="<?php echo $cliente['CodiPagoElec']?>">
				</div>
			</div>

			<div class="form-group">
				<div class="col-md-offset-2 col-md-10">
					<img src="barcode/barcode.php?text=<?php echo $cliente["CodiBarr"]?>&print=true&size=40">
				</div>
			</div>

		</form>

		<hr>
		<h4 class="marginTop20">Seguimientos</h4>
		<?php $seguimientos->listar("NumeClie = ". $cliente["NumeClie"], false, [array('titulo'=>'<i class="fa fa-fw fa-bookmark-o" aria-hidden="true"></i> Ver', 'onclick'=>"verSeguimiento", 'class'=>"btn-primary")]); ?>

		<hr>
		<h4 class="marginTop20">Cuotas</h4>
		<?php $pagos->listar(array('Fecha'=> '', 'Empresa'=> '-1', 'Cliente'=> $cliente["NumeClie"]), false, [array('titulo'=>'<i class="fa fa-fw fa-eye" aria-hidden="true"></i> Ver', 'onclick'=>"verCuota", 'class'=>"btn-primary")]); ?>
	</div>

	<?php
		require_once 'php/footer.php';
	?>

</body>
</html>