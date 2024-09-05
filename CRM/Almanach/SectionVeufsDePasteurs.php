<?php

class CRM_Almanach_SectionVeufsDePasteurs {
  public $data = [];

  public function __construct() {
    $this->data['title'] = 'Veuve / veuf de pasteur·e';
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
        'label' => 'Nom de jeune fille',
        'name' => 'nick_name',
        'dbAlias' => "if (length(trim(nick_name)) > 0, concat('née ', trim(nick_name), if(birth_date IS NOT NULL,concat(' (*', year(birth_date), ')'), '')), if(birth_date IS NOT NULL, concat(' (*', year(birth_date), ')'), NULL))",
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
  }

  private function getQuery() {
    $VEUF_DE_PASTEUR_TAG_ID = 35;
    $HOME_LOCATION_TYPE_ID = 1;

    $fields = CRM_Almanach_SectionHelper::getFieldListAsString($this->getFields());
    $groupByFields = CRM_Almanach_SectionHelper::getGroupByFieldsAsString($this->getFields(), ['phone']);

    $sql = "
      select
        $fields
      from
        civicrm_contact c
      left outer join
        civicrm_address a on a.contact_id = c.id and a.is_primary = 1
      left outer join
        civicrm_phone p on p.contact_id = c.id and p.location_type_id = $HOME_LOCATION_TYPE_ID
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
