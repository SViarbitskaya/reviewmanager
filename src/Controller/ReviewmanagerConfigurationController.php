<?php

declare(strict_types=1);

namespace Reviewmanager\Controller;

use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Reviewmanager;

class ReviewmanagerConfigurationController extends FrameworkBundleAdminController
{
    public function __construct()
    {
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

            /** @var UploadedFile $csvFile */
            $uploadedFile = $form->get('csv_file')->getData();

            // Check if a file was uploaded
            if ($uploadedFile) {
                try {
                    $fileSystem = new Filesystem();

                    $targetPath = Reviewmanager::CSV_FILEPATH;
                    
                    // Ensure the directory exists
                    $targetDir = dirname($targetPath); // Get directory from the path
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0777, true);
                    }

                    // Move the uploaded file to the target location
                    $uploadedFile->move($targetDir, basename($targetPath));

                    $this->addFlash('success', $this->trans('File uploaded successfully.', 'Admin.Notifications.Success'));

                } catch (IOExceptionInterface $exception) {
                    // $this->addFlash('error', $this->trans('An error occurred while uploading the file.', 'Admin.Notifications.Error'));
                    $errors[] = $this->trans('An error occurred while uploading the file.', 'Admin.Notifications.Error');
                }
            }
                
            if (empty($errors)) {
                $this->addFlash('success', $this->trans('Successful update.', 'Admin.Notifications.Success'));

                return $this->redirectToRoute('reviewmanager_configuration_form');
            }

            $this->flashErrors($errors);
        }

        // Load the SVG file from the module's data directory
        $svgPath = Reviewmanager::SVG_FILEPATH;
        $svgContent = file_get_contents($svgPath);

        return $this->render('@Modules/reviewmanager/views/templates/admin/configuration.html.twig', [
            'reviewmanagerConfigurationForm' => $form->createView(),
            'svgRating' => $svgContent,
        ]);
    }
}
