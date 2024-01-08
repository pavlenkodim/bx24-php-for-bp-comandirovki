<?php
global $USER;

$authorID = $USER->GetID();

$rsUser = Cuser::GetByID($authorID);
$arUser = $rsUser->Fetch();

$orgUser = $arUser['UF_USR_1695726543854'];

$this->SetVariable('IDorg', $orgUser);