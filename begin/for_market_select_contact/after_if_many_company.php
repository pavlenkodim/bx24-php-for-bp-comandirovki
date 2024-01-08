<?php
$rootActivity = $this->GetRootActivity();
$contactID = $rootActivity->GetVariable("Contact_ID");
$companyID = $rootActivity->GetVariable("CompanyListByContact");

use \Bitrix\Main\Loader;
use \Bitrix\Crm\ContactTable;
use \Bitrix\Crm\CompanyTable;

if (Loader::includeModule('crm')) {
    $contact = ContactTable::getById($contactID)->fetch();
    $contactName = $contact['FULL_NAME'];
    $contactUrl = '[url=https://'.$_SERVER['SERVER_NAME'].'/crm/contact/details/'.$contactID.'/]'.$contactName.'[/url]';

    foreach ($companyID as $id) {
        if ($id) {
            $company = CompanyTable::getById($id)->fetch();
            $companyName = $company['TITLE'];
            $companyUrl = '[url=https://'.$_SERVER['SERVER_NAME'].'/crm/company/details/'.$id.'/]'.'*'.'[/url]';
        } else {
            $companyName = "Компания не найдена c ID ".$id;
            $companyUrl = '*';
        }
    }

    $finalListCC = "\n".$contactUrl.": ".$companyName." ".$companyUrl;
} else {
    echo "Модуль не подключен";
}

$this->SetVariable('FinalListConCom', $finalListCC);