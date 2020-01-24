<?php
	session_start();
	require_once 'php/datos.php';
	require_once 'fpdf/PDF_MemImage.php';
	require_once 'barcode/barcodePdf.php';

	$urlLogin = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/login.php?returnUrl=" . $_SERVER['REQUEST_URI'];
	$urlIndex = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/";

	if (!isset($_SESSION['is_logged_in'])){
		header($urlLogin);
		die();
	}

	ini_set('max_execution_time', 300);

	$filEmpresa = $_REQUEST["filEmpresa"];
	$filCliente = $_REQUEST["filCliente"];

	$filFecha = $_REQUEST["filFecha"];

	$filtro = "";
	if ($filEmpresa != '-1' && $filEmpresa != '') {
		$filtro.= $crlf."c.NumeEmpr = ". $filEmpresa;
	}

	if ($filCliente != '-1' && $filCliente != '') {
		if ($filtro != "") {
			$filtro.= $crlf." AND ";
		}

		$filtro.= $crlf."c.NumeClie = ". $filCliente;
	}

	$strSQL = "SELECT c.NumeClie, c.NumeSoli, c.NombClie, c.DireClie, c.NombBarr, c.NombLoca, c.CodiPost, p.NombProv, c.ValoMovi, c.CodiBarr, c.CodiPagoElec,";
	$strSQL.= $crlf." e.NombEmpr, e.ImagEmpr, e.DireEmpr, e.TeleEmpr, e.MailEmpr, e.WebsEmpr, e.ObsePago";
	$strSQL.= $crlf." FROM clientes c";
	$strSQL.= $crlf." INNER JOIN provincias p ON c.NumeProv = p.NumeProv";
	$strSQL.= $crlf." INNER JOIN empresas e ON c.NumeEmpr = e.NumeEmpr";
	if ($filtro != "") {
		$strSQL.= $crlf." WHERE " . $filtro;
	}

	$tbClientes = $config->cargarTabla($strSQL);


	if ($tbClientes) {
		$strSQL = "UPDATE clientes c SET c.FechImpr = STR_TO_DATE(DATE_FORMAT(SYSDATE(), '%d/%m/%Y'), '%d/%m/%Y')";
		if ($filtro != "") {
			$strSQL.= $crlf." WHERE " . $filtro;
		}

		$config->ejecutarCMD($strSQL);
	}

	$blnRegVar = true;
	$I = 0;

	while ($cliente = $tbClientes->fetch_assoc()) {
		$strSQL = "SELECT p.NumePago,";
		// $strSQL.= $crlf." p.NumeCuot,";
		$strSQL.= $crlf."DATE_FORMAT(p.FechVenc1, '%d/%m/%Y') FechVenc1,";
		$strSQL.= $crlf."DATE_FORMAT(p.FechVenc2, '%d/%m/%Y') FechVenc2,";
		$strSQL.= $crlf."DATE_FORMAT(p.FechVenc3, '%d/%m/%Y') FechVenc3,";
		$strSQL.= $crlf."DATE_FORMAT(p.FechVenc1, '%M %Y') NumeCuot,";
		$strSQL.= $crlf."CONCAT(UPPER(LEFT(DATE_FORMAT(p.FechVenc1, '%M %Y'),1)),SUBSTR(DATE_FORMAT(p.FechVenc1, '%M %Y'),2)) NumeCuot,";
		$strSQL.= $crlf."p.ImpoPura,";
		$strSQL.= $crlf."p.ImpoAdmi,";
		$strSQL.= $crlf."p.ImpoGest,";
		$strSQL.= $crlf."p.ImpoOtro,";
		$strSQL.= $crlf."p.ImpoPura + p.ImpoAdmi + p.ImpoGest + p.ImpoOtro ImpoTota,";
		$strSQL.= $crlf."p.ImpoVenc1,";
		$strSQL.= $crlf."p.ImpoVenc2,";
		$strSQL.= $crlf."p.ImpoVenc3,";
		$strSQL.= $crlf."DATE_FORMAT(p.FechCuot, '%d/%m/%Y') FechCuot,";
		$strSQL.= $crlf."p.CodiBarr";
		$strSQL.= $crlf."FROM pagos p";
		$strSQL.= $crlf."WHERE p.NumeClie = ". $cliente["NumeClie"];
		$strSQL.= $crlf."AND p.FechVenc1 >= STR_TO_DATE('{$filFecha}-01', '%Y-%m-%d')";
		$strSQL.= $crlf."ORDER BY p.NumeClie, p.NumeCuot";
		$strSQL.= $crlf."LIMIT 1;";
		
		$tbCuotas = $config->cargarTabla($strSQL);

		if (!$tbCuotas) {
			continue;
		}

		$pdf = new PDF_MemImage('P', 'mm', 'A4', $blnRegVar);
		$pdf->SetCreator("Vector-IT");

		if ($blnRegVar) {
			$blnRegVar = false;
		}

		$cuota = $tbCuotas->fetch_assoc();
		if ($cuota == null) {
			continue;
		}
		$I++;

		$pdf->AddPage();

	//Encabezado
		$pdf->Image($cliente["ImagEmpr"], 5, 5, 0, 25);
		$pdf->Image('img/decs_blanco.png', $pdf->GetPageWidth() - 45, 5, 0, 25);
		$pdf->SetXY(($pdf->GetPageWidth() / 2) - 25, 10);
		$pdf->SetFont('Times', '', 12);
		$pdf->Cell(50, 10, "TALON DE PAGO", 1, 0, 'C');

	//Cuadro de página
		$pdf->Rect(5, 35, $pdf->GetPageWidth() - 10, $pdf->GetPageHeight() - 65);

	//Talon 1
		$pdf->SetXY(0, 0);
		$pdf->SetFont('Times', 'B', 8);
		
		//NumeSoli
		$pdf->Text(18, 39, utf8_decode('Nº de referencia'));
		$pdf->Line(18, 40, 38, 40);
		$pdf->Line(18, 40, 18, 44);

		$pdf->Text(20, 43, $cliente["NumeSoli"]);

		//NumeCuot
		$pdf->Text(58, 39, utf8_decode('Mes'));
		$pdf->Line(58, 40, 78, 40);
		$pdf->Line(58, 40, 58, 44);

		$pdf->Text(60, 43, $cuota["NumeCuot"]);

		//FechVenc1
		$pdf->Text(99, 40, utf8_decode('1º Venc.'));
		$pdf->Line(99, 41, 115, 41);
		$pdf->Line(99, 41, 99, 45);

		$pdf->Text(101, 44, $cuota["FechVenc1"]);

		//FechVenc2
		if ($cuota["FechVenc2"] != '') {
			$pdf->Text(119, 40, utf8_decode('2º Venc.'));
			$pdf->Line(119, 41, 135, 41);
			$pdf->Line(119, 41, 119, 45);
			
			$pdf->Text(121, 44, $cuota["FechVenc2"]);
		}
		//FechVenc3
		if ($cuota["FechVenc3"] != '') {
			$pdf->Text(137, 40, utf8_decode('3º Venc.'));
			$pdf->Line(137, 41, 153, 41);
			$pdf->Line(137, 41, 137, 45);

			$pdf->Text(139, 44, $cuota["FechVenc3"]);
		}
		//ImpoTota1
		$pdf->Text(101, 49, "$ ".number_format(floatval($cuota["ImpoVenc1"]), 2));
		//ImpoTota2
		if ($cuota["FechVenc2"] != '') {
			$pdf->Text(121, 49, "$ ".number_format(floatval($cuota["ImpoVenc2"]), 2));
		}
		//ImpoTota3
		if ($cuota["FechVenc3"] != '') {
			$pdf->Text(139, 49, "$ ".number_format(floatval($cuota["ImpoVenc3"]), 2));
		}


		//NumePago
		$pdf->Text(91, 58, utf8_decode('Nº Comprobante'));
		$pdf->Line(91, 59, 111, 59);
		$pdf->Line(91, 59, 91, 63);

		$pdf->Text(93, 62, $cuota["NumePago"]);

		//FechCuot
		$pdf->Text(119, 58, utf8_decode('F. Emisión'));
		$pdf->Line(119, 59, 139, 59);
		$pdf->Line(119, 59, 119, 63);

		$pdf->Text(121, 62, $cuota["FechCuot"]);

		//ValoMovi
		// $pdf->Text(141, 58, utf8_decode('Producto'));
		// $pdf->Line(141, 59, 151, 59);
		// $pdf->Line(141, 59, 141, 63);

		// $pdf->Text(143, 62, $cliente["ValoMovi"]);

		//NombClie
		$pdf->Text(21, 50, utf8_decode('Nombre y Dirección'));
		$pdf->Line(21, 51, 75, 51);
		$pdf->Line(21, 51, 21, 67);

		$pdf->Text(23, 54, utf8_decode($cliente["NombClie"]));
		//DireClie
		$pdf->Text(23, 57, utf8_decode($cliente["DireClie"]));
		//NombBarr
		$pdf->Text(23, 60, utf8_decode($cliente["NombBarr"]));
		//NombLoca - CodiPost
		$pdf->Text(23, 63, utf8_decode($cliente["NombLoca"]. " - " .$cliente["CodiPost"]));
		//NombProv
		$pdf->Text(23, 66, utf8_decode($cliente["NombProv"]));

		//Importes
		$pdf->Text(190, 46, utf8_decode('Importes'));
		$pdf->Line(160, 47, $pdf->GetPageWidth()-6, 47);
		$pdf->Line(160, 47, 160, 68);

		//ImpoPura
		$pdf->Text(161, 51, 'Expensas:');
		$pdf->SetXY(185, 50);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoPura"]), 2), 0, 0, 'R');

		//ImpoAdmi
		$pdf->Text(161, 55, 'Gastos Admin.:');
		$pdf->SetXY(185, 54);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoAdmi"]), 2), 0, 0, 'R');

		//ImpoGest
		$pdf->Text(161, 58, 'Gastos Bancarios:');
		$pdf->SetXY(185, 57);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoGest"]), 2), 0, 0, 'R');
		
		//ImpoOtro
		$pdf->Text(161, 61, 'Otros:');
		$pdf->SetXY(185, 60);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoOtro"]), 2), 0, 0, 'R');
		//ImpoTota
		$pdf->Text(161, 66, 'Total:');
		$pdf->SetXY(185, 66);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoTota"]), 2), 0, 0, 'R');

		//CodiBarr
		$pdf->GDImage(barcode("", $cuota["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 70, 135, 7);
		$pdf->Text(50, 80, $cuota["CodiBarr"]);

		$pdf->Line(10, 82, $pdf->GetPageWidth() - 10, 82);

	//Observaciones
		if ($cliente["ObsePago"] != '') {
			$pdf->SetFont('Times', 'B', 8);
			$pdf->Text(10, 88, utf8_decode('Notificaciones:'));

			$pdf->SetFont('Times', '', 8);
			$pdf->SetXY(30, 88);
			$pdf->MultiCell(228, 3, utf8_decode($cliente["ObsePago"]));
		}


	//Codigos
		fin:
		//CodiBarrClie
		$pdf->Text(7, 228, utf8_decode('Código sin factura'));
		$pdf->GDImage(barcode("", $cliente["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 231, 135, 7);
		$pdf->Text(50, 241, $cliente["CodiBarr"]);

		//CodiPagoElec
		$pdf->Text(7, 251, utf8_decode('Código de pago electrónico'));
		$pdf->Text(27, 255, $cliente["CodiPagoElec"]);

	//Footer
		$pdf->Rect(5, $pdf->GetPageHeight() - 25, $pdf->GetPageWidth() - 10, 20);
		$pdf->SetAutoPageBreak(false, 5);

		$pdf->SetFont('Times', '', 14);
		$pdf->SetXY(5, $pdf->GetPageHeight() - 18);
		$pdf->Cell(0, 0, utf8_decode($cliente["DireEmpr"] .' - '. $cliente["TeleEmpr"]), 0, 0, 'C');

		$pdf->SetXY(5, $pdf->GetPageHeight() - 12);
		$pdf->Cell(0, 0, utf8_decode($cliente["MailEmpr"] .' / '. $cliente["WebsEmpr"]), 0, 0, 'C');

		//Creo la ruta de directorios
		if (!file_exists('pdfs') && !is_dir('pdfs')) {
			mkdir('pdfs');         
		}
		if (!file_exists('pdfs/'.$cliente["NombEmpr"]) && !is_dir('pdfs/'.$cliente["NombEmpr"])) {
			mkdir('pdfs/'.$cliente["NombEmpr"]);
		}
		if (!file_exists('pdfs/'.$cliente["NombEmpr"].'/'.$filFecha) && !is_dir('pdfs/'.$cliente["NombEmpr"].'/'.$filFecha)) {
			mkdir('pdfs/'.$cliente["NombEmpr"].'/'.$filFecha);
		} 

		//Si ya se generó el pdf lo elimino
		if (file_exists('pdfs/'.$cliente["NombEmpr"].'/'.$filFecha.'/'.$cliente["NumeSoli"].'-'.$cliente["NombClie"].'.pdf')) {
			unlink('pdfs/'.$cliente["NombEmpr"].'/'.$filFecha.'/'.$cliente["NumeSoli"].'-'.$cliente["NombClie"].'.pdf');
		}

		//Grabo el nuevo pdf
		$pdf->Output('F', 'pdfs/'.$cliente["NombEmpr"].'/'.$filFecha.'/'.$cliente["NumeSoli"].'-'.$cliente["NombClie"].'.pdf');
		//$pdf->Output();
		$pdf = null;

	}
	echo $I." PDFs generados";
	//header("Location:explorar.php");
?>