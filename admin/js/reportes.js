$(document).ready(function () {
    $("#actualizando").hide();

    $("#frmReportes").submit(function() {
        aceptar();
    });
});

function buscarParametros() {
    if ($("#NumeRepo").val() == "") {
        $("#divParametros").html("");
        $("#btnAceptar").addClass("hide");
    }
    else {
        $("#actualizando").show();

        $.post("php/reportes.php",
            {
                "operacion": 1, 
                "NumeRepo": $("#NumeRepo").val()            
            },
            function (data, textStatus, jqXHR) {
                $("#actualizando").hide();
                $("#btnAceptar").removeClass("hide");

                $("#divParametros").html(data);
            },
            "html"
        );
    }
}

function aceptar() {
    $("#actualizando").show();

    var params = [];
    $("#divParametros input").each(function() {
        params.push(this.value);
    });

    $.post("php/reportes.php",
        {
            "operacion": 2, 
            "NumeRepo": $("#NumeRepo").val(),
            "Params": params
        },
        function (data, textStatus, jqXHR) {
            $("#actualizando").hide();

            $("#divDatos").html(data);
        },
        "html"
    );
}
