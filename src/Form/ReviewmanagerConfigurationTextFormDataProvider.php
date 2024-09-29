<?php

declare(strict_types=1);

namespace Reviewmanager\Form;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Form\FormDataProviderInterface;

/**
 * Provider is responsible for providing form data, in this case, it is returned from the configuration component.
 *
 * Class ReviewmanagerConfigurationTextFormDataProvider
 */
class ReviewmanagerConfigurationTextFormDataProvider implements FormDataProviderInterface
{
    /**
     * @var DataConfigurationInterface
     */
    private $reviewmanagerConfigurationTextDataConfiguration;

    public function __construct(DataConfigurationInterface $reviewmanagerConfigurationTextDataConfiguration)
    {
        $this->reviewmanagerConfigurationTextDataConfiguration = $reviewmanagerConfigurationTextDataConfiguration;
    }

    public function getData(): array
    {
        return $this->reviewmanagerConfigurationTextDataConfiguration->getConfiguration();
    }

    public function setData(array $data): array
    {
        return $this->reviewmanagerConfigurationTextDataConfiguration->updateConfiguration($data);
    }
}
