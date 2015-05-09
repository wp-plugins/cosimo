<?php
/*
 CosimoAdmin - Class for wordpress plugin "Cosimo" backend
 Author: andurban.de
 Version: latest
 ----------------------------------------------------------------------------------------
 Copyright 2009-2015 andurban.de  (email: http://www.andurban.de/kontakt)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

class CosimoAdmin {

	/**
	 * Construktor
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
    <input type="checkbox" id="orflag" name="orflag" value="true"'.((bool)$orflag ? ' checked="checked"' : null).' /><label for="orflag"> AND</label></td>
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
		$startat = $orflag = $nggallery = $title = $desc = $caption = null;
		$interval = 1;
		$unit = 'weeks';
		$csstag = 'body';
		$pattern = 'Summer:2010*';

		// Default Werte mit den gespeicherten Optionen überschreiben
		$opts = get_option('cosimo');
		if (!$opts)
			$opts = array();

			// Das zuletzt verwendete Image wird verworfen.
			unset($opts['imgurl']);

		// Die verbliebenen Optionen extrahieren
		extract($opts,EXTR_OVERWRITE);

		$message = null;
		$nonce = isset($_POST['_wpnonce']) ? $_POST['_wpnonce'] : null;

		// Einstellungen speichern
		if (wp_verify_nonce($nonce, 'my-nonce') ) {

			// Post Variablen mit '_' am Anfang ignorieren
			foreach (array_keys($_POST) as $k)
				if (substr($k,0,1) == '_') unset($_POST[$k]);

			// zuletzt gespeicherte Checkbutton-Flagsverwerfen.
			// diese durch den POST entweder bestätigt oder entfernt.
			$caption = $desc = $orflag = $title = null;

			// Werte zum Säubern extrahieren
			extract($_POST,EXTR_OVERWRITE);

			$pattern = trim($pattern);
			if (!is_numeric($interval))
				$interval = 1;

			if (isset($opts['unit']) && ($opts['unit'] != 'views'))
				$_POST['timestamp'] = strtotime("now + $interval $unit");

			// Gesäuberte Werte ins $_POST Array zum Speichern zurückschreiben
			$_POST['pattern'] = $pattern;
			$_POST['interval'] = $interval;

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


		$nextgenSelectBox = $this->getNextGENGalleries($orflag,$nggallery);
		$title = ($title) ? ' checked="checked"' : '';
		$caption = ($caption) ? ' checked="checked"' : '';
		$desc = ($desc) ? ' checked="checked"' : '';
		
echo <<<_EOT
<div class="wrap">
 <div id="icon-options-general" class="icon32"><br /></div>
	<h2>Cosimo Settings</h2>${message}
	   <div style="margin:10px;padding-right:50px;">
	    <a href="http://donate.andurban.de/"><img src="https://www.paypal.com/en_GB/i/btn/btn_donate_SM.gif" border="0" alt="donate" title="Sollte Ihnen das Plugin gefallen, w&auml;re ich &uuml;ber eine kleine Spende sehr erfreut" /></a
	   </div>
	   <form id="cosimo" name="cosimo" method="post">
	    <table class="form-table" summary="">
	    ${nextgenSelectBox}
	    <tr>
	      <td width="110"><label for="pattern">Media Library Filter:</label></td>
	      <td><input type="text" id="pattern" name="pattern" size="30" value="${pattern}" />
	     </tr>
	     <tr>
	     <td></td>
	     <td>
	       <input type="checkbox" id="title" name="title" value="true"${title} /><label for="title"> in title</label> |
	       <input type="checkbox" id="caption" name="caption" value="true"${caption} /><label for="caption"> in caption</label> |
	       <input type="checkbox" id="desc" name="desc" value="true"${desc} /><label for="description"> in description</label>
	     </td>
	    </tr>
	    <tr>
	      <td colspan="2" style="line-height:5px;padding:0px;"><hr size="1" width="90%" /></td>
	    </tr>
	    <tr>
	      <td><label for="interval">Interval:</label></td>
	      <td><input type="text" id="interval" name="interval" size="4" value="${interval}" />
	      <select id="unit" name="unit">
	       <option value="views"${views}>Views</option>
	       <option value="minutes"${minutes}>Minutes</option>
	       <option value="hours"${hours}>Hours</option>
	       <option value="days"${days}>Days</option>
	       <option value="weeks"${weeks}>Weeks</option>
	       <option value="month"${month}>Month</option>
	       <option value="years"${years}>Years</option>
	      </select>
       </td>
	    </tr>
	    <!-- <tr>
	      <td>Start at: <span style="font-size: x-small;">(hh:mi)</span></td>
	      <td><input type="text" id="startat" name="startat" size="5" value="'.$startat.'" /></td>
	    </tr> -->
	    <tr>
	     <td><label for="tag">CSS-Selector:</label></td>
	      <td><input type="text" id="csstag" name="csstag" size="20" value="${csstag}" /></td>
	    </tr>    
	    <tr>
	      <td class="submit"><input type="submit" name="_submit" value="Submit" />
	      <input type="hidden" name="_wpnonce" value="${nonce}" /></td>
	    </tr>
	   </table>
	  </form>
	 </div>
_EOT;

	}

} /* end of class */

?>