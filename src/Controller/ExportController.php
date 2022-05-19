<?php

namespace App\Controller;


use App\Repository\EmployeeDataRepository;
use App\Services\ExportService;
use App\Services\SpreadsheetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

class ExportController extends AbstractController
{
    /**
     * @Route("/export", name="app_export")
     */
    public function index(KernelInterface $kernel, SpreadsheetService $spreadsheetService, ExportService $exportService,
                          EmployeeDataRepository $employeeDataRepository): Response
    {

        $employeeDataRepository = $employeeDataRepository->findAll();
        $temporaryFolderPath = $kernel->getProjectDir() . '/tmp/';
        $fileName = uniqid('', true) . '.csv';
        [$headers, $resultsData] = $exportService->getExportData($employeeDataRepository);

        return $spreadsheetService->toCsv($headers, $resultsData, $temporaryFolderPath, $fileName);

    }
}
