<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'datos.php';

    $strSalida = '';

    switch ($_POST["operacion"]) {
        case 1: //Solicitar parametros
            $strSQL = "SELECT NombRepo, SQLRepo, CantParams, TipoParam, Tabla, CampoNumero, CampoNombre, ColumnFoot, FooterEsMoneda";
            $strSQL.= " FROM reportes";
            $strSQL.= " WHERE NumeRepo = ". $_POST["NumeRepo"];

            $tabla = $config->cargarTabla($strSQL);

            $fila = $tabla->fetch_assoc();

            for ($I = 1; $I <= $fila["CantParams"]; $I++) {
                $strSalida.= '<div class="form-group form-group-sm">';

                switch ($fila["TipoParam"]) {
                    case '0'://Numero / Texto
                        $strSalida.= '<label for="param'.$I.'" class="control-label col-md-2 col-lg-2">Ingrese dato:</label>';
                        $strSalida.= '<div class="col-md-4 col-lg-4">';
                        $strSalida.= '<input type="text" id="param'.$I.'" class="form-control input-sm ucase">';
                        $strSalida.= '</div>';
                        break;
                    
                    case '1'://Fecha
                        $strSalida.= '<label for="param'.$I.'" class="control-label col-md-2 col-lg-2">Ingrese fecha:</label>';
                        $strSalida.= '<div class="col-md-4 col-lg-4">';
                        $strSalida.= $crlf.'<div class="input-group date margin-bottom-sm inp'.$I.'">';
                        $strSalida.= $crlf.'<input type="text" class="form-control input-sm" id="param'.$I.'"size="16" value="" readonly />';
                        $strSalida.= $crlf.'<span class="input-group-addon add-on clickable"><i class="fa fa-calendar fa-fw"></i></span>';
                        $strSalida.= $crlf.'</div>';
                        $strSalida.= $crlf.'<script type="text/javascript">';
                        $strSalida.= $crlf.'$(".inp'.$I.'").datetimepicker({';
                        $strSalida.= $crlf.'	language: "es",';
                        $strSalida.= $crlf.'	format: "yyyy-mm-dd",';
                        $strSalida.= $crlf.'	minView: 2,';
                        $strSalida.= $crlf.'	autoclose: true,';
                        $strSalida.= $crlf.'	todayBtn: true,';
                        $strSalida.= $crlf.'	todayHighlight: false,';
                        $strSalida.= $crlf.'	pickerPosition: "bottom-left"';
                        $strSalida.= $crlf.'});';
                        $strSalida.= $crlf.'</script>';
                        $strSalida.= '</div>';
                        break;

                    case 2://Mes
                        $strSalida.= '<label for="param'.$I.'" class="control-label col-md-2 col-lg-2">Ingrese mes:</label>';
                        $strSalida.= '<div class="col-md-4 col-lg-4">';
                        $strSalida.= $crlf.'<div class="input-group date margin-bottom-sm inp'.$I.'">';
                        $strSalida.= $crlf.'<input type="text" class="form-control input-sm" id="param'.$I.'"size="16" value="" required />';
                        $strSalida.= $crlf.'<span class="input-group-addon add-on clickable"><i class="fa fa-calendar fa-fw"></i></span>';
                        $strSalida.= $crlf.'</div>';
                        $strSalida.= $crlf.'<script type="text/javascript">';
                        $strSalida.= $crlf.'$(".inp'.$I.'").datetimepicker({';
                        $strSalida.= $crlf.'	language: "es",';
                        $strSalida.= $crlf.'	format: "yyyy-mm",';
                        $strSalida.= $crlf.'	minView: 3,';
                        $strSalida.= $crlf.'	startView: 3,';
                        $strSalida.= $crlf.'	autoclose: true,';
                        $strSalida.= $crlf.'	todayBtn: true,';
                        $strSalida.= $crlf.'	todayHighlight: false,';
                        $strSalida.= $crlf.'	pickerPosition: "bottom-left"';
                        $strSalida.= $crlf.'});';
                        $strSalida.= $crlf.'</script>';
                        $strSalida.= '</div>';
                        break;

                    case 3://Tabla
                        $strSalida.= '<label for="param'.$I.'" class="control-label col-md-2 col-lg-2">Seleccione:</label>';
                        $strSalida.= '<div class="col-md-4 col-lg-4">';
                        $strSalida.= '<select id="param'.$I.'" class="form-control input-sm ucase">';
                        $strSalida.= $config->cargarCombo($fila["Tabla"], $fila["CampoNumero"], $fila["CampoNombre"], '', $fila["CampoNombre"]);
                        $strSalida.= '</select>';
                        $strSalida.= '</div>';
                        break;
                }
                
                $strSalida.= '</div>';
                
            }
            
            break;

        case 2: //Armar reporte
            $strSQL = "SELECT NombRepo, SQLRepo, CantParams, TipoParam, Tabla, CampoNumero, CampoNombre, ColumnFoot, FooterEsMoneda";
            $strSQL.= " FROM reportes";
            $strSQL.= " WHERE NumeRepo = ". $_POST["NumeRepo"];

            $strSalida.= '';

            $tabla = $config->cargarTabla($strSQL);
            $fila = $tabla->fetch_assoc();

            $strSQL2 = $fila["SQLRepo"];

            for ($I = 1; $I <= $fila["CantParams"]; $I++) {
                $strSQL2 = str_ireplace('@param'.$I, $_POST["Params"][$I-1], $strSQL2);
            }

            $tabla = $config->cargarTabla($strSQL2);

            $strSalida.= $crlf.'<h4>'.$fila["NombRepo"].' '.(isset($_POST["Params"])? $_POST["Params"][0]: '').'</h4>';

            if ($tabla->num_rows > 0) {
                $strSalida.= $crlf.'<button class="btn btn-success" onclick="window.print();"><i class="fa fa-fw fa-print" aria-hidden="true"></i> Imprimir</button>';
                $strSalida.= $crlf.'<table class="table table-striped table-bordered table-hover table-condensed table-responsive marginTop10">';

                $footer = 0;

                for ($I = 0; $I < $tabla->num_rows; $I++) {
                    $fila2 = $tabla->fetch_assoc();

                    //Armo la fila encabezado
                    if ($I == 0) {
                        $strSalida.= $crlf.'<tr>';
                        foreach ($fila2 as $key => $value) {
                            $strSalida.= $crlf.'<th>'. $key .'</th>';
                        }
                        $strSalida.= $crlf.'</tr>';
                    }

                    //Cargo los datos
                    $strSalida.= $crlf.'<tr>';
                    $J = 1;
                    foreach ($fila2 as $key => $value) {
                        if ($J == $fila["ColumnFoot"]) {
                            $strSalida.= $crlf.'<td class="text-right">'.($fila["FooterEsMoneda"]=="1"?"$ ":"").$value.'</td>';
                            $footer+= $value;
                        }
                        else {
                            $strSalida.= $crlf.'<td>'.$value.'</td>';
                        }
                        $J++;                }
                    $strSalida.= $crlf.'</tr>';
                }

                //Cargo el footer
                if ($fila["ColumnFoot"] != "") {
                    $strSalida.= $crlf.'<tfoot><tr>';

                    if ($fila["ColumnFoot"] != "1") {
                        $strSalida.= $crlf.'<th colspan="'.(intval($fila["ColumnFoot"])-1).'"></th>';
                    }
                    
                    $strSalida.= $crlf.'<th class="text-right">';
                    $strSalida.= $crlf.($fila["FooterEsMoneda"]=="1"?"$ ":"").$footer;
                    $strSalida.= $crlf.'</th>';

                    $strSalida.= $crlf.'</tr></tfoot>';
                }
                $strSalida.= $crlf.'</table>';
            }
            else {
                $strSalida.= $crlf.'<h5>SIN DATOS PARA MOSTRAR</h5>';
            }

            break;
    }
    
    echo $strSalida;
}
?>