<?php 
/**
 * http://php-html.net/tutorials/model-view-controller-in-php/
 * http://en.wikipedia.org/wiki/Model%E2%80%93view%E2%80%93controller
 */
error_reporting(E_ALL);
require_once(dirname(__FILE__).'/../private.php');
include_once('class/Controller.php');
$controller = new Controller($private['mb_host'],$private['mb_user'],$private['mb_password']);
$controller->httpRequest($_GET);
$controller->httpResponse();
?>
