<?php
	session_start();
	require_once 'php/datos.php';

	$urlLogin = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/login.php?returnUrl=" . $_SERVER['REQUEST_URI'];
	$urlIndex = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/";

	if (!isset($_SESSION['is_logged_in'])){
		header($urlLogin);
		die();
	}

	$clientes = $config->getTabla("clientes");

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

	<script src="js/custom/cargarClientes.js"></script>
</head>
<body>
	<?php
		$config->crearMenu();

		require_once 'php/header.php';
	?>

	<div class="container-fluid">
		<div class="page-header">
			<h2>Carga de clientes por archivo csv</h2>
		</div>

		<form id="frmCarga" class="form-horizontal marginTop20 frmObjeto" method="post" onsubmit="return false;" style="display: block;">
			<div class="form-group form-group-sm ">
				<label for="NumeEmpr" class="control-label col-md-2">Empresa:</label>
				<div class="col-md-6">
					<select class="form-control input-sm ucase " id="NumeEmpr" required>
						<?php echo $clientes->cargarCombo('empresas', 'NumeEmpr', 'NombEmpr', 'NumeEsta = 1', 'NumeEmpr')?>
					</select>
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="Archivo" class="control-label col-md-2">Archivo:</label>
				<div class="col-md-6">
					<input type="file" class="form-control input-sm " id="Archivo" required>
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="Fecha" class="control-label col-md-2">Fecha de la primer cuota:</label>
				<div class="col-md-6">
					<div class="input-group date margin-bottom-sm inpFecha">
						<input type="text" class="form-control" id="Fecha" size="16" value="" required onkeydown="return false;">
						<span class="input-group-addon add-on clickable" title="Abrir Calendario"><i class="fa fa-calendar fa-fw"></i></span>
					</div>
					<input type="hidden" id="filFecha" name="filFecha">
					<script type="text/javascript">
						$(".inpFecha").datetimepicker({
							language: "es",
							format: "MM yyyy",
							minView: 3,
							startView: 3,
							autoclose: true,
							todayBtn: true,
							todayHighlight: false,
							minuteStep: 15,
							pickerPosition: "bottom-left",
							linkField: "filFecha",
							linkFormat: "yyyy-mm",
							fontAwesome: true
						});
					</script>
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="CantCuot" class="control-label col-md-2">Cantidad de cuotas a generar:</label>
				<div class="col-md-6">
					<input type="number" class="form-control input-sm " id="CantCuot" required min="0">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-offset-2 col-lg-offset-2 col-md-4 col-lg-4">
					<button type="submit" id="btnAceptar" class="btn btn-sm btn-primary"><i class="fa fa-check fa-fw" aria-hidden="true"></i> Aceptar</button>
					&nbsp;
					<button type="reset" id="btnCancelar" class="btn btn-sm btn-default"><i class="fa fa-times fa-fw" aria-hidden="true"></i> Cancelar</button>
				</div>
			</div>
		</form>
		<div id="divMsj" class="alert alert-danger" role="alert">
			<span id="txtHint">Info</span>
		</div>

		<div id="actualizando" class="alert alert-info" role="alert">
			<i class="fa fa-refresh fa-fw fa-spin"></i> Actualizando datos, por favor espere...
		</div>
	</div>
	
	<?php
		require_once 'php/footer.php';
	?>	
</body>
</html>