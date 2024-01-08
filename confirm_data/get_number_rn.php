<?php
global $USER;

$rootActivity = $this->GetRootActivity();

$task_ID = $rootActivity->GetVariable("TaskIDforGetRN");

$author_ID = $USER->GetID();

use \Bitrix\Main\Loader;

if (Loader::includeModule("tasks")) {
    $resTasks = CTasks::GetByID($task_ID);
    if ($arTask = $resTasks->GetNext()) {
        $numRN = substr($arTask['TITLE'], 0, 15);
        $numberRN = preg_replace("/[^0-9.,]/", "", $numRN);
    }
    $this->SetVariable('nomer_rn2', $numberRN);
}