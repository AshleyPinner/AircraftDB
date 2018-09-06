<?php
namespace App\Controller;

use App\Controller\AppController;
use App\Model\Entity\Flight;
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

        $flights = TableRegistry::get('Flights');

        $flightsData = $flights->find()->where(function ($exp) use($start, $end) {
            return $exp->between('Flights.StartTime', $start, $end); })->contain(['Sessions' => ['Locations']])->order(['Flights.StartTime' => 'desc'])->toList();


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

        $polar = [
            '200+' => [
                0 => ['y' => 50, 'low' => 200, 'hits' => null],
                1 => ['y' => 50, 'low' => 200, 'hits' => null],
                2 => ['y' => 50, 'low' => 200, 'hits' => null],
                3 => ['y' => 50, 'low' => 200, 'hits' => null],
                4 => ['y' => 50, 'low' => 200, 'hits' => null],
                5 => ['y' => 50, 'low' => 200, 'hits' => null],
                6 => ['y' => 50, 'low' => 200, 'hits' => null],
                7 => ['y' => 50, 'low' => 200, 'hits' => null],
            ],
            200 => [
                0 => ['y' => 100, 'low' => 100, 'hits' => null],
                1 => ['y' => 100, 'low' => 100, 'hits' => null],
                2 => ['y' => 100, 'low' => 100, 'hits' => null],
                3 => ['y' => 100, 'low' => 100, 'hits' => null],
                4 => ['y' => 100, 'low' => 100, 'hits' => null],
                5 => ['y' => 100, 'low' => 100, 'hits' => null],
                6 => ['y' => 100, 'low' => 100, 'hits' => null],
                7 => ['y' => 100, 'low' => 100, 'hits' => null],
            ],
            100  => [
                0 => ['y' => 50, 'low' => 50, 'hits' => null],
                1 => ['y' => 50, 'low' => 50, 'hits' => null],
                2 => ['y' => 50, 'low' => 50, 'hits' => null],
                3 => ['y' => 50, 'low' => 50, 'hits' => null],
                4 => ['y' => 50, 'low' => 50, 'hits' => null],
                5 => ['y' => 50, 'low' => 50, 'hits' => null],
                6 => ['y' => 50, 'low' => 50, 'hits' => null],
                7 => ['y' => 50, 'low' => 50, 'hits' => null],
            ],
            50 => [
                0 => ['y' => 50, 'low' => 1, 'hits' => null],
                1 => ['y' => 50, 'low' => 1, 'hits' => null],
                2 => ['y' => 50, 'low' => 1, 'hits' => null],
                3 => ['y' => 50, 'low' => 1, 'hits' => null],
                4 => ['y' => 50, 'low' => 1, 'hits' => null],
                5 => ['y' => 50, 'low' => 1, 'hits' => null],
                6 => ['y' => 50, 'low' => 1, 'hits' => null],
                7 => ['y' => 50, 'low' => 1, 'hits' => null],
            ]
        ];

        $aircraftSeenArray = [];
        $aircraftSeen = 0;

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
            $polar[$bucket][$compDir]['hits']++;
            //convert them into nautical miles
            $lastConDistance = (float) number_format($flightObj->haversineGreatCircleDistance($myLat, $myLon, $flightObj->LastLat, $flightObj->LastLon) * 0.000539957, 2);
            $flightObj->lastConDistance = $lastConDistance;

            //bail out if the distance is absurd
            if ($firstConDistance > 400) {
                continue;
            }

            //calculate maximum overall range
            $maxDistance = ($firstConDistance > $maxDistance) ? $firstConDistance : $maxDistance;

            //calculate maximum directional range
            $range[$compDir] = (float)($firstConDistance > $range[$compDir]) ? $firstConDistance : $range[$compDir];

        }

        foreach($polar as &$polarData) {
            foreach ($polarData as &$row) {
                if (empty($row['hits'])) {
                    $row['color'] = 'rgba(255,255,255,0)';
                } else {
                    $row['color'] = 'rgba(100,190,241, 0.7)';
                }
            }
        }



        $this->set(compact('polar', 'range', 'totalHits', 'totalFlights', 'totalPositions', 'aircraftSeen', 'maxDistance', 'hitsData', 'positionsData', 'today'));
    }
}