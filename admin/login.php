<?php
	require_once 'php/datos.php';

	header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
	header("Pragma: no-cache"); // HTTP 1.0.
	header("Expires: 0"); // Proxies.	
?>
<!DOCTYPE html>
<html>
<head>
	<?php require_once 'php/linksHeader.php'; ?>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$("#usuario").focus();

			var styleSheets = $("link");
			var href = 'css/bootstrap-dark.css';
			for (var i = 0; i < styleSheets.length; i++) {
				if (styleSheets[i].href != null) {
				    if (styleSheets[i].href.indexOf(href) >= 0) {
				        styleSheets[i].disabled = true;
				        break;
				    }
				}
			}

		});


		function cambiarTema() {
			var styleSheets = $("link");
			var href = 'css/bootstrap-dark.css';
			for (var i = 0; i < styleSheets.length; i++) {
				if (styleSheets[i].href != null) {
				    if (styleSheets[i].href.indexOf(href) >= 0) {
				        styleSheets[i].disabled = !$("#theme").prop("checked");
				        break;
				    }
				}
			}
		}
				
	</script>
<body>
	<?php require_once 'php/header.php'; ?>	
	
	<div class="container-fluid">
		<div class="page-header">
			<h2>Acceso al sistema</h2>
		</div>
			
		<form action="php/loginProcesar.php" method="post" class="form-horizontal">
			<?php
				if (isset($_REQUEST["error"])) {
					$strSalida = '';
					$strSalida.= '<div class="alert alert-danger" role="alert">';
					$strSalida.= '<strong>Error!</strong> El usuario o la contrase&ntilde;a son incorrectos.';
					$strSalida.= '</div>';
					
					echo $strSalida;
				}
				
				if (isset($_REQUEST["returnUrl"])) {
					$returnUrl = $_REQUEST["returnUrl"];
				}
				else {
					$returnUrl = '';
				}
				echo '<input type="hidden" name="returnUrl" value="'.$returnUrl.'" />';
			?>
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Ingrese su usuario y contraseña</h3>
				</div>
				<div class="panel-body">
					<div class="form-group marginTop20">
						<label for="usuario" class="control-label col-md-2 col-md-offset-2">Usuario:</label>
						<div class="col-md-4">
							<input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuario" required />
						</div>
					</div>
					<div class="form-group">
						<label for="password" class="control-label col-md-2 col-md-offset-2">Contrase&ntilde;a:</label>
						<div class="col-md-4">
							<input type="password" class="form-control" name="password" placeholder="Contrase&ntilde;a" required />
						</div>
					</div>
					
					<label class="labelCheck col-md-4 col-md-offset-4">
						<input type="checkbox" name="theme" id="theme" onchange="cambiarTema()" /> Tema oscuro
					</label>
					
					<div class="form-group">
						<div class="col-md-offset-4 col-md-4 text-right">
							<button type="submit" class="btn btn-primary">Login</button>
						</div>
					</div>
				</div>
			</div>
			
		</form>

		<div class="row">
			<div class="col-md-12">
				<button class="btn btn-default" onclick="location.href = '<?php echo $config->raiz?>';">Ir al Sitio</button>
			</div>
		</div>
	</div>
	
	<?php
		require_once 'php/footer.php';
	?>	
</body>
</html>