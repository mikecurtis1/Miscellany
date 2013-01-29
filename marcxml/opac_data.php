<?php 

/**
  * http://www.loc.gov/z3950/agency/asn1.html, see section 'RecordSyntax-opac'
  */

$opac_data = array();
$holdings = $x->getXpathObjects('//holdings');
foreach ( $holdings as $cur ) {
	$temp['localLocation'] = '';
	$temp['shelvingLocation'] = '';
	$temp['callNumber'] = '';
	$temp['copyNumber'] = '';
	$temp['publicNote'] = '';
	$temp['availableNow'] = '';
	$temp['availabiltyDate'] = '';
	$temp['availableThru'] = '';
	$temp['itemId'] = '';
	$temp['renewable'] = '';
	$temp['onHold'] = '';
	if ( isset($cur->holding) ) {
		$temp['localLocation'] = (string) $cur->holding->localLocation;
	}
	if ( isset($cur->holding) ) {
		$temp['shelvingLocation'] = (string) $cur->holding->shelvingLocation;
	}
	if ( isset($cur->holding) ) {
		$temp['callNumber'] = (string) $cur->holding->callNumber;
	}
	if ( isset($cur->holding) ) {
		$temp['copyNumber'] = (string) $cur->holding->copyNumber;
	}
	if ( isset($cur->holding) ) {
		$temp['publicNote'] = (string) $cur->holding->publicNote;
	}
	if ( isset($cur->holding->circulations) ) {
		$temp['availableNow'] = (string) $cur->holding->circulations->circulation->availableNow->attributes()->value;
	}
	if ( isset($cur->holding->circulations) ) {
		$temp['availabiltyDate'] = (string) $cur->holding->circulations->circulation->availabiltyDate;
	}
	if ( isset($cur->holding->circulations) ) {
		$temp['availableThru'] = (string) $cur->holding->circulations->circulation->availableThru;
	}
	if ( isset($cur->holding->circulations) ) {
		$temp['itemId'] = (string) $cur->holding->circulations->circulation->itemId;
	}
	if ( isset($cur->holding->circulations) ) {
		$temp['renewable'] = (string) $cur->holding->circulations->circulation->renewable->attributes()->value;
	}
	if ( isset($cur->holding->circulations) ) {
		$temp['onHold'] = (string) $cur->holding->circulations->circulation->onHold->attributes()->value;
	}
	$opac_data[] = $temp;
}
print_r($opac_data);
?>
