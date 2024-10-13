<?php
/* Copyright (C) 2022 Laurent Destailleur  <eldy@users.sourceforge.net>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 *       \file       htdocs/listedit/ajax/myobject.php
 *       \brief      File to return Ajax response on product list request
 */

if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', 1); // Disables token renewal
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', '1');
}
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', '1');
}
if (!defined('NOREQUIREAJAX')) {
	define('NOREQUIREAJAX', '1');
}
if (!defined('NOREQUIRESOC')) {
	define('NOREQUIRESOC', '1');
}
if (!defined('NOCSRFCHECK')) {
	define('NOCSRFCHECK', '1');
}
if (!defined('NOREQUIREHTML')) {
	define('NOREQUIREHTML', '1');
}

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
	$res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"] . "/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
	$i--;
	$j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/main.inc.php")) {
	$res = @include substr($tmp, 0, ($i + 1)) . "/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php")) {
	$res = @include dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../../main.inc.php")) {
	$res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
	$res = @include "../../../main.inc.php";
}
if (!$res) {
	die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT . '/core/class/commonobject.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/class/commonobjectline.class.php';
require_once DOL_DOCUMENT_ROOT . '/core/lib/functions2.lib.php';

/**
 * @global $db 			DoliDB
 * @global $user 		User
 * @global $mysoc 		Societe
 * @global $langs 		Translate
 * @global $conf 		Conf
 * @global $hookmanager HookManager
 */
global $db, $user, $mysoc, $langs, $conf, $hookmanager;

$langs->loadLangs([
	"fastmodulecheck@fastmodulecheck",
]);

$timer_start = microtime(true);
$modulesdir = dolGetModulesDirs();

$modules = [];
$orders = [];
$filename = [];
$disabled_modules = [];

$urlParts = parse_url($_SERVER['HTTP_REFERER']);
$path = $urlParts['path'];
$query = $urlParts['query'] ?? '';

$familyinfo = array(
	'hr'=>array('position'=>'001', 'label'=>$langs->trans("ModuleFamilyHr")),
	'crm'=>array('position'=>'006', 'label'=>$langs->trans("ModuleFamilyCrm")),
	'srm'=>array('position'=>'007', 'label'=>$langs->trans("ModuleFamilySrm")),
	'financial'=>array('position'=>'009', 'label'=>$langs->trans("ModuleFamilyFinancial")),
	'products'=>array('position'=>'012', 'label'=>$langs->trans("ModuleFamilyProducts")),
	'projects'=>array('position'=>'015', 'label'=>$langs->trans("ModuleFamilyProjects")),
	'ecm'=>array('position'=>'018', 'label'=>$langs->trans("ModuleFamilyECM")),
	'technic'=>array('position'=>'021', 'label'=>$langs->trans("ModuleFamilyTechnic")),
	'portal'=>array('position'=>'040', 'label'=>$langs->trans("ModuleFamilyPortal")),
	'interface'=>array('position'=>'050', 'label'=>$langs->trans("ModuleFamilyInterface")),
	'base'=>array('position'=>'060', 'label'=>$langs->trans("ModuleFamilyBase")),
	'other'=>array('position'=>'100', 'label'=>$langs->trans("ModuleFamilyOther")),
);

if (!empty($_SESSION["disablemodules"])) {
	$disabled_modules = explode(',', $_SESSION["disablemodules"]);
}

foreach ($modulesdir as $dir) {
// Load modules attributes in arrays (name, numero, orders) from dir directory
//print $dir."\n<br>";
	dol_syslog("Scan directory " . $dir . " for module descriptor files (modXXX.class.php)");
	$handle = @opendir($dir);
	if (is_resource($handle)) {
		while (($file = readdir($handle)) !== false) {
			if (is_readable($dir . $file) && substr($file, 0, 3) == 'mod' && substr($file, dol_strlen($file) - 10) == '.class.php') {
				$modName = substr($file, 0, dol_strlen($file) - 10);

				if ($modName) {
					if (!empty($modNameLoaded[$modName])) {   // In cache of already loaded modules ?
						$mesg = "Error: Module " . $modName . " was found twice: Into " . $modNameLoaded[$modName] . " and " . $dir . ". You probably have an old file on your disk.<br>";
						setEventMessages($mesg, null, 'warnings');
						dol_syslog($mesg, LOG_ERR);
						continue;
					}

					try {
// Création d'une instance class
						$res = include_once $dir . $file; // A class already exists in a different file will send a non catchable fatal error.
						if (class_exists($modName)) {
							$objMod = new $modName($db);
							$modNameLoaded[$modName] = $dir;
							if (!$objMod->numero > 0 && $modName != 'modUser') {
								dol_syslog('The module descriptor ' . $modName . ' must have a numero property', LOG_ERR);
							}
							$j = $objMod->numero;

							$modulequalified = 1;

// We discard modules according to features level (PS: if module is activated we always show it)
							$const_name = 'MAIN_MODULE_' . strtoupper(preg_replace('/^mod/i', '', get_class($objMod)));
							if ($objMod->version == 'development' && (!getDolGlobalString($const_name) && (getDolGlobalInt('MAIN_FEATURES_LEVEL') < 2))) {
								$modulequalified = 0;
							}
							if ($objMod->version == 'experimental' && (!getDolGlobalString($const_name) && (getDolGlobalInt('MAIN_FEATURES_LEVEL') < 1))) {
								$modulequalified = 0;
							}
							if (preg_match('/deprecated/', $objMod->version) && (!getDolGlobalString($const_name) && (getDolGlobalInt('MAIN_FEATURES_LEVEL') >= 0))) {
								$modulequalified = 0;
							}

// We discard modules according to property ->hidden
							if (!empty($objMod->hidden)) {
								$modulequalified = 0;
							}

							if ($modulequalified > 0) {
								$publisher = dol_escape_htmltag($objMod->getPublisher());
								$external = ($objMod->isCoreOrExternalModule() == 'external');
								if ($external) {
									if ($publisher) {
										$arrayofnatures['external_' . $publisher] = $langs->trans("External") . ' - ' . $publisher;
									} else {
										$arrayofnatures['external_'] = $langs->trans("External") . ' - ' . $langs->trans("UnknownPublishers");
									}
									ksort($arrayofnatures);
								}

// Define array $categ with categ with at least one qualified module
								$filename[$i] = $modName;
								$modules[$modName] = $objMod;
								if ($filename[$i] === 'modFastmodulecheck') {

								}
// Gives the possibility to the module, to provide his own family info and position of this family
// var_dump($familyinfo['products']['position']);
								if (is_array($objMod->familyinfo) && !empty($objMod->familyinfo)) {
									$familyinfo = array_merge($familyinfo, $objMod->familyinfo);
									$familykey = key($objMod->familyinfo);
								} else {
									$familykey = $objMod->family;
								}

								$moduleposition = ($objMod->module_position ? $objMod->module_position : '50');
								if ($objMod->isCoreOrExternalModule() == 'external' && $moduleposition < 100000) {
// an external module should never return a value lower than '80'.
									$moduleposition = '80'; // External modules at end by default
								}

// Add list of warnings to show into arrayofwarnings and arrayofwarningsext
								if (!empty($objMod->warnings_activation)) {
									$arrayofwarnings[$modName] = $objMod->warnings_activation;
								}
								if (!empty($objMod->warnings_activation_ext)) {
									$arrayofwarningsext[$modName] = $objMod->warnings_activation_ext;
								}

								$familyposition = (empty($familyinfo[$familykey]['position']) ? 0 : $familyinfo[$familykey]['position']);
// var_dump($familyposition);
								$listOfOfficialModuleGroups = ['hr', 'technic', 'interface', 'technic', 'portal', 'financial', 'crm', 'base', 'products', 'srm', 'ecm', 'projects', 'other'];
								if ($external && !in_array($familykey, $listOfOfficialModuleGroups)) {
// If module is extern and into a custom group (not into an official predefined one), it must appear at end (custom groups should not be before official groups).
									if (is_numeric($familyposition)) {
										$familyposition = sprintf("%03d", (int)$familyposition + 100);
									}
								}

								$orders[$i] = $familyposition . "_" . $familykey . "_" . $moduleposition . "_" . $j; // Sort by family, then by module position then number

// Set categ[$i]
								$specialstring = 'unknown';
								if ($objMod->version == 'development' || $objMod->version == 'experimental') {
									$specialstring = 'expdev';
								}
								if (isset($categ[$specialstring])) {
									$categ[$specialstring]++; // Array of all different modules categories
								} else {
									$categ[$specialstring] = 1;
								}
								$j++;
								$i++;
							} else {
								dol_syslog("Module " . get_class($objMod) . " not qualified");
							}
						} else {
							print "admin/modules.php Warning bad descriptor file : " . $dir . $file . " (Class " . $modName . " not found into file)<br>";
						}
					} catch (Exception $e) {
						dol_syslog("Failed to load " . $dir . $file . " " . $e->getMessage(), LOG_ERR);
					}
				}
			}
		}
		closedir($handle);
	} else {
		dol_syslog("htdocs/admin/modules.php: Failed to open directory " . $dir . ". See permission and open_basedir option.", LOG_WARNING);
	}
}

asort($orders);

$menu = '<tr class="table_fast_title">
			<th>Module</th>
			<th>Active</th>
		 </tr>';
foreach ($orders as $key => $value) {
	$linenum++;
	$tab = explode('_', $value);
	$familykey = $tab[1];
	$module_position = $tab[2];

	$modName = $filename[$key];

	$codeenabledisable = '';
	/** @var DolibarrModules $objMod */
	$objMod = $modules[$modName];
	$const_name = 'MAIN_MODULE_'.strtoupper(preg_replace('/^mod/i', '', get_class($objMod)));

	$modulename = $objMod->getName();
	$moduletechnicalname = $objMod->name;
	$moduledesc = $objMod->getDesc();
	$moduledesclong = $objMod->getDescLong();
	$moduleauthor = $objMod->getPublisher();

	// 	if (!empty($objMod->warnings_unactivation[$mysoc->country_code]) && method_exists($objMod, 'alreadyUsed') && $objMod->alreadyUsed()) {
	// 		$codeenabledisable .= '<a class="reposition valignmiddle" href="'.$_SERVER["PHP_SELF"].'?id='.$objMod->numero.'&amp;token='.newToken().'&amp;module_position='.$module_position.'&amp;action=reset_confirm&amp;confirm_message_code='.urlencode($objMod->warnings_unactivation[$mysoc->country_code]).'&amp;value='.$modName.'&amp;mode='.$mode.$param.'">';
	// 		$codeenabledisable .= img_picto($langs->trans("Activated"), 'switch_on');
	// 		$codeenabledisable .= '</a>';
	// 	} else {
	// 		$codeenabledisable .= '<a class="reposition valignmiddle" href="'.$_SERVER["PHP_SELF"].'?id='.$objMod->numero.'&amp;token='.newToken().'&amp;module_position='.$module_position.'&amp;action=reset&amp;value='.$modName.'&amp;mode='.$mode.'&amp;confirm=yes'.$param.'">';
	// 		$codeenabledisable .= img_picto($langs->trans("Activated"), 'switch_on');
	// 		$codeenabledisable .= '</a>';
	// 	}
	// }

	// if (empty($objMod->disabled)) {
	// 	if (!empty($objMod->warnings_unactivation[$mysoc->country_code]) && method_exists($objMod, 'alreadyUsed') && $objMod->alreadyUsed()) {
	// 		$codeenabledisable .= '<a class="reposition valignmiddle" href="'.$_SERVER["PHP_SELF"].'?id='.$objMod->numero.'&amp;token='.newToken().'&amp;module_position='.$module_position.'&amp;action=reset_confirm&amp;confirm_message_code='.urlencode($objMod->warnings_unactivation[$mysoc->country_code]).'&amp;value='.$modName.'&amp;mode='.$mode.$param.'">';
	// 		$codeenabledisable .= img_picto($langs->trans("Activated").($warningstring ? ' '.$warningstring : ''), 'switch_on');
	// 		$codeenabledisable .= '</a>';
	// 	} else {
	// 		$codeenabledisable .= '<a class="reposition valignmiddle" href="'.$_SERVER["PHP_SELF"].'?id='.$objMod->numero.'&amp;token='.newToken().'&amp;module_position='.$module_position.'&amp;action=reset&amp;value='.$modName.'&amp;mode='.$mode.'&amp;confirm=yes'.$param.'">';
	// 		$codeenabledisable .= img_picto($langs->trans("Activated").($warningstring ? ' '.$warningstring : ''), 'switch_on');
	// 		$codeenabledisable .= '</a>';
	// 	}
	// }

	if (!getDolGlobalString($const_name)) {
		if (!empty($objMod->warnings_unactivation[$mysoc->country_code]) && method_exists($objMod, 'alreadyUsed') && $objMod->alreadyUsed()) {
			$codeenabledisable .= '<a class="reposition" href="'.$path.'?'.$query.'id='.$objMod->numero.'&amp;token='.newToken().'&amp;module_position='.$module_position.'&amp;action=fastmodulecheck_set&amp;value='.$modName.'&amp;mode='.$mode.'&amp;confirm=yes'.$param.'">';
			$codeenabledisable .= img_picto($langs->trans("Disabled"), 'switch_off');
			$codeenabledisable .= '</a>';
		} else {
			// activer
			$codeenabledisable .= '<a class="reposition" href="'.$path.'?'.$query.'id='.$objMod->numero.'&amp;token='.newToken().'&amp;module_position='.$module_position.'&amp;action=fastmodulecheck_set&amp;value='.$modName.'&amp;mode='.$mode.'&amp;confirm=yes'.$param.'">';
			$codeenabledisable .= img_picto($langs->trans("Disabled"), 'switch_off');
			$codeenabledisable .= '</a>';
		}

	} else {
		// MODULE ACTIVÉ
		$codeenabledisable .= '<a class="reposition valignmiddle" href="'.$path.'?'.$query.'id='.$objMod->numero.'&token='.newToken().'&module_position='.$module_position.'&action=fastmodulecheck_reset&token='.newToken().'&value='.$modName.'&mode='.$mode.'&amp;confirm=yes'.$param.'"';
		$codeenabledisable .= '>';
		$codeenabledisable .= img_picto($langs->trans("Activated"), 'switch_on');
		$codeenabledisable .= "</a>";
		$codeenabledisable .= '&nbsp;';
		$codeenabledisable .= '<a class="reposition reload" href="'.$path.'?'.$query.'id='.$objMod->numero.'&amp;token='.newToken().'&amp;module_position='.$module_position.'&amp;action=fastmodulecheck_reload&amp;value='.$modName.'&amp;mode='.$mode.'&amp;confirm=yes'.$param.'">';
		$codeenabledisable .= img_picto($langs->trans("Reload"), 'refresh', 'class="opacitymedium"');
		$codeenabledisable .= '</a>';

	}

	// $iconValue = ($moduleValue == 1) ? 'no' : 'yes';
	$menu .= '<tr class="table_fast_value">';
	// Affiche le nom du module
	$menu .= '<td>' . htmlspecialchars($moduletechnicalname) . '</td>';
	// Crée le bouton pour activer/désactiver
	$menu .= '<td>';
	$menu .= $codeenabledisable;
	$menu .= '</td>';

	$menu .= '</tr>';
}

$duration = round(microtime(true) - $timer_start, 2);

$menu .= "<tr class='table_fast_value'><td colspan='2' class='fast_summary'><em>Processed in {$duration}s</em></td>";
print $menu;
