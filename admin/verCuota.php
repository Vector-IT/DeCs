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

	if ($item == '') {
		header($urlIndex);
	}

	$strSQL = "SELECT p.NumePago,";
	$strSQL.= " DATE_FORMAT(p.FechPago, '%d/%m/%Y') FechPago,";
	$strSQL.= " p.NumeClie,";
	$strSQL.= " p.NumeCuot,";
	$strSQL.= " p.NumeEstaPago,";
	$strSQL.= " p.ObsePago,";
	$strSQL.= " p.CodiBarr,";
	$strSQL.= " DATE_FORMAT(p.FechVenc1, '%d/%m/%Y') FechVenc1,";
	$strSQL.= " DATE_FORMAT(p.FechVenc2, '%d/%m/%Y') FechVenc2,";
	$strSQL.= " DATE_FORMAT(p.FechVenc3, '%d/%m/%Y') FechVenc3,";
	$strSQL.= " p.NumeTipoPago,";
	$strSQL.= " p.ImpoPura,";
	$strSQL.= " p.ImpoAdmi,";
	$strSQL.= " p.ImpoGest,";
	$strSQL.= " p.ImpoOtro,";
	$strSQL.= " (p.ImpoPura + p.ImpoAdmi + p.ImpoGest + p.ImpoOtro) ImpoTota,";
	$strSQL.= " p.CodiBarr,";
	$strSQL.= " c.NombClie";
	$strSQL.= " FROM pagos p";
	$strSQL.= " INNER JOIN clientes c ON p.NumeClie = c.NumeClie";
	$strSQL.= " WHERE p.NumePago = {$item}";

	$tabla = $config->cargarTabla($strSQL);

	if ($tabla === false) {
		header($urlIndex);
	}

	$pago = $tabla->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		require_once 'php/linksHeader.php';

		//$cuotas->script();
	?>

	<script>
		$(document).ready(function() {
			$("#actualizando").hide();
			$("#divMsj").hide();
			$("#frmpagos").submit(function() {aceptarpagos();});

			$("button[type='reset']").on("click", function(event){
				event.preventDefault();
				$("#frmpagos")[0].reset();
				$("textarea.autogrow").removeAttr("style");
				$(".divPreview").html("");
			});
		});

		function aceptarpagos(){
			$("#actualizando").show();
			var frmData = new FormData();
			if ($("#hdnOperacion").val() != "2") {
				if (typeof validar == "function") {
					if (!validar())
						return;
				}
			}
			frmData.append("operacion", 1);
			frmData.append("tabla", "cuotas");
			frmData.append("NumePago", $("#NumePago").val());
			frmData.append("ObsePago", $("#ObsePago").val());
			frmData.append("NumeTipoPago", $("#NumeTipoPago").val());
			frmData.append("NumeEstaPago", $("#NumeEstaPago").val());

			if (window.XMLHttpRequest)
			{// code for IE7+, Firefox, Chrome, Opera, Safari
				xmlhttp = new XMLHttpRequest();
			}
			else
			{// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState==4 && xmlhttp.status==200) {
					$("#txtHint").html(xmlhttp.responseText);
			
					if (xmlhttp.responseText.indexOf("Error") == -1) {
						$("#divMsj").removeClass("alert-danger");
						$("#divMsj").addClass("alert-success");
						$(".selectpicker").selectpicker("deselectAll");
					}
					else {
						$("#divMsj").removeClass("alert-success");
						$("#divMsj").addClass("alert-danger");
					}
			
					$("#actualizando").hide();
					$("#divMsj").show();
				}
			};
			
			xmlhttp.open("POST","php/tablaHandler.php",true);
			xmlhttp.send(frmData);
		}
	</script>
</head>
<body>
	<?php
		$config->crearMenu();

		require_once 'php/header.php';
	?>

	<div class="container">
		<div class="page-header">
			<h2><?php echo $cuotas->tituloSingular. ' ' .$pago["NumeCuot"]. ' de ' .$pago["NombClie"]?></h2>
		</div>
		<button class="btn btn-sm btn-info clickable" data-js="history.go(-1);"><i class="fa fa-chevron-circle-left fa-fw" aria-hidden="true"></i> Volver</button>

		<form id="frmpagos" class="form-horizontal marginTop20" method="post" onSubmit="return false;">
			<input type="hidden" id="hdnTabla" value="pagos">
			<div class="form-group form-group-sm ">
				<label for="NumePago" class="control-label col-md-2 col-lg-2">Nº Comprobante:</label>
				<div class="col-md-2 col-lg-2">
					<input type="number" class="form-control input-sm " id="NumePago" readonly value="<?php echo $pago["NumePago"] ?>" >
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="NumeCuot" class="control-label col-md-2 col-lg-2">Anticipo Nº:</label>
				<div class="col-md-2 col-lg-2">
					<input type="number" class="form-control input-sm " id="NumeCuot" readonly value="<?php echo $pago["NumeCuot"] ?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="FechPago" class="control-label col-md-2 col-lg-2">Fecha Emisión:</label>
				<div class="col-md-2 col-lg-2">
					<div class="input-group date margin-bottom-sm inpFechPago">
						<input type="text" class="form-control input-sm " id="FechPago" size="16" readonly value="<?php echo $pago["FechPago"] ?>">
						<span class="input-group-addon add-on "><i class="fa fa-calendar fa-fw"></i></span>
					</div>
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="NumeClie" class="control-label col-md-2 col-lg-2">Cliente:</label>
				<div class="col-md-4 col-lg-4">
					<input type="text" class="form-control input-sm ucase " id="NombClie" readonly value="<?php echo $pago["NombClie"] ?>">
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="FechVenc1" class="control-label col-md-2 col-lg-2">1º Vencimiento:</label>
				<div class="col-md-4 col-lg-4">
					<div class="input-group date margin-bottom-sm inpFechVenc1">
						<input type="text" class="form-control input-sm " id="FechVenc1" size="16" readonly value="<?php echo $pago["FechVenc1"] ?>">
						<span class="input-group-addon add-on "><i class="fa fa-calendar fa-fw"></i></span>
					</div>
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="FechVenc2" class="control-label col-md-2 col-lg-2">2º Vencimiento:</label>
				<div class="col-md-2 col-lg-2">
					<div class="input-group date margin-bottom-sm inpFechVenc2">
						<input type="text" class="form-control input-sm " id="FechVenc2" size="16" readonly value="<?php echo $pago["FechVenc2"] ?>">
						<span class="input-group-addon add-on "><i class="fa fa-calendar fa-fw"></i></span>
					</div>
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="FechVenc3" class="control-label col-md-2 col-lg-2">3º Vencimiento:</label>
				<div class="col-md-2 col-lg-2">
					<div class="input-group date margin-bottom-sm inpFechVenc3">
						<input type="text" class="form-control input-sm " id="FechVenc3" size="16" readonly  value="<?php echo $pago["FechVenc3"] ?>">
						<span class="input-group-addon add-on "><i class="fa fa-calendar fa-fw"></i></span>
					</div>
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="ImpoPura" class="control-label col-md-2 col-lg-2">Cuota pura:</label>
				<div class="col-md-2 col-lg-2">
					<input type="text" class="form-control input-sm text-right" id="ImpoPura" readonly value="$ <?php echo $pago["ImpoPura"] ?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="ImpoAdmi" class="control-label col-md-2 col-lg-2">Gastos Admin.:</label>
				<div class="col-md-2 col-lg-2">
					<input type="text" class="form-control input-sm text-right" id="ImpoAdmi" readonly value="$ <?php echo $pago["ImpoAdmi"] ?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="ImpoGest" class="control-label col-md-2 col-lg-2">Gestión de Cobranza:</label>
				<div class="col-md-2 col-lg-2">
					<input type="text" class="form-control input-sm text-right" id="ImpoGest" readonly value="$ <?php echo $pago["ImpoGest"] ?>">
				</div>
			</div>
			<div class="form-group form-group-sm form-group2">
				<label for="ImpoOtro" class="control-label col-md-2 col-lg-2">Otros gastos:</label>
				<div class="col-md-2 col-lg-2">
					<input type="text" class="form-control input-sm text-right" id="ImpoOtro" readonly value="$ <?php echo $pago["ImpoOtro"] ?>">
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="ImpoTota" class="control-label col-md-2 col-lg-2">Importe Total:</label>
				<div class="col-md-4 col-lg-4">
					<input type="text" class="form-control input-sm text-right" id="ImpoTota" readonly value="$ <?php echo $pago["ImpoTota"] ?>">
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="NumeTipoPago" class="control-label col-md-2 col-lg-2">Forma de pago:</label>
				<div class="col-md-4 col-lg-4">
					<select class="form-control input-sm ucase " id="NumeTipoPago" required>
						<?php echo $cuotas->cargarCombo('tipospagos', 'NumeTipoPago', 'NombTipoPago', 'NumeEsta = 1', 'NumeTipoPago', $pago["NumeTipoPago"])?>
					</select>
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="NumeEstaPago" class="control-label col-md-2 col-lg-2">Estado:</label>
				<div class="col-md-4 col-lg-4">
					<select class="form-control input-sm ucase " id="NumeEstaPago" required="">
						<?php echo $cuotas->cargarCombo('estadospagos', 'NumeEstaPago', 'NombEstaPago', 'NumeEsta = 1', 'NumeEstaPago', $pago["NumeEstaPago"])?>
					</select>
				</div>
			</div>
			<div class="form-group form-group-sm ">
				<label for="ObsePago" class="control-label col-md-2 col-lg-2">Observaciones:</label>
				<div class="col-md-10 col-lg-10">
					<textarea class="form-control input-sm autogrow " id="ObsePago" style="height: 48px;"><?php echo $pago["ObsePago"] ?></textarea>
						<script type="text/javascript">
							$("#ObsePago").autogrow({vertical: true, horizontal: false, minHeight: 36});
						</script>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-offset-2 col-lg-offset-2 col-md-10 col-lg-10">
					<img src="barcode/barcode.php?text=<?php echo $pago["CodiBarr"]?>&print=true&size=40">
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-offset-2 col-lg-offset-2 col-md-4 col-lg-4">
					<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-check fa-fw" aria-hidden="true"></i> Aceptar</button>
			&nbsp;
					<button type="reset" class="btn btn-sm btn-default"><i class="fa fa-times fa-fw" aria-hidden="true"></i> Cancelar</button>
				</div>
			</div>			
		</form>

		<div id="actualizando" class="alert alert-info" role="alert">
			<i class="fa fa-refresh fa-fw fa-spin"></i> Actualizando datos, por favor espere...
		</div>

		<div id="divMsj" class="alert alert-danger" role="alert">
			<span id="txtHint">Info</span>
		</div>
	</div>

	<?php
		require_once 'php/footer.php';
	?>

</body>
</html>