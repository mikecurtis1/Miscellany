<?php 
/** 
 * A view requests from the model the information that it needs to generate an output representation.
 */
error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../private.php');
require_once('state.php');
require_once('items.php');
$state = new State();
$items = new Items($private['mb_host'],$private['mb_user'],$private['mb_password']);
$state->setState($_GET);
$items->setItems($state);
include_once('template.html');
?>
