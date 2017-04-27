<?php 
namespace VectorForms;

class Cuota extends Tabla {
	public function customFunc($post) {
		global $config, $crlf;
	
		switch ($post['field']) {
			case 'Generar':
				$Filtro = "";

				$strSQL = "SELECT c.NumeClie, c.ValoCuot, c.NumeEmpr,";
				$strSQL.= $crlf." e.ImpoAdmi, e.PorcAdmi, e.ImpoGest, e.PorcGest, e.ImpoOtro, e.PorcOtro,";
				$strSQL.= $crlf." e.FechVenc1, e.FechVenc2, e.FechVenc3";
				$strSQL.= $crlf." FROM clientes c";
				$strSQL.= $crlf." INNER JOIN empresas e ON c.NumeEmpr = e.NumeEmpr";
				if ($post["dato"]["Empresa"] != '-1') {
					$Filtro.= $crlf." WHERE e.NumeEmpr = ". $post["dato"]["Empresa"];
				}
				if ($post["dato"]["Cliente"] != '-1') {
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
						$NumePago = $config->buscarDato("SELECT COALESCE(MAX(NumePago), 0) + 1 FROM pagos");
						$NumeCuot = $config->buscarDato("SELECT COALESCE(MAX(NumeCuot), 0) + 1 FROM pagos WHERE NumeClie = ". $fila["NumeClie"]);

						//Vencimientos
						$FechVenc1 = "STR_TO_DATE('".$post['dato']['Fecha']."-".$fila["FechVenc1"]."', '%Y-%m-%d')";
						$FechVenc1Barr = date_format(new \DateTime($post['dato']['Fecha']."-".$fila["FechVenc1"]), 'ymd');
						
						if ($fila["FechVenc2"] != "" && $fila["FechVenc2"] != "0") {
							$FechVenc2 = "STR_TO_DATE('".$post['dato']['Fecha']."-". ($fila["FechVenc1"] + $fila["FechVenc2"]) ."', '%Y-%m-%d')";
							$FechVenc2Barr = substr('00'.$fila["FechVenc2"], -2);
						}
						else {
							$FechVenc2 = "''";
							$FechVenc2Barr = '000000';
						}

						if ($fila["FechVenc3"] != "" && $fila["FechVenc3"] != "0") {
							$FechVenc3 = "STR_TO_DATE('".$post['dato']['Fecha']."-". ($fila["FechVenc1"] + $fila["FechVenc2"] + $fila["FechVenc3"]) ."', '%Y-%m-%d')";
							$FechVenc3Barr = substr('00'.$fila["FechVenc3"], -2);
						}
						else {
							$FechVenc3 = "''";
							$FechVenc3Barr = '000000';
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

						$ImpoAux = number_format($ImpoPura + $ImpoAdmi + $ImpoGest + $ImpoOtro, 2, "", "");
						$Impo1Barr = substr('0000000'.$ImpoAux, -7);

						$NumeClieBarr = substr('00000000'.$fila["NumeClie"], -8);

						$CodiBarr = '04470' . $NumeClieBarr . $FechVenc1Barr . $Impo1Barr . $FechVenc2Barr . $Impo1Barr . $FechVenc3Barr . $Impo1Barr . '5150041794';

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
						
						$strSQL = "INSERT INTO pagos(NumePago, FechCuot, NumeClie, NumeCuot, NumeEstaPago, NumeTipoPago, CodiBarr, FechVenc1, FechVenc2, FechVenc3, ImpoPura, ImpoAdmi, ImpoGest, ImpoOtro)";
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
						$strSQL.= $crlf." {$ImpoOtro})";
						
						$result = $config->ejecutarCMD($strSQL);
						
						if ($result !== true) {
							continue;
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
				
				$result = $this->cargarCombo("clientes", "NumeClie", "NombClie", $filtro, "NombClie", "", true, "TODOS LOS CLIENTES");

				return $result;
				break;
		}
	
	}
	
	public function listar($strFiltro="", $conBotones = true, $btnList = [], $order = '') {
		$Filtro = "";
		if ($strFiltro["Fecha"] != "") {
			$Filtro.= "DATE_FORMAT(FechVenc1, '%Y-%m') = '{$strFiltro["Fecha"]}'";
		}

		if ($strFiltro["Empresa"] != "-1") {
			if ($Filtro != "") {
				$Filtro.= " AND ";
			}

			$Filtro.= "NumeClie IN (SELECT NumeClie FROM clientes WHERE NumeEmpr = {$strFiltro["Empresa"]})";
		}

		if ($strFiltro["Cliente"] != "-1") {
			if ($Filtro != "") {
				$Filtro.= " AND ";
			}

			$Filtro.= "NumeClie = {$strFiltro["Cliente"]}";
		}

		parent::listar($Filtro, $conBotones, $btnList, $order);
	}
}
?>