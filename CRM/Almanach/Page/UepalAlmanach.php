<?php
use CRM_Almanach_ExtensionUtil as E;

class CRM_Almanach_Page_UepalAlmanach extends CRM_Core_Page {
  public function run() {
    CRM_Utils_System::setTitle(E::ts("Union des Églises protestantes d'Alsace et de Lorraine"));

    // assign template variables
    $this->assign('epcaalTable', $this->getUpcaalTable());

    parent::run();
  }

  private function getUpcaalTable() {
    $t = [
      ['aa', 'aaa', 'aaaa'],
      ['bb', 'bbb', 'bbbb'],
      ['cc', 'ccc', 'cccc'],
    ];

    return $t;
  }
}
