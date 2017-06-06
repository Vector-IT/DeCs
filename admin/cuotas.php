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

	(isset($_REQUEST["NumeClie"]))? $NumeClie = $_REQUEST["NumeClie"]: $NumeClie= "";

	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
	header("Pragma: no-cache"); // HTTP 1.0.
	header("Expires: 0"); // Proxies.	
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		require_once 'php/linksHeader.php';

		$cuotas->script();
		
		if ($NumeClie != '') {
			$strSalida = $crlf."<script>";
			$strSalida.= $crlf. "$(function () {";
			$strSalida.= $crlf. "	$('#filCliente').val({$NumeClie});";
			$strSalida.= $crlf. "	listarCuotas();";
			$strSalida.= $crlf. "});";
			$strSalida.= $crlf. "</script>";
			
			echo $strSalida;
		}
	?>

</head>
<body>
	<?php
		$config->crearMenu();

		require_once 'php/header.php';
	?>

	<div class="container-fluid">
		<div class="page-header">
			<h2><?php echo $cuotas->titulo?></h2>
		</div>

		<div class="row">
			<div class="col-md-3 text-center">
				<h4>Generar cuotas</h4>
				<button type="button" class="btn btn-sm btn-info marginBottom10 clickable" data-js="abrirModal('Generar')"><i class="fa fa-fw fa-plus-square" aria-hidden="true"></i> Generar</button>
			</div>
			<div class="col-md-3 text-center">
				<h4>Imprimir</h4>
				<button type="button" class="btn btn-sm btn-info marginBottom10 clickable" data-js="abrirModal('Imprimir')"><i class="fa fa-fw fa-print" aria-hidden="true"></i> Imprimir</button>
			</div>
			<div class="col-md-3 text-center">
				<h4>Explorar PDFs</h4>
				<button type="button" class="btn btn-sm btn-info marginBottom10 clickable" data-url="explorar.php"><i class="fa fa-fw fa-file-pdf-o" aria-hidden="true"></i> Explorar</button>
			</div>
			<div class="col-md-3 text-center">
				<h4>Buscar cuotas</h4>
				<button type="button" class="btn btn-sm btn-info marginBottom10 clickable" data-js="abrirModal('Buscar')"><i class="fa fa-fw fa-search" aria-hidden="true"></i> Buscar</button>
			</div>
		</div>

		<div id="actualizando" class="alert alert-info" role="alert">
			<i class="fa fa-refresh fa-fw fa-spin"></i> Actualizando datos, por favor espere...
		</div>

		<div id="divMsj" class="alert alert-danger" role="alert">
			<span id="txtHint">Info</span>
		</div>

		<div id="divDatos" class="table-responsive marginTop40">
		</div>
	</div>

	<?php
		require_once 'php/footer.php';
	?>

<div id="modalFiltros" class="modal fade" tabindex="-1" role="dialog">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form class="form-horizontal" method="post" onsubmit="return false;">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title"><span id="operacion"></span> cuotas</h4>
				</div>
				<div class="modal-body">
					<div class="form-group form-group-sm">
						<label for="filEmpresa" class="control-label col-md-2">Empresa:</label>
						<div class="col-md-8">
							<select class="form-control ucase" id="filEmpresa" name="filEmpresa" onchange="filtrarClientes(this.value, '#filCliente')">
								<?php echo $config->cargarCombo('empresas', 'NumeEmpr', 'NombEmpr', '', 'NombEmpr', '-1', true, 'TODAS LAS EMPRESAS')?>
							</select>
						</div>
					</div>
					<div class="form-group form-group-sm">
						<label for="filCliente" class="control-label col-md-2">Cliente:</label>
						<div class="col-md-8">
							<select class="form-control ucase" id="filCliente" name="filCliente">
								<?php echo $config->cargarCombo('clientes', 'NumeClie', 'NombClie', '', 'NombClie', '-1', true, 'TODOS LOS CLIENTES')?>
							</select>
						</div>
					</div>

					<div class="form-group form-group-sm ">
						<label for="Fecha" class="control-label col-md-2">Mes:</label>
						<div class="col-md-8">
							<div class="input-group date margin-bottom-sm inpFecha">
								<input type="text" class="form-control" id="Fecha" size="16" value="" readonly required>
								<span class="input-group-addon add-on clickable" title="Abrir Calendario"><i class="fa fa-calendar fa-fw"></i></span>
								<span class="input-group-addon add-on clickable btnLimpiarCal" title="Limpiar"><i class="fa fa-times fa-fw"></i></span>
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

					<div id="CantCuot" class="form-group form-group-sm">
						<label for="filCuotas" class="control-label col-md-2">Cantidad a generar:</label>
						<div class="col-md-8">
							<input type="number" min="1" class="form-control ucase" id="filCuotas" name="filCuotas" required value="1">
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div id="divMsjModal" class="alert alert-danger text-left" role="alert">
						<span id="txtHintModal">Info</span>
					</div>
					<button type="submit" id="btnAceptar" class="btn btn-primary clickable" data-js="cerrarModal();">Aceptar</button>
					<button type="button" id="btnCancelar" class="btn btn-default" data-dismiss="modal">Cancelar</button>
				</div>
			</form>
		</div>
	</div>
</div>
</body>
</html>