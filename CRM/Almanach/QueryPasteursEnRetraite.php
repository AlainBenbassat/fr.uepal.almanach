<?php

class CRM_Almanach_QueryPasteursEnRetraite extends CRM_Almanach_Query {
  public function __construct() {
    $this->title = "Pasteur·es en retraite de l’Union des Églises protestantes d’Alsace et de Lorraine";

    $this->fields = [
      [
        'label' => 'Nom',
        'name' => 'name',
        'dbAlias' => "concat(last_name, ' ', replace(first_name, ' e.r.', ''))",
      ],
      [
        'label' => 'Années',
        'name' => "annee",
        'dbAlias' => "concat('(',ifnull(year(birth_date),'-'),'/',ifnull(annee_entree_ministere,'-'),'/',ifnull(annee_consecration,'-'),'/',ifnull(annee_poste_actuel,'-'),')')",
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
      ],
    ];
    $this->query = $this->getQuery();
    $this->execute();
  }

  private function getQuery() {
    $HOME_LOCATION_TYPE_ID = 1;

    $fields = $this->getFieldListAsString();
    $groupByFields = $this->getGroupByFieldsAsString(['phone']);

    $sql = "
      select
        $fields
      from
        civicrm_contact c
      left outer join
        civicrm_value_ministre_detail md on md.entity_id = c.id
      left outer join
        civicrm_address a on a.contact_id = c.id and a.is_primary = 1
      left outer join
        civicrm_phone p on p.contact_id = c.id and p.location_type_id = $HOME_LOCATION_TYPE_ID
      left outer join
        civicrm_email e on e.contact_id = c.id and e.is_primary = 1
      where
        contact_sub_type like '%Ministre%'
      and
        is_deleted = 0
      and
        is_deceased = 0
      and
        statut = 3
      group by
        $groupByFields
      order by
        sort_name
    ";

    return $sql;
  }
}
