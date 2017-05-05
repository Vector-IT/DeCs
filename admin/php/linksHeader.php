
	<meta charset="UTF-8">
	<meta name="author" content="Vector-IT" />
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0" />

	<link rel="shortcut icon" href="img/favicon.png" type="image/png" />
	<link rel="apple-touch-icon" href="img/favicon.png"/>

	<title><?php echo $config->titulo ?></title>

	<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

<?php if (isset($_SESSION['is_logged_in'])) { ?>
	<script src="<?php echo $config->raiz ?>admin/js/vectorMenu.js"></script>
<?php }?>

	<!-- BOOTSTRAP -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

	<?php
		if (isset($_SESSION['DarkTheme']) || !isset($_SESSION['is_logged_in'])) {
			echo '<link rel="stylesheet" href="'.$config->raiz.'admin/css/bootstrap-dark.css">';
		}
	?>

	<!-- FONT AWESOME -->
	<link rel="stylesheet" href="<?php echo $config->raiz ?>admin/css/font-awesome.css">
	

	<!-- DATETIME PICKER -->
	<link rel="stylesheet" type="text/css" href="<?php echo $config->raiz ?>admin/css/bootstrap-datetimepicker.css">
	<script src="<?php echo $config->raiz ?>admin/js/bootstrap-datetimepicker/bootstrap-datetimepicker.js"></script>
	<script src="<?php echo $config->raiz ?>admin/js/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.es.js"></script>

	<!-- TEXTAREA AUTOGROW -->
	<script src="<?php echo $config->raiz ?>admin/js/jquery.ns-autogrow.min.js"></script>

	<!-- BOOTSTRAP-SELECT -->
	<link rel="stylesheet" type="text/css" href="<?php echo $config->raiz ?>admin/css/bootstrap-select.min.css">
	<script src="<?php echo $config->raiz ?>admin/js/bootstrap-select/bootstrap-select.min.js"></script>
	<script src="<?php echo $config->raiz ?>admin/js/bootstrap-select/i18n/defaults-es_CL.min.js"></script>

	<!-- CKEditor -->
	<script src="<?php echo $config->raiz ?>admin/ckeditor/ckeditor.js"></script>

	<link rel="stylesheet" type="text/css" href="<?php echo $config->raiz ?>admin/css/estilos.css">

	<?php
		echo '<base href="'. $config->raiz .'admin/" />';
		
		foreach ($config->cssFiles as $css) {
			echo $crlf.'	<link rel="stylesheet" type="text/css" href="'.$config->raiz.$css.'">';
		}
	?>

	<script>
		$(function () {
			$('[data-toggle="tooltip"]').tooltip();
		});
	</script>