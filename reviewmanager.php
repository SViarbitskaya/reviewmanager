<?php

declare(strict_types=1);

use Reviewmanager\Service\ReviewUpdateService;

class Reviewmanager extends Module
{ 
    public const REVIEWMANAGEMENT_SOURCE_TYPE = 'REVIEWMANAGEMENT_SOURCE_TYPE';
    public const SVG_FILEPATH =  _PS_MODULE_DIR_ . 'reviewmanager/data/svg/';
    public const CSV_FILEPATH =  _PS_MODULE_DIR_ . 'reviewmanager/data/csv/';
    public const SOURCE_SQL = 'sql';
    public const SOURCE_CSV = 'csv';

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

        $this->ps_versions_compliancy = ['min' => '1.7.0', 'max' => '1.7.99'];
    }

    public function getContent()
    {
        $route = $this->get('router')->generate('reviewmanager_configuration_form_simple');
        Tools::redirectAdmin($route);
    }

    // This is the function caled by cron_job.php
    public function updateReviewData()
    {
        try {
            $this->updateSvgWithReviewData();
        } catch (Exception $e) {
            // If an error occurs, revert to backup
            copy($backupFilePath, $svgFilePath);
            // Log the error
            PrestaShopLogger::addLog('Error updating SVG: ' . $e->getMessage(), 3);
        }
    }

    public function updateSvgWithReviewData()
    {
        $reviewData = $this->getReviewData();

        $svgFilePath = self::SVG_FILEPATH . '/avis.svg';
        $backupFilePath = self::SVG_FILEPATH . '/avis_backup.svg';

        // Backup existing SVG
        copy($svgFilePath, $backupFilePath);

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

        // Update the <text> element with id="review-badge-avis-total"
        $avis_total = $xpath->query('//*[@id="review-badge-note"]')->item(0);
        if ($avis_total) {
            $avis_total->nodeValue =  $reviewData['average'] . " / 5";
        }

        // Find and update the JSON-LD block
        $script = $xpath->query('//script[@type="application/ld+json"]')->item(0);
        dump($xpath->query('//script[@type="application/ld+json"]'));
        if ($script) {
            $json_ld = json_decode($script->nodeValue, true);
            
            if ($json_ld) {
                // Update the review count and average values in JSON-LD
                $json_ld['aggregateRating']['reviewCount'] =  $reviewData['review_count'];
                $json_ld['aggregateRating']['ratingValue'] =  $reviewData['average'];
                
                // Update the script content with the new JSON-LD
                $script->nodeValue = json_encode($json_ld, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }
        }

        // Save the updated SVG back to a file
        $svg->save($svgFilePath);

        echo "SVG updated successfully!";

        die;

        $jsonLd = generateJsonLd($reviewData);

        // Replace placeholders for review count and average rating
        $updatedSvg = str_replace('PLACEHOLDER_REVIEW_COUNT', $reviewData['review_count'], $svgContent);
        $updatedSvg = str_replace('PLACEHOLDER_AVERAGE_RATING', $reviewData['average'], $updatedSvg);

        // Save the updated SVG
        file_put_contents($svgFilePath, $updatedSvg);
    }

    public function getReviewData()
    {
        $source = Configuration::get(static::REVIEWMANAGEMENT_SOURCE_TYPE);
        if ($source === 'sql') {
            return $this->getReviewDataFromSql();
        } elseif ($source === 'csv') {
            return $this->getReviewDataFromCsv(self::CSV_FILEPATH . '/avis.csv');
        }
    }

    public function getReviewDataFromSql()
    {
        // Fake SQL for testing
        return [
            'average' => 4.5,
            'review_count' => 123,
        ];
    }
    public function getReviewDataFromCsv($filePath)
    {
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
        }
        
        return false; // Return false if the file could not be opened
    }



    public function generateJsonLd($reviewData)
    {
        $jsonLd = [
            "@context" => "http://schema.org",
            "@type" => "AggregateRating",
            "ratingValue" => $reviewData['average'],
            "reviewCount" => $reviewData['review_count']
        ];
        return json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
}
