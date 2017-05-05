$(document).ready(function() {
	$("#actualizando").hide();
	$("#divMsj").hide();
	$("#frmCarga").submit(function() {aceptarcarga();});
});

function aceptarcarga() {
	$("#btnAceptar, #btnCancelar").addClass("disabled");
	$("#actualizando").show();

	var frmData = new FormData();
	frmData.append("operacion", 100);
	frmData.append("tabla", 'clientes');
	frmData.append("field", 'CSV');

	frmData.append("NumeEmpr", $("#NumeEmpr").val());
	frmData.append("Archivo", $("#Archivo").get(0).files[0]);

	if (window.XMLHttpRequest)
	{// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp = new XMLHttpRequest();
	}
	else
	{// code for IE6, IE5
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	
	xmlhttp.onreadystatechange = function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			var respuesta = JSON.parse(xmlhttp.responseText);

			if (respuesta.valor.estado) {
				$("#txtHint").html("Carga Exitosa!<br>"+ respuesta.valor.mensaje);

				$("#divMsj").removeClass("alert-danger");
				$("#divMsj").addClass("alert-success");

				$("#divMsj").show();

				if ($("#CantCuot").val() > 0) {
					$("#txtHint").append("<br>----------------------<br>");
					$("#txtHint").append('Generando cuotas<br>');
					$("#txtHint").append('<div id="txtCuotas"></div>');

					respuesta.valor.clientes.forEach(generarCuotas);
				}

				$('#frmCarga')[0].reset();
				$("#actualizando").hide();

				$("#btnAceptar, #btnCancelar").removeClass("disabled");
			}
			else {
				$("#txtHint").html("Error en la Carga!<br>"+ respuesta.valor.mensaje);
				$("#divMsj").removeClass("alert-success");
				$("#divMsj").addClass("alert-danger");

				$("#actualizando").hide();						
				$("#divMsj").show();
				$("#btnAceptar, #btnCancelar").removeClass("disabled");
			}
		}
	};
	
	xmlhttp.open("POST","php/tablaHandler.php",true);
	xmlhttp.send(frmData);
}

function generarCuotas(cliente, index) {
	var fecha = $("#filFecha").val();
	var empresa = $("#NumeEmpr").val();
	var cantCuotas = $("#CantCuot").val();
	
	$.post('php/tablaHandler.php', { 
			operacion: '100', 
			tabla: 'cuotas', 
			field: 'Generar', 
			dato: {"Fecha": fecha, "Empresa": empresa, "Cliente": cliente, "Cantidad": cantCuotas}
		}, 
		function(data) {
			if (data['valor'].indexOf("Error") == -1) {
				//Exito
				if (index == 0) {
					$("#txtCuotas").html(index + 1 + " Cuota generada");
				}
				else {
					$("#txtCuotas").html(index + 1 + " Cuotas generadas");
				}
				
				$("#divMsj").removeClass("alert-danger");
				$("#divMsj").addClass("alert-success");
			}
			else {
				//Error
				
				$("#divMsj").removeClass("alert-success");
				$("#divMsj").addClass("alert-danger");
			}
		}
	);		
}
