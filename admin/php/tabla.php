<?php
/**
 * Archivo de clase de tabla generica
 *
 * @package vectorAdmin
 * @author Vector-IT
 *
 */
class Tabla {
    public $tabladb;
    public $titulo;
    public $tituloSingular;
  	public $showMenu;
  	public $url;
  	public $icono;
  	public $fields;
  	public $order;

  	public $IDField;
  	public $labelField;

  	public $allowNew;
  	public $allowEdit;
  	public $allowDelete;

  	public $masterTable;
  	public $masterField;

  	public $numeCarg;

  	public $jsFiles;
  	public $jsOnLoad;
  	public $jsOnEdit;

  	public $btnList;

  	public $footerFunc;
  	public $footerField;

  	//Google maps
  	public $gmaps;
  	public $gmapsApiKey;
  	public $gmapsCenterLat;
  	public $gmapsCenterLng;


    /**
     * Constructor de la clase Tabla
     * @param string $tabladb
     * @param string $titulo
     * @param string $tituloSingular
     * @param boolean $showMenu
     * @param string $url
     * @param string $icono
     * @param string $order
     */
    public function __construct($tabladb='', $titulo='', $tituloSingular = '', $showMenu=true, $url='', $icono='', $order='', $allowEdit = true, $allowDelete=true) {
        $this->tabladb = $tabladb;
        $this->titulo = $titulo;
        $this->tituloSingular = $tituloSingular;
        $this->showMenu = $showMenu;
        $this->url = $url;
        $this->icono = $icono;
        $this->order = $order;

        $this->allowNew = true;
        $this->allowEdit = $allowEdit;
        $this->allowDelete = $allowDelete;

        $this->numeCarg = '';

        $this->jsFiles = [];
        $this->jsOnLoad = '';
        $this->jsOnEdit = '';

        $this->btnList = [];

        $this->footerFunc = '';
        $this->footerField = '';

        $this->masterTable = '';
        $this->masterField = '';

        $this->gmaps = false;
        $this->gmapsApiKey = '';
        $this->gmapsCenterLat = '0';
        $this->gmapsCenterLng = '0';
    }

    /**
     * Agrega un campo a la tabla
     * @param string $name: nombre del campo
     * @param string $type: tipo de control al crear en un form
     * @param number $size: tamaño del campo
     * @param string $label: etiqueta del control al crear en un form
     * @param boolean $requ: requerido
     * @param string $value: valor por defecto
     * @param string $cssGrp: clases css del control al crear en un form
     */
	public function addField($name, $type='text', $size=0, $label='', $required=true, $readOnly=false, $isID=false, $showOnList=true, $value='', $cssGroup='',
			$lookupTable='', $lookupFieldID='', $lookupFieldLabel='', $lookupConditions='', $lookupOrder = '',
			$isHiddenInForm=false, $isHiddenInList=false, $isMasterID=false, $onChange='', $showOnForm=true, $itBlank=false) {

		$this->fields[$name] = array (
				'name' => $name,
				'type' => $type,
				'size' => $size,
				'label' => $label,
				'required' => $required,
				'readOnly' => $readOnly,
				'isID' => $isID,
				'showOnList' => $showOnList,
				'value' => $value,
				'cssControl' => '',
				'cssGroup' => $cssGroup,
				'isID' => $isID,
				'lookupTable' => $lookupTable,
				'lookupFieldID' => $lookupFieldID,
				'lookupFieldLabel' => $lookupFieldLabel,
				'lookupConditions' => $lookupConditions,
				'lookupOrder' => $lookupOrder,
				'isHiddenInForm' => $isHiddenInForm,
				'isHiddenInList' => $isHiddenInList,
				'isMasterID' => $isMasterID,
				'onChange' => $onChange,
				'showOnForm' => $showOnForm,
				'itBlank' => $itBlank,
				'hoursDisabled' => '',
				'dtpOnRender' => '',
				'txtAlign' => 'left',
				'ruta' => '',
				'nomFileField' => '',
				'mirrorField' => '',
				'mirrorFormat' => '',
				'formatDb' => '',
				'isMD5' => false
		);

		if ($isID) {
			$this->IDField = $name;
		}
	}

	public function createForm() {
		global $crlf;

		$strSalida = '';
		$strCampo = '';

		if (isset($this->fields)) {
			$strSalida.= $crlf.'<button id="btnNuevo" type="button" class="btn btn-sm btn-primary" onclick="editar'. $this->tabladb .'(0);"><i class="fa fa-plus-circle fa-fw" aria-hidden="true"></i> Nuevo</button>';
			$strSalida.= $crlf.'<form id="frm'. $this->tabladb .'" class="form-horizontal marginTop20" method="post" onSubmit="return false;">';
			$strSalida.= $crlf.'<input type="hidden" id="hdnTabla" value="'.$this->tabladb.'" />';
			$strSalida.= $crlf.'<input type="hidden" id="hdnOperacion" value="0" />';

			foreach ($this->fields as $field) {
				$strSalida.= $crlf . $this->createField($field);
			}

			$strSalida.= $crlf.'<div class="form-group">';
			$strSalida.= $crlf.'	<div class="col-md-offset-2 col-md-4">';
			$strSalida.= $crlf.'		<button type="submit" class="btn btn-sm btn-primary"><i class="fa fa-check fa-fw" aria-hidden="true"></i> Aceptar</button>';
			$strSalida.= $crlf.'&nbsp;';
			$strSalida.= $crlf.'		<button type="reset" class="btn btn-sm btn-default" onclick="editar'. $this->tabladb .'(-1);"><i class="fa fa-times fa-fw" aria-hidden="true"></i> Cancelar</button>';
			$strSalida.= $crlf.'	</div>';
			$strSalida.= $crlf.'</div>';
			$strSalida.= '</form>';
		}

		echo $strSalida;
	}

	protected function createField($field, $prefix = '') {
		global $crlf;

		$strSalida = '';

		if ($prefix == '') {
			$fname = $field['name'];
		}
		else {
			$fname = $prefix  .'-'. $field['name'];
		}


		//if (!$field['isMasterID'] && $field['showOnForm']) {
		if ($field['showOnForm']) {
			if ($field['isHiddenInForm']) {
				$strSalida.= $crlf.'<input type="hidden" id="'.$fname.'" value="'.$field['value'].'" />';
			}
			else {
				$strSalida.= $crlf.'<div class="form-group form-group-sm '.$field['cssGroup'].'">';

				if ($field['type'] != 'checkbox') {
					$strSalida.= $crlf.'<label for="'.$fname.'" class="control-label col-md-2">'.$field['label'].':</label>';

					if ($field['size'] <= 20) {
						$strSalida.= $crlf.'<div class="col-md-2">';
					}
					elseif ($field['size'] <= 40) {
						$strSalida.= $crlf.'<div class="col-md-3">';
					}
					elseif ($field['size'] <= 80) {
						$strSalida.= $crlf.'<div class="col-md-4">';
					}
					elseif  ($field['size'] <= 160) {
						$strSalida.= $crlf.'<div class="col-md-5">';
					}
					elseif   ($field['size'] <= 200) {
						$strSalida.= $crlf.'<div class="col-md-6">';
					}
					else {
						$strSalida.= $crlf.'<div class="col-md-10">';
					}
				}

				switch ($field["type"]) {
					case 'number':
					case 'text':
					case 'email':
					case 'password':
					case 'color':
						$strSalida.= $crlf.'<input type="'.$field['type'].'" class="form-control input-sm '.$field['cssControl'].'" id="'.$fname.'" '. ($field['isID']?'disabled':'') .' '. ($field['required']?'required':'') .' '. ($field['size'] > 0?'size="'.$field['size'].'"':'') .' '. ($field['readOnly']?'readonly':'') .' '. ($field['value']!=''?'value="'.$field['value'].'"':'') .'/>';
						break;

					case 'file':
						$strSalida.= $crlf.'<input type="'.$field['type'].'" class="form-control input-sm" id="'.$fname.'" '. ($field['isID']?'disabled':'') .' '. ($field['required']?'required':'') .' '. ($field['size'] > 0?'size="'.$field['size'].'"':'') .' '. ($field['readOnly']?'readonly':'') .' '. ($field['value']!=''?'value="'.$field['value'].'"':'') .'/>';
						break;

					case 'image':
						$strSalida.= $crlf.'<div id="divPreview'.$fname.'" class="divPreview"></div>';
						$strSalida.= $crlf.'<input onchange="preview(event, $(\'#divPreview'.$fname.'\'));" type="file" class="form-control input-sm" id="'.$fname.'" '. ($field['isID']?'disabled':'') .' '. ($field['required']?'required':'') .' '. ($field['size'] > 0?'size="'.$field['size'].'"':'') .' '. ($field['readOnly']?'readonly':'') .' '. ($field['value']!=''?'value="'.$field['value'].'"':'') .'/>';
						break;

					case 'textarea':
						$strSalida.= $crlf.'<textarea class="form-control input-sm autogrow '.$field['cssControl'].'" id="'.$fname.'" '. ($field['required']?'required':'') .' '. ($field['readOnly']?'readonly':'') .'></textarea>';
						$strSalida.= $crlf.'<script type="text/javascript">';
						$strSalida.= $crlf.'$("#'.$fname.'").autogrow({vertical: true, horizontal: false});';
						$strSalida.= $crlf.'</script>';
						break;

					case 'select':
						$strSalida.= $crlf.'<select class="form-control input-sm ucase '.$field['cssControl'].'" id="'.$fname.'" '. ($field['required']?'required':'') .' '. ($field['readOnly']?'readonly':'') .' '. ($field['onChange'] !=''?'onchange="'.$field['onChange'].'"':'') .'>';
						$strSalida.= $crlf. $this->cargarCombo($field['lookupTable'], $field['lookupFieldID'], $field['lookupFieldLabel'], $field['lookupConditions'], $field['lookupOrder'], $field['value'], $field['itBlank']);
						$strSalida.= $crlf.'</select>';
						break;

					case 'selectmultiple':
						$strSalida.= $crlf.'<select class="form-control input-sm ucase selectpicker '.$field['cssControl'].'" multiple id="'.$fname.'" '. ($field['required']?'required':'') .' title="SELECCIONE" '. ($field['readOnly']?'readonly':'') .' '. ($field['onChange'] !=''?'onchange="'.$field['onChange'].'"':'') .'>';
						$strSalida.= $crlf. $this->cargarCombo($field['lookupTable'], $field['lookupFieldID'], $field['lookupFieldLabel'], $field['lookupConditions'], $field['lookupOrder'], $field['value'], $field['itBlank']);
						$strSalida.= $crlf.'</select>';
						$strSalida.= $crlf.'<script type="text/javascript">';
						$strSalida.= $crlf.'$("#'.$fname.'").selectpicker({
								actionsBox: true,
								selectAllText: "Todos",
								deselectAllText: "Ninguno",
								}).selectpicker("deselectAll");';
						$strSalida.= $crlf.'</script>';
						break;

					case 'datalist':
						$strSalida.= $crlf.'<input class="form-control input-sm '.$field['cssControl'].'" list="lst-'.$fname.'" id="'.$fname.'" '. ($field['isID']?'disabled':'') .' '. ($field['required']?'required':'') .' '. ($field['size'] > 0?'size="'.$field['size'].'"':'') .' '. ($field['readOnly']?'readonly':'') .' '. ($field['onChange'] !=''?'onchange="'.$field['onChange'].'"':'') .'/>';
						$strSalida.= $crlf.'<datalist id="lst-'.$fname.'">';
						$strSalida.= $crlf. $this->cargarCombo($field['lookupTable'], $field['lookupFieldLabel'], '', $field['lookupConditions'], $field['lookupOrder'], $field['value'], $field['itBlank']);
						$strSalida.= $crlf.'</datalist>';
						break;

					case 'checkbox':
						$strSalida.= $crlf.'<div class="col-md-4 col-md-offset-2">';
						$strSalida.= $crlf.'<label class="labelCheck ucase">';
						$strSalida.= $crlf.'<input type="checkbox" id="'.$fname.'" '. ($field['readOnly']?'readonly':'') .'> '. $field['label'];
						$strSalida.= $crlf.'</label>';
						break;

					case 'datetime':
						$strSalida.= $crlf.'<div class="input-group date margin-bottom-sm inp'.$fname.'">';
						$strSalida.= $crlf.'<input type="text" class="form-control input-sm '.$field['cssControl'].'" id="'.$fname.'"size="16" value="" readonly />';
						$strSalida.= $crlf.'<span class="input-group-addon add-on clickable"><i class="fa fa-calendar fa-fw"></i></span>';
						$strSalida.= $crlf.'</div>';
						if ($field['mirrorField'] != '') {
							$strSalida.= $crlf.'<input type="hidden" id="'. $field['mirrorField'] .'" />';
						}
						$strSalida.= $crlf.'<script type="text/javascript">';
						$strSalida.= $crlf.'$(".inp'.$fname.'").datetimepicker({';
						$strSalida.= $crlf.'	language: "es",';
						$strSalida.= $crlf.'	format: "dd-mm-yyyy hh:ii",';
						$strSalida.= $crlf.'	autoclose: true,';
						$strSalida.= $crlf.'	todayBtn: true,';
						$strSalida.= $crlf.'	todayHighlight: false,';
						$strSalida.= $crlf.'	minuteStep: 15,';
						$strSalida.= $crlf.'	pickerPosition: "bottom-left",';

						if ($field['mirrorField'] != '') {
							$strSalida.= $crlf.'	linkField: "'. $field['mirrorField'] .'",';
							$strSalida.= $crlf.'	linkFormat: "'. $field['mirrorFormat'] .'",';
						}

						if ($field['dtpOnRender'] != '') {
							$strSalida.= $crlf.'	onRender: function(date) {';
							$strSalida.= $crlf.'			return '. $field['dtpOnRender'];
							$strSalida.= $crlf.'		},';
						}

						if ($field['onChange'] == '') {
							$strSalida.= $crlf.'	});';
						}
						else {
							$strSalida.= $crlf.'	}).on("changeDate", function(ev){';
							$strSalida.= $crlf.'		'. $field['onChange'];
							$strSalida.= $crlf.'	});';
						}

						$strSalida.= $crlf.'</script>';
						break;

					case 'date':
						$strSalida.= $crlf.'<div class="input-group date margin-bottom-sm inp'.$fname.'">';
						$strSalida.= $crlf.'<input type="text" class="form-control input-sm '.$field['cssControl'].'" id="'.$fname.'"size="16" value="" readonly />';
						$strSalida.= $crlf.'<span class="input-group-addon add-on clickable"><i class="fa fa-calendar fa-fw"></i></span>';
						$strSalida.= $crlf.'</div>';
						$strSalida.= $crlf.'<script type="text/javascript">';
						$strSalida.= $crlf.'$(".inp'.$fname.'").datetimepicker({';
						$strSalida.= $crlf.'	language: "es",';
						$strSalida.= $crlf.'	format: "yyyy-mm-dd",';
						$strSalida.= $crlf.'	minView: 2,';
						$strSalida.= $crlf.'	autoclose: true,';
						$strSalida.= $crlf.'	todayBtn: true,';
						$strSalida.= $crlf.'	todayHighlight: false,';
						$strSalida.= $crlf.'	pickerPosition: "bottom-left"';

						if ($field['mirrorField'] != '') {
							$strSalida.= $crlf.'	linkField: "'. $field['mirrorField'] .'",';
							$strSalida.= $crlf.'	linkFormat: "'. $field['mirrorFormat'] .'",';
						}

						if ($field['dtpOnRender'] != '') {
							$strSalida.= $crlf.'	onRender: function(date) {';
							$strSalida.= $crlf.'			return '. $field['dtpOnRender'];
							$strSalida.= $crlf.'		},';
						}

						if ($field['onChange'] == '') {
							$strSalida.= $crlf.'	});';
						}
						else {
							$strSalida.= $crlf.'	}).on("changeDate", function(ev){';
							$strSalida.= $crlf.'		'. $field['onChange'];
							$strSalida.= $crlf.'	});';
						}

						$strSalida.= $crlf.'</script>';
						break;

					case 'time':
						$strSalida.= $crlf.'<div class="input-group date margin-bottom-sm inp'.$fname.'">';
						$strSalida.= $crlf.'<input type="text" class="form-control input-sm '.$field['cssControl'].'" id="'.$fname.'" size="16" value="" readonly />';
						$strSalida.= $crlf.'<span class="input-group-addon clickable"><i class="fa fa-calendar fa-fw"></i></span>';
						$strSalida.= $crlf.'</div>';
						$strSalida.= $crlf.'<script type="text/javascript">';
						$strSalida.= $crlf.'';
						$strSalida.= $crlf.'$(".inp'.$fname.'").datetimepicker({';
						$strSalida.= $crlf.'	language: "es",';
						$strSalida.= $crlf.'	format: "hh:ii",';
						$strSalida.= $crlf.'	startView: 1,';
						$strSalida.= $crlf.'	maxView: 1,';
						$strSalida.= $crlf.'	autoclose: true,';
						$strSalida.= $crlf.'	minuteStep: 15,';
						$strSalida.= $crlf.'	pickerPosition: "bottom-left",';
						$strSalida.= $crlf.'	fontAwesome: true,';

						if ($field['mirrorField'] != '') {
							$strSalida.= $crlf.'	linkField: "'. $field['mirrorField'] .'",';
							$strSalida.= $crlf.'	linkFormat: "'. $field['mirrorFormat'] .'",';
						}

						if ($field['dtpOnRender'] != '') {
							$strSalida.= $crlf.'	onRender: function(date) {';
							$strSalida.= $crlf.'			return '. $field['dtpOnRender'];
							$strSalida.= $crlf.'		},';
						}

						if ($field['onChange'] == '') {
							$strSalida.= $crlf.'	});';
						}
						else {
							$strSalida.= $crlf.'	}).on("changeDate", function(ev){';
							$strSalida.= $crlf.'		'. $field['onChange'];
							$strSalida.= $crlf.'	});';
						}

						if ($field['hoursDisabled'] != '') {
							$strSalida.= $crlf.'$(".inp'.$fname.'").datetimepicker("setHoursDisabled", '. $field['hoursDisabled'] .');';
						}
						$strSalida.= $crlf.'</script>';
						break;

					case "ckeditor":
						$strSalida.= $crlf.'<textarea class="form-control input-sm" id="'.$fname.'" '. ($field['required']?'required':'') .' '. ($field['readOnly']?'readonly':'') .'></textarea>';
						$strSalida.= $crlf.'<script type="text/javascript">';
						$strSalida.= $crlf.'CKEDITOR.replace( "'.$fname.'" );';
						$strSalida.= $crlf.'</script>';
						break;

					case "gmaps":
						$strSalida.= $crlf.'<input type="hidden" id="'.$fname.'" />';
						$strSalida.= $crlf.'<input type="text" class="form-control input-sm '.$field['cssControl'].'" id="'.$fname.'-buscar" placeholder="Ingrese localidad" /> ';
						$strSalida.= $crlf.'</div>';
						$strSalida.= $crlf.'<div class="col-md-2">';
						$strSalida.= $crlf.'<button type="button" class="btn btn-default" id="'.$fname.'-btnBuscar" onclick="buscarLoc($(\'#'.$fname.'-buscar\').val(), \'#'.$fname.'\')">Buscar</button>';
						$strSalida.= $crlf.'</div>';
						$strSalida.= $crlf.'</div>';
						$strSalida.= $crlf.'<div class="form-group">';
						$strSalida.= $crlf.'<div class="col-md-10 col-md-offset-2">';
						$strSalida.= $crlf.'<div id="map" style="height: 500px;" data-campo="#'.$fname.'"></div>';
						$strSalida.= $crlf.'<script type="text/javascript">';
						$strSalida.= $crlf.'$(document).ready(function() {';
						$strSalida.= $crlf.'	initMap();';
						$strSalida.= $crlf.'});';
						$strSalida.= $crlf.'</script>';
						break;

					default:
						$strSalida.= $crlf.'<input type="text" class="form-control input-sm '.$field['cssControl'].'" id="'.$fname.'" '. ($field['isID']?'disabled':'') .' '. ($field['required']?'required':'') .' '. ($field['size'] > 0?'size="'.$field['size'].'"':'') .' '. ($field['readOnly']?'readonly':'') .'/>';
						break;
				}

				$strSalida.= $crlf.'</div>'; //col-md
				$strSalida.= $crlf.'</div>'; //form-group
			}
		}

		return $strSalida;
	}

	public function listar($strFiltro="") {
		global $config, $crlf;

		$strSalida = '';

		if (isset($this->fields)) {
			$strSQL = "SELECT ";

			$strFields = '';
			foreach ($this->fields as $field) {
				if ($field['type'] != "calcfield") {
					if ($strFields != '') {
						$strFields.= ', ';
					}

					if ($field['formatDb'] == '') {
						$strFields.= $field['name'];
					}
					else {
						$strFields.= $field['formatDb'];
					}
				}
			}
			$strSQL.= $strFields;

			$strSQL.= " FROM ". $this->tabladb;

			if ($this->masterField != '') {
				if (isset($_GET[$this->masterField])) {
					$strSQL.= " WHERE ". $this->masterField ." = '" . $_GET[$this->masterField] ."'";
				}
				elseif (isset($_POST[$this->masterField])) {
					$strSQL.= " WHERE ". $this->masterField ." = '" . $_POST[$this->masterField] ."'";
				}

				if ($strFiltro != "") {
					$strSQL.= " AND ". $strFiltro;
				}
			}
			else {
				if ($strFiltro != "") {
					$strSQL.= " WHERE ". $strFiltro;
				}
			}

			if ($this->order != '')
				$strSQL.= " ORDER BY ". $this->order;

			$tabla = $config->cargarTabla($strSQL);

			if ($tabla) {
				if ($tabla->num_rows > 0) {
					$strSalida.= $crlf.'<table class="table table-striped table-bordered table-hover table-condensed table-responsive">';
					$strSalida.= $crlf.'<tr>';
					foreach ($this->fields as $field) {
						if ($field['showOnList'])
							if (!$field['isHiddenInList'])
								$strSalida.= $crlf.'<th class="text-'. $field['txtAlign'] .'">'. $field['label'] .'</th>';
					}

					//Botones opcionales
					if (count($this->btnList) > 0) {
						for ($I = 0; $I < count($this->btnList); $I++) {
							$strSalida.= $crlf.'<th></th>';
						}
					}

					//Editar
					if ($this->allowEdit) {
						$strSalida.= $crlf.'<th></th>';
					}

					//Borrar
					if ($this->allowDelete) {
						$strSalida.= $crlf.'<th></th>';
					}
					$strSalida.= $crlf.'</tr>';

					$strFootValue = 0;
					$colFooter = 0;

					while ($fila = $tabla->fetch_assoc()) {
						$col = 0;

						$strSalida.= $crlf.'<tr>';

						foreach ($this->fields as $field) {
							if ($field['showOnList']) {
								if ($field['isHiddenInList']) {
									$strSalida.= $crlf.'<input type="hidden" id="'.$field['name']. $fila[$this->IDField].'" value="'.htmlentities($fila[$field['name']]).'" />';
								}
								else {
									switch ($field["type"]) {
										case 'select':
											$strSalida.= $crlf.'<td class="ucase text-'. $field['txtAlign'] .' '. $field['cssControl'] .'">';
											$strSalida.= $crlf. $config->buscarDato("SELECT {$field['lookupFieldLabel']} FROM {$field['lookupTable']} WHERE {$field['lookupFieldID']} = {$fila[$field['name']]}");
											$strSalida.= $crlf.'<input type="hidden" id="'.$field['name']. $fila[$this->IDField].'" value="'.$fila[$field['name']].'" />';
											$strSalida.= $crlf.'</td>';
											break;

										case 'calcfield':
											$strSalida.= $crlf.'<td class="text-'. $field['txtAlign'] .' '. $field['cssControl'] .'">';

											$post['field'] = $field['name'];
											$post['dato'] = $fila[$this->IDField];
											$dato = $this->customFunc($post);

											$strSalida.= $crlf. $dato;
											$strSalida.= $crlf.'</td>';
											break;

										case 'image':
											$strSalida.= $crlf.'<td>';
											$strSalida.= $crlf.'<input type="hidden" id="'.$field['name']. $fila[$this->IDField].'" value="'.$fila[$field['name']].'" />';
											$strSalida.= $crlf.'<img src="'. $fila[$field['name']].'" class="thumbnailChico">';
											$strSalida.= $crlf.'</td>';
											break;

										default:
											$strSalida.= $crlf.'<td class="text-'. $field['txtAlign'] .' '. $field['cssControl'] .'" id="'.$field['name'] . $fila[$this->IDField].'">'.$fila[$field['name']].'</td>';
											break;
									}

									if ($field['name'] == $this->footerField) {
										$colFooter = $col;

										switch ($this->footerFunc) {
											case "SUM":
												$strFootValue+= floatval($fila[$field['name']]);
												break;

											case "COUNT":
												$strFootValue++;
												break;
										}
									}

									$col++;
								}
							}
						}

						//Botones
						//$strSalida.= $crlf.'<td class="text-right">';
						//Opcionales
						if (count($this->btnList) > 0) {
							for ($I = 0; $I < count($this->btnList); $I++) {
								$strSalida.= $crlf.'<td class="text-center"><button class="btn btn-sm '. $this->btnList[$I][2] .'" onclick="'. $this->btnList[$I][1] .'(\''.$fila[$this->IDField].'\')">'. $this->btnList[$I][0] .'</button></td>';
							}
						}

						//Editar
						if ($this->allowEdit) {
							$strSalida.= $crlf.'<td class="text-center"><button class="btn btn-sm btn-info" onclick="editar'. $this->tabladb .'(\''.$fila[$this->IDField].'\')"><i class="fa fa-pencil fa-fw" aria-hidden="true"></i> Editar</button></td>';
						}
						//Borrar
						if ($this->allowDelete) {
							$strSalida.= $crlf.'<td class="text-center"><button class="btn btn-sm btn-danger" onclick="borrar'. $this->tabladb .'(\''.$fila[$this->IDField].'\')"><i class="fa fa-trash-o fa-fw" aria-hidden="true"></i> Borrar</button></td>';
						}
						$strSalida.= $crlf.'</td>';

						$strSalida.= $crlf.'</tr>';
					}

					if ($this->footerField != '') {
						$strSalida.= $crlf.'<tfoot><tr>';

						for ($I = 0; $I < $col; $I++) {
							if ($I == $colFooter) {
								$strSalida.= $crlf.'<td class="text-'. $this->fields[$this->footerField]['txtAlign'] .'">';

								switch ($this->footerFunc) {
									case "SUM":
										$strSalida.= $crlf.'TOTAL: '. (is_float($strFootValue)? number_format($strFootValue, 2) : $strFootValue);
										break;

									case "COUNT":
										$strSalida.= $crlf.'CANT: '. (is_float($strFootValue)? number_format($strFootValue, 2) : $strFootValue);
										break;
								}
							}
							else {
								$strSalida.= $crlf.'<td>';
							}

							$strSalida.= $crlf.'</td>';
						}

						$strSalida.= $crlf.'</tr></tfoot>';
					}

					$strSalida.= $crlf.'</table>';
				}
				else {
					$strSalida.= "<h3>No hay datos para mostrar</h3>";
				}
				$tabla->free();
			}
			else {
				$strSalida.= "<h3>No hay datos para mostrar</h3>";
			}
		}
		else {
			$strSalida = "<h3>No hay datos para mostrar</h3>";
		}

		echo $strSalida;
	}

	public function script() {
		global $config, $crlf;

		$strSalida = '';

		if (count($this->jsFiles) > 0) {
			for ($I = 0; $I < count($this->jsFiles); $I++) {
				$strSalida.= $crlf.'	<script src="'. $config->raiz . $this->jsFiles[$I] .'"></script>';
			}
		}

		if ($this->gmaps) {
			$strSalida.= $crlf.'	<script async defer src="https://maps.googleapis.com/maps/api/js?key='.$this->gmapsApiKey.'"></script>';
		}

		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'<script>';
		$strSalida.= $crlf.'var blnEdit = false;';
		$strSalida.= $crlf.'';


		//Document Ready
		$strSalida.= $crlf.'$(document).ready(function() {';
		$strSalida.= $crlf.'	$("#actualizando").hide();';
		$strSalida.= $crlf.'	$("#divMsj").hide();';
		$strSalida.= $crlf.'	$("#frm'. $this->tabladb .'").submit(function() {aceptar'. $this->tabladb .'();});';
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'	$("button[type=\'reset\']").on("click", function(event){';
		$strSalida.= $crlf.'		event.preventDefault();';
		//$strSalida.= $crlf.'		$(this).closest("form").get(0).reset();';
		$strSalida.= $crlf.'		$("#frm'. $this->tabladb .'")[0].reset();';
		$strSalida.= $crlf.'		$("textarea.autogrow").removeAttr("style");';
		$strSalida.= $crlf.'		$(".divPreview").html("");';

		if ($this->masterField != '' && isset($_GET[$this->masterField])) {
			$strSalida.= $crlf.'		$("#'. $this->masterField .'").val('. $_GET[$this->masterField] .');';
		}

		$strSalida.= $crlf.'	});';
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'	'.$this->jsOnLoad;

		global $item;
		if ($item != "") {
			$strSalida.= $crlf.'editar'. $this->tabladb .'("'. $item .'");';
			$strSalida.= $crlf.'';
		}
		$strSalida.= $crlf.'});';

		//Preview IMG
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'function preview(event, divPreview) {';
		$strSalida.= $crlf.'	divPreview.html("");';
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'	var files = event.target.files; //FileList object';
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'	for(var i = 0; i< files.length; i++)';
		$strSalida.= $crlf.'	{';
		$strSalida.= $crlf.'		var file = files[i];';
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'		//Solo imagenes';
		$strSalida.= $crlf.'		if(!file.type.match("image"))';
		$strSalida.= $crlf.'			continue;';
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'		var picReader = new FileReader();';
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'		picReader.addEventListener("load",function(event){';
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'			var picFile = event.target;';
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'			divPreview.append(\'<img id="img\' + divPreview.children().length + \'" class="thumbnail" src="\' + picFile.result + \'" />\');';
		$strSalida.= $crlf.'		});';
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'		//Leer la imagen';
		$strSalida.= $crlf.'		picReader.readAsDataURL(file);';
		$strSalida.= $crlf.'	}';
		$strSalida.= $crlf.'}';

		//Listar
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'function listar'. $this->tabladb .'() {';
		$strSalida.= $crlf.'	$("#divDatos").html("");';
		$strSalida.= $crlf.'	$.post("php/tablaHandler.php",';
		$strSalida.= $crlf.'		{ operacion: "10"';
		$strSalida.= $crlf.'			, tabla: "'.$this->tabladb.'"';

		if ($this->masterField != '' && isset($_GET[$this->masterField])) {
			$strSalida.= $crlf.'			, '. $this->masterField .': "'. $_GET[$this->masterField] .'"';
		}
		$strSalida.= $crlf.'		},';
		$strSalida.= $crlf.'		function(data) {';
		$strSalida.= $crlf.'			$("#actualizando").hide();';
		$strSalida.= $crlf.'			$("#divDatos").html(data);';
		$strSalida.= $crlf.'		}';
		$strSalida.= $crlf.'	);';
		$strSalida.= $crlf.'}';

		//Borrar
		if ($this->allowDelete) {
			$strSalida.= $crlf.'';
			$strSalida.= $crlf.'function borrar'. $this->tabladb .'(strID){';
			$strSalida.= $crlf.'	if (confirm("Desea borrar '.$this->tituloSingular. ($this->labelField!=''?' " + $("#'.$this->labelField.'" + strID).html()':' seleccionado"') . ' + "?" )) {';
			$strSalida.= $crlf.'		$("#hdnOperacion").val("2");';
			$strSalida.= $crlf.'		$("#'.$this->IDField.'").val(strID);';
			$strSalida.= $crlf.'		aceptar'. $this->tabladb .'();';
			$strSalida.= $crlf.'	}';
			$strSalida.= $crlf.'}';
		}

		//Editar
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'function editar'. $this->tabladb .'(strID){';
		$strSalida.= $crlf.'	if (strID > 0) {';
		$strSalida.= $crlf.'		$("#frm'. $this->tabladb .'").fadeIn();';
		$strSalida.= $crlf.'		$("html, body").animate({';
		$strSalida.= $crlf.'			scrollTop: $("#frm'. $this->tabladb .'").offset().top';
		$strSalida.= $crlf.'		}, 1000);';
		$strSalida.= $crlf.'		$("#hdnOperacion").val("1");';
		$strSalida.= $crlf.'		blnEdit = true;';
		$strSalida.= $crlf.'		$("#frm'. $this->tabladb .'").find("input[type!=\'hidden\'][disabled!=disabled]").focus()';

		if (isset($this->fields)) {
			foreach ($this->fields as $field) {
				if ($field['showOnForm']) {
					if ($field['showOnList']) {
						if (($field['isHiddenInList']) || ($field['type'] == "select") || ($field['type'] == "selectmultiple")) {

							switch ($field["type"]) {
								case "ckeditor":
									$strSalida.= $crlf.'		CKEDITOR.instances.'.$field['name'].'.setData($("#'.$field['name'].'" + strID).val());';
									break;

								case "selectmultiple":
									$strSalida.= $crlf.'		$("#'.$field['name'].'").selectpicker("val", $("#'.$field['name'].'" + strID).val().split(","));';
									break;

								case "image":
									$strSalida.= $crlf.'		$("#divPreview'.$field['name'].'").html(\'<img id="img0" class="thumbnail" src="\' + $("#'.$field['name'].'" + strID).val() + \'" />\');';
									break;

								default:
									$strSalida.= $crlf.'		$("#'.$field['name'].'").val($("#'.$field['name'].'" + strID).val());';
									break;
							}

							/*
							if ($field['type'] == "ckeditor") {
								$strSalida.= $crlf.'		CKEDITOR.instances.'.$field['name'].'.setData($("#'.$field['name'].'" + strID).val());';
							}
							elseif ($field['type'] != "selectmultiple") {
								if ($field['type'] != 'file')
									$strSalida.= $crlf.'		$("#'.$field['name'].'").val($("#'.$field['name'].'" + strID).val());';
							}
							else  {
								$strSalida.= $crlf.'		$("#'.$field['name'].'").selectpicker("val", $("#'.$field['name'].'" + strID).val().split(","));';
							}
							*/
						}
						else {
							switch ($field["type"]) {
								case "image":
									$strSalida.= $crlf.'		$("#divPreview'.$field['name'].'").html(\'<img id="img0" class="thumbnail" src="\' + $("#'.$field['name'].'" + strID).val() + \'" />\');';
									break;

								default:
									$strSalida.= $crlf.'		$("#'.$field['name'].'").val($("#'.$field['name'].'" + strID).html());';
									break;
							}
						}
					}
					elseif ($field['type'] == "password") {
						$strSalida.= $crlf.'		$("#'.$field['name'].'").val("****");';
					}

					if ($field['onChange'] != '') {
						$strSalida.= $crlf.'		$("#'.$field['name'].'").trigger("change");';
					}

					switch ($field['type']) {
						case 'image':
						case 'file':
							$strSalida.= $crlf.'		$("#'.$field['name'].'").removeAttr("required");';
							break;

						case 'textarea':
							$strSalida.= $crlf.'		$("#'.$field['name'].'").autogrow({vertical: true, horizontal: false});';
							break;

						case 'calcfield':
							$strSalida.= $crlf.'		if (typeof calcField == "function") {';
							$strSalida.= $crlf.'			calcField("'.$field['name'].'");';
							$strSalida.= $crlf.'		}';
							$strSalida.= $crlf.'';
							break;

						case 'datetime':
						case 'date':
						case 'time':
							$strSalida.= $crlf.'		$(".inp'.$field['name'].'").datetimepicker("show");';
							$strSalida.= $crlf.'		$(".inp'.$field['name'].'").datetimepicker("hide");';
							$strSalida.= $crlf.'';
							break;

						case 'gmaps':
							$strSalida.= $crlf.'		if (marker != null)';
							$strSalida.= $crlf.'				marker.setMap(null);';
							$strSalida.= $crlf.'';
							$strSalida.= $crlf.'		if ($("#'.$field['name'].'").val() != "") {';
							$strSalida.= $crlf.'';
							$strSalida.= $crlf.'			var aux = $("#'.$field['name'].'").val();';
							$strSalida.= $crlf.'			var lat = aux.substring(0, aux.indexOf(","));';
							$strSalida.= $crlf.'			var lng = aux.substring(aux.indexOf(",")+1);';
							$strSalida.= $crlf.'';
							$strSalida.= $crlf.'				var pos = new google.maps.LatLng(lat, lng);';
							$strSalida.= $crlf.'';
							$strSalida.= $crlf.'				marker = new google.maps.Marker({';
							$strSalida.= $crlf.'					position: pos,';
							$strSalida.= $crlf.'					map: map';
							$strSalida.= $crlf.'				});';
							$strSalida.= $crlf.'';
							$strSalida.= $crlf.'				map.setCenter(pos);';
							$strSalida.= $crlf.'		}';
							break;
					}
				}
			}
		}
		$strSalida.= $crlf.'		'. $this->jsOnEdit;

		$strSalida.= $crlf.'	}';

		$strSalida.= $crlf.'	else {';
		$strSalida.= $crlf.'		if (strID == 0) {';
		$strSalida.= $crlf.'			$("#frm'. $this->tabladb .'").fadeIn(function() {';
		$strSalida.= $crlf.'				$("#frm'. $this->tabladb .'").find("input[type!=\'hidden\'][disabled!=disabled]").focus()';
		$strSalida.= $crlf.'			});';
		$strSalida.= $crlf.'		}';
		$strSalida.= $crlf.'		else {';
		$strSalida.= $crlf.'			$("#frm'. $this->tabladb .'").fadeOut();';
		$strSalida.= $crlf.'		}';
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'		$("#hdnOperacion").val("0");';
		$strSalida.= $crlf.'		blnEdit = false;';
		$strSalida.= $crlf.'		$(".divPreview").html("");';

		if (isset($this->fields)) {
			foreach ($this->fields as $field) {
				if ($field['showOnForm']) {

					if ($field['type'] == "ckeditor") {
						$strSalida.= $crlf.'		CKEDITOR.instances.'.$field['name'].'.setData("");';
					}
					else {
						$strSalida.= $crlf.'		$("#'.$field['name'].'").val("'.$field['value'].'");';
					}

					if ($field['mirrorField'] != '') {
						$strSalida.= $crlf.'		$("#'.$field['mirrorField'].'").val("'.$field['value'].'");';
					}

					switch ($field['type']) {
						case 'image':
							if ($field['required']) {
								$strSalida.= $crlf.'		$("#'.$field['name'].'").attr("required", true);';
							}
							break;

						case 'textarea':
							$strSalida.= $crlf.'		$("#'.$field['name'].'").autogrow({vertical: true, horizontal: false});';
							break;

						case 'selectmultiple':
							$strSalida.= $crlf.'		$("#'.$field['name'].'").selectpicker("deselectAll");';
							break;

						case 'gmaps':
							$strSalida.= $crlf.'		if (marker != null)';
							$strSalida.= $crlf.'			marker.setMap(null);';
							$strSalida.= $crlf.'		map.setCenter({lat: '.$this->gmapsCenterLat.', lng: '.$this->gmapsCenterLng.'});';
							break;
					}
				}
			}

			if ($this->masterField != '' && isset($_GET[$this->masterField])) {
				$strSalida.= $crlf.'		$("#'. $this->masterField .'").val('. $_GET[$this->masterField] .');';
			}
		}

		//Detalles
		/*
		if (isset($this->details)) {
			foreach ($this->details as $tabla) {
				$strSalida.= $crlf.'';
				$strSalida.= $crlf.'		while ($("#'. $tabla->tabladb .'").children().length > 1) {';
				$strSalida.= $crlf.'			$($("#'. $tabla->tabladb .'").children()[1]).remove();';
				$strSalida.= $crlf.'		}';
				$strSalida.= $crlf.'';
				$strSalida.= $crlf.'		$("#'. $tabla->tabladb .'").children().find("input").val("");';
				$strSalida.= $crlf.'		$("#'. $tabla->tabladb .'").children().find("textarea").val("");';
			}
		}
		*/

		$strSalida.= $crlf.'	}';
		$strSalida.= $crlf.'}';

		//Aceptar
		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'function aceptar'. $this->tabladb .'(){';
		$strSalida.= $crlf.'	$("#actualizando").show();';
		$strSalida.= $crlf.'	var frmData = new FormData();';
		$strSalida.= $crlf.'	if ($("#hdnOperacion").val() != "2") {';
		$strSalida.= $crlf.'		if (typeof validar == "function") {';
		$strSalida.= $crlf.'			if (!validar())';
		$strSalida.= $crlf.'				return;';
		$strSalida.= $crlf.'		}';
		$strSalida.= $crlf.'	}';

		$strSalida.= $crlf.'	frmData.append("operacion", $("#hdnOperacion").val());';
		$strSalida.= $crlf.'	frmData.append("tabla", "'.$this->tabladb.'");';
		if (isset($this->fields)) {
			foreach ($this->fields as $field) {
				if (($field['showOnForm']) && ($field['type'] != 'calcfield')) {
					switch ($field['type']) {
						case "checkbox":
							$strSalida.= $crlf.'	frmData.append("'.$field['name'].'", $("#'.$field['name'].'").prop("checked") ? 1 : 0);';
							break;

						case "file":
						case "image":
							$strSalida.= $crlf.'	if ($("#'.$field['name'].'").get(0).files[0] != null)';
							$strSalida.= $crlf.'		frmData.append("'.$field['name'].'", $("#'.$field['name'].'").get(0).files[0]);';
							break;

						case "ckeditor":
							$strSalida.= $crlf.'	frmData.append("'.$field['name'].'", CKEDITOR.instances.'.$field['name'].'.getData());';
							break;

						default:
							if ($field['mirrorField'] == '') {
								$strSalida.= $crlf.'	frmData.append("'.$field['name'].'", $("#'.$field['name'].'").val());';
							}
							else {
								$strSalida.= $crlf.'	frmData.append("'.$field['name'].'", $("#'.$field['mirrorField'].'").val());';
							}
							break;
					}
				}
			}
		}
		$strSalida.= $crlf.'';

		//Detalles
		/*
		if (isset($this->details)) {
			foreach ($this->details as $tabla) {
				$strSalida.= $crlf.'//Detalles de '. $tabla->titulo;
				$strSalida.= $crlf.'	frmData.append("tbDetalle-'.$tabla->tabladb.'", $("#'. $tabla->tabladb .'").children().length);';
				$strSalida.= $crlf.'';

				$strSalida.= $crlf.'	var I = 1;';

				$strSalida.= $crlf.'	$("#'. $tabla->tabladb .'").children().each( function() {';

				if (isset($tabla->fields)) {
					foreach ($tabla->fields as $field) {
						if ($field['showOnForm']) {
							if ($field['isMasterID']) {
								$strSalida.= $crlf.'		frmData.append("'.$tabla->tabladb.'-'.$field['name'].'"+I, $("#'.$field['name'].'").val());';
							}
							else {
								$strSalida.= $crlf.'		frmData.append("'.$tabla->tabladb.'-'.$field['name'].'"+I, $(this).find("[id^=\''.$tabla->tabladb.'-'.$field['name'].'\']").val());';
							}
						}
					}
				}
				$strSalida.= $crlf.'		I++;';
				$strSalida.= $crlf.'	});';

			}
		}
		*/

		$strSalida.= $crlf.'';
		$strSalida.= $crlf.'	if (window.XMLHttpRequest)';
		$strSalida.= $crlf.'	{// code for IE7+, Firefox, Chrome, Opera, Safari';
		$strSalida.= $crlf.'		xmlhttp = new XMLHttpRequest();';
		$strSalida.= $crlf.'	}';
		$strSalida.= $crlf.'	else';
		$strSalida.= $crlf.'	{// code for IE6, IE5';
		$strSalida.= $crlf.'		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");';
		$strSalida.= $crlf.'	}';
		$strSalida.= $crlf.'	';
		$strSalida.= $crlf.'	xmlhttp.onreadystatechange = function() {';
		$strSalida.= $crlf.'		if (xmlhttp.readyState==4 && xmlhttp.status==200) {';
		$strSalida.= $crlf.'			$("#txtHint").html(xmlhttp.responseText);';
		$strSalida.= $crlf.'	';
		$strSalida.= $crlf.'			if (xmlhttp.responseText.indexOf("Error") == -1) {';
		$strSalida.= $crlf.'				$("#divMsj").removeClass("alert-danger");';
		$strSalida.= $crlf.'				$("#divMsj").addClass("alert-success");';
		$strSalida.= $crlf.'				$(".selectpicker").selectpicker("deselectAll");';
		$strSalida.= $crlf.'				editar'. $this->tabladb .'(-1);';
		$strSalida.= $crlf.'				listar'. $this->tabladb .'();';
		$strSalida.= $crlf.'			}';
		$strSalida.= $crlf.'			else {';
		$strSalida.= $crlf.'				$("#divMsj").removeClass("alert-success");';
		$strSalida.= $crlf.'				$("#divMsj").addClass("alert-danger");';
		$strSalida.= $crlf.'			}';
		$strSalida.= $crlf.'	';
		$strSalida.= $crlf.'			$("#actualizando").hide();';
		$strSalida.= $crlf.'			$("#divMsj").show();';
		$strSalida.= $crlf.'		}';
		$strSalida.= $crlf.'	};';
		$strSalida.= $crlf.'	';
		$strSalida.= $crlf.'	xmlhttp.open("POST","php/tablaHandler.php",true);';
		$strSalida.= $crlf.'	xmlhttp.send(frmData);';
		$strSalida.= $crlf.'}';


		//Detalles
		/*
		if (isset($this->details)) {
			$strSalida.= $crlf.'';
			$strSalida.= $crlf.'function agregarChildren(child) {';
			$strSalida.= $crlf.'	var CantChild = $("#" + child).children().length;';
			$strSalida.= $crlf.'	var idChild = $("#" + child).children()[0].id;';
			$strSalida.= $crlf.'';

			$strSalida.= $crlf.'	var childNew = $("#" + idChild).clone();';
			$strSalida.= $crlf.'';

			$strSalida.= $crlf.'	var CantChildOld = CantChild;';
			$strSalida.= $crlf.'	CantChild++;';
			$strSalida.= $crlf.'';

			$strSalida.= $crlf.'	childNew.attr("id", "div" + child + CantChild);';
			$strSalida.= $crlf.'';

			$strSalida.= $crlf.'	childNew.html(childNew.html().replace("quitarChildren(\'" + idChild, "quitarChildren(\'div" + child  + CantChild));';
			$strSalida.= $crlf.'';

			$strSalida.= $crlf.'	childNew.find("input").val("");';
			$strSalida.= $crlf.'	childNew.find("textarea").val("");';

			$strSalida.= $crlf.'';

			$strSalida.= $crlf.'	childNew.find("input, textarea, select").each(function() {';
			$strSalida.= $crlf.'		$(this).attr("id", $(this).attr("id") + CantChild);';
			$strSalida.= $crlf.'	});';
			$strSalida.= $crlf.'';

			//$strSalida.= $crlf.'	childNew.css("display", "none");';
			$strSalida.= $crlf.'';

			$strSalida.= $crlf.'	childNew.appendTo($("#" + child));';
			//$strSalida.= $crlf.'	childNew.fadeIn("slow");';

			$strSalida.= $crlf.'}';
		}
		*/

		if ($this->gmaps) {
		//InitMap
			$strSalida.= $crlf.'';
			$strSalida.= $crlf.'var map;';
			$strSalida.= $crlf.'var marker;';
			$strSalida.= $crlf.'var geocoder;';
			$strSalida.= $crlf.'';
			$strSalida.= $crlf.'function initMap() {';
			$strSalida.= $crlf.'	map = new google.maps.Map(document.getElementById("map"), {';
			$strSalida.= $crlf.'		center: {lat: '.$this->gmapsCenterLat.', lng: '.$this->gmapsCenterLng.'},';
			$strSalida.= $crlf.'		zoom: 8';
			$strSalida.= $crlf.'	});';
			$strSalida.= $crlf.'';
			$strSalida.= $crlf.'	map.addListener("click", function(event){';
			$strSalida.= $crlf.'		if (marker != null)';
			$strSalida.= $crlf.'			marker.setMap(null);';
			$strSalida.= $crlf.'		';
			$strSalida.= $crlf.'		marker = new google.maps.Marker({';
			$strSalida.= $crlf.'			position: event.latLng,';
			$strSalida.= $crlf.'			map: map';
			$strSalida.= $crlf.'		});';
			$strSalida.= $crlf.'';
			$strSalida.= $crlf.'		var campo = $("#map").data("campo");';
			$strSalida.= $crlf.'		$(campo).val(event.latLng.lat() + "," + event.latLng.lng()); ';
			$strSalida.= $crlf.'	});';
			$strSalida.= $crlf.'	geocoder = new google.maps.Geocoder();';
			$strSalida.= $crlf.'}';

			$strSalida.= $crlf.'';
			$strSalida.= $crlf.'function buscarLoc(address, campo) {';
			$strSalida.= $crlf.'	if (address != "") {';
			$strSalida.= $crlf.'		geocoder.geocode({"address": address}, function(results, status) {';
			$strSalida.= $crlf.'			if (status === google.maps.GeocoderStatus.OK) {';
			$strSalida.= $crlf.'				map.setCenter(results[0].geometry.location);';
			$strSalida.= $crlf.'';
			$strSalida.= $crlf.'				if (marker != null)';
			$strSalida.= $crlf.'					marker.setMap(null);';
			$strSalida.= $crlf.'';
			$strSalida.= $crlf.'				marker = new google.maps.Marker({';
			$strSalida.= $crlf.'					map: map,';
			$strSalida.= $crlf.'					position: results[0].geometry.location';
			$strSalida.= $crlf.'				});';
			$strSalida.= $crlf.'';
			$strSalida.= $crlf.'				$(campo).val(results[0].geometry.location.lat() + "," + results[0].geometry.location.lng());';
			$strSalida.= $crlf.'			} else {';
			$strSalida.= $crlf.'				alert("Localidad no encontrada");';
			$strSalida.= $crlf.'			}';
			$strSalida.= $crlf.'		});';
			$strSalida.= $crlf.'	}';
			$strSalida.= $crlf.'}';
		}

		$strSalida.= $crlf.'</script>';
		$strSalida.= $crlf;

		echo $strSalida;
	}

	public function insertar($datos) {
		try {
			global $config;
			$strCampos = "";
			$strValores = "";
			$strID = "";

			$strSQL = "INSERT INTO ". $this->tabladb;

			foreach ($datos as $name => $value) {

				if (strcmp($this->IDField,$name) == 0) {
					$value = $config->buscarDato("SELECT COALESCE(MAX($name), 0) + 1 Numero FROM $this->tabladb");

					$strID = $value;
				}

				if ($strCampos != "") {
					$strCampos.= ", ";
					$strValores.= ", ";
				}

				$strCampos.= $name;
				if ($this->fields[$name]['isMD5']) {
					$strValores.= "'".md5($value)."'";	
				}
				else {
					$strValores.= "'$value'";
				}
			}
			$strSQL.= " ($strCampos)";
			$strSQL.= " VALUES ($strValores)";

			$result["estado"] = $config->ejecutarCMD($strSQL);
			$result["id"] = $strID;

			return json_encode($result);

		} catch (Exception $e) {
			return false;
		}
	}

	public function editar($datos) {
		try {
			global $config;
			$strCampos = "";
			$strWhere = "";
			$strID = "";

			$strSQL = "UPDATE ". $this->tabladb;

			foreach ($datos as $name => $value) {

				if (strcmp($this->IDField,$name) != 0) {
					switch ($this->fields[$name]['type']) {
						case "password":
							if ($value != "****") {
								if ($strCampos != "") {
									$strCampos.= ", ";
								}

								if ($this->fields[$name]['isMD5']) {
									$strCampos.= $name." = '".md5($value)."'";
								}
								else {
									$strCampos.= $name." = '$value'";
								}
							}
							break;

						default:
							if ($strCampos != "") {
								$strCampos.= ", ";
							}

							if ($this->fields[$name]['isMD5']) {
								$strCampos.= $name." = '".md5($value)."'";
							}
							else {
								$strCampos.= $name." = '$value'";
							}
							break;
					}
				}
				else {
					if ($strWhere != "") {
						$strWhere.= " AND ";
					}

					$strWhere.= $name." = '$value'";

					$strID = $value;
				}
			}
			$strSQL.= " SET ". $strCampos;
			$strSQL.= " WHERE ". $strWhere  ;

			$result["estado"] = $config->ejecutarCMD($strSQL);
			$result["id"] = $strID;

			return json_encode($result);

		} catch (Exception $e) {
			return false;
		}
	}

	public function borrar($datos, $filtro='') {
		try {
			global $config;
			$strWhere = "";
			$strID = "";

			$strSQL = "DELETE FROM ". $this->tabladb;

			if ($filtro != '') {
				$strID = $filtro;

				$strWhere = $filtro;
			}
			else {
				foreach ($datos as $name => $value) {

					if (strcmp($this->IDField,$name) == 0) {
						$strID = $value;

						if ($strWhere != "") {
							$strWhere.= " AND ";
						}

						$strWhere.= $name." = '$value'";
					}
				}
			}

			$strSQL.= " WHERE ". $strWhere  ;

			$result["estado"] = $config->ejecutarCMD($strSQL);
			$result["id"] = $strID;

			return json_encode($result);

		} catch (Exception $e) {
			return false;
		}
	}

	public function cargarCombo($tabla, $CampoNumero, $CampoTexto, $filtro = "", $orden = "", $seleccion = "", $itBlank = false) {
		global $config, $crlf;

		$strSQL = "SELECT ". $CampoNumero;
		if ($CampoTexto != "")
			$strSQL.= ",". $CampoTexto;
		$strSQL.= " FROM ". $tabla;

		if ($filtro != "")
			$strSQL.= " WHERE $filtro";

		if ($orden != "")
			$strSQL.= " ORDER BY $orden";

		$tabla = $config->cargarTabla($strSQL);

		$strSalida = "";
		if ($itBlank) {
			$strSalida.= $crlf.'<option value="-1">Seleccione...</option>';
		}

		while ($fila = $tabla->fetch_assoc()) {
			if ($CampoTexto != "") {
				if (strcmp($fila[$CampoNumero], $seleccion) != "0")
					$strSalida.= $crlf.'<option value="'.$fila[$CampoNumero].'">'.htmlentities($fila[$CampoTexto]).'</option>';
					else
						$strSalida.= $crlf.'<option value="'.$fila[$CampoNumero].'" selected>'.htmlentities($fila[$CampoTexto]).'</option>';
			}
			else {
				if (strcmp($fila[$CampoNumero], $seleccion) != "0")
					$strSalida.= $crlf.'<option value="'.$fila[$CampoNumero].'" />';
					else
						$strSalida.= $crlf.'<option value="'.$fila[$CampoNumero].'" selected />';
			}
		}

		return $strSalida;
	}

	public function customFunc($post) {
		return true;
	}
}
?>