<?php
namespace VectorForms;

/**
 * Archivo de configuracion general
 *
 * @author Vector-IT
 * @package vectorAdmin
 *
 */
require_once 'tabla.php';

class VectorForms {
	private $dbhost;
	private $db;
	private $dbuser;
	private $dbpass;

	public $raiz;
	public $titulo;
	public $logo;
	public $tablas;
	public $showTitulo;
	public $menuItems = [];
	
	public $cssFiles = [];
	
	public $imgCKEditor = '';

	public $theme;

	/**
	 * Constructor de la clase Configuracion
	 *
	 * @param string $dbhost
	 * @param string $db
	 * @param string $dbuser
	 * @param string $dbpass
	 * @param string $raiz
	 * @param string $title
	 * @param string $logo
	 */
	public function __construct($dbhost='', $db='', $dbuser='', $dbpass='', $raiz='', $titulo='', $logo='', $showTitulo=true) {
		$this->dbhost = $dbhost;
		$this->db = $db;
		$this->dbuser = $dbuser;
		$this->dbpass = $dbpass;
		$this->raiz = $raiz;
		$this->titulo = $titulo;
		$this->logo = $logo;
		$this->showTitulo = $showTitulo;
	}

	/**
	 * Nueva conexión a la BD
	 *
	 * @return mysqli
	 */
	private function newConn() {
		$conn = new \mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->db);
		$conn->set_charset("utf8");

		return $conn;
	}


	/**
	 * Ejecutar comando en la BD
	 *
	 * @param string $strSQL
	 * @return boolean|string
	 */
	public function ejecutarCMD($strSQL) {
		$conn = $this->newConn();
		$strError = "";

		if (!$conn->query($strSQL))
			$strError = $conn->error;
			$conn->close();

			if ($strError == "") {
				return true;
			}
			else {
				return $strError;
			}
	}

	/**
	 * Ejecutar query en la BD y devolver el valor del primer campo
	 *
	 * @param string $strSQL
	 * @return string
	 */
	public function buscarDato($strSQL) {

		$conn = $this->newConn();

		$strSalida = "";

		if (!($tabla = $conn->query($strSQL))) {
			$strSalida = "Error al realizar la consulta.";
		}
		else {
			if ($tabla->num_rows > 0) {
				if ($tabla->field_count == 1) {
					$fila = $tabla->fetch_row();
					$strSalida = $fila[0];
				}
				else {
					$strSalida = $tabla->fetch_assoc();
				}
				$tabla->free();
			}
			else {
				$strSalida = '';
			}
		}

		if (is_resource($conn)) {
			$conn->close();
		}

		return $strSalida;
	}

	public function cargarCombo($tabla, $CampoNumero, $CampoTexto, $filtro = "", $orden = "", $seleccion = "", $itBlank = false, $itBlankText = 'Seleccione...')
    {
        global $crlf;

        $strSQL = "SELECT ". $CampoNumero;
        if ($CampoTexto != "") {
            $strSQL.= ",". $CampoTexto;
        }
        $strSQL.= " FROM ". $tabla;

        if ($filtro != "") {
            $strSQL.= " WHERE $filtro";
        }

        if ($orden != "") {
            $strSQL.= " ORDER BY $orden";
        }

        $tabla = $this->cargarTabla($strSQL);

        $strSalida = "";
        if ($itBlank) {
            $strSalida.= $crlf.'<option value="">'.$itBlankText.'</option>';
        }

        while ($fila = $tabla->fetch_assoc()) {
            if ($CampoTexto != "") {
                if (strcmp($fila[$CampoNumero], $seleccion) != "0") {
                    $strSalida.= $crlf.'<option value="'.$fila[$CampoNumero].'">'.htmlentities($fila[$CampoTexto]).'</option>';
                } else {
                    $strSalida.= $crlf.'<option value="'.$fila[$CampoNumero].'" selected>'.htmlentities($fila[$CampoTexto]).'</option>';
                }
            } else {
                if (strcmp($fila[$CampoNumero], $seleccion) != "0") {
                    $strSalida.= $crlf.'<option value="'.$fila[$CampoNumero].'" />';
                } else {
                    $strSalida.= $crlf.'<option value="'.$fila[$CampoNumero].'" selected />';
                }
            }
        }

        return $strSalida;
    }

	/**
	 * Ejecutar query en la BD y devolver el resultado
	 *
	 * @param string $strSQL
	 * @return boolean|mysqli_result
	 */
	public function cargarTabla($strSQL) {
		$conn = $this->newConn();

		$tabla = $conn->query($strSQL);

		$conn->close();

		return $tabla;
	}

	/**
	 * Crear menu de opciones
	 */
	public function crearMenu() {
		global $crlf, $config;

		$strSalida = '';
		$strSeparador = $crlf.'<div class="separator"></div>';
		
		$strItem = $crlf.'<div class="item" data-url="#url#" data-toggle="tooltip" data-placement="right" title="#titulo#">';
		$strItem.= $crlf.'#titulo#';
		$strItem.= $crlf.'<div class="flRight"><i class="fa #icono# fa-fw"></i></div>';
		$strItem.= $crlf.'</div>';

		$strSubMenuInicio = $crlf.'<div class="item submenu" data-url="#url#" data-toggle="tooltip" data-placement="top" title="#titulo#">';
		$strSubMenuInicio.= $crlf.'#titulo#';
		$strSubMenuInicio.= $crlf.'<div class="flRight"><i class="fa #icono# fa-fw"></i></div>';
		$strSubMenuInicio.= $crlf.'<ul class="dropdown-menu">';
		
		$strSubMenuFin = $crlf.'</ul>';
		$strSubMenuFin.= $crlf.'</div>';
		
		$strSubItem = $crlf.'<li data-url="#url#">';;
		$strSubItem.= $crlf.'#titulo#';
		$strSubItem.= $crlf.'<div class="flRight"><i class="fa #icono# fa-fw"></i></div>';
		$strSubItem.= $crlf.'</li>';
		
		$strSalida.= $crlf.'<div id="sidebar" class="menuVector">';
		$strSalida.= $crlf.'<div class="absolute top5 right3">';
		$strSalida.= $crlf.'<button class="btnMenu btn btn-default btn-xs noMobile" data-toggle="tooltip" data-placement="right" title="Men&uacute;"><i class="fa fa-bars"></i></button>';
		$strSalida.= $crlf.'</div>';
		$strSalida.= $crlf.'<div id="sidebar-content" class="menuVector-content">';

		$strSalida.= str_replace("#titulo#", "Inicio", str_replace("#icono#", "fa-home", str_replace("#url#", $this->raiz."admin/", $strItem)));
		$strSalida.= $strSeparador;

		$I = 1;
		$submenu = false;
		foreach ($this->menuItems as $item) {
			$item->Used = false;
		}

		foreach ($this->tablas as $tabla) {
			//Items de menu adicionales
			foreach ($this->menuItems as $item) {
				if (!$item->Used) {
					if ($item->NumeCarg != '') {
						$NumeCarg = intval($this->buscarDato("SELECT NumeCarg FROM ".$config->tbLogin." WHERE NumeUser = ". $_SESSION["NumeUser"]));
							
						if (intval($item->NumeCarg) < $NumeCarg) {
							continue;
						}
					}
						
					if (intval($item->Index) == $I) {
						if ($item->Submenu) {
							if ($submenu) {
								$strSalida.= $strSubMenuFin;
								$strSalida.= $strSeparador;
							}

							$submenu = true;
							
							$strSalida.= str_replace("#titulo#", $item->Titulo,
											str_replace("#icono#", $item->Icono,
											str_replace("#url#", $item->Url, $strSubMenuInicio)));

							$item->Used = true;
						}
						else {
							if ($item->Subitem) {
								$strSalida.= str_replace("#titulo#", $item->Titulo,
												str_replace("#icono#", $item->Icono,
												str_replace("#url#", $item->Url, $strSubItem)));
								$strSalida.= $strSeparador;

								$item->Used = true;
							}
							else {
								if ($submenu) {
									$strSalida.= $strSubMenuFin;
									$strSalida.= $strSeparador;
									$submenu = false;
								}
								
								$strSalida.= str_replace("#titulo#", $item->Titulo, 
												str_replace("#icono#", $item->Icono, 
												str_replace("#url#", $item->Url, $strItem)));

								$strSalida.= $strSeparador;
								$item->Used = true;
								$I++;
							}
						}
					}
				}
			}
				
			//Tablas
			if ($tabla->showMenu) {
				if ($tabla->numeCarg != '') {
					$NumeCarg = intval($this->buscarDato("SELECT NumeCarg FROM ".$config->tbLogin." WHERE NumeUser = ". $_SESSION["NumeUser"]));
		
					if (intval($tabla->numeCarg) < $NumeCarg) {
						continue;
					}
				}
		
				if ($tabla->isSubMenu) {
					if ($submenu) {
						$strSalida.= $strSubMenuFin;
						$strSalida.= $strSeparador;
					}

					$submenu = true;
					
					$strSalida.= str_replace("#titulo#", $tabla->titulo,
									str_replace("#icono#", $tabla->icono,
									str_replace("#url#", $tabla->url, $strSubMenuInicio)));

					$I++;
				}
				else {
					if ($tabla->isSubItem) {
						$strSalida.= str_replace("#titulo#", $tabla->titulo,
										str_replace("#icono#", $tabla->icono,
										str_replace("#url#", $tabla->url, $strSubItem)));
						$strSalida.= $strSeparador;
					}
					else {
						if ($submenu) {
							$strSalida.= $strSubMenuFin;
							$strSalida.= $strSeparador;
							$submenu = false;
						}
							
						$strSalida.= str_replace("#titulo#", $tabla->titulo,
										str_replace("#icono#", $tabla->icono,
										str_replace("#url#", $tabla->url, $strItem)));

						$strSalida.= $strSeparador;
						$I++;
					}
				}
			}
		}
		
		foreach ($this->menuItems as $item) {
			if ($item->Index == '' || !$item->Used) {
				if ($item->NumeCarg != '') {
					$NumeCarg = intval($this->buscarDato("SELECT NumeCarg FROM ".$config->tbLogin." WHERE NumeUser = ". $_SESSION["NumeUser"]));
		
					if (intval($item->NumeCarg) < $NumeCarg) {
						continue;
					}
				}
				
				if ($item->Submenu) {
					if ($submenu) {
						$strSalida.= $strSubMenuFin;
						$strSalida.= $strSeparador;
					}

					$submenu = true;
					
					$strSalida.= str_replace("#titulo#", $item->Titulo,
									str_replace("#icono#", $item->Icono,
									str_replace("#url#", $item->Url, $strSubMenuInicio)));

					$item->Used = true;
				}
				else {
					if ($item->Subitem) {
						$strSalida.= str_replace("#titulo#", $item->Titulo,
										str_replace("#icono#", $item->Icono,
										str_replace("#url#", $item->Url, $strSubItem)));
						$strSalida.= $strSeparador;

						$item->Used = true;
					}
					else {
						if ($submenu) {
							$strSalida.= $strSubMenuFin;
							$strSalida.= $strSeparador;
							$submenu = false;
						}
						
						$strSalida.= str_replace("#titulo#", $item->Titulo, 
										str_replace("#icono#", $item->Icono, 
										str_replace("#url#", $item->Url, $strItem)));

						$strSalida.= $strSeparador;
						$item->Used = true;
					}
				}

				// $strSalida.= str_replace("#titulo#", $item->Titulo, str_replace("#icono#", $item->Icono, str_replace("#url#", $item->Url, $strItem)));
				// $strSalida.= $strSeparador;
			}
		}
		
		if ($submenu) {
			$strSalida.= $strSubMenuFin;
			$strSalida.= $strSeparador;
		}

		$strSalida.= $crlf.'</div>';
		$strSalida.= $crlf.'</div>';

		$strSalida.= $crlf.'<button class="btnMenu btn btn-default btn-xs fixed top5 left5 noDesktop" title="Men&uacute;"><i class="fa fa-bars"></i></button>';

		echo $strSalida;
	}


	public function getTabla($name) {
		return $this->tablas[$name];
	}

	public function get_random_string($valid_chars, $length)
	{
		// start with an empty random string
		$random_string = "";

		// count the number of chars in the valid chars string so we know how many choices we have
		$num_valid_chars = strlen($valid_chars);

		// repeat the steps until we've created a string of the right length
		for ($i = 0; $i < $length; $i++)
		{
			// pick a random number from 1 up to the number of valid chars
			$random_pick = mt_rand(1, $num_valid_chars);

			// take the random character out of the string of valid chars
			// subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
			$random_char = $valid_chars[$random_pick-1];

			// add the randomly-chosen char onto the end of our string so far
			$random_string .= $random_char;
		}

		// return our finished random string
		return $random_string;
	}
}

class MenuItem {
	public $Titulo;
	public $Url;
	public $NumeCarg;
	public $Icono;
	public $Index;
	public $Submenu;
	public $Subitem;
	public $Used;

	/**
	 * Constructor de items
	 *
	 * @param string $titulo
	 * @param string $url
	 * @param string $NumeCarg
	 * @param string $Icono
	 * @param string $Index
	 * @param string $Submenu
	 */
	public function __construct($titulo, $url, $numeCarg='', $icono='', $index='', $submenu=false, $subitem=false) {
		$this->Titulo = $titulo;
		$this->Url = $url;
		$this->NumeCarg = $numeCarg;
		$this->Icono = $icono;
		$this->Index = $index;
		$this->Submenu = $submenu;
		$this->Subitem = $subitem;
		$this->Used = false;
	}
}
?>