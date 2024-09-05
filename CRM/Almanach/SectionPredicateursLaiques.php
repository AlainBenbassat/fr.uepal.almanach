<?php

class CRM_Almanach_SectionPredicateursLaiques {
  const REL_TYPE_ID_est_predicateur_laique_pour = 27;

  public function __construct() {
    $this->data['title'] = 'Prédicateurs / prédicatrices';
    $this->data['subsections'] = [];
    $this->data['subsections'][] = [
      'title' => '',
      'fields' => $this->getFields(),
      'records' => CRM_Almanach_SectionHelper::executeQuery($this->getQuery()),
    ];
  }

  private function getFields() {
    return [
      [
        'label' => 'Nom',
        'name' => 'name',
        'dbAlias' => "concat(last_name, ' ', first_name)",
      ],
      [
        'label' => 'Années',
        'name' => "annee",
        'dbAlias' => "concat('(',ifnull(year(birth_date),'-'),'/',ifnull(year(r.start_date),'-'),')')",
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
        'dbAlias' => "group_concat(phone ORDER BY phone SEPARATOR ' - ')",
      ],
      [
        'label' => 'E-mail',
        'name' => 'email',
        'dbAlias' => 'if(do_not_trade = 1, NULL, email)',
      ],
    ];
  }

  private function getQuery() {
    $HOME_LOCATION_TYPE_ID = 1;

    $fields = CRM_Almanach_SectionHelper::getFieldListAsString($this->getFields());
    $groupByFields = CRM_Almanach_SectionHelper::getGroupByFieldsAsString($this->getFields(), ['phone']);

    $sql = "
      select
        $fields
      from
        civicrm_contact c
      inner join
        civicrm_relationship r on r.contact_id_a = c.id and r.relationship_type_id = " . self::REL_TYPE_ID_est_predicateur_laique_pour . "
      left outer join
        civicrm_address a on a.contact_id = c.id and a.is_primary = 1
      left outer join
        civicrm_phone p on p.contact_id = c.id and p.location_type_id = $HOME_LOCATION_TYPE_ID
      left outer join
        civicrm_email e on e.contact_id = c.id and e.is_primary = 1
      where
        ifnull(contact_sub_type, '') = ''
      and
        r.is_active = 1
      and
        is_deleted = 0
      and
        is_deceased = 0
      group by
        $groupByFields
      order by
        sort_name
    ";

    return $sql;
  }
}
