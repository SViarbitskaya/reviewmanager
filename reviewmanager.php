<?php

declare(strict_types=1);

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

class Reviewmanager extends Module
{
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
}
