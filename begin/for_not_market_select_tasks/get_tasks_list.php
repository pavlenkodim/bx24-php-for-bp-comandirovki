<?php
Global $USER;

$rootActivity = $this->GetRootActivity();
//Получить переменную с ID групп задач
$groupID = $rootActivity->GetVariable("TasksGroupID");

// Считываем автора
$author_ID = $USER->GetID();

// Проделываем операции с переменными
if (\Bitrix\Main\Loader::includeModule("tasks"))
{
    $result = CTasks::GetList(
        array("TITLE" => "ASC"),
        array("RESPONSIBLE_ID" => "$author_ID",
            'REAL_STATUS' => array(CTasks::STATE_NEW, CTasks::STATE_PENDING, CTasks::STATE_IN_PROGRESS),
//          фильтр для групп
            'GROUP_ID' => $groupID),
    );

    while ($arTask = $result->GetNext())
    {
        $arTaskUrl[] = "\n".$arTask['ID']." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$arTask["ID"].'/]'.$arTask["TITLE"].'[/url]';
        $arTaskId[] = $arTask['ID'];
    }

    // Передаем значения переменных в переменные БП
    $this->SetVariable('TasksNumbers', $arTaskId);
    $this->SetVariable('ListTasks', $arTaskUrl);
}