var cmbCliente;
var modal;

$(document).ready(function() {
	$('#modalFiltros').on('shown.bs.modal', function() {
		$("#divMsjModal").hide();

		switch (modal) {
			case 'Generar':
				$(".btnLimpiarCal").hide();
				$("#CantCuot").show();
				break;

			case 'Imprimir': 
				$(".btnLimpiarCal").hide();
				$("#CantCuot").hide();
				break;

			case 'Buscar':
				$(".btnLimpiarCal").show();
				$("#CantCuot").hide();
				break;
		}
	});
	
	filtrarClientes($('#filEmpresa').val(), '#filCliente');
	$('#Fecha').val('');
});

function abrirModal(objeto) {
	modal = objeto;

	$("#operacion").html(modal);

	$("#modalFiltros").modal();
}

function cerrarModal() {
	switch (modal) {
		case 'Generar':
			return generarCuotas();

		case 'Imprimir': 
			return imprimirCuotas();

		case 'Buscar':
			return listarCuotas();
	}
}

function generarCuotas() {
	var fecha = $("#filFecha").val();
	var empresa = $("#filEmpresa").val();
	var cliente = $("#filCliente").val();
	var cantCuotas = parseInt($("#filCuotas").val());
	
	if (fecha == '') {
		$("#divMsjModal").addClass("alert-danger");
		$("#txtHintModal").html("Debe establecer un mes!");
		$("#divMsjModal").show();
		return false;
	}
	else {
		$("#divMsj").hide();
		
		$("#actualizando").show();
		
		$.post('php/tablaHandler.php', { 
				operacion: '100', 
				tabla: 'cuotas', 
				field: 'Generar', 
				dato: {"Fecha": fecha, "Empresa": empresa, "Cliente": cliente, "Cantidad": cantCuotas}
			}, 
			function(data) {
				$("#txtHint").html(data['valor']);
				
				if (data['valor'].indexOf("Error") == -1) {
					//Exito
					
					$("#divMsj").removeClass("alert-danger");
					$("#divMsj").addClass("alert-success");
				}
				else {
					//Error
					
					$("#divMsj").removeClass("alert-success");
					$("#divMsj").addClass("alert-danger");

				}
		
				$("#actualizando").hide();
				$("#divMsj").show();
			}
		);		
		$("#modalFiltros").modal('hide');
	}
}

function listarCuotas() {
	var fecha = $("#filFecha").val();
	var empresa = $("#filEmpresa").val();
	var cliente = $("#filCliente").val();
	
	$("#actualizando").show();

	$("#divDatos").html("");
	$.post("php/tablaHandler.php",
		{ operacion: "10"
			, tabla: "cuotas"
			, filtro: {"Fecha": fecha, "Empresa": empresa, "Cliente": cliente}
		},
		function(data) {
			$("#actualizando").hide();
			$("#divDatos").html(data);
		}
	);

	$("#modalFiltros").modal('hide');
}

function imprimirCuotas() {
	var fecha = $("#filFecha").val();
	var empresa = $("#filEmpresa").val();
	var cliente = $("#filCliente").val();
	var plantilla = $("#filPlantilla").val();

	if (fecha == "") {
		$("#divMsjModal").addClass("alert-danger");
		$("#txtHintModal").html("Debe establecer un mes!");
		$("#divMsjModal").show();
		return false;
	}

	$("#modalFiltros").modal('hide');

	$("#actualizando").show();
	$.get(plantilla,
		{
			'filFecha': fecha,
			'filEmpresa': empresa,
			'filCliente': cliente
		},
		function(data) {
			$("#actualizando").hide();
			$("#divMsj").removeClass("alert-danger");
			$("#divMsj").addClass("alert-success");
			
			$("#txtHint").html(data);

			location.href = "explorar.php";
		}
	);
}

function filtrarClientes(strNumeEmpr, combo) {
	cmbCliente = combo;

	$.ajax({
		url: 'php/tablaHandler.php',
		type: 'post',
		async: true,
		data: {	
			operacion: '100', 
			tabla: 'cuotas', 
			field: "NumeEmpr", 
			dato: strNumeEmpr 
		}, 
		success: 
			function(data) {
				$(cmbCliente).html(data['valor']['clientes']);
				$('#filPlantilla').val(data['valor']['plantilla']);
			}
	});
}

function verCuota(strID) {
	location.href = "verCuota.php?id=" + strID;
}


function verCliente(strID) {
	var idCliente = $("#NumeClie" + strID).val();
	
	location.href = "verCliente.php?id=" + idCliente;
}