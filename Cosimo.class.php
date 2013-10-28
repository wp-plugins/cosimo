<?php
/**
 * Cosimo - Main Class for wordpress plugin "Cosimo"
 * Author: grobator
 * Version: latest
 */
class Cosimo {

	// Members
	var $debug = false;
	var $firstImage = null;


	/**
	 * PHP5 Construktor
	 */
	function __construct() {}


	/**
	 * Schreibt das Inline CSS nach stdout
	 * @param $imgurl - Die URL des Background Images
	 */
	function out($imgurl) {
		echo '<!--Cosimo $Rev$-->
<style type="text/css" media="screen">
body {background-image:url('.$imgurl.') !important;}
</style>
';

	}

	/**
	 * Testet eine String mit Regexp unter Verwendung des mit glob_exec() erzeugten Patterns
	 * @param $wildcard_pattern
	 * @param $haystack
	 * @see http://www.php.net/manual/en/function.glob.php#91475
	 */
	function match($str) {
		return preg_match($this->glob_pattern, $str);
	}

	/**
	 * Speichert das Regexp-Pattern in Member, da dieses mehrfach in Schleifen benötigt wird.
	 * @param $wildcard_pattern
	 */
	function setPattern($wildcard_pattern) {
		$regex = str_replace(
			array("\*", "\?"), // wildcard chars
			array('.*','.'),   // regexp chars
			preg_quote($wildcard_pattern)
		);
		$this->glob_pattern = '/^'.$regex.'$/is';
	}


	/**
	 * Untersucht die NextGEN Galerie nach dem nächsten Bild anhand seines Vorgängers
	 * @param $ngid - NextGEN Galerie ID
	 * @param $currentURL - Derzeit verwendete Image URL
	 * @return Die nächste URL, falls diese hier gefunden wurde
	 */
	function selectFromNextGEN($ngid,$currentURL) {
		global $wpdb;

		$result = false;

		// NextGEN ist nicht aktiviert
		if (!isset($wpdb->nggallery))
		return $result;		//-->> exit function

		$siteurl = get_option ('siteurl');
		$stmt = "SELECT concat('$siteurl','/',path,'/', filename) as url FROM $wpdb->nggpictures, $wpdb->nggallery WHERE exclude = 0 and galleryid = $ngid and galleryid = gid order by sortorder, imagedate, filename";
		$rs = $wpdb->get_results($stmt);
		if ($rs) {
			foreach ($rs as $item) {

				if (is_null($this->firstImage))
				$this->firstImage = $item->url;

				if (empty($currentURL)) {
					$result = $this->firstImage;
					break;
				}

				if ($item->url == $currentURL) {
					$currentFound = true;
				} elseif (isset($currentFound)) {
					$result = $item->url;
					break;
				}
			}
		}

		return $result;
	}

	/**
	 * Untersucht die Mediathek nach dem nächsten Bild anhand seines Vorgängers
	 * @param &$opts - Die akt. Plugin Einstellungen
	 * @param $currentURL - Derzeit verwendete Image URL
	 * @return Die nächste URL, falls die zuletzt verwendete hier gefunden wurde
	 */
	function selectFromMediathek(&$opts,$currentURL) {

		$result = false;
		// Array mit Optionen in lokale Variablen importieren
		extract($opts,EXTR_OVERWRITE);

		$this->setPattern($pattern);

		$in_caption = isset($caption);
		$in_title =  isset($title);
		$in_desc = isset($desc);

		$args = array(
			'post_type' => 'attachment',
			'nopaging' => true,
		);
		foreach (get_posts($args) as $item) {

			$isvalid = ($in_caption && $this->match($item->post_excerpt));
			if (!$isvalid)
			$isvalid = ($in_title && $this->match($item->post_title));
			if (!$isvalid)
			$isvalid = ($in_desc && $this->match($item->post_content));

			if ($isvalid) {
				if (is_null($this->firstImage))
				$this->firstImage = $item->guid;

				if (empty($currentURL)) {
					$result = $this->firstImage;
					break;
				} else {
					if ($item->guid == $currentURL) {
						$currentFound = true;
					} elseif (isset($currentFound)) {
						$result = $item->guid;
						break;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Apply the Plugin
	 */
	function apply() {

//echo "DEBUG: <pre style='background:#fff;'>";

		// Gespeicherte Optionen lesen
		$opts = get_option('cosimo');
		if (!$opts)
			$opts = array();

		extract($opts,EXTR_OVERWRITE);

		// Es wurden noch keine Einstellungen vorgenommen
		if (!isset($unit))
			return false;

		if (!is_numeric($interval))
			$interval = 1;

		if (!isset($imgurl))
			$imgurl = null;

		if ($unit == 'views') {
			// View Counter inkrementieren
			$views = isset($views) ? ++$views : 0;
			$doCosimo = ($views >= $interval);

			if (!$doCosimo) {
				// View Counter inkrementieren und speichern
				$opts['views'] = $views;
				update_option('cosimo',$opts);
			}
		} else {

			// Entscheidungsfindung, ob Cosimo ausgeführt werden soll.
			$doCosimo = (!isset($timestamp));

			if (!$doCosimo) {
				$doCosimo = (empty($timestamp) || !is_numeric($timestamp));

				if (!$doCosimo)
					$doCosimo = ($timestamp < time());  // gegen akt. Zeitstempel testen
			}
		}

		if (!$doCosimo) {
//echo "DEBUG: ".var_export($opts,true)."<br /> now + $interval $unit".time()."</pre>";
			$this->out($imgurl);
			return true;
		}


		//
		// ** Cosimo wird ausgeführt **
		//
		$useNextGEN = is_numeric($nggallery);
		$nextURL = false;

		if ($useNextGEN)
			$nextURL = $this->selectFromNextGEN($nggallery,$imgurl);

		if ($nextURL === false && (isset($orflag) || !$useNextGEN))
			$nextURL = $this->selectFromMediathek($opts,$imgurl);

		if ($nextURL === false && (bool)$this->firstImage)
			$nextURL = $this->firstImage;

		if ($nextURL) {

			$opts['views'] = 0;
			$opts['imgurl'] = $nextURL;

			if ($unit != 'views')
				$opts['timestamp'] = strtotime("now + $interval $unit");

			update_option('cosimo',$opts);
		}

		$this->out($imgurl);
		return true;
	}

}
/* eof */
?>
