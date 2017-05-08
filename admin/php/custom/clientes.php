<?php 
namespace VectorForms;

class Clientes extends Tabla {
	public function customFunc($post) {
		global $config;

		switch ($post['field']) {
			case 'CSV':		
				$NumeEmpr = $post['NumeEmpr'];

				$NumeClie = array();

				$archivo = $_FILES["Archivo"]["tmp_name"];

				$file = fopen($archivo,"r");

				$blnSalida = true;
				$mensaje = "";
				$I = 0;

				while(! feof($file))
				{
					if ($I == 0) {
						fgets($file);
					}
					$I++;	

					$strAux = utf8_encode(fgets($file));

					$fila = explode(";", $strAux);

					if (strlen(trim(str_replace(";", "", $strAux))) > 0) {
						$NumeProv = $config->buscarDato("SELECT NumeProv FROM provincias WHERE UPPER(NombProv) = '". strtoupper($fila[8]) ."'");
						$NumeVend = $config->buscarDato("SELECT NumeVend FROM vendedores WHERE UPPER(NombVend) = '". strtoupper($fila[10]) ."'");

						if ($NumeProv != '') {
							$datos = array(
								"NumeClie"=> '',
								"NumeSoli"=> $fila[0],
								"NombClie"=> $fila[1],
								"NumeEmpr"=> $NumeEmpr,
								"NumeTele"=> $fila[2],
								"NumeCelu" => $fila[3],
								"MailClie" => $fila[4],
								"DireClie" => $fila[5],
								"NombBarr" => $fila[6],
								"NombLoca" => $fila[7],
								"NumeProv" => $NumeProv,
								"CodiPost" => $fila[9],
								"NumeVend" => $NumeVend,
								"ObseClie" => trim($fila[14]),
								"NumeEstaClie" => 1,
								"ValoMovi" => $fila[11],
								"ValoCuot" => $fila[12],
								"FechIngr" => substr($fila[13], 6, 4).'-'.substr($fila[13], 3, 2).'-'.substr($fila[13], 0, 2),
								//"FechPagoDesd" => '',
								//"FechPagoHast" => '',
								//"CantCuot" => '',
								//"CodiBarr" => '',
								//"CodiPagoElec" => '',
								//"FechImpr" => ''
							);	

							if ($NumeVend == '') {
								unset($datos["NumeVend"]);
							}

							$result = $this->insertar($datos);
							$resultAux = json_decode($result, true);

							if ($resultAux["estado"] === true) {
								$NumeClie[] = $resultAux["id"];
							}
							else {
								//Elimino los clientes ya creados
								for ($J = 0; $J < count($NumeClie); $J++) {
									$this->borrar(["NumeClie" => $NumeClie[$J]]);
								}

								$blnSalida = false;
								$mensaje = "Fila: ". ($I+1) ." - Mensaje: ". $resultAux["estado"];
								break;
							}
						}
						else {
							for ($J = 0; $J < count($NumeClie); $J++) {
								$this->borrar(["NumeClie" => $NumeClie[$J]]);
							}

							$blnSalida = false;
							$mensaje = "Fila: ". ($I+1) ." - Mensaje: Provincia incorrecta!";
							break;
						}
					}
				}

				fclose($file);

				$salida["estado"] = $blnSalida;
				if ($blnSalida) {
					$salida["mensaje"] = count($NumeClie) ." Clientes cargados!";
				}
				else {
					$salida["mensaje"] = $mensaje;
				}
				$salida["clientes"] = $NumeClie;

            	return $salida;
			break;
		}
	}

	public function insertar($datos) {
		global $config, $crlf;

		$result = parent::insertar($datos);
		$resultAux = json_decode($result, true);

		if ($resultAux["estado"] === true) {
				
			$NumeClieBarr = substr('00000000'.$datos["NumeSoli"], -8);
			$CodiBarr = '04470' . $NumeClieBarr . '0000000000000000000000000000000' . '5150041794';

			//Digito verificador
			$serie = array(3,5,7,9);
			for ($k = 0; $k < 2; $k++) {
				$j = 0;
				$aux = 0;

				for ($I = 1; $I < strlen($CodiBarr); $I++) {
					$aux+= substr($CodiBarr, $I, 1) * $serie[$j];

					if ($j < 3) {
						$j++;
					}
					else {
						$j = 0;
					}
				}

				$aux = intval($aux / 2);
				$aux = $aux % 10;
				$CodiBarr.= $aux;
			}

			$CodiPagoElec = '0' . $NumeClieBarr . '5150041794';

			$strSQL = "UPDATE clientes SET";
			$strSQL.= " CodiBarr = '{$CodiBarr}'";
			$strSQL.= ", CodiPagoElec = '{$CodiPagoElec}'";
			$strSQL.= " WHERE NumeClie = " . $resultAux["id"];
			$config->ejecutarCMD($strSQL);
		}
		return $result;
	}

	public function editar($datos) {
		$NumeClieBarr = substr('00000000'.$datos["NumeSoli"], -8);
		$CodiBarr = '04470' . $NumeClieBarr . '0000000000000000000000000000000' . '5150041794';

		//Digito verificador
		$serie = array(3,5,7,9);
		for ($k = 0; $k < 2; $k++) {
			$j = 0;
			$aux = 0;

			for ($I = 1; $I < strlen($CodiBarr); $I++) {
				$aux+= substr($CodiBarr, $I, 1) * $serie[$j];

				if ($j < 3) {
					$j++;
				}
				else {
					$j = 0;
				}
			}

			$aux = intval($aux / 2);
			$aux = $aux % 10;
			$CodiBarr.= $aux;
		}

		$CodiPagoElec = '0' . $NumeClieBarr . '5150041794';

		$datos["CodiBarr"] = $CodiBarr;
		$datos["CodiPagoElec"] = $CodiPagoElec;

		$result = parent::editar($datos);
		return $result;
	}

	public function borrar($datos, $filtro = '') {
		global $config, $crlf;

		$nombEmpr = $config->buscarDato("SELECT NombEmpr FROM empresas WHERE NumeEmpr IN (SELECT NumeEmpr FROM clientes WHERE NumeClie = ".$datos["NumeClie"].")");
		$archivo = $config->buscarDato("SELECT CONCAT(NumeSoli, '-', NombClie, '.pdf') Nombre FROM clientes WHERE NumeClie = ".$datos["NumeClie"]);

		$result = $config->ejecutarCMD("DELETE FROM pagos WHERE NumeClie = ".$datos["NumeClie"]);

		$result = parent::borrar($datos, $filtro);
		$resultAux = json_decode($result, true);

		if ($resultAux["estado"] === true) {
			$dir = "../pdfs/".$nombEmpr;
			if (file_exists($dir)) {
				$ffs = scandir($dir);

				foreach($ffs as $ff){
					if($ff != '.' && $ff != '..'){
						
						if(is_dir($dir.'/'.$ff)) {
							//Es directorio
							if (file_exists($dir.'/'.$ff.'/'.$archivo)) {
								unlink($dir.'/'.$ff.'/'.$archivo);
							}
						}
					}
				}
			}
		}

		return $result;
	}
}
