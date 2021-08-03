<?php

class CRM_Almanach_QueryPasteursAutresMinistres extends CRM_Almanach_Query {
  public function __construct() {
    $this->title = 'Pasteur•es et autres ministres de l’UEPAL';
    $this->fields = [
      [
        'label' => 'Nom',
        'name' => 'last_name',
      ],
      [
        'label' => 'Prénom',
        'name' => 'first_name',
      ],
      [
        'label' => 'Années',
        'name' => "annee",
        'dbAlias' => "concat('(',annee_consecration,'/',annee_entree_ministere,'/',annee_poste_actuel,')')",
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
        'label' => 'CP et Ville',
        'name' => "cp_ville",
        'dbAlias' => "concat(a.postal_code, ' ', a.city)",
      ],
      [
        'label' => 'Tél',
        'name' => 'phone',
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
    $fields = $this->getFieldListAsString();

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
        civicrm_phone p on p.contact_id = c.id and p.is_primary = 1
      left outer join
        civicrm_email e on e.contact_id = c.id and e.is_primary = 1
      where
        contact_sub_type like '%Ministre%'
      and
        is_deleted = 0
      and
        is_deceased = 0
      order by
        sort_name
    ";

    return $sql;
  }
}
