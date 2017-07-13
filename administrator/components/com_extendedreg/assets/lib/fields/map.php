<?php 
/**
 * @package		ExtendedReg
 * @version		1.01
 * @date		2013-11-18
 * @copyright	Copyright (C) 2007 - 2013 jVitals Digital Technologies Inc. All rights reserved.
 * @license		http://www.gnu.org/copyleft/gpl.html GNU/GPLv3 or later
 * @link		http://jvitals.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class erFieldMap extends erField implements erFieldInterface {

	function __construct($record) {
		parent::__construct($record);
		
		$lang = JFactory::getLanguage();
		$lang->load('com_extendedreg_field_map');
		$doc = JFactory::getDocument();
		$doc->addScript('http://maps.googleapis.com/maps/api/js?sensor=true');
	}
	
	public function getSqlType() {
		return  "varchar(255)";
	}
	
	public function hasParams() {
		return true;
	}
	
	public function hasOptions() {
		return false;
	}
	
	public function isMultiselect() {
		return false;
	}
	
	public function hasFormField() {
		return true;
	}
	
	public function getJavascptValidation() {
		return false;
	}
	
	public function hideTitle() {
		return true;
	}
	
	public function getSearchHtml($value, $name = '') {
		return '';
	}
	
	public function isExportable() {
		return false;
	}
	
	protected function getGoogleMap($value) {
		$def_latitude = trim($this->_params->get('def_latitude', '42.4923525914282'));
		$def_longitude = trim($this->_params->get('def_longitude', '-96.4215087890625'));
		$latlang = strlen($value) > 0 ? $value : $def_latitude . ', ' . $def_longitude ;
		$def_zoom = (int)trim($this->_params->get('def_zoom', '2'));
		$latitude_field = trim($this->_params->get('latitude_field', ''));
		$longitude_field = trim($this->_params->get('longitude_field', ''));
		$decimal_precision = (int)trim($this->_params->get('decimal_precision', '14'));
		
		if ((int)strlen($latitude_field) > 0 && (int)strlen($longitude_field) > 0) {
			erHelperJavascript::OnDomBegin('
				jQuery(document).ready(function() {
					var latlng = new google.maps.LatLng(' . $latlang . ');
					var myOptions = {
						zoom: ' . $def_zoom . ',
						center: latlng,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					};
					var map = new google.maps.Map(document.getElementById("er_map_canvas"), myOptions);
					var marker = new google.maps.Marker({
						position: latlng,
						map: map
					});
					google.maps.event.addListener(map, "click", function(event) {
						marker.setPosition(event.latLng);
						' . (((int)strlen($latitude_field) > 0 && (int)strlen($longitude_field) > 0) ?
						'jQuery("input[name=\'' . $latitude_field . '\']").val(event.latLng.lat().toFixed(' . (int)$decimal_precision . '));
						jQuery("input[name=\'' . $longitude_field . '\']").val(event.latLng.lng().toFixed(' . (int)$decimal_precision . '));'
						: '') . '
						jQuery("input[name=\'' . $this->_fld->name. '\']").val(event.latLng.lat().toFixed(' . (int)$decimal_precision . ') + ", " + event.latLng.lng().toFixed(' . (int)$decimal_precision . '));
					});
				});
			');
			$ret = '<div id="er_map_canvas" style="width: 300px; height: 300px;"></div>';
			$ret .= '<input type="hidden" name="' . $this->_fld->name . '" value="' . $value . '" />';
			return $ret;
		}
	}
	
	public function getHtml($value, $id = null) {
		return $this->getGoogleMap($value);
	}
	
	public function getResultHtml($value) {
		$def_zoom = (int)trim($this->_params->get('def_zoom', '2'));
		
		if ((int)strlen($value) > 0) {
			return '<script type="text/javascript">
				jQuery(document).ready(function() {
					var latlng = new google.maps.LatLng(' . $value . ');
					var myOptions = {
						zoom: ' . $def_zoom . ',
						center: latlng,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					};
					var map = new google.maps.Map(document.getElementById("er_map_canvas"), myOptions);
					var marker = new google.maps.Marker({
						position: latlng,
						map: map
					});
				});
			</script>
			<div id="er_map_canvas" style="width: 300px; height: 300px;"></div>
			';
		}
	}
	
	public function getNoeditHtml($value) {
		return $this->getGoogleMap($value);
	}
}