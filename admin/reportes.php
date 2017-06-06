<?php
    namespace VectorForms;

    ini_set("log_errors", 1);
    ini_set("error_log", "php-error.log");

    session_start();
    require_once 'php/datos.php';

    $urlLogin = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/login.php?returnUrl=" . $_SERVER['REQUEST_URI'];
    $urlIndex = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/";
    
    if (!isset($_SESSION['is_logged_in'])) {
        header($urlLogin);
        die();
    }
    
    header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
    header("Pragma: no-cache"); // HTTP 1.0.
    header("Expires: 0"); // Proxies.
?>
<!DOCTYPE html>
<html>
<head>
    <?php
        require_once 'php/linksHeader.php';
    ?>
    <script src="js/reportes.js"></script>
</head>
<body>
    <?php
        $config->crearMenu();
        
        require_once 'php/header.php';
    ?>
    
    <div class="container-fluid">
        <div class="page-header">
            <h2><i class="fa fa-fw fa-slideshare" aria-hidden="true"></i> Reportes</h2>
        </div>

        <form id="frmReportes" class="form-horizontal marginTop20" method="post" onsubmit="return false;">
            <div class="form-group form-group-sm">
                <label for="NumeRepo" class="control-label col-md-2 col-lg-2">Reporte:</label>
                <div class="col-md-4 col-lg-4">
                    <select id="NumeRepo" class="form-control input-sm ucase" onchange="buscarParametros();">
                    <?php echo $config->cargarCombo("reportes", "NumeRepo", "NombRepo", "NumeEsta = 1", "NombRepo", '', true);?>
                    </select>
                </div>
            </div>
            <div id="divParametros"></div>
            <div class="form-group">
                <div class="col-md-offset-2 col-lg-offset-2 col-md-4 col-lg-4 text-right">
                    <button id="btnAceptar" type="submit" class="btn btn-sm btn-primary hide"><i class="fa fa-check fa-fw" aria-hidden="true"></i> Aceptar</button>
                </div>
            </div>
        </form>
            
        <div id="actualizando" class="alert alert-info" role="alert">
            <i class="fa fa-refresh fa-fw fa-spin"></i> Actualizando datos, por favor espere...
        </div>
        
        <div id="divDatos" class="marginTop40">
        </div>
    </div>
    
    <?php
        require_once 'php/footer.php';
    ?>  
</body>
</html>
