function generarcuotas() {
	var fecha = $("#hdnFechPago").val();
	var empresa = $("#cmbNumeEmpr").val();
	var cliente = $("#cmbNumeClie").val();
	
	if (fecha == '') {
		$("#divMsj").removeClass("alert-success");
		$("#divMsj").addClass("alert-danger");
		$("#txtHint").html("Debe establecer una fecha!");
		$("#divMsj").show();
	}
	else {
		$("#divMsj").hide();
		
		$("#actualizando").show();
		
		$.post('php/tablaHandler.php', { 
				operacion: '100', 
				tabla: 'cuotas', 
				field: 'Generar', 
				dato: {"Fecha": fecha, "Empresa": empresa, "Cliente": cliente}
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
	}
		
}

function listarCuotas() {
	$("#actualizando").show();
	var fechpago = $("#filFechPago").val();
	var empresa = $("#filNumeEmpr").val();
	var cliente = $("#filNumeClie").val();
	
	$("#divDatos").html("");
	$.post("php/tablaHandler.php",
		{ operacion: "10"
			, tabla: "cuotas"
			, filtro: {"FechPago": fechpago, "Empresa": empresa, "Cliente": cliente}
		},
		function(data) {
			$("#actualizando").hide();
			$("#divDatos").html(data);
		}
	);
}

function verCuota(strID) {
	location.href = "verCuota.php?id=" + strID;
}

function filtrarClientes(strNumeEmpr) {
	$("#actualizando").show();
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
				$('#cmbNumeClie').html(data['valor']);
				$("#actualizando").hide();
			}
	});
}

function filtrarClientesFiltro(strNumeEmpr) {
	$("#actualizando").show();
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
				$('#filNumeClie').html(data['valor']);
				$("#actualizando").hide();
			}
	});
}

