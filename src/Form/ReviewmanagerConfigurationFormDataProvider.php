<?php

declare(strict_types=1);

namespace Reviewmanager\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Provider is responsible for providing form data, in this case, it is returned from the configuration component.
 *
 * Class ReviewmanagerConfigurationFormDataProvider
 */
class ReviewmanagerConfigurationFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $reviewmanagerConfigurationDataConfiguration;

    public function __construct(DataConfigurationInterface $reviewmanagerConfigurationDataConfiguration)
    {
        $this->reviewmanagerConfigurationDataConfiguration = $reviewmanagerConfigurationDataConfiguration;
    }

    public function getData(): array
    {
        return $this->reviewmanagerConfigurationDataConfiguration->getConfiguration();
    }

    public function setData(array $data): array
    {
        return $this->reviewmanagerConfigurationDataConfiguration->updateConfiguration($data);
    }
}
