function generarcuotas() {
	var fecha = $("#hdnFechPago").val();
	var empresa = $("#cmbNumeEmpr").val();
	
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
				dato: {"Fecha": fecha, "Empresa": empresa}
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
	var filtro = "";

	if ($("#filFechPago").val() != "") {
		filtro+= "DATE_FORMAT(FechVenc, '%Y-%m') = '"+ $("#filFechPago").val() +"'";
	}

	
	if ($("#filNumeEmpr").val() != "-1") {
		if (filtro != "") {
			filtro+= " AND ";
		}

		filtro+= "NumeClie IN (SELECT NumeClie FROM clientes WHERE NumeEmpr = " + $("#filNumeEmpr").val() + ")";
	}
	
	if ($("#filNumeClie").val() != "-1") {
		if (filtro != "") {
			filtro+= " AND ";
		}

		filtro+= "NumeClie = " + $("#filNumeClie").val();
	}
	
	$("#divDatos").html("");
	$.post("php/tablaHandler.php",
		{ operacion: "10"
			, tabla: "cuotas"
			, filtro: filtro
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
				$('#filNumeClie').html(data['valor']);
				$("#actualizando").hide();
			}
	});
}