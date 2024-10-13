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

/**
 * Class ActionsFastmodulecheck
 */
class ActionsFastmodulecheck
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

		return 0;
	}

	public function updateSession($parameters, &$object, &$action, HookManager $hookmanager) {
		global $user, $langs, $conf;

		$action = GETPOST('action', 'aZ09');
		$value = GETPOST('value', 'alpha');
		$error = 0; // Error counter
		$i = 0;
		$contexts = explode(':', $parameters['context'] ?? '');

		/**
		 * Action
		 */
		if ($action == 'fastmodulecheck_set' && $user->admin) {
			$langs->load('admin');
			require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
			$checkOldValue = getDolGlobalInt('CHECKLASTVERSION_EXTERNALMODULE');
			$csrfCheckOldValue = getDolGlobalInt('MAIN_SECURITY_CSRF_WITH_TOKEN');
			$resarray = activateModule($value);
			if ($checkOldValue != getDolGlobalInt('CHECKLASTVERSION_EXTERNALMODULE')) {
				setEventMessage($langs->trans('WarningModuleHasChangedLastVersionCheckParameter', $value), 'warnings');
			}
			if ($csrfCheckOldValue != getDolGlobalInt('MAIN_SECURITY_CSRF_WITH_TOKEN')) {
				setEventMessage($langs->trans('WarningModuleHasChangedSecurityCsrfParameter', $value), 'warnings');
			}
			dolibarr_set_const($this->db, "MAIN_IHM_PARAMS_REV", getDolGlobalInt('MAIN_IHM_PARAMS_REV') + 1, 'chaine', 0, '', $conf->entity);
			if (!empty($resarray['errors'])) {
				setEventMessages('', $resarray['errors'], 'errors');
			} else {
				if ($resarray['nbperms'] > 0) {
					$tmpsql = "SELECT COUNT(rowid) as nb FROM ".MAIN_DB_PREFIX."user WHERE admin <> 1";
					$resqltmp = $this->db->query($tmpsql);
					if ($resqltmp) {
						$obj = $this->db->fetch_object($resqltmp);
						//var_dump($obj->nb);exit;
						if ($obj && $obj->nb > 1) {
							$msg = $langs->trans('ModuleEnabledAdminMustCheckRights');
							setEventMessages($msg, null, 'warnings');
						}
					} else {
						dol_print_error($this->db);
					}
				}
			}
			header("Location: ".$_SERVER["PHP_SELF"]);
			exit;
		}

		if ($action == 'fastmodulecheck_reset' && $user->admin) {
			$langs->load('admin');
			require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
			$result = unActivateModule($value);
			dolibarr_set_const($this->db, "MAIN_IHM_PARAMS_REV", getDolGlobalInt('MAIN_IHM_PARAMS_REV') + 1, 'chaine', 0, '', $conf->entity);
			if ($result) {
				setEventMessages($result, null, 'errors');
			}
			header("Location: ".$_SERVER["PHP_SELF"]);
			exit;
		}

		if ($action === 'fastmodulecheck_reload' && $user->admin) {
			$langs->load('admin');
			require_once DOL_DOCUMENT_ROOT . '/core/lib/admin.lib.php';
			$result = unActivateModule($value, 0);
			dolibarr_set_const($this->db, "MAIN_IHM_PARAMS_REV", getDolGlobalInt('MAIN_IHM_PARAMS_REV') + 1, 'chaine', 0, '', $conf->entity);
			if ($result) {
				setEventMessages($result, null, 'errors');
				header("Location: ".$_SERVER["PHP_SELF"]);
			}

			// Tweak for <=V17 Dolibarr, otherwise reload wont work
			if (class_exists($value)) {
				$objMod = new $value($this->db);
				$constName = $objMod->const_name;
				$conf->global->$constName = 0;
			}

			$resarray = activateModule($value, 0, 1);
			dolibarr_set_const($this->db, "MAIN_IHM_PARAMS_REV", getDolGlobalInt('MAIN_IHM_PARAMS_REV') + 1, 'chaine', 0, '', $conf->entity);
			if (!empty($resarray['errors'])) {
				setEventMessages('', $resarray['errors'], 'errors');
			} else {
				if ($resarray['nbperms'] > 0) {
					$tmpsql = "SELECT COUNT(rowid) as nb FROM ".MAIN_DB_PREFIX."user WHERE admin <> 1";
					$resqltmp = $this->db->query($tmpsql);
					if ($resqltmp) {
						$obj = $this->db->fetch_object($resqltmp);
						if ($obj && $obj->nb > 1) {
							$msg = $langs->trans('ModuleEnabledAdminMustCheckRights');
							setEventMessages($msg, null, 'warnings');
						}
					} else {
						dol_print_error($this->db);
					}
				}
			}
			header("Location: ".$_SERVER["PHP_SELF"]);
			exit;

		}
		return 0;
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
	public function printTopRightMenu(&$parameters, $object, &$action, $hookmanager)
	{
		global $conf, $user, $langs, $mysoc;

		require_once DOL_DOCUMENT_ROOT.'/core/lib/admin.lib.php';

		// FIX for V15/V16/V17 : getEntity() will call the hookGetEntity, overwriting $hookmanager->resPrint...
		$this->resprints = $hookmanager->resPrint;
		$hookmanager->resPrint = '';

		$contexts = explode(':', $parameters['context'] ?? '');

		if (in_array('toprightmenu', $contexts) && $user->admin) {

			$menu = '
			<button id="toggleButton" onclick="toggleTable()"></button>
			<table id="hiddenTable_fastmodulecheck" style="display:none; border: 1px solid black;">
			</table>';

			$this->resprints .= $menu;
			ob_start();
			?>
			<script>
				function toggleTable() {
					let table = document.getElementById("hiddenTable_fastmodulecheck");
					let button = document.getElementById("toggleButton");

					if (table.style.display === "none") {
						fetch('<?= dol_buildpath('fastmodulecheck/ajax/get_modules.php', 1) ?>', {
							method: 'GET',
						})
							.then(response => {
								if (!response.ok) {
									throw new Error("Erreur lors du chargement des données");
								}
								return response.text();
							})
							.then(data => {
								table.innerHTML = data;
							})
							.catch(error => {
								console.error("Erreur:", error);
							});
						table.style.display = "flex";
					} else {
						table.style.display = "none";
					}
				}

				// Handle clicks outside the table and button
				document.addEventListener("click", function(e) {
					let table = document.getElementById("hiddenTable_fastmodulecheck");
					let button = document.getElementById("toggleButton");

					// Check if click is outside the table and button
					if (!table.contains(e.target) && e.target !== button) {
						if (table.style.display === "flex") {
							table.style.display = "none";
						}
					}
				});
			</script>
			<?php
			$this->resprints .= ob_get_clean();
		}
		return 0;
	}
}
