<?php
use CRM_Almanach_ExtensionUtil as E;

class CRM_Almanach_Page_UepalAlmanach extends CRM_Core_Page {
  public function run() {
    $queries = [];

    CRM_Utils_System::setTitle("Union des Églises protestantes d'Alsace et de Lorraine");

    $q = new CRM_Almanach_QueryPasteursAutresMinistres();
    $queries[] = $this->toArrayForTemplate($q);

    $this->assign('queries', $queries);
    parent::run();

    parent::run();
  }

  private function toArrayForTemplate($q) {
    $arr = [
      'title' => $q->title,
      'fields' => $q->fields,
      'records' => $q->records,
    ];

    return $arr;
  }
}
