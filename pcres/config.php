<?php 

// config object
require_once('PcResConfig.php');
$cfg = new PcResConfig($_GET);

// PHP
error_reporting(E_ALL);
include_once('helpers.php');

// initialize an errors array FIXME: this probably should be a function in helpers
$config_errors = array();

// display elements
$config_template = 'template_main.php';

// define functions
$config_operation_template = 'template_request.php';
if($cfg->operation == "Errors"){$config_operation_template = 'template_errors.php';}
if($cfg->operation == "Request"){$config_operation_template = 'template_request.php';}
if($cfg->operation == "Schedule"){$config_operation_template = 'template_schedule.php';}
if($cfg->operation == "Confirmation"){$config_operation_template = 'template_confirmation.php';}

?>
