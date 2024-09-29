<?php

declare(strict_types=1);

namespace Reviewmanager\Form;

use PrestaShopBundle\Form\Admin\Type\TranslatorAwareType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

class ReviewmanagerConfigurationFormType extends TranslatorAwareType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('source', ChoiceType::class, [
                'label' => $this->trans('Select data source', 'Modules.Reviewmanager.Admin'),
                'help' => $this->trans('Choose between SQL or CSV as the data source for reviews.', 'Modules.Reviewmanager.Admin'),
                'choices' => [
                    $this->trans('SQL', 'Modules.Reviewmanager.Admin') => 'sql',
                    $this->trans('CSV', 'Modules.Reviewmanager.Admin') => 'csv',
                ],
                'expanded' => true,  // Renders as radio buttons
                'multiple' => false, // Ensures only one option can be selected
                'required' => true
            ]);
    }
}
