<?php 
class Clientes extends Tabla {
	public function insertar($datos) {
		global $config, $crlf;

		$result = parent::insertar($datos);
		$resultAux = json_decode($result, true);

		if ($resultAux["estado"] === true) {
				
			$NumeClieBarr = substr('00000000'.$resultAux["id"], -8);
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

			$strSQL = "UPDATE clientes SET CodiBarr = '{$CodiBarr}' WHERE NumeClie = " . $resultAux["id"];
			$config->ejecutarCMD($strSQL);

			return $result;
		}
	}
}