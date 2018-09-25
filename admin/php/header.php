	<div class="jumbotron" style="padding:10px 0;">
		<div class="container-fluid" style="min-height:50px;">
			<div class="row">
				<img class="logo" alt="Logo" src="<?php echo $config->logo?>" />
				<?php if ($config->showTitulo) { ?>
				<span class="titulo">
					<?php echo $config->titulo?>
				</span>
				<?php } ?>
			</div>
		</div>
		<?php if (isset($_SESSION['is_logged_in'])) { ?>
		<div class="absolute top5 right5 ucase">
			<small>
			<?php
				echo $_SESSION["NombUsua"];
			?>
			</small>
			<button class="btn btn-default btn-xs" onclick="location.href='logout.php';" data-toggle="tooltip" data-placement="bottom" title="Salir del sistema"><i class="fa fa-sign-out fa-fw"></i></button>
		</div>
		<?php }?>
	</div>