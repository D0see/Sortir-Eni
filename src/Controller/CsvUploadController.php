<?php

namespace App\Controller;



use App\Services\CsvImporter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class CsvUploadController extends AbstractController
{
    #[Route('/upload-csv', name: 'upload_csv')]
    public function upload(Request $request, CsvImporter $csvImporter): Response
    {
        // Create a simple form for file upload
        $form = $this->createFormBuilder()
            ->add('csv_file', FileType::class, ['label' => 'Upload CSV File'])
            ->add('submit', SubmitType::class, ['label' => 'Upload & Process'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $csvFile */
            $csvFile = $form->get('csv_file')->getData();

            if ($csvFile) {
                $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads';
                $newFilename = uniqid() . '.' . $csvFile->guessExtension();

                try {
                    $csvFile->move($uploadsDirectory, $newFilename);
                } catch (FileException $e) {
                    return new Response('Error uploading file: ' . $e->getMessage());
                }

                // Process CSV
                $filePath = $uploadsDirectory . '/' . $newFilename;
                $data = $csvImporter->import($filePath);

                return new Response('CSV uploaded and processed! Found ' . count($data) . ' records.');
            }
        }
        $this->addFlash("success","Fichier importé avec succès !");
        return $this->render('csv/csvUpload.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
