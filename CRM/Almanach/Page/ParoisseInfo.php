<?php
use CRM_Almanach_ExtensionUtil as E;

class CRM_Almanach_Page_ParoisseInfo extends CRM_Core_Page {
  private $config;
  private $customFieldName_numParoissiens = '';
  private $customFieldName_numElecteurs = '';

  private $paroisseId = 0;
  private $consistoireLutherienId = 0;
  private $inspectionConsistoireReformeId = 0;

  public function __construct($title = NULL, $mode = NULL) {
    $this->config = new CRM_Uepalconfig_Config();
    $this->customFieldName_numParoissiens = 'custom_' . $this->config->getCustomField_paroisseDetailNombreParoissiens()['id'];
    $this->customFieldName_numElecteurs = 'custom_' . $this->config->getCustomField_paroisseDetailNombreElecteurs()['id'];

    parent::__construct($title, $mode);
  }

  public function run() {
    try {
      $this->checkPermissionAndRetrieveIds();

      CRM_Utils_System::setTitle('');

      $this->assign('hierarchy', $this->buildHierarchy());
    }
    catch (Exception $e) {
      CRM_Core_Session::setStatus($e->getMessage());
    }

    parent::run();
  }

  private function getParoisseDetail($paroisseId) {
    $paroisseDetail = [];

    $paroisse = civicrm_api3('Contact', 'getsingle', [
      'id' => $paroisseId,
      'contact_sub_type' => 'paroisse',
      'is_deleted' => 0,
      'return' => "organization_name,{$this->customFieldName_numElecteurs},{$this->customFieldName_numParoissiens}",
    ]);

    $numPresbyt = $this->getNumPresbyt($paroisse[$this->customFieldName_numParoissiens]);
    if ($numPresbyt > 0) {
      $paroisseDetail['numPresbyt'] = $numPresbyt;
    }
    else {
      $paroisseDetail['numPresbyt'] = '';
    }

    $paroisseDetail['managementArr'] = $this->getParoisseManagement($paroisseId);

    $relType = $this->config->getRelationshipType_estMembreDeDroitDe();
    $paroisseDetail['rightMembersArr'] = $this->getContacts($paroisseId, $relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    $relType = $this->config->getRelationshipType_estMembreEluDe();
    $paroisseDetail['electedMembersArr'] = $this->getContacts($paroisseId, $relType['id'], str_replace('a pour ', '', $relType['label_b_a']), $numPresbyt);

    $relType = $this->config->getRelationshipType_estMembreInviteDe();
    $paroisseDetail['invitedMembersArr'] = $this->getContacts($paroisseId, $relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    $relType = $this->config->getRelationshipType_estMembreCoopteDe();
    $paroisseDetail['cooptedMembersArr'] = $this->getContacts($paroisseId, $relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    $relType = $this->config->getRelationshipType_estReceveurDe();
    $paroisseDetail['receveursArr'] = $this->getContacts($paroisseId, $relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    $paroisseDetail['numParoissiens'] = $paroisse[$this->customFieldName_numParoissiens];
    $paroisseDetail['numElecteurs'] = $paroisse[$this->customFieldName_numElecteurs];


    return $paroisseDetail;
  }

  private function buildHierarchy() {
    // we build an array of 3 nested levels:
    // level 1: one or more Inspection / Consistoire réformé [for now, will only be 1 because eglise level is not implemented yet]
    //   level 2: one or more consistoire_lutherien OR one dummy in case of réformé
    //     level 3: one or more paroisse

    $level1 = [];
    $inspectionName = $this->getInspectionConsistoireReformeName($this->inspectionConsistoireReformeId);
    $level1[$inspectionName] = $this->getConsistoiresOrDummy($this->inspectionConsistoireReformeId);

    return $level1;
  }

  private function getInspectionConsistoireReformeName($inspectionConsistoireReformeId) {
    if ($inspectionConsistoireReformeId == 0) {
      $inspectionConsistoireReformeId = $this->getInspectionIdFromLowerLevel();
      $this->inspectionConsistoireReformeId = $inspectionConsistoireReformeId;
    }

    return $this->getOrganizationName($inspectionConsistoireReformeId);
  }

  private function getConsistoiresOrDummy($inspectionConsistoireReformeId) {
    $level2 = [];

    if ($this->consistoireLutherienId) {
      $consistoireLutherienName = $this->getOrganizationName($this->consistoireLutherienId);
      $level2[$consistoireLutherienName] = $this->getParoisesFromConsistoireLutherien($this->consistoireLutherienId);
    }
    else {
      $consistoireIds = $this->getConsistoireIdsFromInspection($inspectionConsistoireReformeId);
      if (count($consistoireIds) > 0) {
        foreach ($consistoireIds as $consistoireId) {
          $consistoireLutherienName = $this->getOrganizationName($consistoireId);
          $level2[$consistoireLutherienName] = $this->getParoisesFromConsistoireLutherien($consistoireId);
        }
      }
      else {
        $level2[''] = $this->getParoisesFromInspection($inspectionConsistoireReformeId);
      }
    }

    return $level2;
  }

  private function getConsistoireIdsFromInspection($inspectionConsistoireReformeId) {
    $ids = [];

    if ($this->paroisseId) {
      $whereParoisse = " and pd.entity_id = " . $this->paroisseId;
    }
    else {
      $whereParoisse = '';
    }

    $sql = "
      select
        pd.consistoire_lutherien
      from
        civicrm_value_paroisse_detail pd
      inner join
        civicrm_contact c on c.id = pd.consistoire_lutherien
      where
        pd.inspection_consistoire_reforme = $inspectionConsistoireReformeId
      and
        c.is_deleted = 0
      $whereParoisse
      order by
        c.sort_name
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $ids[] = $dao->consistoire_lutherien;
    }

    return $ids;
  }

  private function getParoisesFromConsistoireLutherien($consistoireLutherienId) {
    $level3 = [];

    if ($this->paroisseId) {
      $whereParoisse = " and pd.entity_id = " . $this->paroisseId;
    }
    else {
      $whereParoisse = '';
    }

    $sql = "
      select
        pd.entity_id
      from
        civicrm_value_paroisse_detail pd
      inner join
        civicrm_contact c on c.id = pd.entity_id
      where
        consistoire_lutherien = $consistoireLutherienId
      and
        c.is_deleted = 0
      $whereParoisse
      order by
        c.sort_name
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);

    while ($dao->fetch()) {
      $paroisseName = $this->getOrganizationName($dao->entity_id);
      $level3[$paroisseName] = $this->getParoisseDetail($dao->entity_id);
    }

    return $level3;
  }

  private function getParoisesFromInspection($inspectionConsistoireReformeId) {
    $level3 = [];

    if ($this->paroisseId) {
      $whereParoisse = " and pd.entity_id = " . $this->paroisseId;
    }
    else {
      $whereParoisse = '';
    }

    $sql = "
      select
        pd.entity_id
      from
        civicrm_value_paroisse_detail pd
      inner join
        civicrm_contact c on c.id = pd.entity_id
      where
        inspection_consistoire_reforme = $inspectionConsistoireReformeId
      and
        c.is_deleted = 0
      $whereParoisse
      order by
        c.sort_name
    ";
    $dao = CRM_Core_DAO::executeQuery($sql);

    while ($dao->fetch()) {
      $paroisseName = $this->getOrganizationName($dao->entity_id);
      $level3[$paroisseName] = $this->getParoisseDetail($dao->entity_id);
    }

    return $level3;
  }

  private function getOrganizationName($contactId) {
    return CRM_Core_DAO::singleValueQuery("select organization_name from civicrm_contact where id = $contactId");
  }

  private function getInspectionIdFromLowerLevel() {
    if ($this->consistoireLutherienId) {
      $sql = "select inspection_consistoire_reforme from civicrm_value_paroisse_detail where consistoire_lutherien = " . $this->consistoireLutherienId;
    }
    else {
      $sql = "select inspection_consistoire_reforme from civicrm_value_paroisse_detail where entity_id = " . $this->paroisseId;
    }

    $dao = CRM_Core_DAO::executeQuery($sql);
    if ($dao->fetch()) {
      return $dao->inspection_consistoire_reforme;
    }
    else {
      return 0;
    }
  }

  private function checkPermissionAndRetrieveIds() {
    // this page is public but we give access if:
    // - the user is logged in, has access to civicrm and a paroisse ID, consistoire ID or inspection ID was given
    // or
    // - we have a valid cid and checksum in the url, and the contact has a appropriate role
    if ($this->isLoggedInCiviCrmUser()) {
      // the user is logged in, get the id of the provided paroisse or consistoire or inspection
      $this->paroisseId = CRM_Utils_Request::retrieve('paroisse', 'Integer', $this, FALSE, 0);
      $this->consistoireLutherienId = CRM_Utils_Request::retrieve('consistoire_lutherien', 'Integer', $this, FALSE, 0);
      $this->inspectionConsistoireReformeId = CRM_Utils_Request::retrieve('inspection_consistoire_reforme', 'Integer', $this, FALSE, 0);
    }
    elseif ($this->hasValidChecksum()) {
      $cid = CRM_Utils_Request::retrieve('cid', 'Int');

      // get the inspection, consistoire, or paroisse
      $this->paroisseId = 0;
      $this->consistoireLutherienId = 0;

      $this->inspectionConsistoireReformeId = $this->getInspectionConsistoireReformeIdOfContact($cid);
      if (empty($this->inspectionConsistoireReformeId)) {
        $this->consistoireLutherienId = $this->getConsistoireLutherienIdOfContact($cid);

        if (empty($this->consistoireLutherienId)) {
          $this->paroisseId = $this->getParoisseIdOfContact($cid);
        }
      }
    }
  }

  private function isLoggedInCiviCrmUser() {
    if (CRM_Core_Permission::check('access CiviCRM')) {
      return TRUE;
    }
    else {
      return FALSE;
    }
  }

  private function hasValidChecksum() {
    $cs = CRM_Utils_Request::retrieve('cs', 'String');
    $cid = CRM_Utils_Request::retrieve('cid', 'Int');

    if (empty($cs) || empty($cid)) {
      throw new Exception("L'information n'est pas disponible.");
    }
    elseif (CRM_Contact_BAO_Contact_Utils::validChecksum($cid, $cs) !== TRUE) {
      throw new Exception("Le lien que vous avez utilisé n'est plus actif.");
    }

    return TRUE;
  }

  private function getNumPresbyt($numParoissiens) {
    $numPresbyt = 0;
    $n = (int)$numParoissiens;
    if ($n < 500) {
      $numPresbyt = 6;
    }
    elseif ($n < 800) {
      $numPresbyt = 8;
    }
    elseif ($n < 1500) {
      $numPresbyt = 10;
    }
    elseif ($n < 2500) {
      $numPresbyt = 12;
    }
    elseif ($n < 5000) {
      $numPresbyt = 14;
    }
    elseif ($n >= 5000) {
      $numPresbyt = 16;
    }

    return $numPresbyt;
  }

  private function getParoisseManagement($paroisseId) {
    $relType = $this->config->getRelationshipType_estPresidentDe();
    $a = $this->getContacts($paroisseId, $relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    $relType = $this->config->getRelationshipType_estVicePresidentDe();
    $b = $this->getContacts($paroisseId, $relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    $relType = $this->config->getRelationshipType_estTresorierDe();
    $c = $this->getContacts($paroisseId, $relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    $relType = $this->config->getRelationshipType_estSecretaireDe();
    $d = $this->getContacts($paroisseId, $relType['id'], str_replace('a pour ', '', $relType['label_b_a']));

    return array_merge($a, $b, $c, $d);
  }

  private function getContacts($paroisseId, $relTypeId, $relTypeName, $minRecords = 1) {
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
      , p.phone phone
      , concat(a.street_address, ', ', a.city) address
      , c.birth_date
      from
        civicrm_relationship r
      inner join
        civicrm_contact c on r.contact_id_a = c.id and c.is_deleted = 0
      left outer join
        civicrm_email e on e.contact_id = c.id and e.is_primary = 1
      left outer join
        civicrm_phone p on p.contact_id = c.id and p.is_primary = 1
      left outer join
        civicrm_address a on a.contact_id = c.id and a.is_primary = 1
      where
        r.relationship_type_id = $relTypeId
        and r.is_active = 1
        and r.contact_id_b = $paroisseId
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
    // allowed relationship types:
    $relTypeIds = '(' .
      $this->config->getRelationshipType_estPasteurNommeDe()['id'] . ',' .
      $this->config->getRelationshipType_estPresidentDe()['id'] . ',' .
      $this->config->getRelationshipType_estSecretaireDe()['id'] . ',' .
      $this->config->getRelationshipType_estTresorierDe()['id'] . ',' .
      $this->config->getRelationshipType_estVicePresidentDe()['id'] .
    ')';

    $sql = "
      select
        ifnull(max(r.contact_id_b), 0)
      from
        civicrm_relationship r
      inner join
        civicrm_contact c on c.id = r.contact_id_b
      where
        r.relationship_type_id in $relTypeIds
      and
        r.contact_id_a = %1
      and
        r.is_active = 1
      and
        c.is_deleted = 0
      and
        c.contact_type = 'Organization'
      and
        c.contact_sub_type like '%paroisse%'
    ";
    $paroisseId = CRM_Core_DAO::singleValueQuery($sql, [1 => [$contactId, 'Integer']]);

    if ($paroisseId > 0) {
      return $paroisseId;
    }
    else {
      return 0; //throw new Exception("L'information n'est pas disponible.");
    }
  }

  private function getConsistoireLutherienIdOfContact($contactId) {
    $relTypeIds = '(' .
      $this->config->getRelationshipType_estPresidentDe()['id'] . ',' .
      $this->config->getRelationshipType_estVicePresidentDe()['id'] .
    ')';

    $sql = "
      select
        ifnull(max(r.contact_id_b), 0)
      from
        civicrm_relationship r
      inner join
        civicrm_contact c on c.id = r.contact_id_b
      where
        r.relationship_type_id in $relTypeIds
      and
        r.contact_id_a = %1
      and
        r.is_active = 1
      and
        c.is_deleted = 0
      and
        c.contact_type = 'Organization'
      and
        c.contact_sub_type like '%consistoire_lutherien%'
    ";
    $consistoireLutherienId = CRM_Core_DAO::singleValueQuery($sql, [1 => [$contactId, 'Integer']]);

    if ($consistoireLutherienId > 0) {
      return $consistoireLutherienId;
    }
    else {
      return 0;
    }
  }

  private function getInspectionConsistoireReformeIdOfContact($contactId) {
    $relTypeIds = '(' .
      $this->config->getRelationshipType_estPresidentDe()['id'] . ',' .
      $this->config->getRelationshipType_estVicePresidentDe()['id'] . ',' .
      $this->config->getRelationshipType_estInspecteurDe()['id'] . ',' .
      $this->config->getRelationshipType_estInspecteurEcclesiastiqueDe()['id'] .
    ')';

    $sql = "
      select
        ifnull(max(r.contact_id_b), 0)
      from
        civicrm_relationship r
      inner join
        civicrm_contact c on c.id = r.contact_id_b
      where
        r.relationship_type_id in $relTypeIds
      and
        r.contact_id_a = %1
      and
        r.is_active = 1
      and
        c.is_deleted = 0
      and
        c.contact_type = 'Organization'
      and
        c.contact_sub_type like '%inspection_consistoire_reforme%'
    ";
    $consistoireLutherienId = CRM_Core_DAO::singleValueQuery($sql, [1 => [$contactId, 'Integer']]);

    if ($consistoireLutherienId > 0) {
      return $consistoireLutherienId;
    }
    else {
      return 0;
    }
  }
}
