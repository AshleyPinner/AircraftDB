<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\I18n\FrozenTime;

/**
 * Class Flight
 * @package App\Model\Entity
 * @property int FlightID
 * @property  int SessionID
 * @property  int AircraftID
 * @property  Cake\I18n\FrozenTime StartTime
 * @property  Cake\I18n\FrozenTime EndTime
 * @property  string Callsign
 * @property  int NumPosMsgRec
 * @property  int NumADSBMsgRec
 * @property  int NumModeSMsgRec
 * @property  int NumIDMsgRec
 * @property  int NumSurPosMsgRec
 * @property  int NumAirPosMsgRec
 * @property  int NumAirVelMsgRec
 * @property  int NumSurAltMsgRec
 * @property  int NumSurIDMsgRec
 * @property  int NumAirToAirMsgRec
 * @property  int NumAirCallRepMsgRec
 * @property  bool FirstIsOnGround
 * @property  bool LastIsOnGround
 * @property  float FirstLat
 * @property  float LastLat
 * @property  float FirstLon
 * @property  float LastLon
 * @property  float FirstGroundSpeed
 * @property  float LastGroundSpeed
 * @property  int FirstAltitude
 * @property  int LastAltitude
 * @property  int FirstVerticalRate
 * @property  int LastVerticalRate
 * @property  float FirstTrack
 * @property  float LastTrack
 * @property  int FirstSquawk
 * @property  int LastSquawk
 * @property  bool HadAlert
 * @property  bool HadEmergency
 * @property  bool HadSPI
 * @property  string UserNotes
 */
class Flight extends Entity
{


    /**
     * Calculates the great-circle distance between two points, with
     * the Haversine formula.
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @param int $earthRadius Mean earth radius in [m]
     * @return float Distance between points in [m] (same as earthRadius)
     */
    public function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
    {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    /**
     * @param float $latitudeFrom Latitude of start point in [deg decimal]
     * @param float $longitudeFrom Longitude of start point in [deg decimal]
     * @param float $latitudeTo Latitude of target point in [deg decimal]
     * @param float $longitudeTo Longitude of target point in [deg decimal]
     * @return int number between 0 and 360
     */
    public function getRhumbLineBearing($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo) {
        //difference in longitudinal coordinates
        $dLongitude = deg2rad($longitudeTo) - deg2rad($longitudeFrom);

        //difference in the phi of latitudinal coordinates
        $dPhi = log(tan(deg2rad($latitudeTo) / 2 + pi() / 4) / tan(deg2rad($latitudeFrom) / 2 + pi() / 4));

        //we need to recalculate $dLon if it is greater than pi
        if(abs($dLongitude) > pi()) {
            if($dLongitude > 0) {
                $dLongitude = (2 * pi() - $dLongitude) * -1;
            }
            else {
                $dLongitude = 2 * pi() + $dLongitude;
            }
        }
        //return the angle, normalized
        return (rad2deg(atan2($dLongitude, $dPhi)) + 360) % 360;
    }

    /**
     * @param int $bearing number between 0 and 360
     * @return string Cardinal compass direction based upon an 8 section compass
     */
    public function getCompassDirection($bearing) {
        $tmp = round($bearing / 45);
        switch($tmp) {
            case 1:
                $dir = 'NE';
                break;
            case 2:
                $dir = 'E';
                break;
            case 3:
                $dir = 'SE';
                break;
            case 4:
                $dir = 'S';
                break;
            case 5:
                $dir = 'SW';
                break;
            case 6:
                $dir = 'W';
                break;
            case 7:
                return 'NW';
                break;
            default:
                return 'N';
                break;
        }
        return $dir;
    }

    public function getRoundedCompassDirection($bearing) {
        $res = round($bearing / 45);
        return ($res == 8) ? 0 : $res;
    }

    /**
     * @param $distance
     * @return int|string
     */
    public function getDistanceBucket($distance) {
        if ($distance < 50) {
            return '50';
        } elseif ($distance > 50 && $distance < 100) {
            return '100';
        } elseif ($distance > 100 && $distance < 200) {
            return '200';
        } else {
            return '200+';
        }
    }

}