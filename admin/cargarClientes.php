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
?>
<!DOCTYPE html>
<html>
<head>
	<?php
		require_once 'php/linksHeader.php';
	?>

	<script>
		$(document).ready(function() {
			$("#actualizando").hide();
			$("#divMsj").hide();
			$("#frmCarga").submit(function() {aceptarcarga();});
		});

		function aceptarcarga() {
			$("#btnAceptar, #btnCancelar").addClass("disabled");
			$("#actualizando").show();

			var frmData = new FormData();
			frmData.append("operacion", 100);
			frmData.append("tabla", 'clientes');
			frmData.append("field", 'CSV');

			frmData.append("NumeEmpr", $("#NumeEmpr").val());
			frmData.append("Archivo", $("#Archivo").get(0).files[0]);

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
					if (JSON.parse(JSON.parse(xmlhttp.responseText)["valor"]).estado) {
						$("#txtHint").html("Carga Exitosa!<br>"+ JSON.parse(JSON.parse(xmlhttp.responseText)["valor"]).mensaje);
				
						$("#divMsj").removeClass("alert-danger");
						$("#divMsj").addClass("alert-success");

						$('#frmCarga')[0].reset();
					}
					else {
						$("#txtHint").html("Error en la Carga!<br>"+ JSON.parse(JSON.parse(xmlhttp.responseText)["valor"]).mensaje);
						$("#divMsj").removeClass("alert-success");
						$("#divMsj").addClass("alert-danger");
					}
			
					$("#actualizando").hide();
					$("#divMsj").show();
					$("#btnAceptar, #btnCancelar").removeClass("disabled");
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
			<div class="form-group">
				<div class="col-md-offset-2 col-lg-offset-2 col-md-4 col-lg-4">
					<button type="submit" id="btnAceptar" class="btn btn-sm btn-primary"><i class="fa fa-check fa-fw" aria-hidden="true"></i> Aceptar</button>
					&nbsp;
					<button type="reset" id="btnCancelar" class="btn btn-sm btn-default"><i class="fa fa-times fa-fw" aria-hidden="true"></i> Cancelar</button>
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