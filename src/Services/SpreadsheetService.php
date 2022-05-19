<?php


namespace App\Services;


use League\Csv\Writer;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;

/**
 * This Service is responsible for creating Excel files and Csv files
 */
class SpreadsheetService
{

    /**
     * @var Spreadsheet
     */
    private $spreadsheet;

    /**
     * @var Worksheet
     */
    private $activeSheet;

    /**
     * SpreadsheetService constructor.
     */
    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->activeSheet = $this->spreadsheet->getActiveSheet();
    }

    /**
     * @return Worksheet
     */
    public function getActiveSheet(): Worksheet
    {
        return $this->activeSheet;
    }

    /**
     * Creates a csv file using league/csv instead of php spreadsheet to improve performance.
     * @param $headers
     * @param $resultsData
     * @param $temporaryFolderPath
     * @param $filename
     * @return BinaryFileResponse
     * @throws \League\Csv\CannotInsertRecord
     */
    public function toCsv($headers, $resultsData, $temporaryFolderPath, $filename)
    {
        $writer = Writer::createFromPath($temporaryFolderPath.$filename, 'w+');
        $writer->insertOne($headers);
        $writer->insertAll($resultsData);
        $writer = null;

        // This should return the file to the browser as response
        $response = new BinaryFileResponse($temporaryFolderPath.$filename);

        // To generate a file download, you need the mimetype of the file
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        // Set the mimetype with the guesser or manually
        if($mimeTypeGuesser->isGuesserSupported()){
            // Guess the mimetype of the file according to the extension of the file
            $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($temporaryFolderPath.$filename));
        }else{
            // Set the mimetype of the file manually, in this case for a text file is text/plain
            $response->headers->set('Content-Type', 'text/plain');
        }

        // Set content disposition inline of the file
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $filename
        );

        // remove tmp file after the response has been sent
        $response->deleteFileAfterSend(true);

        // return response and delete tmp file
        return $response;
    }




    /**
     * Stream the file as Response.
     *
     * @param $writer
     * @param int $status
     * @param array $headers
     *
     * @return StreamedResponse
     */
    public function createStreamedResponse($writer, $status = 200, array $headers = array())
    {
        return new StreamedResponse(
            function () use ($writer) {
                $writer->save('php://output');
            },
            $status,
            $headers
        );
    }

}
