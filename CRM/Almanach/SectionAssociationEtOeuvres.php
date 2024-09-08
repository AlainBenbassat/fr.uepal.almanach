<?php

class CRM_Almanach_SectionAssociationEtOeuvres {
  public $data = [];

  public function __construct() {
    $this->data['title'] = 'Association et Œuvres';
    $this->data['subsections'] = [];

    $subsectionTitles = $this->getSubsectionTitles();
    foreach ($subsectionTitles as $subsectionValue => $subsectionTitle) {
      $this->data['subsections'][] = [
        'title' => $subsectionTitle,
        'fields' => $this->getFields(),
        'records' => CRM_Almanach_SectionHelper::executeQuery($this->getQuery($subsectionValue)),
      ];
    }
  }

  private function getFields() {
    return [
      [
        'label' => 'Nom',
        'name' => 'name',
        'dbAlias' => "(case when ciom_41 = '1' and fep_adh_rent_42 = '1' then concat (organization_name, ' <sup>(CIOM, FEP)</sup>') WHEN ciom_41 = '1' THEN concat (organization_name, ' <sup>(CIOM)</sup>') WHEN fep_adh_rent_42 = '1' THEN concat (organization_name, ' <sup>(FEP)</sup>') ELSE organization_name END)",
      ],
      [
        'label' => 'Rue',
        'name' => 'street_address',
      ],
      [
        'label' => "Complément d'adresse",
        'name' => 'supplemental_address_1',
      ],
      [
        'label' => "Complément d'adresse 2",
        'name' => 'supplemental_address_2',
      ],
      [
        'label' => 'CP et Ville',
        'name' => "cp_ville",
        'dbAlias' => "concat(a.postal_code, ' ', a.city)",
      ],
      [
        'label' => 'Tél',
        'name' => 'phone',
        'dbAlias' => "group_concat(DISTINCT phone ORDER BY phone SEPARATOR ' - ')",
      ],
      [
        'label' => 'E-mail',
        'name' => 'email',
      ],
      [
        'label' => 'Site',
        'name' => 'url',
        'dbAlias' => "group_concat(DISTINCT url ORDER BY url SEPARATOR ' - ')",
      ],
    ];
  }

  private function getQuery($subsectionValue) {
    $fields = CRM_Almanach_SectionHelper::getFieldListAsString($this->getFields());
    $groupByFields = CRM_Almanach_SectionHelper::getGroupByFieldsAsString($this->getFields(), ['phone', 'url']);

    $sql = "
      select
        $fields
      from
        civicrm_contact c
      inner join
        civicrm_value_almanach_13 alm on alm.entity_id = c.id
      left outer join
        civicrm_address a on a.contact_id = c.id and a.is_primary = 1
      left outer join
        civicrm_phone p on p.contact_id = c.id
      left outer join
        civicrm_email e on e.contact_id = c.id and e.is_primary = 1
      left outer join
        civicrm_website ws on ws.contact_id = c.id
      where
        is_deleted = 0
      and
        is_deceased = 0
      and
        alm.rubrique_40 = '$subsectionValue'
      group by
        $groupByFields
      order by
        sort_name
    ";

    return $sql;
  }

  private function getSubsectionTitles() {
    $sectionTitles = [];

    $sql = "select value, label from civicrm_option_value where option_group_id = 122";
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $sectionTitles[$dao->value] = $dao->label;
    }

    return $sectionTitles;
  }
}
