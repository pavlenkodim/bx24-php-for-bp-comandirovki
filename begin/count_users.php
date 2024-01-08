<?php
$documentService = $this->workflow->GetService("DocumentService");
$document = $documentService->getDocument($this->getDocumentId());
$fieldValue = $document['PROPERTY_SOTRUDNIKI_NAPRAVLENNYE_V_KOMANDIROVKU'];

$var1 = count($fieldValue);

$this->SetVariable('HowManyPeopleOnTrip', intval($var1));
