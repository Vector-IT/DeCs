<?php 
	namespace VectorForms;

	ini_set("log_errors", 1);
	ini_set("error_log", "php-error.log");

	session_start();
	require_once 'php/datos.php';

	$urlLogin = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/login.php?returnUrl=" . $_SERVER['REQUEST_URI'];    
	$urlIndex = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/";
	
	if (!isset($_SESSION['is_logged_in'])){
		header($urlLogin);
		die();
	}
	
	if (isset($_REQUEST["tb"])) {
		if ($_REQUEST["tb"] != "") {
			if (isset($config->tablas[$_REQUEST["tb"]])) {
				$tabla = $config->tablas[$_REQUEST["tb"]];
				
				if ($tabla->numeCarg != '') {
					if (intval($tabla->numeCarg) < intval($config->buscarDato("SELECT NumeCarg FROM usuarios WHERE NumeUser = ". $_SESSION["NumeUser"]))) {
						header($urlIndex);
						die();
					}
				}
				
				(isset($_REQUEST["id"]))? $item = $_REQUEST["id"]: $item = "";
				
			} 
			else {
				header($urlIndex);
				die();
			}
		}
		else {
			header($urlIndex);
			die();
		}
	}
	else {
		header($urlIndex);
		die();
	}

	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
	header("Pragma: no-cache"); // HTTP 1.0.
	header("Expires: 0"); // Proxies.	
?>
<!DOCTYPE html>
<html>
<head>
	<?php 
		require_once 'php/linksHeader.php';
		
		$tabla->script();
	?>
</head>
<body>
	<?php
		$config->crearMenu();
		
		require_once 'php/header.php';
	?>
	
	<div class="container-fluid">
		<div class="page-header">
			<h2>
			<?php 
				if ($tabla->masterTable == '') {
					echo $tabla->titulo;
				}
				else {
					if (isset($_REQUEST[$tabla->masterFieldId])) {
						$strAux = $config->buscarDato("SELECT {$tabla->masterFieldName} FROM {$tabla->masterTable} WHERE {$tabla->masterFieldId} = '{$_REQUEST[$tabla->masterFieldId]}'");
						echo $tabla->titulo. ' de ' .$strAux;
					}
					else {
						echo $tabla->titulo;
					}
				}
			?>
			</h2>
		</div>
		
		<?php
			if ((($tabla->masterTable != '') && isset($_REQUEST[$tabla->masterFieldId])) || (isset($_REQUEST["id"]))) {
				//echo '<button class="btn btn-sm btn-info clickable" data-js="location.href = \''. $config->tablas[$tabla->masterTable]->url .'\';"><i class="fa fa-chevron-circle-left fa-fw" aria-hidden="true"></i> Volver</button>';
				echo '<button class="btn btn-sm btn-info clickable" data-js="history.go(-1);"><i class="fa fa-chevron-circle-left fa-fw" aria-hidden="true"></i> Volver</button>';
			}
			
			if ($tabla->allowNew || $tabla->allowEdit) {
				$tabla->createForm();
			} 
			else {
				//Botones opcionales
				if (count($tabla->btnForm) > 0) {
					for ($I = 0; $I < count($tabla->btnForm); $I++) {
						echo $crlf.'<button class="btn btn-sm '. $tabla->btnForm[$I][2] .'" onclick="'. $tabla->btnForm[$I][1] .'">'. $tabla->btnForm[$I][0] .'</button>';
					}
				}
			}
		?>
		
		<div id="actualizando" class="alert alert-info" role="alert">
			<i class="fa fa-refresh fa-fw fa-spin"></i> Actualizando datos, por favor espere...
		</div>
		
		<div id="divMsj" class="alert alert-danger" role="alert">
			<span id="txtHint">Info</span>
		</div>

		<?php $tabla->searchForm(); ?>

		<div id="divDatos" class="marginTop40">
			<?php if ($tabla->listarOnLoad) $tabla->listar(); ?>
		</div>
	</div>
	
	<?php
		require_once 'php/footer.php';
	?>	
</body>
</html>