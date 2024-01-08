<?php
global $USER;

$rootActivity = $this->GetRootActivity();

$task_ID = $rootActivity->GetVariable("TaskIDForPayControl");

$author_ID = $USER->GetID();

use \Bitrix\Main\Loader;
use \Bitrix\Crm\CompanyTable;

if (Loader::includeModule("tasks")) {
    $resTasks = CTasks::GetByID($task_ID);
    if ($arTask = $resTasks->GetNext()) {
        $numRN = substr($arTask['TITLE'], 0, 15);
        $numberRN = preg_replace("/[^0-9.,]/", "", $numRN);
        if ($arTask['UF_CRM_TASK']) {
            foreach ($arTask['UF_CRM_TASK'] as $idEl) {
                if (str_starts_with($idEl, 'CO')) {
                    $arCompany_ID[] = $idEl;
                    $idComp = ltrim($idEl, "CO_");
                    if (Loader::includeModule('crm')){
                        $company = CompanyTable::getById($idComp,['select' => array("ID", "TITLE")])->fetch();
                        $companyTitle[] = $company["TITLE"];
                    }
                } elseif (str_starts_with($idEl, 'C_')) {
                    $arContact_ID[] = $idEl;
                } else {
                    $arOther_IDel[] = $idEl;
                }
            }
        }
    }

    $this->SetVariable('CompanyIDForPayControl', $arCompany_ID);
    $this->SetVariable('ContactIDForPayControl', $arContact_ID);
    $this->SetVariable('NumberRNForPayControl', $numberRN);
    $this->SetVariable('CompanyTitleForPayControl', $companyTitle);
}