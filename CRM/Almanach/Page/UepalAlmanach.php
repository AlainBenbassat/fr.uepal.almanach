<?php
use CRM_Almanach_ExtensionUtil as E;

class CRM_Almanach_Page_UepalAlmanach extends CRM_Core_Page {
  public function run() {
    $sections = [];

    CRM_Utils_System::setTitle("Union des Églises protestantes d'Alsace et de Lorraine");

    $s = new CRM_Almanach_SectionPasteursAutresMinistres();
    $sections[] = $s->data;

    $s = new CRM_Almanach_SectionPredicateursLaiques();
    $sections[] = $s->data;

    $s = new CRM_Almanach_SectionPasteursEnRetraite();
    $sections[] = $s->data;

    $s = new CRM_Almanach_SectionVeufsDePasteurs();
    $sections[] = $s->data;

    $s = new CRM_Almanach_SectionAssociationEtOeuvres();
    $sections[] = $s->data;

    $this->assign('sections', $sections);
    parent::run();
  }

  public function getTemplateFileName() {
    $layout = CRM_Utils_Request::retrieve('layout', 'String');
    if ($layout == 'block') {
      $templateFile = "CRM/Almanach/Page/UepalAlmanachBlock.tpl";
      return $templateFile;
    }
    else {
      return parent::getTemplateFileName();
    }
  }

}
