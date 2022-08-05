<?php

class CRM_Almanach_Query {
  public $title = 'TITRE';
  public $fields = [];
  public $records = [];
  public $query = '';

  public function execute() {
    if ($this->query) {
      $this->executeQuery();
    }
  }

  public function getFieldListAsString() {
    $i = 0;
    $fieldList = '';

    foreach ($this->fields as $field) {
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

  public function getGroupByFieldsAsString($excludeCols) {
    $fieldList = '';

    foreach ($this->fields as $field) {
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

  private function executeQuery() {
    $dao = CRM_Core_DAO::executeQuery($this->query);
    $this->records = $dao->fetchAll();
  }
}
