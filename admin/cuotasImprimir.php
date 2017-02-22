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

	$strSQL = "SELECT c.NumeClie, c.NombClie, c.DireClie, c.NombBarr, c.NombLoca, c.CodiPost, p.NombProv, c.ValoMovi, c.CodiBarr, c.CodiPagoElec";
	$strSQL.= $crlf." FROM clientes c";
	$strSQL.= $crlf." INNER JOIN provincias p ON c.NumeProv = p.NumeProv";
	if ($filtro != "") {
		$strSQL.= $crlf." WHERE " . $filtro;
	}

	$tbClientes = $config->cargarTabla($strSQL);

	$pdf = new PDF_MemImage('P', 'mm', 'A4');
	
	while ($cliente = $tbClientes->fetch_assoc()) {

		$strSQL = "SELECT p.NumePago,";
		$strSQL.= $crlf." p.NumeClie,";
		$strSQL.= $crlf." p.NumeCuot,";
		$strSQL.= $crlf." DATE_FORMAT(p.FechVenc1, '%d/%m/%Y') FechVenc1,";
		$strSQL.= $crlf." DATE_FORMAT(p.FechVenc2, '%d/%m/%Y') FechVenc2,";
		$strSQL.= $crlf." DATE_FORMAT(p.FechVenc3, '%d/%m/%Y') FechVenc3,";
		$strSQL.= $crlf." p.ImpoPura,";
		$strSQL.= $crlf." p.ImpoAdmi,";
		$strSQL.= $crlf." p.ImpoGest,";
		$strSQL.= $crlf." p.ImpoOtro,";
		$strSQL.= $crlf." p.ImpoPura + p.ImpoAdmi + p.ImpoGest + p.ImpoOtro ImpoTota,";
		$strSQL.= $crlf." DATE_FORMAT(p.FechPago, '%d/%m/%Y') FechPago,";
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
		//NumeClie
		$pdf->Text(20, 46, $cuota["NumeClie"]);
		//NumeCuot
		$pdf->Text(60, 46, $cuota["NumeCuot"]);
		//FechVenc1
		$pdf->Text(101, 47, $cuota["FechVenc1"]);
		//FechVenc2
		$pdf->Text(119, 47, $cuota["FechVenc2"]);
		//FechVenc3
		$pdf->Text(136, 47, $cuota["FechVenc3"]);
		//ImpoTota1
		$pdf->Text(101, 52, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota2
		$pdf->Text(119, 52, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota3
		$pdf->Text(136, 52, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//NumePago
		$pdf->Text(93, 65, $cuota["NumePago"]);
		//FechPago
		$pdf->Text(120, 65, $cuota["FechPago"]);
		//ValoMovi
		$pdf->Text(141, 65, $cliente["ValoMovi"]);
		//NombClie
		$pdf->Text(23, 56, utf8_decode($cliente["NombClie"]));
		//DireClie
		$pdf->Text(23, 59, utf8_decode($cliente["DireClie"]));
		//NombBarr
		$pdf->Text(23, 62, utf8_decode($cliente["NombBarr"]));
		//NombLoca - CodiPost
		$pdf->Text(23, 65, utf8_decode($cliente["NombLoca"]. " - " .$cliente["CodiPost"]));
		//NombProv
		$pdf->Text(23, 68, utf8_decode($cliente["NombProv"]));
		//ImpoPura
		$pdf->SetXY(185, 52);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoPura"]), 2), 0, 0, 'R');
		//ImpoAdmi
		$pdf->SetXY(185, 56);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoAdmi"]), 2), 0, 0, 'R');
		//ImpoGest
		$pdf->SetXY(185, 59);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoGest"]), 2), 0, 0, 'R');
		//ImpoOtro
		$pdf->SetXY(185, 62);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoOtro"]), 2), 0, 0, 'R');
		//ImpoTota
		$pdf->Text(157, 68, 'Total:');
		$pdf->SetXY(185, 68);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoTota"]), 2), 0, 0, 'R');

		//CodiBarr
		$pdf->GDImage(barcode("", $cuota["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 75, 135, 5);
		$pdf->Text(50, 83, $cuota["CodiBarr"]);

	//Talon 2
	// Y = Talon1 + 48
		$cuota = $tbCuotas->fetch_assoc();
		if ($cuota == null) {
			goto fin;
		}

		//NumeClie
		$pdf->Text(20, 94, $cuota["NumeClie"]);
		//NumeCuot
		$pdf->Text(60, 94, $cuota["NumeCuot"]);
		//FechVenc1
		$pdf->Text(101, 94, $cuota["FechVenc1"]);
		//FechVenc2
		$pdf->Text(119, 94, $cuota["FechVenc2"]);
		//FechVenc3
		$pdf->Text(136, 94, $cuota["FechVenc3"]);
		//ImpoTota1
		$pdf->Text(101, 100, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota2
		$pdf->Text(119, 100, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota3
		$pdf->Text(136, 100, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//NumePago
		$pdf->Text(93, 113, $cuota["NumePago"]);
		//FechPago
		$pdf->Text(120, 113, $cuota["FechPago"]);
		//ValoMovi
		$pdf->Text(141, 113, $cliente["ValoMovi"]);
		//NombClie
		$pdf->Text(23, 102, utf8_decode($cliente["NombClie"]));
		//DireClie
		$pdf->Text(23, 105, utf8_decode($cliente["DireClie"]));
		//NombBarr
		$pdf->Text(23, 108, utf8_decode($cliente["NombBarr"]));
		//NombLoca - CodiPost
		$pdf->Text(23, 111, utf8_decode($cliente["NombLoca"]. " - " .$cliente["CodiPost"]));
		//NombProv
		$pdf->Text(23, 114, utf8_decode($cliente["NombProv"]));
		//ImpoPura
		$pdf->SetXY(185, 98);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoPura"]), 2), 0, 0, 'R');
		//ImpoAdmi
		$pdf->SetXY(185, 102);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoAdmi"]), 2), 0, 0, 'R');
		//ImpoGest
		$pdf->SetXY(185, 105);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoGest"]), 2), 0, 0, 'R');
		//ImpoOtro
		$pdf->SetXY(185, 108);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoOtro"]), 2), 0, 0, 'R');
		//ImpoTota
		$pdf->Text(157, 114, 'Total:');
		$pdf->SetXY(185, 114);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoTota"]), 2), 0, 0, 'R');

		//CodiBarr
		$pdf->GDImage(barcode("", $cuota["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 121, 135, 5);
		$pdf->Text(50, 129, $cuota["CodiBarr"]);

	//Talon 3
	// Y = Talon2 + 45
		$cuota = $tbCuotas->fetch_assoc();
		if ($cuota == null) {
			goto fin;
		}

		//NumeClie
		$pdf->Text(20, 139, $cuota["NumeClie"]);
		//NumeCuot
		$pdf->Text(60, 139, $cuota["NumeCuot"]);
		//FechVenc1
		$pdf->Text(101, 139, $cuota["FechVenc1"]);
		//FechVenc2
		$pdf->Text(119, 139, $cuota["FechVenc2"]);
		//FechVenc3
		$pdf->Text(136, 139, $cuota["FechVenc3"]);
		//ImpoTota1
		$pdf->Text(101, 145, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota2
		$pdf->Text(119, 145, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota3
		$pdf->Text(136, 145, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//NumePago
		$pdf->Text(93, 158, $cuota["NumePago"]);
		//FechPago
		$pdf->Text(120, 158, $cuota["FechPago"]);
		//ValoMovi
		$pdf->Text(141, 158, $cliente["ValoMovi"]);
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
		$pdf->SetXY(185, 143);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoPura"]), 2), 0, 0, 'R');
		//ImpoAdmi
		$pdf->SetXY(185, 147);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoAdmi"]), 2), 0, 0, 'R');
		//ImpoGest
		$pdf->SetXY(185, 150);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoGest"]), 2), 0, 0, 'R');
		//ImpoOtro
		$pdf->SetXY(185, 153);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoOtro"]), 2), 0, 0, 'R');
		//ImpoTota
		$pdf->Text(157, 159, 'Total:');
		$pdf->SetXY(185, 159);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoTota"]), 2), 0, 0, 'R');

		//CodiBarr
		$pdf->GDImage(barcode("", $cuota["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 166, 135, 5);
		$pdf->Text(50, 174, $cuota["CodiBarr"]);

	//Talon 4
	//Y = Talon3 + 45
		$cuota = $tbCuotas->fetch_assoc();
		if ($cuota == null) {
			goto fin;
		}

		//NumeClie
		$pdf->Text(20, 184, $cuota["NumeClie"]);
		//NumeCuot
		$pdf->Text(60, 184, $cuota["NumeCuot"]);
		//FechVenc1
		$pdf->Text(101, 184, $cuota["FechVenc1"]);
		//FechVenc2
		$pdf->Text(119, 184, $cuota["FechVenc2"]);
		//FechVenc3
		$pdf->Text(136, 184, $cuota["FechVenc3"]);
		//ImpoTota1
		$pdf->Text(101, 190, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota2
		$pdf->Text(119, 190, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//ImpoTota3
		$pdf->Text(136, 190, "$ ".number_format(floatval($cuota["ImpoTota"]), 2));
		//NumePago
		$pdf->Text(93, 203, $cuota["NumePago"]);
		//FechPago
		$pdf->Text(120, 203, $cuota["FechPago"]);
		//ValoMovi
		$pdf->Text(141, 203, $cliente["ValoMovi"]);
		//NombClie
		$pdf->Text(23, 192, utf8_decode($cliente["NombClie"]));
		//DireClie
		$pdf->Text(23, 195, utf8_decode($cliente["DireClie"]));
		//NombBarr
		$pdf->Text(23, 198, utf8_decode($cliente["NombBarr"]));
		//NombLoca - CodiPost
		$pdf->Text(23, 201, utf8_decode($cliente["NombLoca"]. " - " .$cliente["CodiPost"]));
		//NombProv
		$pdf->Text(23, 204, utf8_decode($cliente["NombProv"]));
		//ImpoPura
		$pdf->SetXY(185, 188);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoPura"]), 2), 0, 0, 'R');
		//ImpoAdmi
		$pdf->SetXY(185, 192);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoAdmi"]), 2), 0, 0, 'R');
		//ImpoGest
		$pdf->SetXY(185, 195);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoGest"]), 2), 0, 0, 'R');
		//ImpoOtro
		$pdf->SetXY(185, 198);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoOtro"]), 2), 0, 0, 'R');
		//ImpoTota
		$pdf->Text(157, 204, 'Total:');
		$pdf->SetXY(185, 204);
		$pdf->Cell(17, 0, "$ ".number_format(floatval($cuota["ImpoTota"]), 2), 0, 0, 'R');

		//CodiBarr
		$pdf->GDImage(barcode("", $cuota["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 211, 135, 5);
		$pdf->Text(50, 219, $cuota["CodiBarr"]);

	//Final
		fin:
		//CodiBarrClie
		$pdf->GDImage(barcode("", $cliente["CodiBarr"], 10, "horizontal", "code128", false, 1, "gd"), 25, 230, 135, 5);
		$pdf->Text(50, 238, $cliente["CodiBarr"]);

		//CodiPagoElec
		$pdf->Text(27, 255, $cliente["CodiPagoElec"]);
	}
	$pdf->Output();
	
?>