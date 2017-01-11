<?php 
	session_start();
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
</head>
<body>
	<?php
		$config->crearMenu();
		
		require_once 'php/header.php';
	?>
	
	<div class="container">
		<div class="page-header">
			<h2>Consola de Administraci&oacute;n</h2>
		</div>
		
		<p class="lead">
			Bienvenido a la consola de administraci&oacute;n de <?php echo $config->titulo ?><br>
			Utilice el men&uacute; situado en el margen izquierdo de la pantalla para acceder a las distintas 
			secciones del sistema.			
		</p>
	</div>	
	
	<?php
		require_once 'php/footer.php';
	?>
</body>
</html>