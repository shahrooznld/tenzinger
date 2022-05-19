<?php


namespace App\Services;


use App\DTO\CSVRowDTO;
use App\Entity\EmployeeData;

class ExportService
{

    /**
     * ExportService constructor.
     */
    public function __construct()
    {
    }

    private function createExportHeaders(): array
    {
        return ["employee", "transport", "traveled distance", "compensation", "payment date"];

    }

    /**
     * @param EmployeeData[] $results
     */
    private function createExportBodyFromResults(array $results): array
    {
        $firstMondayOfNextMonth = $this->getFirstMondayOfNextMonth();
        $dateDiffInThisMonth = $this->dateDiffInThisMonth();
        $csvBody = [];
        foreach ($results as $result) {
            $csvBody[] = [
                "name" => $result->getName(),
                "transport" => $result->getTransport(),
                "traveledDistance" => $this->calculateDistance($dateDiffInThisMonth, $result->getDistance(), $result->getWorkDays()),
                "compensation" => $this->calculateCompensation($dateDiffInThisMonth, $result->getWorkDays(), $result->getTransport(), $result->getDistance()),
                "paymentDate" => $firstMondayOfNextMonth
            ];
        }

        return $csvBody;

    }

    /**
     * @param EmployeeData[] $results
     * @return array
     */
    public function getExportData(array $results): array
    {

        return [$this->createExportHeaders(), $this->createExportBodyFromResults($results)];
    }

    /**
     * @param $dateDiffInThisMonth
     * @param float $distance
     * @param float $workDays
     * @return float
     */
    public function calculateDistance($dateDiffInThisMonth, float $distance, float $workDays): float
    {
        return (($distance * 2) * (floor($dateDiffInThisMonth / 7) * ceil($workDays)));

    }

    public function calculateCompensation($dateDiffInThisMonth, float $workDays, string $transport, float $distance)
    {
        switch ($transport) {
            case 'Bike':
                $calculatedCompensation = $this->calculateBikeCompensation($dateDiffInThisMonth, $workDays, $distance);
                break;
            case 'Bus':
            case 'Train':
                $calculatedCompensation = $this->calculateBusOrTrainCompensation($dateDiffInThisMonth, $workDays, $distance);
                break;
            case 'Car':
                $calculatedCompensation = $this->calculateCarCompensation($dateDiffInThisMonth, $workDays, $distance);
                break;
            default:
                $calculatedCompensation = 0;

        }
        return $calculatedCompensation;
    }

    /**
     * @param $dateDiffInThisMonth
     * @param float $workDays
     * @param float $distance
     * @return float|int
     */
    public function calculateBikeCompensation($dateDiffInThisMonth, float $workDays, float $distance)
    {
        if ((5 < $distance) && ($distance < 10)) {
            return (floor($dateDiffInThisMonth / 7) * $workDays * $distance * 2 * .50 * 2);

        }
        return (floor($dateDiffInThisMonth / 7) * $workDays * $distance * 2 * .50);

    }

    /**
     * @param $dateDiffInThisMonth
     * @param float $workDays
     * @param float $distance
     * @return float
     */
    public function calculateBusOrTrainCompensation($dateDiffInThisMonth, float $workDays, float $distance): float
    {
        return (floor($dateDiffInThisMonth / 7) * $workDays * $distance * 2 * .25);
    }

    /**
     * @param $dateDiffInThisMonth
     * @param float $workDays
     * @param float $distance
     * @return float
     */
    public function calculateCarCompensation($dateDiffInThisMonth, float $workDays, float $distance): float
    {
        return (floor($dateDiffInThisMonth / 7) * $workDays * $distance * 2 * .10);
    }

    /**
     * @return false|int
     */
    private function getFirstDayOfThisMonth()
    {
        return strtotime("first day of this month");
    }

    /**
     * @return false|int
     */
    private function getLastDayOfThisMonth()
    {
        return strtotime("last day of this month");
    }

    /**
     * @return false|string
     */
    private function getFirstMondayOfNextMonth()
    {
        return date("d-M-Y", strtotime("first monday of next month"));
    }

    /**
     * Function to find the difference between two dates.
     **/
    function dateDiffInThisMonth()
    {
        // Calculating the difference in timestamps
        $diff = $this->getLastDayOfThisMonth() - $this->getFirstDayOfThisMonth();

        // 1 day = 24 hours
        // 24 * 60 * 60 = 86400 seconds
        return abs(round($diff / 86400));
    }
}
