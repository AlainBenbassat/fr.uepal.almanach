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
  return empty($contactSubType) ? null : str_replace(CRM_Core_DAO::VALUE_SEPARATOR, '', $contactSubType);
}

function almanach_getHref($contactSubType, $contactID) {
  return CRM_Utils_System::url('civicrm/paroisse-info', "reset=1&$contactSubType=$contactID");
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
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function almanach_civicrm_install() {
  _almanach_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function almanach_civicrm_enable() {
  _almanach_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 *

 // */

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
