<?php

class CRM_Almanach_SectionHelper {

  public static function getFieldListAsString($fields) {
    $i = 0;
    $fieldList = '';

    foreach ($fields as $field) {
      if ($i > 0) {
        $fieldList .= ',';
      }

      if (isset($field['dbAlias'])) {
        $fieldList .= $field['dbAlias'] . ' ';
      }

      $fieldList .= $field['name'];

      $i++;
    }

    return $fieldList;
  }

  public static function getGroupByFieldsAsString($fields, $excludeCols) {
    $fieldList = '';

    foreach ($fields as $field) {
      if (!in_array($field['name'], $excludeCols)) {
        if ($fieldList) {
          $fieldList .= ',';
        }

        if (isset($field['dbAlias'])) {
          $fieldList .= $field['dbAlias'];
        }
        else {
          $fieldList .= $field['name'];
        }
      }
    }

    return $fieldList;
  }

  public static function executeQuery($query) {
    $dao = CRM_Core_DAO::executeQuery($query);
    return $dao->fetchAll();
  }

}
