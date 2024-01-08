<?php
global $USER;

$rootActivity = $this->GetRootActivity();
$TasksId = $rootActivity->GetVariable("TasksNumbers");

$author_ID = $USER->GetID();
$arTaskUrl = array();

if (\Bitrix\Main\Loader::includeModule("tasks"))
{
    $result = CTasks::GetList(
        array("TITLE" => "ASC"),
        array(
            "RESPONSIBLE_ID" => "$author_ID",
            'REAL_STATUS' => array(CTasks::STATE_NEW, CTasks::STATE_PENDING, CTasks::STATE_IN_PROGRESS),
        ),
    );

    while ($arTask = $result->GetNext())
    {
        foreach ($arTask['ID'] as $id)
        {
            $task = CTaskItem::getInstance($id, $author_ID);
            $tag = $task->getTags();

            if (in_array('Выставка', $tag) || in_array('выставка', $tag))
            {
                if (!in_array($arTask['ID'], $TasksId)) {
                    $arTaskUrl = "\n".$arTask['ID'].": ".'[URL=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$arTask["ID"].'/]'.$arTask["TITLE"].'[/URL]';
                    $arTaskId[] = $arTask['ID'];
                } else {
                    $arTaskUrl = "\n".$arTask['ID'].": ".'[URL=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$arTask["ID"].'/]'.$arTask["TITLE"].'[/URL]'." [I](Добавлены ранее)[/I]";
                }
            }
        }
    }

    if (!$arTaskUrl)
    {
        $arTaskUrl = "Дополнительные задачи с тегом [B]Выставка[/B] - не найдены, вставьте в поле номер задачи.";
    }

    $this->SetVariable('TasksNumbers', $arTaskId);
    $this->SetVariable('ListTasks', $arTaskUrl);
}