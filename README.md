# Review manager module

## About

- **Automated Review Processing**: Triggers via a cron job, allowing for automated updates without manual intervention.
- **Flexible Data Sources**: Supports data retrieval from both SQL queries and CSV files.
- **Admin Configuration Interface**: Provides an admin panel to select the desired data source for review processing.
- **Dynamic SVG Updates**: Automatically updates the SVG file with the latest review data, including the total number of reviews and average ratings.
- **Resilience and Backup**: Implements a safety mechanism that retains the previous version of the SVG file in case of any errors during updates.

## Supported PrestaShop versions

This module has been tested with PrestaShop 1.7.8.10

## Requirements

Composer

## How to install

Download or clone the module into the modules directory of your PrestaShop installation.
Rename the directory to make sure that the module directory is named `reviewmanager`.

`cd` into module's directory and run following commands:

`composer install` - to download dependencies into vendor folder

Install the module from Back Office or via CLI.

## Additional useful information
- The cron job can be executed by running cron_job.php.
- Cron job logs are stored in the /logs folder and are managed using the Symfony Logger component.
- The base SVG model file is located at /data/svg/avis.svg. This file is required for proper functionality.
- A CSV file can be uploaded via the module configuration page and will be saved at /data/csv/avis.csv.
- An example CSV file can be found at /data/csv/avis.csv.