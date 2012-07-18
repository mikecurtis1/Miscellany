<?php 
/** 
 * A view requests from the model the information that it needs to generate an output representation.
 */
error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../ConfigPrivate.php');
require_once('state.php');
require_once('items.php');
$cfg = new ConfigPrivate();
$state = new State();
$items = new Items($cfg);
$state->setState($_GET);
$items->setItems($state);
include_once('template.html');
?>
