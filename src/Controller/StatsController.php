<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Flight;
use App\Model\Entity\Polar;
use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class StatsController extends AppController
{


    public function index() {

        $today = date('Y-m-d');
        if ($this->request->is('post')) {
            $date = $this->request->getData('date');
            $ret = \DateTime::createFromFormat('Y-m-d', $date);
            if ($ret !== false) {
                $today = $ret->format('Y-m-d');
            }

        }


        $start = $today.' 00:00:00';
        $end = $today.' 23:59:59';
        $sevenDaysAgo = \DateTime::createFromFormat('Y-m-d H:i:s', $start)->sub(new \DateInterval('P6D'))->format('Y-m-d H:i:s');
        $thirtyDaysAgo = \DateTime::createFromFormat('Y-m-d H:i:s', $start)->sub(new \DateInterval('P30D'))->format('Y-m-d H:i:s');

        $flights = TableRegistry::get('Flights');

        $flightsData = $flights->find()->where(function ($exp) use($start, $end) {
            return $exp->between('Flights.StartTime', $start, $end); })->contain(['Sessions' => ['Locations']])->order(['Flights.StartTime' => 'desc'])->toList();

        $barChart = $flights->find()->select(['hits' => 'count(*)', 'positions' => 'sum(case when FirstLat not null then 1 else 0 END)', 'dayOfYear' => "strftime('%j', StartTime)"])->where(function ($exp) use($end, $sevenDaysAgo) {
            return $exp->between('Flights.StartTime', $sevenDaysAgo, $end); })->group("strftime('%j', StartTime)")->toList();

        $histogramData = $flights->find()->select(['hits' => 'sum(NumPosMsgRec + NumADSBMsgRec + NumModeSMsgRec)', 'positions' => 'sum(NumPosMsgRec)', 'hour' => "strftime('%H', StartTime)" ])
            ->where(function ($exp) use($start, $end) {
                return $exp->between('Flights.StartTime', $start, $end); })->group("strftime('%H', StartTime)")->toList();

        $thirtyBarChart = $flights->find()->select(['hits' => 'count(*)', 'positions' => 'sum(case when FirstLat not null then 1 else 0 END)', 'dayOfYear' => "strftime('%j', StartTime)"])->where(function ($exp) use($end, $thirtyDaysAgo) {
            return $exp->between('Flights.StartTime', $thirtyDaysAgo, $end); })->group("strftime('%j', StartTime)")->toList();


        $hitsData = array_map('intval', Hash::extract($histogramData, '{n}.hits'));

        $positionsData = array_map('intval', Hash::extract($histogramData, '{n}.positions'));

        $maxDistance = 0;
        $totalHits = 0;
        $totalFlights = 0;
        $totalPositions = 0;
        $range = [
            0 => 0.0,
            1 => 0.0,
            2 => 0.0,
            3 => 0.0,
            4 => 0.0,
            5 => 0.0,
            6 => 0.0,
            7 => 0.0
        ];

        $contactsData = [
            0 => 0,
            1 => 0,
            2 => 0,
            3 => 0,
            4 => 0,
        ];

        $polarEntity = new Polar();

        $aircraftSeenArray = [];
        $aircraftSeen = $flightHits = $binnedCount = $totalDistances = 0;
        $emergencies = $binned = [];
        $maxDistanceFlight = '';
        /** @var Flight $flightObj */
        foreach($flightsData as &$flightObj) {
            //get the lat/lon used for this session
            $myLat = $flightObj->session->location->Latitude;
            $myLon = $flightObj->session->location->Longitude;

            $totalHits += $flightObj->NumPosMsgRec + $flightObj->NumADSBMsgRec + $flightObj->NumModeSMsgRec;
            $flightHits = $flightObj->NumPosMsgRec + $flightObj->NumADSBMsgRec + $flightObj->NumModeSMsgRec;
            $totalFlights++;

            if ($flightObj->HadEmergency) {
                $emergencies[] = $flightObj->Callsign;
            }

            if (!isset($aircraftSeenArray[$flightObj->AircraftID])) {
                $aircraftSeenArray[$flightObj->AircraftID] = 1;
                $aircraftSeen++;
            }

            if (empty($flightObj->FirstLat)) {
                $flightObj->firstConDistance = null;
                $flightObj->lastConDistance = null;
                continue;
            }
            $totalPositions++;
            //convert them into nautical miles
            $firstConDistance = (float) number_format($flightObj->haversineGreatCircleDistance($myLat, $myLon, $flightObj->FirstLat, $flightObj->FirstLon) * 0.000539957, 2);
            $totalDistances += $firstConDistance;

            $flightObj->firstConDistance = $firstConDistance;

            //also get the distance bucket for this item
            $bucket = $flightObj->getDistanceBucket($firstConDistance);
            $bearing = $flightObj->getRhumbLineBearing($myLat, $myLon, $flightObj->FirstLat, $flightObj->FirstLon);
            $compDir = $flightObj->getRoundedCompassDirection($bearing);
            $polarEntity->recordPolarPlot($bucket, $compDir, $flightHits);
            $bucketId = $flightObj->getChartBucket($firstConDistance);
            $contactsData[$bucketId] += $flightObj->NumPosMsgRec;
            //convert them into nautical miles
            $lastConDistance = (float) number_format($flightObj->haversineGreatCircleDistance($myLat, $myLon, $flightObj->LastLat, $flightObj->LastLon) * 0.000539957, 2);
            $flightObj->lastConDistance = $lastConDistance;

            //bail out if the distance is absurd
            if ($firstConDistance > 400 || $lastConDistance > 400) {
                $binnedCount++;
                $binned[] = "{$flightObj->Callsign} (FC $firstConDistance - LC $lastConDistance)";
                continue;
            }

            //calculate maximum overall range
            if ($firstConDistance > $maxDistance) {
                $maxDistance = $firstConDistance;
                $tmpCallSign = empty($flightObj->Callsign) ? 'No Callsign' : $flightObj->Callsign;
                $maxDistanceFlight =  $tmpCallSign . ' - ' . $flightObj->StartTime->format('Y-m-d H:i:s');
            }

            //also capture the maximum distance direction
            if ($maxDistance == $firstConDistance || $maxDistance == $lastConDistance) {
                $maxDistanceDirection = $flightObj->getCompassDirection($bearing);
            }

            //calculate maximum directional range
            $range[$compDir] = (float)($firstConDistance > $range[$compDir]) ? $firstConDistance : $range[$compDir];

            $polar = $polarEntity->getPolarPlot();

        }


        $score = $this->calculateScore($today);


        $this->set(compact('polar', 'range', 'totalHits', 'totalFlights', 'totalPositions', 'aircraftSeen',
            'maxDistance', 'hitsData', 'positionsData', 'today', 'barChart', 'binnedCount', 'contactsData', 'totalDistances', 'score', 'emergencies',
            'maxDistanceDirection', 'maxDistanceFlight', 'binned', 'thirtyBarChart'));
    }

    /**
     * This function generates a score for the past 30 days based upon the way FlightRadar24 scores
     * Basically:
     * Sum of:
     * (2 * uptime (in minutes) + 2* max range + average range) of day 1
     * (2 * uptime (in minutes) + 2* max range + average range) of day 2
     * ...
     * (2 * uptime (in minutes) + 2* max range + average range) of day 30
     * Divided by 10
     *
     * Since we can't work out uptime of receiver given the way the data is stored (I could technically look at session times,
     * but this will never match FR24), we assume the full 24h (1440 seconds) of uptime per day, and code the multiplier in as well
     * @param String $date
     * @return float|int
     * @throws \Exception
     */
    private function calculateScore(String $date) {
        $flights = TableRegistry::get('Flights');

        $ret = \DateTimeImmutable::createFromFormat('Y-m-d', $date);

        $dayScores = [];
        for($i = 0; $i < 30; $i++) {
            $start = $ret->sub(new \DateInterval("P{$i}D"))->format('Y-m-d') . ' 00:00:00';

            $end = \DateTime::createFromFormat('Y-m-d H:i:s', $start)->sub(new \DateInterval('P1D'))->format('Y-m-d H:i:s');


            $cacheKey = "flightScore_{$start}_to_{$end}";

            $data = Cache::read($cacheKey);

            //always overwrite the current day
            if(!$data || $start == date('Y-m-d')) {

                $flightsData = $flights->find()->where(function ($exp) use ($start, $end) {
                    return $exp->between('Flights.StartTime', $end, $start);
                })->contain(['Sessions' => ['Locations']])->order(['Flights.StartTime' => 'desc'])->toList();


                $dateBuckets = [];
                $flightDistances = [];
                /** @var Flight $flightObj */
                foreach ($flightsData as $flightObj) {
                    //get the lat/lon used for this session
                    $myLat = $flightObj->session->location->Latitude;
                    $myLon = $flightObj->session->location->Longitude;
                    $firstConDistance = (float)number_format($flightObj->haversineGreatCircleDistance($myLat, $myLon, $flightObj->FirstLat, $flightObj->FirstLon) * 0.000539957, 2);
                    if ($firstConDistance > 400) {
                        continue;
                    }
                    $flightDistances[] = $firstConDistance;

                }

                unset($flightsData);
                if (!empty($flightDistances)) {
                    $data = (2880 + (2 * max($flightDistances)) + (array_sum($flightDistances) / count($flightDistances)));
                } else {
                    $data = 2880;
                }
                Cache::write($cacheKey, $data);


            }

            $dayScores[$start] = $data;
        }


        $total = array_sum($dayScores);
        return $total /10;
    }
}
