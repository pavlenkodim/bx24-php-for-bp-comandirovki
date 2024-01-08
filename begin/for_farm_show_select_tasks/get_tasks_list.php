<?php
global $USER;

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
        $task = CTaskItem::getInstance($arTask["ID"], $author_ID);
        $tag = $task->getTags();

        if (in_array('Выставка', $tag) || in_array('выставка', $tag))
        {
            $arTaskUrl = "\n".$arTask['ID'].": ".'[url=https://'.$_SERVER['SERVER_NAME'].'/company/personal/user/'.$author_ID.'/tasks/task/view/'.$arTask["ID"].'/]'.$arTask["TITLE"].'[/url]';
            $arTaskId[] = $arTask["ID"];
        }
    }

    if (!$arTaskUrl)
        {
            $arTaskUrl = "Задачи с тегом [B]Выставка[/B] - не найдены, вставьте в поле номер задачи.";
        }

    $this->SetVariable('TasksNumbers', $arTaskId);
    $this->SetVariable('ListTasks', $arTaskUrl);
}