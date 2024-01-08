<?php
$rootActivity = $this->GetRootActivity();
$arCompanyIDs= $rootActivity->GetVariable("CompanyListByContact");

$companyMany = '';

if (count($arCompanyIDs) > 1) {
    $companyMany = true;
} else {
    $companyMany = false;
}

$this->SetVariable('ManyCompanyInContact', $companyMany);