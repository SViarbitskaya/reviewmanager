<?php

// Include PrestaShop configuration
include(dirname(__FILE__).'/../../config/config.inc.php');

// Get the module instance
$module = Module::getInstanceByName('reviewmanager');

if ($module) {
    $module->updateReviewData(); 
}

// Log success or failure
file_put_contents(dirname(__FILE__) . '/cron_job.log', date('Y-m-d H:i:s') . " - Cron job executed\n", FILE_APPEND);

