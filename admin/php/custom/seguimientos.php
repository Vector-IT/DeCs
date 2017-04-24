<?php
namespace VectorForms;

class Seguimiento extends Tabla {
	public function customFunc($post) {
		global $config, $crlf;

		switch ($post['field']) {
			case 'Generar':
				$datos["NumeSegu"] = '';
				$datos["NumeClie"] = $post['dato']['Cliente'];
				$datos["FechSegu"] = $post['dato']['Fecha'];
				$datos["NumeEsta"] = 1;

				$result = parent::insertar($datos);

				return $result;
				break;

		}

	}

	public function insertar($datos) {
		global $config; 

		$fecha = $config->buscarDato("SELECT DATE_FORMAT(SYSDATE(), '%Y-%m-%d %H:%i')");

		$datos["FechSegu"] = $fecha;
		$result = parent::insertar($datos);

		return $result;
	}
}
?>