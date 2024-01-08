<?php
Global $USER;

// получить текущий бизнес-процесс
$rootActivity = $this->GetRootActivity();
// получить значение переменной бизнес-процесса
$TasksId = $rootActivity->GetVariable("TasksNumbers");
//Полчить переменную с ID груп задач
$groupID = $rootActivity->GetVariable("TasksGroupID");

if (!$groupID){
    $groupID = [];
}

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
        if (!in_array($arTask['ID'], $TasksId)) {
            $arTaskUrl[] = "\n".$arTask['ID']." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$arTask["ID"].'/]'.$arTask["TITLE"].'[/url]';
            $arTaskId[] = $arTask['ID'];
        } else {
            $arTaskUrl[] = "\n".$arTask['ID']." | ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$arTask["ID"].'/]'.$arTask["TITLE"].'[/url]'." [I](Добавлены ранее)[/I]";
        }
    }

    // Передаем значения переменных в переменные БП
    $this->SetVariable('TasksNumbersAdd', $arTaskId);
    $this->SetVariable('ListTasks', $arTaskUrl);
}