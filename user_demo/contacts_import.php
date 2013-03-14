<?php
OCP\JSON::checkLoggedIn();

$uid = str_replace(array('/', '\\'), '',  $_SESSION['user_id']);
$file = file_get_contents(\OC_Config::getValue( "datadirectory", \OC::$SERVERROOT."/data" ) . "/" . $uid .'/imports/contacts.vcf'); 

$id = \OCA\Contacts\Addressbook::add(OCP\USER::getUser(), "Demo Data");
\OCA\Contacts\Addressbook::setActive($id, 1);

//analyse the contacts file
$file = str_replace(array("\r","\n\n"), array("\n","\n"), $file);
$lines = explode("\n", $file);

$inelement = false;
$parts = array();
$card = array();

foreach($lines as $line) {
	if(strtoupper(trim($line)) == 'BEGIN:VCARD') {
		$inelement = true;
	} elseif (strtoupper(trim($line)) == 'END:VCARD') {
		$card[] = $line;
		$parts[] = implode("\n", $card);
		$card = array();
		$inelement = false;
	}
	if ($inelement === true && trim($line) != '') {
		$card[] = $line;
	}
}


foreach($parts as $part) {
	$vcard = Sabre\VObject\Reader::read($part);
	OCA\Contacts\VCard::add($id, $vcard);
}