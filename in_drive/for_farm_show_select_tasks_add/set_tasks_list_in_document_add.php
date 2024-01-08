<?php
global $USER;

$rootActivity = $this->GetRootActivity();
$TasksId = $rootActivity->GetVariable("TasksNumbers");

$author_ID = $USER->GetID();

if (\Bitrix\Main\Loader::includeModule("tasks")) {
    foreach ($TasksId as $id){
        $resTasks = CTasks::GetByID($id);
        if ($arTask = $resTasks->GetNext()) {
            $arTaskUrl[] = "\n".$id.": ".'[URL=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$id.'/]'.$arTask["TITLE"].'[/URL]';
        }
    }

    $this->SetVariable('ListTasks', $arTaskUrl);
    $this->SetVariable('FinalList', $arTaskUrl);
}