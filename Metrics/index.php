<?php

require_once(dirname(__FILE__).'/check.php');

require_once(dirname(__FILE__).'/core/config/ProjectConfiguration.class.php');

$configuration = ProjectConfiguration::getApplicationConfiguration('qdPM', 'prod', true);
//echo $configuration;
//if ($configureation == "") {
//	$configuration = "projects";
//}
sfContext::createInstance($configuration)->dispatch();

