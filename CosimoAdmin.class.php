<?php
/**
 * CosimoAdmin - Class for wordpress plugin "Cosimo" backend
 * Author: grobator
 * Version: latest
 */
class CosimoAdmin {

	/**
	 * PHP4 Construktor. Wrapper for __construct()
	 */
	function Cosimo() {
		$args = func_get_args();
		call_user_func_array(array(&$this, '__construct'), $args);
	}

	/**
	 * PHP5 Construktor
	 */
	function __construct() {}


	/**
	 * @param $orflag
	 * @param $nggallery
	 * @return NextGEN Gallery selectbox if available
	 */
	function getNextGENGalleries($orflag,$nggallery) {
		global $wpdb;

		$result = null;

		// NextGEN ist nicht aktiviert
		if (!isset($wpdb->nggallery))
			return $result;		//-->> exit function

		$result.= '<tr>
	      <td>NextGEN Gallery:</td>
	      <td><select id="nggallery" name="nggallery">
<option value="none">-</option>
';

		// NextGEN aus DB lesen
		$rs = $wpdb->get_results("SELECT gid, name FROM $wpdb->nggallery ORDER BY name");
		if(is_array($rs)) {
			foreach($rs as $ng) {
				$gid = $ng->gid;
				$name = $ng->name;
				$selected = ($nggallery == $gid) ? 'selected="selected"' : null;
				$result.= "<option value='$gid' $selected>$gid - $name</option>
";
			}
		}

		$result.= '</select></td>
  </tr>
  <tr>
   <td></td>
   <td style="line-height:5px;">
    <input type="checkbox" id="orflag" name="orflag" value="true"'.($orflag ? ' checked="checked"' : null).' /><label for="orflag"> AND</label></td>
   </td>
	</tr>
';

		return $result;
	}



	/**
	 * Backend Option panel
	 */
	function settings() {

		// Hardcore Defaults
		$orflag = $nggallery = $title = $desc = $caption = null;
		$interval = 1;
		$unit = 'weeks';
		$pattern = 'Summer:2010*';

		// Default Werte mit den gespeicherten Optionen überschreiben
		$opts = get_option('cosimo');
		if (!$opts)
			$opts = array();

		extract($opts,EXTR_OVERWRITE);

		$message = null;

		$nonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : null;

		// Einstellungen speichern
		if (wp_verify_nonce($nonce, 'my-nonce') ) {

			// Post Variablen mit '_' am Anfang ignorieren
			foreach (array_keys($_POST) as $k)
				if (substr($k,0,1) == '_') unset($_POST[$k]);

			// Werte zum Säubern extrahieren
			extract($_POST,EXTR_OVERWRITE);

			$pattern = trim($pattern);
			if (!is_numeric($interval))
				$interval = 1;

			if ($opts['unit'] != 'views')
				$_POST['timestamp'] = strtotime("now + $interval $unit");

			// Gesäuberte Werte ins $_POST Array zum Speichern zurückschreiben
			$_POST['pattern'] = $pattern;
			$_POST['interval'] = $interval;

			// evtl. vorhandene ImageURL verwerfen
			unset($_POST['imgurl']);

			// $_POST Werte in die zu speichernden Optionen überführen
			foreach ($_POST as $k => $v)
				$opts[$k] = $v;

			update_option('cosimo',$opts);

			$message = 'Settings updated';
		}

		// create nonce
		$nonce = wp_create_nonce('my-nonce');

		if (!is_null($message))
		$message = "<div class='updated fade below-h2' id='message'><p>$message</p></div>";

		//
		$views = $minutes = $hours = $days = $weeks = $month = $years = null;
		$selected = ' selected="selected"';
		switch($unit) {
			case 'views':
				$views = $selected;
				break;
			case 'minutes':
				$minutes = $selected;
				break;
			case 'hours':
				$hours = $selected;
				break;
			case 'days':
				$days = $selected;
				break;
			case 'weeks':
				$weeks = $selected;
				break;
			case 'month':
				$month = $selected;
				break;
			case 'years':
				$years = $selected;
				break;
			default:
				break;
		}


		echo '<div class="wrap">
	  <div id="icon-options-general" class="icon32"><br /></div>
	   <h2>'.__('Cosimo', 'cosimo').' Settings</h2>'.$message.'
	   <div style="float: right;margin:10px;padding-right:50px;">
	    <a href="http://donate.grobator.de/"><img src="https://www.paypal.com/en_GB/i/btn/btn_donate_SM.gif" border="0" alt="donate" title="Sollte Ihnen das Plugin gefallen, w&auml;re ich &uuml;ber eine kleine Spende sehr erfreut" /></a
	   </div>
	   <form id="cosimo" name="cosimo" method="post">
	    <table class="form-table" summary="">
	    '.$this->getNextGENGalleries($orflag,$nggallery).'
	    <tr>
	      <td width="110">Media Library Filter:</td>
	      <td><input type="text" name="pattern" size="30" value="'.$pattern.'" />
	     </tr>
	     <tr>
	     <td></td>
	     <td>
	       <input type="checkbox" id="title" name="title" value="true"'.($title ? ' checked="checked"' : null).' /><label for="title"> in title</label> |
	       <input type="checkbox" id="caption" name="caption" value="true"'.($caption ? ' checked="checked"' : null).' /><label for="caption"> in caption</label> |
	       <input type="checkbox" id="desc" name="desc" value="true"'.($desc ? ' checked="checked"' : null).' /><label for="description"> in description</label>
	     </td>
	    </tr>
	    <tr>
	      <td colspan="2" style="line-height:5px;padding:0px;"><hr size="1" width="90%" /></td>
	    </tr>
	    <tr>
	      <td>Interval:</td>
	      <td><input type="text" id="interval" name="interval" size="4" value="'.$interval.'" />
	      <select id="unit" name="unit">
	       <option value="views"'.$views.'>Views</option>
	       <option value="minutes"'.$minutes.'>Minutes</option>
	       <option value="hours"'.$hours.'>Hours</option>
	       <option value="days"'.$days.'>Days</option>
	       <option value="weeks"'.$weeks.'>Weeks</option>
	       <option value="month"'.$month.'>Month</option>
	       <option value="years"'.$years.'>Years</option>
	      </select>
	    </tr>
	    <tr>
	      <td class="submit"><input type="submit" name="_submit" value="Submit" />
	      <input type="hidden" name="_wpnonce" value="'.$nonce.'" /></td>
	    </tr>
	   </table>
	  </form>
	 </div>
';

	}

} /* end of class */

?>