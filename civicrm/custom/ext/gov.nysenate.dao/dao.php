<?php

require_once 'dao.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function dao_civicrm_config(&$config) {
  _dao_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function dao_civicrm_xmlMenu(&$files) {
  _dao_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function dao_civicrm_install() {
  _dao_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function dao_civicrm_uninstall() {
  _dao_civix_civicrm_uninstall();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function dao_civicrm_enable() {
  _dao_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function dao_civicrm_disable() {
  _dao_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function dao_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _dao_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function dao_civicrm_managed(&$entities) {
  _dao_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function dao_civicrm_caseTypes(&$caseTypes) {
  _dao_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function dao_civicrm_angularModules(&$angularModules) {
_dao_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function dao_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _dao_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function dao_civicrm_entityTypes(&$entityTypes) {
  $entityTypes['Contact']['fields_callback'][] = function($class, &$fields) {
    $fields['do_not_trade']['title'] = 'Undeliverable: Do Not Mail';//4766
    $fields['preferred_mail_format']['title'] = 'Preferred Email Format';

    //set fields that should not be exportable
    $fields['user_unique_id']['export'] = FALSE;//2719
    $fields['contact_sub_type']['export'] = FALSE;
    //$fields['current_employer_id']['export'] = FALSE; //13123 this breaks things downstream with API calls
    $fields['hash']['export'] = FALSE;
    $fields['image_URL']['export'] = FALSE;

    $fields['web_user_id'] = [
      'name' => 'web_user_id',
      'type' => CRM_Utils_Type::T_INT,
      'title' => ts('Website User ID'),
      'description' => ts('Public site User ID'),
      'where' => 'civicrm_contact.web_user_id',
      'table_name' => 'civicrm_contact',
      'entity' => 'Contact',
      'bao' => 'CRM_Contact_BAO_Contact',
      'localizable' => 0,
      'FKClassName' => 'CRM_Contact_DAO_Contact',
      'html' => [
        'label' => ts("Website User ID"),
      ],
      'readonly' => TRUE,
      'import' => TRUE,
      'headerPattern' => '/^web_user_id$/i',
      'export' => TRUE,
    ];
  };

  $entityTypes['Address']['fields_callback'][] = function($class, &$fields) {
    $fields['street_number']['import'] = TRUE; //include parsed address fields in import
    $fields['street_name']['import'] = TRUE;
    $fields['street_unit']['import'] = TRUE;
    $fields['supplemental_address_1']['title'] = 'Mailing Address';
    $fields['supplemental_address_2']['title'] = 'Building';
    //unset($fields['country_id']);//2771 //removed with C5.57 upgrade (caused errors)

    //set fields that should not be exportable
    $fields['geo_code_1']['export'] = FALSE;
    $fields['geo_code_2']['export'] = FALSE;
    $fields['name']['export'] = FALSE;
    $fields['master_id']['export'] = FALSE;
    $fields['county_id']['export'] = FALSE;
  };

  $entityTypes['WorldRegion']['fields_callback'][] = function($class, &$fields) {
    $fields['name']['export'] = FALSE;
  };

  //9784
  $entityTypes['CustomField']['fields_callback'][] = function($class, &$fields) {
    $fields['label']['maxlength'] = 1020;
  };

  //9784
  $entityTypes['CustomGroup']['fields_callback'][] = function($class, &$fields) {
    $fields['title']['maxlength'] = 128;
  };

  //2729
  $entityTypes['Email']['fields_callback'][] = function($class, &$fields) {
    $fields['is_primary']['title'] = 'Is Email Primary?';
    $fields['signature_text']['export'] = FALSE;
    $fields['signature_html']['export'] = FALSE;

    $fields['mailing_categories'] = [
      'name' => 'mailing_categories',
      'type' => CRM_Utils_Type::T_STRING,
      'title' => ts('Mailing Categories'),
      'description' => ts('Comma-separated list of mailing categories to EXCLUDE'),
      'where' => 'civicrm_email.mailing_categories',
      'table_name' => 'civicrm_email',
      'entity' => 'Email',
      'bao' => 'CRM_Core_BAO_Email',
      'localizable' => 0,
      'html' => [
        'label' => ts('Mailing Categories'),
      ],
      'import' => FALSE,
      'export' => FALSE,
      'maxlength' => 254,
      'size' => 30,
    ];
  };

  //2719
  $entityTypes['OpenID']['fields_callback'][] = function($class, &$fields) {
    $fields['openid']['export'] = FALSE;
  };

  //9656
  $entityTypes['Tag']['fields_callback'][] = function($class, &$fields) {
    $fields['name']['maxlength'] = 128;
    $fields['label']['maxlength'] = 128;
  };
}
