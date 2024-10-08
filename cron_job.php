<?php

// Include PrestaShop configuration
include(dirname(__FILE__).'/../../config/config.inc.php');

// Get the module instance
$module = Module::getInstanceByName('reviewmanager');

// Update the review
$module->updateReviewData();

