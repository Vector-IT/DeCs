<?php 
namespace VectorForms;

class Cuota extends Tabla {
	public function customFunc($post) {
		global $config, $crlf;
	
		switch ($post['field']) {
			case 'Generar':
				$Filtro = "";

				$strSQL = "SELECT c.NumeClie, c.NumeSoli, c.ValoCuot, c.NumeEmpr,";
				$strSQL.= $crlf." e.NumeTipoComi, e.ImpoAdmi, e.PorcAdmi, e.ImpoGest, e.PorcGest, e.ImpoOtro, e.PorcOtro,";
				$strSQL.= $crlf." e.FechVenc1, e.FechVenc2, e.PorcVenc2, e.FechVenc3, e.PorcVenc3";
				$strSQL.= $crlf." FROM clientes c";
				$strSQL.= $crlf." INNER JOIN empresas e ON c.NumeEmpr = e.NumeEmpr";
				if ($post["dato"]["Empresa"] != '-1'&& $post["dato"]["Empresa"] != '') {
					$Filtro.= $crlf." WHERE e.NumeEmpr = ". $post["dato"]["Empresa"];
				}
				if ($post["dato"]["Cliente"] != '-1' && $post["dato"]["Cliente"] != '') {
					if ($Filtro == "") {
						$Filtro.= $crlf." WHERE ";
					}
					else {
						$Filtro.= $crlf." AND ";
					}
					$Filtro.= " c.NumeClie = ". $post["dato"]["Cliente"];
				}
				$strSQL.= $Filtro;

				$strSQL.= $crlf." ORDER BY c.NumeEmpr, c.NumeClie";
				
				$clientes = $config->cargarTabla($strSQL);
				if ($clientes) {

					while ($fila = $clientes->fetch_assoc()) {
						$fecha = new \DateTime($post['dato']['Fecha']. '-01');

						//Itero por la cantidad de cuotas a generar
						for ($cuota = 0; $cuota < $post["dato"]["Cantidad"]; $cuota++) {

							$NumePago = $config->buscarDato("SELECT NumePago FROM pagos WHERE NumeClie = {$fila["NumeClie"]} AND DATE_FORMAT(FechVenc1, '%Y-%m') = '".$fecha->format('Y-m')."'");
							if ($NumePago != '') {
								$NumeCuot = $config->buscarDato("SELECT NumeCuot FROM pagos WHERE NumeClie = ". $fila["NumeClie"] ." AND DATE_FORMAT(FechVenc1, '%Y-%m') = '".$fecha->format('Y-m')."'");
								$this->borrar(array("NumePago"=>$NumePago));
							}
							else {
								$NumeCuot = $config->buscarDato("SELECT COALESCE(MAX(NumeCuot), 0) + 1 FROM pagos WHERE NumeClie = ". $fila["NumeClie"]);
							}

							$NumePago = $config->buscarDato("SELECT COALESCE(MAX(NumePago), 0) + 1 FROM pagos");
							

							//Vencimientos
							$FechVenc1 = "STR_TO_DATE('".$fecha->format('Y-m')."-".$fila["FechVenc1"]."', '%Y-%m-%d')";
							$FechVenc1Barr = date_format(new \DateTime($fecha->format('Y-m')."-".$fila["FechVenc1"]), 'ymd');
							
							if ($fila["FechVenc2"] != "" && $fila["FechVenc2"] != "0") {
								//$FechVenc2 = "STR_TO_DATE('".$fecha->format('Y-m')."-". ($fila["FechVenc1"] + $fila["FechVenc2"]) ."', '%Y-%m-%d')";
								$FechVenc2 = "DATE_ADD(".$FechVenc1.", INTERVAL ". $fila["FechVenc2"] . " DAY)";
								$FechVenc2Barr = substr('00'.$fila["FechVenc2"], -2);
								$PorcVenc2 = $fila["PorcVenc2"];
							}
							else {
								$FechVenc2 = "''";
								$FechVenc2Barr = '000000';
								$PorcVenc2 = '0';
							}

							if ($fila["FechVenc3"] != "" && $fila["FechVenc3"] != "0") {
								//$FechVenc3 = "STR_TO_DATE('".$fecha->format('Y-m')."-". ($fila["FechVenc1"] + $fila["FechVenc2"] + $fila["FechVenc3"]) ."', '%Y-%m-%d')";
								$FechVenc3 = "DATE_ADD(".$FechVenc2.", INTERVAL ". $fila["FechVenc3"] . " DAY)";
								$FechVenc3Barr = substr('00'.$fila["FechVenc3"], -2);
								$PorcVenc3 = $fila["PorcVenc3"];
							}
							else {
								$FechVenc3 = "''";
								$FechVenc3Barr = '000000';
								$PorcVenc3 = '0';
							}

							//Importes
							$ImpoPura = $fila["ValoCuot"];

							//Gastos Administrativos
							if (boolval($fila["PorcAdmi"])) {
								$ImpoAdmi = $ImpoPura * floatval($fila["ImpoAdmi"]) / 100;
							}
							else {
								$ImpoAdmi = floatval($fila["ImpoAdmi"]);
							}

							//Gastos de Gestión
							if (boolval($fila["PorcGest"])) {
								$ImpoGest = $ImpoPura * floatval($fila["ImpoGest"]) / 100;
							}
							else {
								$ImpoGest = floatval($fila["ImpoGest"]);
							}

							//Otros Gastos
							if (boolval($fila["PorcOtro"])) {
								$ImpoOtro = $ImpoPura * floatval($fila["ImpoOtro"]) / 100;
							}
							else {
								$ImpoOtro = floatval($fila["ImpoOtro"]);
							}

							/**
							 * Importe 1er venc
							 */
							switch ($fila["NumeTipoComi"]) {
								case '1': //Los gastos se suman al valor de la cuota pura
									$ImpoVenc1 = $ImpoPura + $ImpoAdmi + $ImpoGest + $ImpoOtro;
									$ImpoAux = number_format($ImpoVenc1, 2, "", "");
									break;

								case '2': //Los gastos se restan al valor de la cuota pura
									$ImpoVenc1 = $ImpoPura;
									$ImpoAux = number_format($ImpoVenc1, 2, "", "");
									$ImpoPura = $ImpoPura - $ImpoAdmi - $ImpoGest - $ImpoOtro;
									break;
							}
							$Impo1Barr = substr('0000000'.$ImpoAux, -7);
							

							/**
							 * Importe 2do venc
							 */
							$ImpoVenc2 = $ImpoVenc1 + ($ImpoVenc1 * floatval($PorcVenc2) / 100);
							$ImpoAux = number_format($ImpoVenc2, 2, "", "");
							$Impo2Barr = substr('0000000'.$ImpoAux, -7);

							/**
							 * Importe 3er venc
							 */
							$ImpoVenc3 = $ImpoVenc2 + ($ImpoVenc2 * floatval($PorcVenc3) / 100);
							$ImpoAux = number_format($ImpoVenc3, 2, "", "");
							$Impo3Barr = substr('0000000'.$ImpoAux, -7);

							$NumeClieBarr = substr('00000000'.$fila["NumeSoli"], -8);

							$CodiBarr = '04470' . $NumeClieBarr . $FechVenc1Barr . $Impo1Barr . $FechVenc2Barr . $Impo2Barr . $FechVenc3Barr . $Impo3Barr . '5150041794';

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
							
							$strSQL = "INSERT INTO pagos(NumePago, FechCuot, NumeClie, NumeCuot, NumeEstaPago, NumeTipoPago, CodiBarr, FechVenc1, FechVenc2, FechVenc3, ImpoPura, ImpoAdmi, ImpoGest, ImpoOtro, ImpoVenc1, ImpoVenc2, ImpoVenc3)";
							$strSQL.= $crlf." VALUES({$NumePago},";
							$strSQL.= $crlf." SYSDATE(),";
							$strSQL.= $crlf." {$fila['NumeClie']},";
							$strSQL.= $crlf." {$NumeCuot},";
							$strSQL.= $crlf." 0,";
							$strSQL.= $crlf." 1,";
							$strSQL.= $crlf." '{$CodiBarr}',";
							$strSQL.= $crlf." {$FechVenc1},";
							$strSQL.= $crlf." {$FechVenc2},";
							$strSQL.= $crlf." {$FechVenc3},";
							$strSQL.= $crlf." {$ImpoPura},";
							$strSQL.= $crlf." {$ImpoAdmi},";
							$strSQL.= $crlf." {$ImpoGest},";
							$strSQL.= $crlf." {$ImpoOtro},";
							$strSQL.= $crlf." {$ImpoVenc1},";
							$strSQL.= $crlf." {$ImpoVenc2},";
							$strSQL.= $crlf." {$ImpoVenc3})";
							
							$result = $config->ejecutarCMD($strSQL);
							
							if ($result !== true) {
								continue;
							}

							//Sumo un mes
							$fecha->add(new \DateInterval('P1M'));
						}
					}
				}
	
				if ($result === true) {
					return "Datos actualizados!";
				}
				else {
					return "Error al actualizar los datos.<br>".$result;
				}
	
				break;
				
			case "ImpoTota":
				$strSQL = "SELECT ImpoPura + ImpoAdmi + ImpoGest + ImpoOtro FROM pagos WHERE NumePago = ". $post["dato"];
				
				return $config->buscarDato($strSQL);
				break;

			case "NumeEmpr":
				if ($post['dato'] != '-1') {
					$filtro = "NumeEmpr = ". $post['dato'];
				}
				else {
					$filtro = "";
				}
				$result = [];
				$result["clientes"] = $this->cargarCombo("clientes", "NumeClie", "NombClie", $filtro, "NombClie", "", true, "TODOS LOS CLIENTES");
				$result["plantilla"] = $config->buscarDato("SELECT NombArch FROM plantillas WHERE NumePlan IN (SELECT NumePlan FROM empresas WHERE NumeEmpr = {$post["dato"]})");

				return $result;
				break;

			case "Explorar":
				$dir = '../'.$post['dato'];

				$ffs = scandir($dir);
				$salida = "";
				$salida.= '<ul>';
				foreach($ffs as $ff){
					if($ff != '.' && $ff != '..'){
						
						if(!is_dir($dir.'/'.$ff)) {
							//Es archivo
							$salida.= '<li><a href="'.$post['dato'].'/'.$ff.'" download target="_blank">'.$ff.'</a>';
						}
						else {
							//Es directorio
							$rndID = $config->get_random_string("abcdefghijklmnopqrstuvwxyz", 5);
							$salida.= '<li><span class="clickable" id="'.$rndID.'" class="clickable" onclick="verDir(\''.$post['dato'].'/'.$ff.'\', \'#'.$rndID.'\')">'.$ff.'</span>';
						}
						/*
						if(is_dir($dir.'/'.$ff)) {
							$salida.= listFolderFiles($dir.'/'.$ff);
						} 
						*/
						$salida.= '</li>';
					}
				}
				$salida.= '</ul>';

				return $salida;
			
				break;
		}
	
	}
	
	public function listar($strFiltro="", $conBotones = true, $btnList = [], $order = '') {
		$Filtro = "";
		if ($strFiltro["Fecha"] != "") {
			$Filtro.= "DATE_FORMAT(FechVenc1, '%Y-%m') = '{$strFiltro["Fecha"]}'";
		}

		if ($strFiltro["Empresa"] != "-1" && $strFiltro["Empresa"] != "") {
			if ($Filtro != "") {
				$Filtro.= " AND ";
			}

			$Filtro.= "NumeClie IN (SELECT NumeClie FROM clientes WHERE NumeEmpr = {$strFiltro["Empresa"]})";
		}

		if ($strFiltro["Cliente"] != "-1" && $strFiltro["Cliente"] != "") {
			if ($Filtro != "") {
				$Filtro.= " AND ";
			}

			$Filtro.= "NumeClie = {$strFiltro["Cliente"]}";
		}

		parent::listar($Filtro, $conBotones, $btnList, $order);
	}
}
?>