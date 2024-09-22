<?php
/* Copyright (C) 2024 SuperAdmin
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    fastmodulecheck/class/actions_fastmodulecheck.class.php
 * \ingroup fastmodulecheck
 * \brief   Example hook overload.
 *
 * Put detailed description here.
 */

require_once DOL_DOCUMENT_ROOT.'/core/class/commonhookactions.class.php';

/**
 * Class ActionsFastmodulecheck
 */
class ActionsFastmodulecheck extends CommonHookActions
{
	/**
	 * @var DoliDB Database handler.
	 */
	public $db;

	/**
	 * @var string Error code (or message)
	 */
	public $error = '';

	/**
	 * @var array Errors
	 */
	public $errors = array();


	/**
	 * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
	 */
	public $results = array();

	/**
	 * @var string String displayed by executeHook() immediately after return
	 */
	public $resprints;

	/**
	 * @var int		Priority of hook (50 is used if value is not defined)
	 */
	public $priority;


	/**
	 * Constructor
	 *
	 *  @param		DoliDB		$db      Database handler
	 */
	public function __construct($db)
	{
		$this->db = $db;
	}


	/**
	 * Execute action
	 *
	 * @param	array			$parameters		Array of parameters
	 * @param	CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	string			$action      	'add', 'update', 'view'
	 * @return	int         					Return integer <0 if KO,
	 *                           				=0 if OK but we want to process standard actions too,
	 *                            				>0 if OK and we want to replace standard actions.
	 */
	public function getNomUrl($parameters, &$object, &$action)
	{
		global $db, $langs, $conf, $user;
		$this->resprints = '';
		return 0;
	}

	/**
	 * Overloading the doActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {	    // do something only for the context 'somecontext1' or 'somecontext2'
			// Do what you want here...
			// You can for example call global vars like $fieldstosearchall to overwrite them, or update database depending on $action and $_POST values.
		}

		if (!$error) {
			$this->results = array('myreturn' => 999);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}


	/**
	 * Overloading the doMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function doMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			foreach ($parameters['toselect'] as $objectid) {
				// Do action on each object id
			}
		}

		if (!$error) {
			$this->results = array('myreturn' => 999);
			$this->resprints = 'A text to show';
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}


	/**
	 * Overloading the addMoreMassActions function : replacing the parent's function with the one below
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function addMoreMassActions($parameters, &$object, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$error = 0; // Error counter
		$disabled = 1;

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
			$this->resprints = '<option value="0"'.($disabled ? ' disabled="disabled"' : '').'>'.$langs->trans("FastmodulecheckMassAction").'</option>';
		}

		if (!$error) {
			return 0; // or return 1 to replace standard code
		} else {
			$this->errors[] = 'Error message';
			return -1;
		}
	}



	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$object		   	Object output on PDF
	 * @param   string	$action     	'add', 'update', 'view'
	 * @return  int 		        	Return integer <0 if KO,
	 *                          		=0 if OK but we want to process standard actions too,
	 *  	                            >0 if OK and we want to replace standard actions.
	 */
	public function beforePDFCreation($parameters, &$object, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0;
		$deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {		// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}

	/**
	 * Execute action
	 *
	 * @param	array	$parameters     Array of parameters
	 * @param   Object	$pdfhandler     PDF builder handler
	 * @param   string	$action         'add', 'update', 'view'
	 * @return  int 		            Return integer <0 if KO,
	 *                                  =0 if OK but we want to process standard actions too,
	 *                                  >0 if OK and we want to replace standard actions.
	 */
	public function afterPDFCreation($parameters, &$pdfhandler, &$action)
	{
		global $conf, $user, $langs;
		global $hookmanager;

		$outputlangs = $langs;

		$ret = 0;
		$deltemp = array();
		dol_syslog(get_class($this).'::executeHooks action='.$action);

		/* print_r($parameters); print_r($object); echo "action: " . $action; */
		if (in_array($parameters['currentcontext'], array('somecontext1', 'somecontext2'))) {
			// do something only for the context 'somecontext1' or 'somecontext2'
		}

		return $ret;
	}



	/**
	 * Overloading the loadDataForCustomReports function : returns data to complete the customreport tool
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int                             Return integer < 0 on error, 0 on success, 1 to replace standard code
	 */
	public function loadDataForCustomReports($parameters, &$action, $hookmanager)
	{
		global $conf, $user, $langs;

		$langs->load("fastmodulecheck@fastmodulecheck");

		$this->results = array();

		$head = array();
		$h = 0;

		if ($parameters['tabfamily'] == 'fastmodulecheck') {
			$head[$h][0] = dol_buildpath('/module/index.php', 1);
			$head[$h][1] = $langs->trans("Home");
			$head[$h][2] = 'home';
			$h++;

			$this->results['title'] = $langs->trans("Fastmodulecheck");
			$this->results['picto'] = 'fastmodulecheck@fastmodulecheck';
		}

		$head[$h][0] = 'customreports.php?objecttype='.$parameters['objecttype'].(empty($parameters['tabfamily']) ? '' : '&tabfamily='.$parameters['tabfamily']);
		$head[$h][1] = $langs->trans("CustomReports");
		$head[$h][2] = 'customreports';

		$this->results['head'] = $head;

		return 1;
	}



	/**
	 * Overloading the restrictedArea function : check permission on an object
	 *
	 * @param   array           $parameters     Hook metadatas (context, etc...)
	 * @param   string          $action         Current action (if set). Generally create or edit or null
	 * @param   HookManager     $hookmanager    Hook manager propagated to allow calling another hook
	 * @return  int 		      			  	Return integer <0 if KO,
	 *                          				=0 if OK but we want to process standard actions too,
	 *  	                            		>0 if OK and we want to replace standard actions.
	 */
	public function restrictedArea($parameters, &$action, $hookmanager)
	{
		global $user;

		if ($parameters['features'] == 'myobject') {
			if ($user->hasRight('fastmodulecheck', 'myobject', 'read')) {
				$this->results['result'] = 1;
				return 1;
			} else {
				$this->results['result'] = 0;
				return 1;
			}
		}

		return 0;
	}

	/**
	 * Execute action completeTabsHead
	 *
	 * @param   array           $parameters     Array of parameters
	 * @param   CommonObject    $object         The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param   string          $action         'add', 'update', 'view'
	 * @param   Hookmanager     $hookmanager    hookmanager
	 * @return  int                             Return integer <0 if KO,
	 *                                          =0 if OK but we want to process standard actions too,
	 *                                          >0 if OK and we want to replace standard actions.
	 */
	public function completeTabsHead(&$parameters, &$object, &$action, $hookmanager)
	{
		global $langs, $conf, $user;

		if (!isset($parameters['object']->element)) {
			return 0;
		}
		if ($parameters['mode'] == 'remove') {
			// used to make some tabs removed
			return 0;
		} elseif ($parameters['mode'] == 'add') {
			$langs->load('fastmodulecheck@fastmodulecheck');
			// used when we want to add some tabs
			$counter = count($parameters['head']);
			$element = $parameters['object']->element;
			$id = $parameters['object']->id;
			// verifier le type d'onglet comme member_stats où ça ne doit pas apparaitre
			// if (in_array($element, ['societe', 'member', 'contrat', 'fichinter', 'project', 'propal', 'commande', 'facture', 'order_supplier', 'invoice_supplier'])) {
			if (in_array($element, ['context1', 'context2'])) {
				$datacount = 0;

				$parameters['head'][$counter][0] = dol_buildpath('/fastmodulecheck/fastmodulecheck_tab.php', 1) . '?id=' . $id . '&amp;module='.$element;
				$parameters['head'][$counter][1] = $langs->trans('FastmodulecheckTab');
				if ($datacount > 0) {
					$parameters['head'][$counter][1] .= '<span class="badge marginleftonlyshort">' . $datacount . '</span>';
				}
				$parameters['head'][$counter][2] = 'fastmodulecheckemails';
				$counter++;
			}
			if ($counter > 0 && (int) DOL_VERSION < 14) {
				$this->results = $parameters['head'];
				// return 1 to replace standard code
				return 1;
			} else {
				// en V14 et + $parameters['head'] est modifiable par référence
				return 0;
			}
		} else {
			// Bad value for $parameters['mode']
			return -1;
		}
	}

	
	/**
	 * Execute action printTopRightMenu
	 *
	 * @param	array<string,mixed>	$parameters		Array of parameters
	 * @param	CommonObject		$object			The object to process (an invoice if you are in invoice module, a propale in propale's module, etc...)
	 * @param	string				$action			'add', 'update', 'view'
	 * @param	Hookmanager			$hookmanager	Hookmanager
	 * @return	int									Return integer <0 if KO,
	 *												=0 if OK but we want to process standard actions too,
	 *												>0 if OK and we want to replace standard actions.
	 */
	public function printTopRightMenu(&$parameters, &$object, &$action, $hookmanager) {
		global $conf, $user, $langs, $mysoc, $db;

		require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

		$error = 0; // Error counter
		$contexts = explode(':', $parameters['context'] ?? '');
		$modulesdir = dolGetModulesDirs();
		// var_dump($modulesdir);

		$modules = [];
		$orders = [];

		if (in_array('toprightmenu', $contexts)) {
			// if we set another view list mode, we keep it (till we change one more time)
			if (GETPOSTISSET('mode')) {
				$mode = GETPOST('mode', 'alpha');
				if ($mode =='common' || $mode =='commonkanban') {
					dolibarr_set_const($db, "MAIN_MODULE_SETUP_ON_LIST_BY_DEFAULT", $mode, 'chaine', 0, '', $conf->entity);
				}
			} else {
				$mode = (!getDolGlobalString('MAIN_MODULE_SETUP_ON_LIST_BY_DEFAULT') ? 'commonkanban' : $conf->global->MAIN_MODULE_SETUP_ON_LIST_BY_DEFAULT);
			}

			$disabled_modules = array();
			if (!empty($_SESSION["disablemodules"])) {
				$disabled_modules = explode(',', $_SESSION["disablemodules"]);
			}

			foreach ($modulesdir as $dir) {
				// Load modules attributes in arrays (name, numero, orders) from dir directory
				//print $dir."\n<br>";
				dol_syslog("Scan directory ".$dir." for module descriptor files (modXXX.class.php)");
				$handle = @opendir($dir);
				if (is_resource($handle)) {
					while (($file = readdir($handle)) !== false) {
						//print "$i ".$file."\n<br>";
						if (is_readable($dir.$file) && substr($file, 0, 3) == 'mod' && substr($file, dol_strlen($file) - 10) == '.class.php') {
							$modName = substr($file, 0, dol_strlen($file) - 10);
							// var_dump($modName);
			
							if ($modName) {
								if (!empty($modNameLoaded[$modName])) {   // In cache of already loaded modules ?
									$mesg = "Error: Module ".$modName." was found twice: Into ".$modNameLoaded[$modName]." and ".$dir.". You probably have an old file on your disk.<br>";
									setEventMessages($mesg, null, 'warnings');
									dol_syslog($mesg, LOG_ERR);
									continue;
								}
			
								try {
									// Création d'une instance class
									$res = include_once $dir.$file; // A class already exists in a different file will send a non catchable fatal error.
									if (class_exists($modName)) {
										$objMod = new $modName($db);
										$modNameLoaded[$modName] = $dir;
										if (!$objMod->numero > 0 && $modName != 'modUser') {
											dol_syslog('The module descriptor '.$modName.' must have a numero property', LOG_ERR);
										}
										$j = $objMod->numero;
										
										$modulequalified = 1;
										
										// We discard modules according to features level (PS: if module is activated we always show it)
										$const_name = 'MAIN_MODULE_'.strtoupper(preg_replace('/^mod/i', '', get_class($objMod)));
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
													$arrayofnatures['external_'.$publisher] = $langs->trans("External").' - '.$publisher;
												} else {
													$arrayofnatures['external_'] = $langs->trans("External").' - '.$langs->trans("UnknownPublishers");
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
											$listOfOfficialModuleGroups = array('hr', 'technic', 'interface', 'technic', 'portal', 'financial', 'crm', 'base', 'products', 'srm', 'ecm', 'projects', 'other');
											if ($external && !in_array($familykey, $listOfOfficialModuleGroups)) {
												// If module is extern and into a custom group (not into an official predefined one), it must appear at end (custom groups should not be before official groups).
												if (is_numeric($familyposition)) {
													$familyposition = sprintf("%03d", (int) $familyposition + 100);
												}
											}
			
											$orders[$i] = $familyposition."_".$familykey."_".$moduleposition."_".$j; // Sort by family, then by module position then number
			
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
											dol_syslog("Module ".get_class($objMod)." not qualified");
										}
									} else {
										print "admin/modules.php Warning bad descriptor file : ".$dir.$file." (Class ".$modName." not found into file)<br>";
									}
								} catch (Exception $e) {
									dol_syslog("Failed to load ".$dir.$file." ".$e->getMessage(), LOG_ERR);
								}
							}
						}
					}
					closedir($handle);
				} else {
					dol_syslog("htdocs/admin/modules.php: Failed to open directory ".$dir.". See permission and open_basedir option.", LOG_WARNING);
				}
			}

			asort($orders);
			
			$menu = '
			<button id="toggleButton" onclick="toggleTable()"></button>
			<table id="hiddenTable_fastmodulecheck" style="display:none; border: 1px solid black;">
				<tr class="table_fast_title">
					<th>Module</th>
					<th>Active</th>
				</tr>';
			if ($mode == 'common' || $mode == 'commonkanban') {
				foreach ($orders as $key => $value) {
					$linenum++;
					$tab = explode('_', $value);
					$familykey = $tab[1];
					$module_position = $tab[2];
	
					$modName = $filename[$key];
	
					/** @var DolibarrModules $objMod */
					$objMod = $modules[$modName];
	
					$modulename = $objMod->getName();
					$moduletechnicalname = $objMod->name;
					$moduledesc = $objMod->getDesc();
					$moduledesclong = $objMod->getDescLong();
					$moduleauthor = $objMod->getPublisher();
	
					if (empty($objMod->always_enabled) && getDolGlobalString($const_name)) {
						$codeenabledisable .= '<a class="reposition" href="'.$_SERVER["PHP_SELF"].'?id='.$objMod->numero.'&token='.newToken().'&module_position='.$module_position.'&action=set&token='.newToken().'&value='.$modName.'&mode='.$mode.$param.'"';
						$codeenabledisable .= '>';
						$codeenabledisable .= img_picto($langs->trans("Activated"), 'switch_on');
						$codeenabledisable .= "</a>\n";
					} else {
						$codeenabledisable .= '<a class="reposition" href="'.$_SERVER["PHP_SELF"].'?id='.$objMod->numero.'&token='.newToken().'&module_position='.$module_position.'&action=set&token='.newToken().'&value='.$modName.'&mode='.$mode.$param.'"';
						$codeenabledisable .= img_picto($langs->trans("Activated"), 'switch_on');
						$codeenabledisable .= "</a>";
						var_dump($codeenabledisable);
					}
	
					// $iconValue = ($moduleValue == 1) ? 'no' : 'yes';
					$menu .= '<tr class="table_fast_value">';
					
					// Affiche le nom du module
					$menu .= '<td>' . htmlspecialchars($moduletechnicalname) . '</td>';
					
					// Affiche le statut du module (activé/désactivé)
					// $statusText = ($moduleValue == 1) ? 'Enabled' : 'Disabled';
					// $menu .= '<td>' . htmlspecialchars($statusText) . '</td>';
					
					// Crée le bouton pour activer/désactiver
					$menu .= '<td>';
					// $menu .= '<a class="reposition" href="' . $_SERVER["PHP_SELF"] . '?value=' . urlencode($moduleName) . '&token=' . newToken() . '&action=activedModule&confirm='.$iconValue.'">';
					$menu .= print $codeenabledisable;
					// $icon = ($objMod->disabled == true) ? 'switch_on' : 'switch_off';
					// $menu .= img_picto($langs->trans($statusText), $icon);
					// $menu .= '</a>';
					$menu .= '</td>';
					
					$menu .= '</tr>';
				}
			}

			

			// foreach ($modulTitle as $moduleName => $moduleValue) {
			// 	$iconValue = ($moduleValue == 1) ? 'no' : 'yes';
			// 	$menu .= '<tr class="table_fast_value">';
				
			// 	// Affiche le nom du module
			// 	$menu .= '<td>' . htmlspecialchars($moduleName) . '</td>';
				
			// 	// Affiche le statut du module (activé/désactivé)
			// 	$statusText = ($moduleValue == 1) ? 'Enabled' : 'Disabled';
			// 	// $menu .= '<td>' . htmlspecialchars($statusText) . '</td>';
				
			// 	// Crée le bouton pour activer/désactiver
			// 	$menu .= '<td>';
			// 	$menu .= '<a class="reposition" href="' . $_SERVER["PHP_SELF"] . '?value=' . urlencode($moduleName) . '&token=' . newToken() . '&action=activedModule&confirm='.$iconValue.'">';
			// 	$icon = ($moduleValue == 1) ? 'switch_on' : 'switch_off';
			// 	$menu .= img_picto($langs->trans($statusText), $icon);
			// 	$menu .= '</a>';
			// 	$menu .= '</td>';
				
			// 	$menu .= '</tr>';
			// }

			$menu .= '</table>';



			$this->resprints .= $menu . '
			<script>
				function toggleTable() {
					var table = document.getElementById("hiddenTable_fastmodulecheck");
					var button = document.getElementById("toggleButton");

					if (table.style.display === "none") {
						table.style.display = "flex";
					} else {
						table.style.display = "none";
					}
				}

				// Handle clicks outside the table and button
				document.addEventListener("click", function(e) {
					var table = document.getElementById("hiddenTable_fastmodulecheck");
					var button = document.getElementById("toggleButton");

					// Check if click is outside the table and button
					if (!table.contains(e.target) && e.target !== button) {
						if (table.style.display === "flex") {
							table.style.display = "none";
						}
					}
				});
			</script>';
		}
		// Check filters
		// var_dump($objMod->getName());

		// if (in_array('toprightmenu', $contexts)) {
		// 	// include_once DOL_DOCUMENT_ROOT .'/admin/modules.php';

		// 	$action = GETPOST('action');
			
		// 	if ($action == 'activedModule') {
		// 		$moduleName = GETPOST('value');
		// 		$confirm = GETPOST('confirm');

		// 		$moduleName = 'MAIN_MODULE_' . strtoupper($moduleName);
		// 		if ($confirm == 'yes') {
		// 			// We made some check against evil eternal modules that try to low security options.
		// 			$checkOldValue = getDolGlobalInt('CHECKLASTVERSION_EXTERNALMODULE');
		// 			$csrfCheckOldValue = getDolGlobalInt('MAIN_SECURITY_CSRF_WITH_TOKEN');
		// 			$resarray = activateModule($moduleName);
		// 			if ($checkOldValue != getDolGlobalInt('CHECKLASTVERSION_EXTERNALMODULE')) {
		// 				setEventMessage($langs->trans('WarningModuleHasChangedLastVersionCheckParameter', $moduleName), 'warnings');
		// 			}
		// 			if ($csrfCheckOldValue != getDolGlobalInt('MAIN_SECURITY_CSRF_WITH_TOKEN')) {
		// 				setEventMessage($langs->trans('WarningModuleHasChangedSecurityCsrfParameter', $moduleName), 'warnings');
		// 			}

		// 			dolibarr_set_const($db, "MAIN_IHM_PARAMS_REV", getDolGlobalInt('MAIN_IHM_PARAMS_REV') + 1, 'chaine', 0, '', $conf->entity);
		// 			if (!empty($resarray['errors'])) {
		// 				setEventMessages('', $resarray['errors'], 'errors');
		// 			} else {
		// 				//var_dump($resarray);exit;
		// 				if ($resarray['nbperms'] > 0) {
		// 					$tmpsql = "SELECT COUNT(rowid) as nb FROM ".MAIN_DB_PREFIX."user WHERE admin <> 1";
		// 					$resqltmp = $db->query($tmpsql);
		// 					if ($resqltmp) {
		// 						$obj = $db->fetch_object($resqltmp);
		// 						//var_dump($obj->nb);exit;
		// 						if ($obj && $obj->nb > 1) {
		// 							$msg = $langs->trans('ModuleEnabledAdminMustCheckRights');
		// 							setEventMessages($msg, null, 'warnings');
		// 						}
		// 					} else {
		// 						dol_print_error($db);
		// 					}
		// 				}
		// 			}
		// 			header("Location: ".$_SERVER["PHP_SELF"]."?mode=".$mode.$param.($page_y ? '&page_y='.$page_y : ''));
		// 			exit;
		// 			// $mod->activateModule($modulename);
		// 		} else {
		// 			$sql_delete = "DELETE FROM " . MAIN_DB_PREFIX . "const";
		// 			$sql_delete .= " WHERE " . $this->db->decrypt('name') . " = '$moduleName'";

		// 			if ($this->db->query($sql_delete)) {
		// 				$action = '';
		// 				header("Location: ".$_SERVER["PHP_SELF"].'?token=' . newToken());
		// 			} else {
		// 				setEventMessage('Erreurs', 'errors');
		// 			}
		// 			// $mod->unActivateModule($modulename);
		// 			// exit;
		// 		}
		// 	}

		// 	// $moduldir = dolGetModulesDirs();
		// 	$modulTitle = [];
		// 	$sql = 'SELECT * FROM '.MAIN_DB_PREFIX.'const WHERE name like "%MAIN_MODULE%"';
		// 	$resql_get = $this->db->query($sql);
			
		// 	while ($modulTitleSql = $this->db->fetch_object($resql_get)) {
		// 		if (!preg_match('/CSS/', $modulTitleSql->name) && 
		// 			!preg_match('/HOOKS/', $modulTitleSql->name) && 
		// 			!preg_match('/MAIN_MODULE_SETUP_ON_LIST_BY_DEFAULT/', $modulTitleSql->name) && 
		// 			!preg_match('/ICON/', $modulTitleSql->name)) {

		// 			$modname = explode('_', $modulTitleSql->name);
		// 			$fmodulename = strtolower($modname[2]);
		// 			$modulTitle[$fmodulename] = $modulTitleSql->value;
		// 		}
		// 	}

		// 	$menu = '
		// 	<button id="toggleButton" onclick="toggleTable()"></button>
		// 	<table id="hiddenTable_fastmodulecheck" style="display:none; border: 1px solid black;">
		// 		<tr class="table_fast_title">
		// 			<th>Module</th>
		// 			<th>Active</th>
		// 		</tr>';

		// 	foreach ($modulTitle as $moduleName => $moduleValue) {
		// 		$iconValue = ($moduleValue == 1) ? 'no' : 'yes';
		// 		$menu .= '<tr class="table_fast_value">';
				
		// 		// Affiche le nom du module
		// 		$menu .= '<td>' . htmlspecialchars($moduleName) . '</td>';
				
		// 		// Affiche le statut du module (activé/désactivé)
		// 		$statusText = ($moduleValue == 1) ? 'Enabled' : 'Disabled';
		// 		// $menu .= '<td>' . htmlspecialchars($statusText) . '</td>';
				
		// 		// Crée le bouton pour activer/désactiver
		// 		$menu .= '<td>';
		// 		$menu .= '<a class="reposition" href="' . $_SERVER["PHP_SELF"] . '?value=' . urlencode($moduleName) . '&token=' . newToken() . '&action=activedModule&confirm='.$iconValue.'">';
		// 		$icon = ($moduleValue == 1) ? 'switch_on' : 'switch_off';
		// 		$menu .= img_picto($langs->trans($statusText), $icon);
		// 		$menu .= '</a>';
		// 		$menu .= '</td>';
				
		// 		$menu .= '</tr>';
		// 	}

		// 	$menu .= '</table>';



		// 	$this->resprints .= $menu . '
		// 	<script>
		// 		function toggleTable() {
		// 			var table = document.getElementById("hiddenTable_fastmodulecheck");
		// 			var button = document.getElementById("toggleButton");

		// 			if (table.style.display === "none") {
		// 				table.style.display = "flex";
		// 			} else {
		// 				table.style.display = "none";
		// 			}
		// 		}

		// 		// Handle clicks outside the table and button
		// 		document.addEventListener("click", function(e) {
		// 			var table = document.getElementById("hiddenTable_fastmodulecheck");
		// 			var button = document.getElementById("toggleButton");

		// 			// Check if click is outside the table and button
		// 			if (!table.contains(e.target) && e.target !== button) {
		// 				if (table.style.display === "flex") {
		// 					table.style.display = "none";
		// 				}
		// 			}
		// 		});
		// 	</script>';
		// }
	}

	/* Add here any other hooked methods... */
}
