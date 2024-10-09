<?php

declare(strict_types=1);

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class Reviewmanager extends Module
{ 
    public const REVIEWMANAGEMENT_SOURCE_TYPE = 'REVIEWMANAGEMENT_SOURCE_TYPE';
    public const SOURCE_SQL = 'sql';
    public const SOURCE_CSV = 'csv';
    public const SVG_FILEPATH =  _PS_MODULE_DIR_ . 'reviewmanager/data/svg/avis.svg';
    public const SVG_BACKUP_FILEPATH =  _PS_MODULE_DIR_ . 'reviewmanager/data/svg/avis_backup.svg';
    public const CSV_FILEPATH =  _PS_MODULE_DIR_ . 'reviewmanager/data/csv/avis.csv';
    public const CRON_LOG_FILEPATH =  _PS_MODULE_DIR_ . 'reviewmanager/logs/cron.log';

    public function __construct()
    {
        $this->name = 'reviewmanager';
        $this->author = 'Sviatlana Viarbitskaya';
        $this->version = '1.0.0';
        $this->need_instance = 0;

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Review Manager', [], 'Modules.Reviewmanager.Admin');
        $this->description = $this->trans(
            'Automatically updates product review data and ratings into an SVG file.',
            [],
            'Modules.Reviewmanager.Admin'
        );

        $this->ps_versions_compliancy = ['min' => '1.7.3', 'max' => '1.7.99'];
    }

    public function install()
    {
        return parent::install() && $this->registerHook('displayBackOfficeHeader');
    }

    public function uninstall()
    {
        return (
            parent::uninstall() 
            && Configuration::deleteByName(Configuration::get(static::REVIEWMANAGEMENT_SOURCE_TYPE))
        );
    }

        public function hookDisplayBackOfficeHeader()
    {
        $controller = Tools::getValue('controller');

        if ($controller == 'AdminReviewmanager') {
            $this->context->controller->addJS($this->_path . 'views/js/reviewmanager.js');
        }
    }


    public function getContent()
    {
        $route = $this->get('router')->generate('reviewmanager_configuration_form');
        Tools::redirectAdmin($route);
    }

    // This is the function caled by cron_job.php
    public function updateReviewData()
    {
        // Setup the Logger
        $logPath = self::CRON_LOG_FILEPATH;
        $logger = new Logger('cron_logger');
        $logger->pushHandler(new RotatingFileHandler($logPath, 7, Logger::DEBUG)); // 7 = keep logs for 7 days

        try {
            $svgFilePath = self::SVG_FILEPATH;
            $backupFilePath = self::SVG_BACKUP_FILEPATH;

            // Verify that the model (or the more recent) avis.svg exists
            if (!file_exists($svgFilePath)) {
                throw new Exception("File not found: $svgFilePath");
            }

            // Create a backup of the SVG file before updating
            if (!copy($svgFilePath, $backupFilePath)) {
                throw new Exception("Failed to create backup of the SVG file.");
            }

            // Get the actual review data
            $reviewData = $this->getReviewData();

            // Update SVG file
            $this->updateSvgWithReviewData($reviewData, $svgFilePath);

            $logger->info('Cron job executed successfully.');
        } catch (Exception $e) {
            // If an error occurs, revert to backup. When relaunched, cron job will use the backup file as a model svg file.
            copy($backupFilePath, $svgFilePath);
            // Log the error
            $logger->error($e->getMessage());
        }
    }

    public function updateSvgWithReviewData($reviewData, $svgFilePath)
    {
        // Load the SVG file using DOMDocument
        $svg = new DOMDocument();
        $svg->load($svgFilePath );

        // Use XPath to find and update the text elements
        $xpath = new DOMXPath($svg);

        // Update the <text> element with id="review-badge-note-total"
        $note_total = $xpath->query('//*[@id="review-badge-note-total"]')->item(0);
        if ($note_total) {
            $note_total->nodeValue =  $reviewData['review_count'] . " avis";
        }

        // Update the <text> element with id="review-badge-note"
        $avis_total = $xpath->query('//*[@id="review-badge-note"]')->item(0);
        if ($avis_total) {
            $avis_total->nodeValue =  $reviewData['average'] . " / 5";
        }

        // Save the updated SVG back to a file
        $svg->save($svgFilePath);

        return;
    }

    public function getReviewData()
    {
        $source = Configuration::get(self::REVIEWMANAGEMENT_SOURCE_TYPE);
        if ($source === 'sql') {
            return $this->getReviewDataFromSql();
        } elseif ($source === 'csv') {
            return $this->getReviewDataFromCsv(self::CSV_FILEPATH);
        }else{
            throw new Exception("Source type is not defined.");
        }
    }

    public function getReviewDataFromSql()
    {
        // Fake SQL for testing
        $sqlFails = 0;

        if ($sqlFails === 1){
            throw new Exception("SQL request failed with the message [add error message here].");
        }else{
            return [
                'average' => 4.5,
                'review_count' => 123,
            ];
        }

    }

    public function getReviewDataFromCsv($filePath)
    {
        // Verify that the csv file exists
        if (!file_exists($filePath)) {
            throw new Exception("File not found: $filePath");
        }

        if (($handle = fopen($filePath, "r")) !== false) {
            // Read the first line to get the headers
            $headers = fgetcsv($handle, 1000, ",");
            
            // Initialize variables to store review data
            $reviewData = [
                'average' => null,
                'review_count' => null,
            ];
    
            // Read the remaining lines
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                // Assuming 'review_count' is in the first column and 'average_rating' is in the second
                $reviewData['review_count'] = $data[array_search('review_count', $headers)];
                $reviewData['average'] = $data[array_search('average_rating', $headers)];
            }
            
            fclose($handle);
            return $reviewData; // Return the collected review data
        } else{
            throw new Exception("File could not be opened: $filePath");
        }
    }
}
