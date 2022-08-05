<?php

class CRM_Almanach_QueryVeufsDePasteurs extends CRM_Almanach_Query {
  public function __construct() {
    $this->title = 'Veuve / veuf de pasteur·e';

    $this->fields = [
      [
        'label' => 'Nom',
        'name' => 'name',
        'dbAlias' => "concat(last_name, ' ', first_name)",
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
    $VEUF_DE_PASTEUR_TAG_ID = 35;

    $fields = $this->getFieldListAsString();
    $groupByFields = $this->getGroupByFieldsAsString(['phone']);

    $sql = "
      select
        $fields
      from
        civicrm_contact c
      left outer join
        civicrm_address a on a.contact_id = c.id and a.is_primary = 1
      left outer join
        civicrm_phone p on p.contact_id = c.id and p.location_type_id = 1
      left outer join
        civicrm_email e on e.contact_id = c.id and e.is_primary = 1
      where
        is_deleted = 0
      and
        is_deceased = 0
      and
        exists (
          select * from civicrm_entity_tag et where et.entity_id = c.id and et.entity_table = 'civicrm_contact' and et.tag_id = $VEUF_DE_PASTEUR_TAG_ID
        )
      group by
        $groupByFields
      order by
        sort_name
    ";

    return $sql;
  }
}
