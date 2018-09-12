<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Flight;
use App\Model\Entity\Polar;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

class StatsController extends AppController
{


    public function index() {

        $today = '2018-09-01'; //date('Y-m-d');
        if ($this->request->is('post')) {
            $date = $this->request->data('date');
            $ret = \DateTime::createFromFormat('Y-m-d', $date);
            if ($ret !== false) {
                $today = $ret->format('Y-m-d');
            }

        }


        $start = $today.' 00:00:00';
        $end = $today.' 23:59:59';
        $sevenDaysAgo = \DateTime::createFromFormat('Y-m-d H:i:s', $end)->sub(new \DateInterval('P7D'))->format('Y-m-d H:i:s');

        $flights = TableRegistry::get('Flights');

        $flightsData = $flights->find()->where(function ($exp) use($start, $end) {
            return $exp->between('Flights.StartTime', $start, $end); })->contain(['Sessions' => ['Locations']])->order(['Flights.StartTime' => 'desc'])->toList();

        $barChart = $flights->find()->select(['hits' => 'count(*)', 'positions' => 'sum(case when FirstLat not null then 1 else 0 END)', 'dayOfYear' => "strftime('%j', StartTime)"])->where(function ($exp) use($start, $sevenDaysAgo) {
            return $exp->between('Flights.StartTime', $sevenDaysAgo,$start); })->group("strftime('%j', StartTime)")->toList();

        $histogramData = $flights->find()->select(['hits' => 'count(*)', 'positions' => 'sum(case when FirstLat not null then 1 else 0 END)', 'hour' => "strftime('%H', StartTime)" ])
            ->where(function ($exp) use($start, $end) {
                return $exp->between('Flights.StartTime', $start, $end); })->group("strftime('%H', StartTime)")->toList();

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

        $polarEntity = new Polar();

        $aircraftSeenArray = [];
        $aircraftSeen = 0;
        $binnedCount = 0;
        /** @var Flight $flightObj */
        foreach($flightsData as &$flightObj) {
            //get the lat/lon used for this session
            $myLat = $flightObj->session->location->Latitude;
            $myLon = $flightObj->session->location->Longitude;

            $totalHits += ($flightObj->NumPosMsgRec + $flightObj->NumADSBMsgRec + $flightObj->NumModeSMsgRec + $flightObj->NumIDMsgRec +
                $flightObj->NumSurPosMsgRec + $flightObj->NumAirPosMsgRec + $flightObj->NumAirVelMsgRec + $flightObj->NumSurAltMsgRec + $flightObj->NumSurIDMsgRec + $flightObj->NumAirToAirMsgRec + $flightObj->NumAirCallRepMsgRec);
            $totalFlights++;

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

            $flightObj->firstConDistance = $firstConDistance;

            //also get the distance bucket for this item
            $bucket = $flightObj->getDistanceBucket($firstConDistance);
            $bearing = $flightObj->getRhumbLineBearing($myLat, $myLon, $flightObj->FirstLat, $flightObj->FirstLon);
            $compDir = $flightObj->getRoundedCompassDirection($bearing);
            $polarEntity->recordPolarPlot($bucket, $compDir);
            //convert them into nautical miles
            $lastConDistance = (float) number_format($flightObj->haversineGreatCircleDistance($myLat, $myLon, $flightObj->LastLat, $flightObj->LastLon) * 0.000539957, 2);
            $flightObj->lastConDistance = $lastConDistance;

            //bail out if the distance is absurd
            if ($firstConDistance > 400) {
                $binnedCount++;
                continue;
            }

            //calculate maximum overall range
            $maxDistance = ($firstConDistance > $maxDistance) ? $firstConDistance : $maxDistance;

            //calculate maximum directional range
            $range[$compDir] = (float)($firstConDistance > $range[$compDir]) ? $firstConDistance : $range[$compDir];

            $polar = $polarEntity->getPolarPlot();

        }





        $this->set(compact('polar', 'range', 'totalHits', 'totalFlights', 'totalPositions', 'aircraftSeen',
            'maxDistance', 'hitsData', 'positionsData', 'today', 'barChart', 'binnedCount'));
    }
}