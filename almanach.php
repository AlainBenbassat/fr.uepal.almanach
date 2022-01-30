<?php

require_once 'almanach.civix.php';
use CRM_Almanach_ExtensionUtil as E;

function almanach_civicrm_summaryActions(&$actions, $contactID) {
  // check if it's a paroisse
  if ($contactID > 0) {
    $contactSubType = almanach_getContactSubType($contactID);
    if (almanach_isParoisseConsistoireInspection($contactSubType)) {
      $href = almanach_getHref($contactSubType, $contactID);

      // add link to paroisse info page
      $actions['otherActions']['infoparoisse'] = [
        'title' => 'Info paroisse',
        'weight' => 999,
        'ref' => 'info-paroisse',
        'key' => 'infoparoisse',
        'href' => $href,
      ];
    }
  }
}

function almanach_getContactSubType($contactID) {
  $sql = "select contact_sub_type from civicrm_contact where id = $contactID";
  $contactSubType = CRM_Core_DAO::singleValueQuery($sql);
  return str_replace(CRM_Core_DAO::VALUE_SEPARATOR, '', $contactSubType);
}

function almanach_getHref($contactSubType, $contactID) {
  return "../paroisse-info?reset=1&$contactSubType=$contactID";
}

function almanach_isParoisseConsistoireInspection($contactSubType) {
  if ($contactSubType == 'paroisse' || $contactSubType == 'consistoire_lutherien' || $contactSubType == 'inspection_consistoire_reforme') {
    return TRUE;
  }
  else {
    return FALSE;
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function almanach_civicrm_config(&$config) {
  _almanach_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_xmlMenu
 */
function almanach_civicrm_xmlMenu(&$files) {
  _almanach_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function almanach_civicrm_install() {
  _almanach_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_postInstall
 */
function almanach_civicrm_postInstall() {
  _almanach_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_uninstall
 */
function almanach_civicrm_uninstall() {
  _almanach_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function almanach_civicrm_enable() {
  _almanach_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_disable
 */
function almanach_civicrm_disable() {
  _almanach_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_upgrade
 */
function almanach_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _almanach_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_managed
 */
function almanach_civicrm_managed(&$entities) {
  _almanach_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types.
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_caseTypes
 */
function almanach_civicrm_caseTypes(&$caseTypes) {
  _almanach_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules
 */
function almanach_civicrm_angularModules(&$angularModules) {
  _almanach_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_alterSettingsFolders
 */
function almanach_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _almanach_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Implements hook_civicrm_entityTypes().
 *
 * Declare entity types provided by this module.
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_entityTypes
 */
function almanach_civicrm_entityTypes(&$entityTypes) {
  _almanach_civix_civicrm_entityTypes($entityTypes);
}

/**
 * Implements hook_civicrm_thems().
 */
function almanach_civicrm_themes(&$themes) {
  _almanach_civix_civicrm_themes($themes);
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *
function almanach_civicrm_preProcess($formName, &$form) {

} // */

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 *
function almanach_civicrm_navigationMenu(&$menu) {
  _almanach_civix_insert_navigation_menu($menu, 'Mailings', array(
    'label' => E::ts('New subliminal message'),
    'name' => 'mailing_subliminal_message',
    'url' => 'civicrm/mailing/subliminal',
    'permission' => 'access CiviMail',
    'operator' => 'OR',
    'separator' => 0,
  ));
  _almanach_civix_navigationMenu($menu);
} // */
