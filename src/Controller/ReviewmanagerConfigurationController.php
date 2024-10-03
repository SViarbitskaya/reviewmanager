<?php

declare(strict_types=1);

namespace Reviewmanager\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReviewmanagerConfigurationController extends FrameworkBundleAdminController
{
    public function __construct()
    {
        $this->module = \Module::getInstanceByName('reviewmanager');
        parent::__construct();
    }

    public function index(Request $request): Response
    {
        $formDataHandler = $this->get('reviewmanager.form.reviewmanager_configuration_form_data_handler');

        $form = $formDataHandler->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** You can return array of errors in form handler and they can be displayed to user with flashErrors */
            $errors = $formDataHandler->save($form->getData());

            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('reviewmanager_configuration_form_simple');
            }

            $this->flashErrors($errors);
        }

        // Load the SVG file from the module's data directory
        $svgPath = $this->module::SVG_FILEPATH. '/avis.svg';
        $svgContent = file_get_contents($svgPath);

        return $this->render('@Modules/reviewmanager/views/templates/admin/configuration.html.twig', [
            'reviewmanagerConfigurationForm' => $form->createView(),
            'svgRating' => $svgContent,
        ]);
    }
}
