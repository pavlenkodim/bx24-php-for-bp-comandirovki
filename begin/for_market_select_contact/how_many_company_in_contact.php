<?php
$rootActivity = $this->GetRootActivity();
$contactID = $rootActivity->GetVariable("Contact_ID");

use \Bitrix\Main\Loader;
use Bitrix\Crm\Binding\ContactCompanyTable;

if (Loader::includeModule('crm')) {
    $arCompanyID = ContactCompanyTable::getList([
        'filter' => ['CONTACT_ID' => $contactID],
        'select' => ['COMPANY_ID'],
    ])->fetchAll();

    $companyMany = '';

    if (count($arCompanyID) > 1) {
        $companyMany = true;
    } else {
        $companyMany = false;
    }
} else {
    echo 'Модуль не подключен';
}

$this->SetVariable('ManyCompanyInContact', $companyMany);