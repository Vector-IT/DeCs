<?php 
	session_start();

	ini_set("log_errors", 1);
	ini_set("error_log", "php-error.log");

	require_once 'php/datos.php';

	if (!isset($_SESSION['is_logged_in'])){
		header("Location:login.php");
		die();
	}
	
	$cuotas = $config->getTabla("cuotas");

	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
	header("Pragma: no-cache"); // HTTP 1.0.
	header("Expires: 0"); // Proxies.	
?>
<!DOCTYPE html>
<html>
<head>
	<?php require_once 'php/linksHeader.php';?>

	<script>
		$(document).ready(function() {
			$("#actualizando").hide();

			verDir('pdfs', '#datos');
		});

		function verDir(dir, trigger) {
			if ($(trigger).data("open")) {
				$(trigger).data("open", false);
				$(trigger).parent().find("ul").remove()
			}
			else {
				$("#actualizando").show();

				$.post('php/tablaHandler.php', { 
						operacion: '100', 
						tabla: 'cuotas', 
						field: 'Explorar', 
						'trigger': trigger,
						dato: dir
					}, 
					function(data) {
						$(data['post']['trigger']).data("open", true);
						$(data['post']['trigger']).parent().append(data['valor']);
				
						$("#actualizando").hide();
					}
				);
			}
		}
	</script>
</head>
<body>
	<?php
		$config->crearMenu();
		
		require_once 'php/header.php';
	?>
	
	<div class="container-fluid">	
		<h3>Explorador de PDF</h3>

		<div id="actualizando" class="alert alert-info" role="alert">
			<i class="fa fa-refresh fa-fw fa-spin"></i> Actualizando datos, por favor espere...
		</div>

		<div id="datos"></div>
	</div>	
	
	<?php
		require_once 'php/footer.php';
	?>
</body>
</html>