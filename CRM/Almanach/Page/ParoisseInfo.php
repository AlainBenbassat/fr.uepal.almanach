<?php
use CRM_Almanach_ExtensionUtil as E;

class CRM_Almanach_Page_ParoisseInfo extends CRM_Core_Page {
  private $config;
  private $paroisseId = 0;
  private $paroisse;
  private $customFieldName_numParoissiens = '';
  private $customFieldName_numElecteurs = '';

  public function __construct($title = NULL, $mode = NULL) {
    $this->config = new CRM_Uepalconfig_Config();
    $this->customFieldName_numParoissiens = 'custom_' . $this->config->getCustomField_paroisseDetailNombreParoissiens()['id'];
    $this->customFieldName_numElecteurs = 'custom_' . $this->config->getCustomField_paroisseDetailNombreElecteurs()['id'];

    parent::__construct($title, $mode);
  }

  public function run() {
    try {
      $this->checkPermissionAndRetrieveParoisse();

      // get president, vice-president...
      $this->assign('managementArr', $this->getParoisseManagement());

      // get members by right
      $relType = $this->config->getRelationshipType_estMembreDeDroitDe();
      $this->assign('rightMembersArr', $this->getContacts($relType['id'], str_replace('a pour ', '', $relType['label_b_a'])));

      // get elected members
      $relType = $this->config->getRelationshipType_estMembreEluDe();
      $this->assign('electedMembersArr', $this->getContacts($relType['id'], str_replace('a pour ', '', $relType['label_b_a'])));

      $this->assign('numParoissiens', $this->paroisse[$this->customFieldName_numParoissiens]);
      $this->assign('numElecteurs', $this->paroisse[$this->customFieldName_numElecteurs]);

      CRM_Utils_System::setTitle('Paroisse : ' . $this->paroisse['organization_name']);
    }
    catch (Exception $e) {
      CRM_Core_Session::setStatus($e->getMessage());
    }

    $this->assign('paroisseId', $this->paroisseId);
    parent::run();
  }

  private function checkPermissionAndRetrieveParoisse() {
    // this page is public but we only show content if:
    // - the current user is logged in and has access to civicrm, and a paroisse ID was given
    // or
    // - we have a valid cid and checksum in the url, and the contact has one these roles
    //     - president
    //     - vice president
    //     - secretary
    //     - treasurer
    if (CRM_Core_Permission::check('access CiviCRM')) {
      // the user is logged in, get the paroisse id
      $this->paroisseId = CRM_Utils_Request::retrieve('paroisse', 'Integer', $this, FALSE, 0);
    }
    else {
      // the user is not logged in, check cid and cs
      $cs = CRM_Utils_Request::retrieve('cs', 'String');
      $cid = CRM_Utils_Request::retrieve('cid', 'Int');
      if (empty($cs) || empty($cid)) {
        throw new Exception("L'information n'est pas disponible.");
      }
      elseif (CRM_Contact_BAO_Contact_Utils::validChecksum($cid, $cs) !== TRUE) {
// TODO TIJDELIJK!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
                //throw new Exception("Le lien que vous avez utilisé n'est plus actif.");
      }

      // get the paroisse of this user
      $this->paroisseId = $this->getParoisseIdOfContact($cid);
    }

    // make sure we have a paroisse id
    if ($this->paroisseId > 0) {
      // get the paroisse
      $this->paroisse = civicrm_api3('Contact', 'getsingle', [
        'id' => $this->paroisseId,
        'contact_sub_type' => 'paroisse',
        'is_deleted' => 0,
        'return' => "organization_name,{$this->customFieldName_numElecteurs},{$this->customFieldName_numParoissiens}",
      ]);
    }
    else {
      throw new Exception("L'information n'est pas disponible.");
    }
  }

  private function getParoisseManagement() {
    $relType = $this->config->getRelationshipType_estPresidentDe();
    $a = $this->getContacts($relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    $relType = $this->config->getRelationshipType_estVicePresidentDe();
    $b = $this->getContacts($relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    $relType = $this->config->getRelationshipType_estTresorierDe();
    $c = $this->getContacts($relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    $relType = $this->config->getRelationshipType_estSecretaireDe();
    $d = $this->getContacts($relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    return array_merge($a, $b, $c, $d);
  }

  private function getContacts($relTypeId, $relTypeName, $minRecords = 1) {
    $sql = "
      select
      '$relTypeName' role
      , concat(c.last_name, ' ', c.first_name ) name
      , ifnull(r.start_date, '/') start_date
      , ifnull(r.end_date, '/') end_date
      , (
        select
          group_concat(concat(ex_r.start_date, ' - ', ex_r.end_date) SEPARATOR '<br>')
        from
          civicrm_relationship ex_r
        where
          ex_r.contact_id_a = r.contact_id_a
        and
          ex_r.relationship_type_id = r.relationship_type_id
        and
          ex_r.is_active = 0
        group by
          ex_r.contact_id_a
        order by
          ex_r.end_date desc
      ) ex_relationships
      , e.email
      , p1.phone phone_fixed
      , p2.phone phone_mobile
      from
        civicrm_relationship r
      inner join
        civicrm_contact c on r.contact_id_a = c.id and c.is_deleted = 0
      left outer join
        civicrm_email e on e.contact_id = c.id and e.is_primary = 1
      left outer join
        civicrm_phone p1 on p1.contact_id = c.id and p1.phone_type_id = 1 and p1.location_type_id = 1
      left outer join
        civicrm_phone p2 on p2.contact_id = c.id and p2.phone_type_id = 2 and p2.location_type_id = 1
      where
        r.relationship_type_id = $relTypeId
        and r.is_active = 1
        and r.contact_id_b = {$this->paroisseId}
      order by
        c.sort_name
    ";

    // store the result in an array
    $dao = CRM_Core_DAO::executeQuery($sql);
    $r = $dao->fetchAll();

    // see if we need to add blank records
    if (count($r) < $minRecords) {
      $numRecordsToAdd = $minRecords - count($r);
      for ($i = 0; $i <  $numRecordsToAdd; $i++) {
        $r[] = ['role' => $relTypeName, '', '', '', '', '', '', ''];
      }
    }

    return $r;
  }

  private function getParoisseIdOfContact($contactId) {
    // get relationship type id's of (vice)president, secretary, treasurer
    $relTypeIds = '(' .
      $this->config->getRelationshipType_estPresidentDe()['id'] . ',' .
      $this->config->getRelationshipType_estVicePresidentDe()['id'] . ',' .
      $this->config->getRelationshipType_estSecretaireDe()['id'] . ',' .
      $this->config->getRelationshipType_estTresorierDe()['id'] . ')';

    // get the paroisse
    $sql = "
      select
        ifnull(max(r.contact_id_b), 0)
      from
        civicrm_relationship r
      where
        r.relationship_type_id in $relTypeIds
      and
        r.contact_id_a = %1
      and
        r.is_active = 1
    ";
    return CRM_Core_DAO::singleValueQuery($sql, [1 => [$contactId, 'Integer']]);
  }

}
