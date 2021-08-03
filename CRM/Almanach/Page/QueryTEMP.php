<?php

class TEMP____OLD {

  private function getEpcaalTable() {
    $t = [];

    $sql = "
      select
        distinct c.id, c.organization_name
      from
        civicrm_contact c
      inner join
        civicrm_value_paroisse_detail d on c.id = d.inspection_consistoire_reforme
      where
        d.eglise = 2
      and
        c.is_deleted = 0
      order by
        c.organization_name
    ";
    $daoInspec = CRM_Core_DAO::executeQuery($sql);
    while ($daoInspec->fetch()) {
      $t[] = [$daoInspec->organization_name, '', ''];
      $sql = "
        select
          distinct c.id, c.organization_name
        from
          civicrm_contact c
        inner join
          civicrm_value_paroisse_detail d on c.id = d.consistoire_lutherien
        where
          d.inspection_consistoire_reforme = %1
        and
          c.is_deleted = 0
        order by
          c.organization_name
      ";
      $sqlParams = [
        1 => [$daoInspec->id, 'Integer']
      ];
      $daoConsist = CRM_Core_DAO::executeQuery($sql, $sqlParams);
      while ($daoConsist->fetch()) {
        $t[] = ['', $daoConsist->organization_name, ''];
      }
    }
    return $t;
  }

  /*
  select * from civicrm_value_paroisse_detail limit 0,2;
  +-----+-----------+--------+-----------+--------------------------------+-----------------------+--------------------+------------------+
  | id  | entity_id | eglise | theologie | inspection_consistoire_reforme | consistoire_lutherien
  */

  private
  function getEpralTable() {
    $t = [];

    $sql = "
      select
        distinct c.id, c.organization_name
      from
        civicrm_contact c
      inner join
        civicrm_value_paroisse_detail d on c.id = d.inspection_consistoire_reforme
      where
        d.eglise = 1
      and
        c.is_deleted = 0
      order by
        c.organization_name
    ";
    $daoInspec = CRM_Core_DAO::executeQuery($sql);
    while ($daoInspec->fetch()) {
      $t[] = [$daoInspec->organization_name, ''];
      $sql = "
        select
          distinct c.id, c.organization_name
        from
          civicrm_contact c
        inner join
          civicrm_value_paroisse_detail d on c.id = d.entity_id
        where
          d.inspection_consistoire_reforme = %1
        and
          c.is_deleted = 0
        order by
          c.organization_name
      ";
      $sqlParams = [
        1 => [$daoInspec->id, 'Integer']
      ];
      $daoPar = CRM_Core_DAO::executeQuery($sql, $sqlParams);
      while ($daoPar->fetch()) {
        $t[] = ['', $daoPar->organization_name];
      }
    }

    return $t;
  }

}
