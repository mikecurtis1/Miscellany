<h1>Resource Sharing Karma</h1>
<pre>
<?php 

/**
 * This script ranks the give/take relationships between libraries and their interlibrary loan services
 * The difference between lending and borrowing is calculated, but
 * also a 'factor' between 1 and -1 is calculated for each library relationship relative to total 
 * lending/borrowing transactions. 1 means your library is a 'net lender' and give a lot to another library, 
 * while a -1 mean your library is 'net borrow' and takes a lot from another library
 */

//TODO: replace flatfile input with SQL queries of Atlas-Sys ILLiad
//TODO: I had trouble working with $diff < 0, maybe it could be improved
//NOTE: should I use any objects?
//NOTE: I don't know how this script will scale up with larger data sets

error_reporting(E_ALL);
ini_set('display_errors', '1');

$borrow_from = array();
$lend_to = array();
$symbols = array();
$diffs = array();
$factors = array();
$total_transactions = 0;
$total_lending = 0;
$total_borrowing = 0;

foreach ( file('2013_borrow_from') as $row ) {
	$temp = explode("\t",$row);
	$symbol = strtoupper(trim($temp[0]));
	$name = trim($temp[1]);
	$count = (int) trim($temp[3]);
	if ( $symbol !== '' && $count !== '' ) {
		$borrow_from[$symbol] = $count;
		$symbols[$symbol] = $name;
	}
}

foreach ( file('2013_lend_to') as $row ) {
	$temp = explode("\t",$row);
	$symbol = strtoupper(trim($temp[0]));
	$name = trim($temp[1]);
	$count = (int) trim($temp[2]);
	if ( $symbol !== '' && $count !== '' ) {
		$lend_to[$symbol] = $count;
		$symbols[$symbol] = $name;
	}
}

foreach ( $symbols as $symbol => $name ) {
	if ( !isset($borrow_from[$symbol]) ) {
		$borrow_from[$symbol] = 0;
	}
	if ( !isset($lend_to[$symbol]) ) {
		$lend_to[$symbol] = 0;
	}
}

$total_lending = array_sum($lend_to);
$total_borrowing = array_sum($borrow_from);
$total_transactions = $total_lending + $total_borrowing;
$lending_precision = strlen($total_lending);
$borrowing_precision = strlen($total_borrowing);

echo 'LENDING = '.$total_lending."\n";
echo 'BORROWING = '.$total_borrowing."\n";
echo 'TOTAL = '.$total_transactions."\n";

foreach ( $symbols as $symbol => $name ) {
	$b = $borrow_from[$symbol];
	$l = $lend_to[$symbol];
	$diff = $l - $b;
	$diffs[$symbol] = $diff;
	if ( $diff < 0 ) { // other library is net lender
		$diff2 = $diff*-1; // multiply by negative one to 'reverse' +/- value
		#$factors[$symbol] = round($diff2/$total_transactions,10)*-1;
		#$factors[$symbol] = round($diff2/$total_lending,10)*-1;
		$factors[$symbol] = round($diff2/$total_borrowing,$borrowing_precision)*-1;
	} else {  // other library is net borrower
		#$factors[$symbol] = round($diff/$total_transactions,10);
		#$factors[$symbol] = round($diff/$total_borrowing,10);
		$factors[$symbol] = round($diff/$total_lending,$lending_precision);
	}
}

arsort($diffs);
arsort($factors);

$c1 = 0;
echo "\n\nDIFFS\n";
foreach ( $diffs as $symbol => $diff ) {
	echo $c1.' ('.$diff.')'.$symbols[$symbol].' ('.$symbol.')'.'. BORROW FROM='.$borrow_from[$symbol].', LEND TO='.$lend_to[$symbol]."\n";
	$c1++;
}

$c2 = 0;
echo "\n\nFACTORS\n";
foreach ( $factors as $symbol => $factor ) {
	echo $c2.' ('.$factor.')'.$symbols[$symbol].' ('.$symbol.')'.'. BORROW FROM='.$borrow_from[$symbol].', LEND TO='.$lend_to[$symbol]."\n";
	$c2++;
}

?>
</pre>
