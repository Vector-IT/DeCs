<?php
	session_start();
	require_once 'php/datos.php';

	$urlLogin = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/login.php?returnUrl=" . $_SERVER['REQUEST_URI'];
	$urlIndex = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/";

	if (!isset($_SESSION['is_logged_in'])){
		header($urlLogin);
		die();
	}

	$cuotas = $config->tablas['cuotas'];

	if ($cuotas->numeCarg != '') {
		if (intval($cuotas->numeCarg) < intval($config->buscarDato("SELECT NumeCarg FROM usuarios WHERE NumeUser = ". $_SESSION["NumeUser"]))) {
			header($urlIndex);
			die();
		}
	}

	(isset($_REQUEST["id"]))? $item = $_REQUEST["id"]: $item = "";
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		require_once 'php/linksHeader.php';
/*
		$strSalida = '';
		if (count($cuotas->jsFiles) > 0) {
			for ($I = 0; $I < count($cuotas->jsFiles); $I++) {
				$strSalida.= $crlf.'	<script src="'. $config->raiz . $cuotas->jsFiles[$I] .'"></script>';
			}
		}

		echo $strSalida;
*/
		$cuotas->script();
	?>

</head>
<body>
	<?php
		$config->crearMenu();

		require_once 'php/header.php';
	?>

	<div class="container">
		<div class="page-header">
			<h2><?php echo $cuotas->titulo?></h2>
		</div>
		<h4>Generar cuotas</h4>
		<form id="frmGenerar" class="form-horizontal marginTop20" method="post" onsubmit="return false;">
			<div class="row">
				<div class="col-md-5">
					<label for="dtpFechPago" class="control-label col-md-3">Fecha:</label>
					<div class="col-md-9">
						<div class="input-group date margin-bottom-sm inpFechPago">
							<input type="text" class="form-control" id="txtFechPago" size="16" value="" readonly>
							<span class="input-group-addon add-on clickable" title="Limpiar"><i class="fa fa-times fa-fw"></i></span>
							<span class="input-group-addon add-on clickable" title="Abrir Calendario"><i class="fa fa-calendar fa-fw"></i></span>
						</div>
						<input type="hidden" id="hdnFechPago">
						<script type="text/javascript">
							$(".inpFechPago").datetimepicker({
								language: "es",
								format: "MM yyyy",
								minView: 3,
								startView: 3,
								autoclose: true,
								todayBtn: true,
								todayHighlight: false,
								minuteStep: 15,
								pickerPosition: "bottom-left",
								linkField: "hdnFechPago",
								linkFormat: "yyyy-mm",
								fontAwesome: true
							});
						</script>
					</div>
				</div>
				<div class="col-md-5">
					<label for="cmbNumeEmpr" class="control-label col-md-3">Empresa:</label>
					<div class="col-md-9">
						<select class="form-control ucase" id="cmbNumeEmpr">
							<option value="">TODAS LAS EMPRESAS</option>
							<?php echo $cuotas->cargarCombo('empresas', 'NumeEmpr', 'NombEmpr', '', 'NombEmpr')?>
						</select>
					</div>
				</div>
				<div class="col-md-2 text-right">
					<button type="submit" id="btnGenerar" class="btn btn-sm btn-primary" onclick="generarcuotas();"><i class="fa fa-share-square-o fa-fw" aria-hidden="true"></i> Generar</button>
				</div>
			</div>
		</form>

		<div id="actualizando" class="alert alert-info" role="alert">
			<i class="fa fa-refresh fa-fw fa-spin"></i> Actualizando datos, por favor espere...
		</div>

		<div id="divMsj" class="alert alert-danger" role="alert">
			<span id="txtHint">Info</span>
		</div>

		<hr>
		<h4>Buscar cuotas</h4>
		<form id="frmFiltros" class="form-horizontal marginTop20" method="post" onsubmit="return false;">
			<div class="row">
				<div class="col-md-5">
					<label for="filFech" class="control-label col-md-3">Fecha:</label>
					<div class="col-md-9">
						<div class="input-group date margin-bottom-sm inpFechCuot">
							<input type="text" class="form-control" id="filFechText" size="16" value="" readonly>
							<span class="input-group-addon add-on clickable" title="Limpiar"><i class="fa fa-times fa-fw"></i></span>
							<span class="input-group-addon add-on clickable" title="Abrir Calendario"><i class="fa fa-calendar fa-fw"></i></span>
						</div>
						<input type="hidden" id="filFechPago">
						<script type="text/javascript">
							$(".inpFechCuot").datetimepicker({
								language: "es",
								format: "MM yyyy",
								minView: 3,
								startView: 3,
								autoclose: true,
								todayBtn: true,
								todayHighlight: false,
								minuteStep: 15,
								pickerPosition: "bottom-left",
								linkField: "filFechPago",
								linkFormat: "yyyy-mm",
								fontAwesome: true
							});
						</script>
					</div>
				</div>
				<div class="col-md-5">
					<label for="filNumeEmpr" class="control-label col-md-3">Empresa:</label>
					<div class="col-md-9">
						<select class="form-control ucase" id="filNumeEmpr">
							<option value="">TODAS LAS EMPRESAS</option>
							<?php echo $cuotas->cargarCombo('empresas', 'NumeEmpr', 'NombEmpr', '', 'NombEmpr')?>
						</select>
					</div>
				</div>
			</div>
			<div class="row marginTop20">
				<div class="col-md-5">
					<label for="filNumeClie" class="control-label col-md-3">Cliente:</label>
					<div class="col-md-9">
						<select class="form-control ucase" id="filNumeClie">
							<option value="">TODOS LOS CLIENTES</option>
							<?php echo $cuotas->cargarCombo('clientes', 'NumeClie', 'NombClie', '', 'NombClie')?>
						</select>
					</div>
				</div>
				<div class="col-md-7 text-right">
					<button type="submit" id="btnFiltrar" class="btn btn-sm btn-success" onclick="listarCuotas();"><i class="fa fa-search fa-fw" aria-hidden="true"></i> Filtrar</button>
				</div>
			</div>
		</form>

		<div id="divDatos" class="table-responsive marginTop40 marginBottom60">
		</div>
	</div>

	<?php
		require_once 'php/footer.php';
	?>

</body>
</html>