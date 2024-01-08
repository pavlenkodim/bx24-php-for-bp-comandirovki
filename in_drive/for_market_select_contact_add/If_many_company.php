<?php
$rootActivity = $this->GetRootActivity();
$contactID = $rootActivity->GetVariable("Contact_ID");

use \Bitrix\Main\Loader;
use \Bitrix\Crm\ContactTable;
use \Bitrix\Crm\CompanyTable;
use Bitrix\Crm\Binding\ContactCompanyTable;

if (Loader::includeModule('crm')) {
    $contact = ContactTable::getById($contactID)->fetch();
    $contactName = $contact['FULL_NAME'];
    $contactUrl = '[url=https://'.$_SERVER['SERVER_NAME'].'/crm/contact/details/'.$contactID.'/]'.$contactName.'[/url]';

    $arCompanyID = ContactCompanyTable::getList([
        'filter' => ['CONTACT_ID' => $contactID],
        'select' => ['COMPANY_ID'],
    ])->fetchAll();

    $companyName = [];
    $companyUrl = [];
    $companyListByContact = array();

    foreach ($arCompanyID as $id) {
        $company = CompanyTable::getById($id['COMPANY_ID'])->fetch();
        $companyName = $company['TITLE'];
        $companyUrl = '[url=https://'.$_SERVER['SERVER_NAME'].'/crm/company/details/'.$company['ID'].'/]'.'*'.'[/url]';
        $companyIDs[] = $id['COMPANY_ID'];

        $companyListByContact[] = $companyName." ".$companyUrl;
    }
} else {
    echo "Модуль не подключен";
}

$this->SetVariable('CompanyNameIfMany', $companyListByContact);
$this->SetVariable('ContactNameIfMany', $contactUrl);
$this->SetVariable('CompanyListByContact', $companyIDs);