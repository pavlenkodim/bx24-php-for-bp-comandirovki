<?php
global $USER;

// получить текущий бизнес-процесс
$rootActivity = $this->GetRootActivity();
// получить значение переменной бизнес-процесса
$TasksId = $rootActivity->GetVariable("TasksNumbers");

$author_ID = $USER->GetID();

use \Bitrix\Main\Loader;
use \Bitrix\Crm\CompanyTable;

if (\Bitrix\Main\Loader::includeModule("tasks")) {
    foreach ($TasksId as $id){
        $resTasks = CTasks::GetByID($id);
        if ($arTask = $resTasks->GetNext()) {
            $arTaskUrl[] = "\n".$id." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$id.'/]'.$arTask["TITLE"].'[/url]';
            if ($arTask['UF_CRM_TASK']) {
                foreach ($arTask['UF_CRM_TASK'] as $idEl) {
                    if (str_starts_with($idEl, 'CO')){
                        $arCompanyInTasksId[] = $idEl;
                        $idComp = ltrim($idEl, "CO_");
                        if (Loader::includeModule('crm')){
                            $company = CompanyTable::getById($idComp,['select' => array("ID", "TITLE")])->fetch();
                            $taskURL = "\n".$id." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$id.'/]'.$arTask["TITLE"].'[/url]';
                            $companyTitle = $company["TITLE"];
                            $finalListTaskAndCompany[] = $taskURL." | ".$companyTitle;
                        } else {
                            $companyTitle = "Компания не найдена";
                        }
                    }
                }
            } else {
                $taskURL = "\n".$id." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$id.'/]'.$arTask["TITLE"].'[/url]';
                $finalListTaskAndCompany[] = $taskURL." | "."Компания не найдена";
            }
        }
    }

    $this->SetVariable('ListTasks', $arTaskUrl);
    $this->SetVariable('CompanyByTasks', $arCompanyInTasksId);
    $this->SetVariable('FinalList', $finalListTaskAndCompany);
}