<?php
/**
 * Simple File Lister Module Entry Point
 * 
 * @package    Joomla
 * @subpackage Modules
 * @author Anders Wasén
 * @link http://wasen.net/
 * @license		GNU/GPL, see LICENSE.php
 * mod_simplefileupload is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

$session =& JFactory::getSession();
$baseurl = "";

$sfl_version = "1.0";
$sfl_basepath = "modules/mod_simplefilelisterv".$sfl_version."/";

$sfl_dirlocation = $params->get( 'sfl_dirlocation', '.'.DIRECTORY_SEPARATOR.'images' );

$sfl_maxfiles = $params->get( 'sfl_maxfiles', '20' );
$sfl_bgcolor = $params->get( 'sfl_bgcolor', '#e8edf1' );
if ( substr( $sfl_bgcolor, 0, 1 ) !== "#" ) {
	$sfl_bgcolor = "#" . $sfl_bgcolor;
}
$sfl_maxheight = $params->get( 'sfl_maxheight', '0' );
$sfl_useusernameddir = $params->get( 'sfl_useusernameddir', '0' );
$sfl_usernameddirdefault = $params->get( 'sfl_usernameddirdefault', '0' );
$sfl_userlocation = $params->get( 'sfl_userlocation', '' );
if ( substr( $sfl_userlocation , strlen($sfl_userlocation) - 1) !== DIRECTORY_SEPARATOR ) {
  $sfl_userlocation .= DIRECTORY_SEPARATOR;
}
$sfl_boxleft = $params->get( 'sfl_boxleft', '-16' );
$sfl_allowdelete = $params->get( 'sfl_allowdelete', '0' );
$sfl_jquery = $params->get( 'sfl_jquery', '0' );
$sfl_jqueryinclude = $params->get( 'sfl_jqueryinclude', '0' );

// Get current logged in user
$user =& JFactory::getUser();
$usr_id = $user->get('id');
$usr_name = $user->get('username');
if(stripos($usr_name, "/") !== false) {
	$usr_name = "";
}
if(stripos($usr_name, "\\") !== false) {
	$usr_name = "";
}
if(stripos($usr_name, "..") !== false) {
	$usr_name = "";
}

if ($sfl_maxfiles > 0) {
	// Check if this is a new login
	if ($session->get( 'sfl_usrid', 0) !== $usr_id) {
		$session->set( 'sfl_nextindex', 0);
		$session->set( 'sfl_stopindex', $sfl_maxfiles);
	} else {

		if (isset($_GET["sflPrevious"])) {
			if (strlen($_GET["sflPrevious"]) > 0) {

				$idx_startat = $session->get( 'sfl_nextindex', 0);
				$idx_endat = $session->get( 'sfl_stopindex', $sfl_maxfiles);
				
				if ($idx_startat > 0 && $idx_endat > $sfl_maxfiles) {
					$idx_startat = $_GET["sflPrevious"] - $sfl_maxfiles;
					$idx_endat = $idx_startat + $sfl_maxfiles;
					
					$session->set( 'sfl_nextindex', $idx_startat);
					$session->set( 'sfl_stopindex', $idx_endat);
				}
			}
		}

	}

	$session->set( 'sfl_usrid', $usr_id);
}

if (!isset($_GET["sflPrevious"]) && !isset($_GET["sflNext"])) {
	
	// Neither next nor previous, must be reload from other link
	$session->set( 'sfl_nextindex', 0);
	$session->set( 'sfl_stopindex', $sfl_maxfiles);
	
}


if ($sfl_useusernameddir == 1) {

	// If only list users files clear default path
	if ($sfl_usernameddirdefault === '1' && strlen($sfl_userlocation) > 0) $sfl_dirlocation = '';

	if ($usr_id > 0 && strlen($sfl_userlocation) > 0) {	
		// Set user path, it already has the DIRECTORY_SEPARATOR at the end, don't add after usr_name.
		$sfl_userlocation .= $usr_name;
	} else {
		$sfl_userlocation = '';
	}
} else {
	$sfl_userlocation = '';
}
// Make ready for Ajax calls and avoid any whitespace
if (isset($_GET["sflaction"])) {
if(!class_exists('SFLAjaxServlet')) JLoader::register('SFLAjaxServlet' , dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');
//Security check
if (isset($_GET["sflDir"])) {
	if (strlen($sfl_userlocation) == 0) $sfl_userlocation = "-";
	// Check that either default dir or user dir is present in the given dir. If not set it to default
	if (strpos($_GET["sflDir"], $sfl_dirlocation) === false && strpos($_GET["sflDir"], $sfl_userlocation) === false) {
		// Add warning txt?
	} else {
		$sfl_dirlocation = $_GET["sflDir"];
	}
}
if (strpos($sfl_dirlocation, "../") !== false) $sfl_dirlocation = $params->get( 'sfl_dirlocation', '.'.DIRECTORY_SEPARATOR.'images' );
if (strlen($sfl_dirlocation) == 0) $sfl_dirlocation = $sfl_userlocation;
$sfl_file = "";
$session->set( 'sfl_currentdir', $sfl_dirlocation);
if ($_GET["sflaction"] === "delete") $sfl_file = $_GET["sflDelete"];
if ($_GET["sflaction"] === "sort" && isset($_GET["sflSort"])) $session->set( 'sfl_sort', $_GET["sflSort"]);
echo SFLAjaxServlet::getContent($_GET["sflaction"], $params, $sfl_dirlocation, $sfl_basepath, $sfl_maxfiles, $sfl_userlocation, $sfl_file);
} else {

	// include the helper file
	require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'helper.php');
	$results = '';
	$results .= ModSimpleFileListerHelperv10::getFileList($params, $sfl_dirlocation, $sfl_basepath, $sfl_maxfiles, $sfl_userlocation);

	// include the template for display
	require(JModuleHelper::getLayoutPath('mod_simplefilelisterv'.$sfl_version));

}
?>
