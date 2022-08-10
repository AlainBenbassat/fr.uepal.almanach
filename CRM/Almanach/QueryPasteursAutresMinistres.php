<?php

class CRM_Almanach_QueryPasteursAutresMinistres extends CRM_Almanach_Query {
  public function __construct() {
    $this->title = 'Pasteur•es et autres ministres de l’UEPAL';

    $this->fields = [
      [
        'label' => 'Nom',
        'name' => 'name',
        'dbAlias' => "concat(last_name, ' ', first_name)",
      ],
      [
        'label' => 'Années',
        'name' => "annee",
        'dbAlias' => "concat('(',ifnull(year(birth_date),'-'),'/',ifnull(annee_entree_ministere,'-'),'/',ifnull(annee_consecration,'-'),'/',ifnull(annee_poste_actuel,'-'),')')",
      ],
      [
        'label' => 'Poste actuel',
        'name' => 'job_title',
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
        'dbAlias' => "group_concat(DISTINCT phone ORDER BY p.is_primary DESC SEPARATOR ' - ')",
      ],
      [
        'label' => 'E-mail',
        'name' => 'email',
        'dbAlias' => "group_concat(DISTINCT email ORDER BY e.is_primary DESC SEPARATOR ' - ')",
      ],
    ];
    $this->query = $this->getQuery();
    $this->execute();
  }

  private function getQuery() {
    $WORK_LOCATION_TYPE_ID = 2;

    $fields = $this->getFieldListAsString();
    $groupByFields = $this->getGroupByFieldsAsString(['phone', 'email']);

    $sql = "
      select
        $fields
      from
        civicrm_contact c
      left outer join
        civicrm_value_ministre_detail md on md.entity_id = c.id
      left outer join
        civicrm_address a on a.contact_id = c.id and a.is_primary = 1 and a.location_type_id = $WORK_LOCATION_TYPE_ID
      left outer join
        civicrm_phone p on p.contact_id = c.id and p.location_type_id = $WORK_LOCATION_TYPE_ID
      left outer join
        civicrm_email e on e.contact_id = c.id and e.location_type_id = $WORK_LOCATION_TYPE_ID
      where
        contact_sub_type like '%Ministre%'
      and
        is_deleted = 0
      and
        is_deceased = 0
      and
        statut = 1
      group by
        $groupByFields
      order by
        sort_name
    ";

    return $sql;
  }
}
