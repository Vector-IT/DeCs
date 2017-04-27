<?php 
	session_start();

	ini_set("log_errors", 1);
	ini_set("error_log", "php-error.log");

	require_once 'php/datos.php';

	if (!isset($_SESSION['is_logged_in'])){
		header("Location:login.php");
		die();
	}
?>
<!DOCTYPE html>
<html>
<head>
	<?php require_once 'php/linksHeader.php';?>

	<script src="js/custom/index.js"></script>
</head>
<body>
	<?php
		$config->crearMenu();
		
		require_once 'php/header.php';
	?>
	
	<div class="container-fluid">	
		<!--<div class="page-header">
			<h2>Consola de Administraci&oacute;n</h2>
		</div>
		
		<p class="lead">
			Bienvenido a la consola de administraci&oacute;n de <?php echo $config->titulo ?><br>
			Utilice el men&uacute; situado en el margen izquierdo de la pantalla para acceder a las distintas 
			secciones del sistema.			
		</p>
		<hr>-->

		<h3>Seguimientos sin procesar</h3>
		<?php
			$filtro = "NumeTipoCont IS NULL OR NumeTipoResp IS NULL";
			$seguimientos = $config->tablas["seguimientos"];
			$btnList = [
				array('titulo'=>'<i class="fa fa-fw fa-bookmark-o" aria-hidden="true"></i> Ver', 
					'onclick'=>"verSeguimiento", 
					'class'=>"btn-success")

			];

			$seguimientos->listar($filtro, false, $btnList, "FechSegu");
		?>
	</div>	
	
	<?php
		require_once 'php/footer.php';
	?>
</body>
</html>