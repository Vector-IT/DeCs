<?php
	session_start();
	require_once 'php/datos.php';
	require_once 'fpdf/fpdf2.php';
	require_once 'barcode/barcode.php';

	$urlLogin = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/login.php?returnUrl=" . $_SERVER['REQUEST_URI'];
	$urlIndex = "Location:". "http://". $_SERVER['SERVER_NAME'] . ($_SERVER['SERVER_PORT'] != "80"? ":".$_SERVER['SERVER_PORT']: "") . $config->raiz ."admin/";

	if (!isset($_SESSION['is_logged_in'])){
		header($urlLogin);
		die();
	}

	//$filtro = $_REQUEST["filtro"];

	$pdf = new PDF_MemImage('P', 'mm', 'A4');
	$pdf->AddPage();

//Talon 1
	$pdf->SetFont('Times', 'B', 8);
	//NumeClie
	$pdf->Text(20, 43, '00102203');

	//NumeCuot
	$pdf->Text(60, 43, '1');
	
	//FechVenc1
	$pdf->Text(102, 44, '10/02/2017');
	
	//FechVenc2
	$pdf->Text(120, 44, '10/02/2017');
	
	//FechVenc3
	$pdf->Text(137, 44, '10/02/2017');

	//NombClie
	$pdf->Text(20, 54, utf8_decode('Romero Cuny Jose María'));
	//DireClie
	$pdf->Text(20, 57, utf8_decode('Martel de los ríos 2215'));
	//NombBarr
	$pdf->Text(20, 60, utf8_decode('Villa Centenario'));
	//NombLoca - CodiPost
	$pdf->Text(20, 63, utf8_decode('Córdoba'. ' - ' .'5009'));
	//NombProv
	$pdf->Text(20, 66, utf8_decode('Córdoba'));

	//CodiBarr
	$pdf->GDImage(barcode("", "04470001022031702100378000100378000080378000515004179414", 10, "horizontal", "code128", false, 1, "gd"), 25, 75, 135, 5);

	$pdf->Output();
	
?>