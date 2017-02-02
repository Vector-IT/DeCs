$("document").ready(function() {
	var modal = '';

	modal+= '<div id="mdlSeguimiento" class="modal fade" tabindex="-1" role="dialog">';
	modal+= '	<div class="modal-dialog" role="document">';
	modal+= '		<div class="modal-content">';
	modal+= '			<div class="modal-header">';
	modal+= '				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	modal+= '				<h4 class="modal-title">Crear seguimiento al cliente <span id="spanNombClie"></span></h4>';
	modal+= '			</div>';
	modal+= '			<div class="modal-body">';
	modal+= '				<form class="form-horizontal" method="post" onsubmit="return false;">';
	modal+= '					<div class="form-group form-group-sm ">';
	modal+= '						<label for="FechSegu" class="control-label col-md-4 col-md-offset-1">Fecha del seguimiento:</label>';
	modal+= '						<div class="col-md-5">';
	modal+= '							<div class="input-group date margin-bottom-sm inpFechSegu">';
	modal+= '								<input type="text" class="form-control input-sm " id="FechSegu" size="16" value="" readonly>';
	modal+= '								<span class="input-group-addon add-on clickable"><i class="fa fa-calendar fa-fw"></i></span>';
	modal+= '							</div>';
	modal+= '							<script type="text/javascript">';
	modal+= '							$(".inpFechSegu").datetimepicker({';
	modal+= '								language: "es",';
	modal+= '								format: "yyyy-mm-dd",';
	modal+= '								minView: 2,';
	modal+= '								autoclose: true,';
	modal+= '								todayBtn: true,';
	modal+= '								todayHighlight: false,';
	modal+= '								pickerPosition: "bottom-left"';
	modal+= '								});';
	modal+= '							</script>';
	modal+= '						</div>';
	modal+= '					</div>';
	modal+= '				</form>';
	modal+= '			</div>';
	modal+= '			<div class="modal-footer">';
	modal+= '				<div id="actualizandoModal" class="alert alert-info text-left" role="alert">';
	modal+= '					<i class="fa fa-refresh fa-fw fa-spin"></i> Actualizando datos, por favor espere...';
	modal+= '				</div>';
	modal+= '				<div id="divMsjModal" class="alert alert-danger text-left" role="alert">';
	modal+= '					<span id="txtHintModal">Info</span>';
	modal+= '				</div>';
	modal+= '				<input type="hidden" id="NumeClieSegu" />';
	modal+= '				<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>';
	modal+= '				<button type="button" class="btn btn-primary" onclick="guardarSeguimiento()">Guardar</button>';
	modal+= '			</div>';
	modal+= '		</div>';
	modal+= '	</div>';
	modal+= '</div>';

	$("body").append(modal);
});

function generarCuotas() {
	location.href = "cuotas.php";
}

function verCuotas(strID) {
	location.href = "cuotas.php?NumeClie=" + strID;
}

function crearSeguimiento(strID) {
	$("#spanNombClie").html($("#NombClie"+strID).html());
	$("#NumeClieSegu").val(strID);

	$("#divMsjModal").hide();
	$("#actualizandoModal").hide();
	$('#mdlSeguimiento').modal();
}

function guardarSeguimiento() {
	var fecha = $("#FechSegu").val();
	var cliente = $("#NumeClieSegu").val();

	if (fecha == '') {
		$("#divMsjModal").removeClass("alert-success");
		$("#divMsjModal").addClass("alert-danger");
		$("#txtHintModal").html("Debe establecer una fecha!");
		$("#divMsjModal").show();
	}
	else {
		$("#divMsjModal").hide();

		$("#actualizandoModal").show();

		$.post('php/tablaHandler.php', {
				operacion: '100',
				tabla: 'seguimientos',
				field: 'Generar',
				dato: {"Fecha": fecha, "Cliente": cliente}
			},
			function(data) {
				$("#actualizandoModal").hide();

				if (data['valor']['estado'] === true) {
					$("#txtHint").html("Seguimiento creado!");

					$("#divMsjModal").removeClass("alert-danger");
					$("#divMsjModal").addClass("alert-success");

					$("#divMsjModal").fadeIn(function (){
						$('#mdlSeguimiento').modal('toggle');
					});
				}
				else {
					//Error
					$("#txtHint").html(data['valor']['estado']);

					$("#divMsjModal").removeClass("alert-success");
					$("#divMsjModal").addClass("alert-danger");
					$("#divMsjModal").show();
				}
			}
		);
	}
}