<?php
$rootActivity = $this->GetRootActivity();
$contactID = $rootActivity->GetVariable("Contact_ID");

use \Bitrix\Main\Loader;
use \Bitrix\Crm\ContactTable;
use \Bitrix\Crm\CompanyTable;

$finalListCC = array();
$companyNotFound = '';

if (Loader::includeModule('crm')) {
    $contact = ContactTable::getById($contactID)->fetch();
    $contactName = $contact['FULL_NAME'];
    $companyID = $contact['COMPANY_ID'];
    $contactUrl = '[url=https://'.$_SERVER['SERVER_NAME'].'/crm/contact/details/'.$contactID.'/]'.$contactName.'[/url]';

    if ($companyID) {
        $company = CompanyTable::getById($companyID)->fetch();
        $companyName = $company['TITLE'];
        $companyUrl = '[url=https://'.$_SERVER['SERVER_NAME'].'/crm/company/details/'.$companyID.'/]'.'*'.'[/url]';
    } else {
        $companyNotFound = true;
        $companyName = "Компания не найдена";
        $companyUrl = '*';
    }

    $finalListCC = "\n".$contactUrl.": ".$companyName." ".$companyUrl;
} else {
    echo "Модуль не подключен";
}

$this->SetVariable('FinalListConCom', $finalListCC);
$this->SetVariable('Company_ID', $companyID);
$this->SetVariable('CompanyNotFound', $companyNotFound);