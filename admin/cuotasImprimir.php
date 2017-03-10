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

	$filEmpresa = $_REQUEST["filEmpresa"];
	$filCliente = $_REQUEST["filCliente"];

	$filFecha = $_REQUEST["filFecha"];

	$filtro = "";
	if ($filEmpresa != '-1') {
		$filtro.= $crlf."c.NumeEmpr = ". $filEmpresa;
	}

	if ($filCliente != '-1') {
		if ($filtro != "") {
			$filtro.= $crlf." AND ";
		}

		$filtro.= $crlf."c.NumeClie = ". $filCliente;
	}

	$strSQL = "SELECT c.NumeClie, c.NumeSoli, c.NombClie, c.DireClie, c.NombBarr, c.NombLoca, c.CodiPost, p.NombProv, c.ValoMovi, c.CodiBarr, c.CodiPagoElec";
	$strSQL.= $crlf." FROM clientes c";
	$strSQL.= $crlf." INNER JOIN provincias p ON c.NumeProv = p.NumeProv";
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

	$pdf = new PDF_MemImage('P', 'mm', 'A4');
	
	while ($cliente = $tbClientes->fetch_assoc()) {

		$strSQL = "SELECT p.NumePago,";
		$strSQL.= $crlf." p.NumeCuot,";
		$strSQL.= $crlf." DATE_FORMAT(p.FechVenc1, '%d/%m/%Y') FechVenc1,";
		$strSQL.= $crlf." DATE_FORMAT(p.FechVenc2, '%d/%m/%Y') FechVenc2,";
		$strSQL.= $crlf." DATE_FORMAT(p.FechVenc3, '%d/%m/%Y') FechVenc3,";
		$strSQL.= $crlf." p.ImpoPura,";
		$strSQL.= $crlf." p.ImpoAdmi,";
		$strSQL.= $crlf." p.ImpoGest,";
		$strSQL.= $crlf." p.ImpoOtro,";
		$strSQL.= $crlf." p.ImpoPura + p.ImpoAdmi + p.ImpoGest + p.ImpoOtro ImpoTota,";
		$strSQL.= $crlf." DATE_FORMAT(p.FechCuot, '%d/%m/%Y') FechCuot,";
		$strSQL.= $crlf." p.CodiBarr";
		$strSQL.= $crlf." FROM pagos p";
		$strSQL.= $crlf." WHERE p.NumeClie = ". $cliente["NumeClie"];
		$strSQL.= $crlf." AND p.FechVenc1 >= STR_TO_DATE('{$filFecha}-01', '%Y-%m-%d')";
		$strSQL.= $crlf." ORDER BY p.NumeClie, p.NumeCuot";
		$strSQL.= $crlf." LIMIT 4";
		
		$tbCuotas = $config->cargarTabla($strSQL);

		$pdf->AddPage();

		if (!$tbCuotas) {
			continue;
		}

	//Talon 1
		$cuota = $tbCuotas->fetch_assoc();
		if ($cuota == null) {
			continue;
		}

		$pdf->SetFont('Times', 'B', 8);
		//NumeSoli
		$pdf->Text(20, 43, $cliente["NumeSoli"]);
		//NumeCuot
		$pdf->Text(60, 43, $cuota["NumeCuot"]);
		//FechVenc1
		$pdf->Text(101, 44, $cuota["FechVenc1"]);
		//FechVenc2
		if ($cuota["FechVenc2"] != '') {
			$pdf->Text(121, 44, $cuota["FechVenc2"]);
		}
		//FechVenc3
		if ($cuota["FechVenc3"] != '') {
			$pdf->Text(139, 44, $cuota["FechVenc3"]);
		}
		//ImpoTota1
		$pdf->Text(101, 49, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota2
		$pdf->Text(121, 49, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota3
		$pdf->Text(139, 49, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//NumePago
		$pdf->Text(93, 62, $cuota["NumePago"]);
		//FechCuot
		$pdf->Text(123, 62, $cuota["FechCuot"]);
		//ValoMovi
		$pdf->Text(145, 62, $cliente["ValoMovi"]);
		//NombClie
		$pdf->Text(23, 54, utf8_decode($cliente["NombClie"]));
		//DireClie
		$pdf->Text(23, 57, utf8_decode($cliente["DireClie"]));
		//NombBarr
		$pdf->Text(23, 60, utf8_decode($cliente["NombBarr"]));
		//NombLoca - CodiPost
		$pdf->Text(23, 63, utf8_decode($cliente["NombLoca"]. " - " .$cliente["CodiPost"]));
		//NombProv
		$pdf->Text(23, 66, utf8_decode($cliente["NombProv"]));
		//ImpoPura
		$pdf->SetXY(185, 50);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoPura"]), 2), 0, 0, 'R');
		//ImpoAdmi
		$pdf->SetXY(185, 54);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoAdmi"]), 2), 0, 0, 'R');
		//ImpoGest
		$pdf->SetXY(185, 57);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoGest"]), 2), 0, 0, 'R');
		//ImpoOtro
		$pdf->SetXY(185, 60);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoOtro"]), 2), 0, 0, 'R');
		//ImpoTota
		$pdf->Text(159, 66, 'Total:');
		$pdf->SetXY(185, 66);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoTota"]), 2), 0, 0, 'R');

		//CodiBarr
		$pdf->GDImage(barcode("", $cuota["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 72, 135, 7);
		$pdf->Text(50, 82, $cuota["CodiBarr"]);

	//Talon 2
	// Y = Talon1 + 48
		$cuota = $tbCuotas->fetch_assoc();
		if ($cuota == null) {
			goto fin;
		}

		//NumeSoli
		$pdf->Text(20, 92, $cliente["NumeSoli"]);
		//NumeCuot
		$pdf->Text(60, 92, $cuota["NumeCuot"]);
		//FechVenc1
		$pdf->Text(101, 92, $cuota["FechVenc1"]);
		//FechVenc2
		if ($cuota["FechVenc2"] != '') {
			$pdf->Text(121, 92, $cuota["FechVenc2"]);
		}
		//FechVenc3
		if ($cuota["FechVenc3"] != '') {
			$pdf->Text(139, 92, $cuota["FechVenc3"]);
		}
		//ImpoTota1
		$pdf->Text(101, 97, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota2
		$pdf->Text(121, 97, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota3
		$pdf->Text(139, 97, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//NumePago
		$pdf->Text(93, 111, $cuota["NumePago"]);
		//FechCuot
		$pdf->Text(123, 111, $cuota["FechCuot"]);
		//ValoMovi
		$pdf->Text(145, 111, $cliente["ValoMovi"]);
		//NombClie
		$pdf->Text(23, 100, utf8_decode($cliente["NombClie"]));
		//DireClie
		$pdf->Text(23, 103, utf8_decode($cliente["DireClie"]));
		//NombBarr
		$pdf->Text(23, 106, utf8_decode($cliente["NombBarr"]));
		//NombLoca - CodiPost
		$pdf->Text(23, 109, utf8_decode($cliente["NombLoca"]. " - " .$cliente["CodiPost"]));
		//NombProv
		$pdf->Text(23, 112, utf8_decode($cliente["NombProv"]));
		//ImpoPura
		$pdf->SetXY(185, 96);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoPura"]), 2), 0, 0, 'R');
		//ImpoAdmi
		$pdf->SetXY(185, 100);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoAdmi"]), 2), 0, 0, 'R');
		//ImpoGest
		$pdf->SetXY(185, 103);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoGest"]), 2), 0, 0, 'R');
		//ImpoOtro
		$pdf->SetXY(185, 106);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoOtro"]), 2), 0, 0, 'R');
		//ImpoTota
		$pdf->Text(159, 112, 'Total:');
		$pdf->SetXY(185, 112);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoTota"]), 2), 0, 0, 'R');

		//CodiBarr
		$pdf->GDImage(barcode("", $cuota["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 119, 135, 7);
		$pdf->Text(50, 129, $cuota["CodiBarr"]);

	//Talon 3
	// Y = Talon2 + 45
		$cuota = $tbCuotas->fetch_assoc();
		if ($cuota == null) {
			goto fin;
		}

		//NumeSoli
		$pdf->Text(20, 138, $cliente["NumeSoli"]);
		//NumeCuot
		$pdf->Text(60, 138, $cuota["NumeCuot"]);
		//FechVenc1
		$pdf->Text(101, 138, $cuota["FechVenc1"]);
		//FechVenc2
		if ($cuota["FechVenc2"] != '') {
			$pdf->Text(121, 138, $cuota["FechVenc2"]);
		}
		//FechVenc3
		if ($cuota["FechVenc3"] != '') {
			$pdf->Text(139, 138, $cuota["FechVenc3"]);
		}
		//ImpoTota1
		$pdf->Text(101, 143, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota2
		$pdf->Text(121, 143, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota3
		$pdf->Text(139, 143, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//NumePago
		$pdf->Text(93, 158, $cuota["NumePago"]);
		//FechCuot
		$pdf->Text(123, 158, $cuota["FechCuot"]);
		//ValoMovi
		$pdf->Text(145, 158, $cliente["ValoMovi"]);
		//NombClie
		$pdf->Text(23, 147, utf8_decode($cliente["NombClie"]));
		//DireClie
		$pdf->Text(23, 150, utf8_decode($cliente["DireClie"]));
		//NombBarr
		$pdf->Text(23, 153, utf8_decode($cliente["NombBarr"]));
		//NombLoca - CodiPost
		$pdf->Text(23, 156, utf8_decode($cliente["NombLoca"]. " - " .$cliente["CodiPost"]));
		//NombProv
		$pdf->Text(23, 159, utf8_decode($cliente["NombProv"]));
		//ImpoPura
		$pdf->SetXY(185, 142);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoPura"]), 2), 0, 0, 'R');
		//ImpoAdmi
		$pdf->SetXY(185, 146);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoAdmi"]), 2), 0, 0, 'R');
		//ImpoGest
		$pdf->SetXY(185, 149);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoGest"]), 2), 0, 0, 'R');
		//ImpoOtro
		$pdf->SetXY(185, 152);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoOtro"]), 2), 0, 0, 'R');
		//ImpoTota
		$pdf->Text(159, 158, 'Total:');
		$pdf->SetXY(185, 158);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoTota"]), 2), 0, 0, 'R');

		//CodiBarr
		$pdf->GDImage(barcode("", $cuota["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 165, 135, 7);
		$pdf->Text(50, 175, $cuota["CodiBarr"]);

	//Talon 4
	//Y = Talon3 + 45
		$cuota = $tbCuotas->fetch_assoc();
		if ($cuota == null) {
			goto fin;
		}

		//NumeSoli
		$pdf->Text(20, 185, $cliente["NumeSoli"]);
		//NumeCuot
		$pdf->Text(60, 185, $cuota["NumeCuot"]);
		//FechVenc1
		$pdf->Text(101, 185, $cuota["FechVenc1"]);
		//FechVenc2
		if ($cuota["FechVenc2"] != '') {
			$pdf->Text(121, 185, $cuota["FechVenc2"]);
		}
		//FechVenc3
		if ($cuota["FechVenc3"] != '') {
			$pdf->Text(139, 185, $cuota["FechVenc3"]);
		}
		//ImpoTota1
		$pdf->Text(101, 190, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota2
		$pdf->Text(121, 190, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota3
		$pdf->Text(139, 190, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//NumePago
		$pdf->Text(93, 204, $cuota["NumePago"]);
		//FechCuot
		$pdf->Text(123, 204, $cuota["FechCuot"]);
		//ValoMovi
		$pdf->Text(145, 204, $cliente["ValoMovi"]);
		//NombClie
		$pdf->Text(23, 194, utf8_decode($cliente["NombClie"]));
		//DireClie
		$pdf->Text(23, 197, utf8_decode($cliente["DireClie"]));
		//NombBarr
		$pdf->Text(23, 200, utf8_decode($cliente["NombBarr"]));
		//NombLoca - CodiPost
		$pdf->Text(23, 203, utf8_decode($cliente["NombLoca"]. " - " .$cliente["CodiPost"]));
		//NombProv
		$pdf->Text(23, 206, utf8_decode($cliente["NombProv"]));
		//ImpoPura
		$pdf->SetXY(185, 189);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoPura"]), 2), 0, 0, 'R');
		//ImpoAdmi
		$pdf->SetXY(185, 193);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoAdmi"]), 2), 0, 0, 'R');
		//ImpoGest
		$pdf->SetXY(185, 196);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoGest"]), 2), 0, 0, 'R');
		//ImpoOtro
		$pdf->SetXY(185, 199);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoOtro"]), 2), 0, 0, 'R');
		//ImpoTota
		$pdf->Text(159, 205, 'Total:');
		$pdf->SetXY(185, 205);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoTota"]), 2), 0, 0, 'R');

		//CodiBarr
		$pdf->GDImage(barcode("", $cuota["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 211, 135, 7);
		$pdf->Text(50, 221, $cuota["CodiBarr"]);

	//Final
		fin:
		//CodiBarrClie
		$pdf->GDImage(barcode("", $cliente["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 231, 135, 7);
		$pdf->Text(50, 241, $cliente["CodiBarr"]);

		//CodiPagoElec
		$pdf->Text(27, 255, $cliente["CodiPagoElec"]);
	}
	$pdf->Output();
	
?>