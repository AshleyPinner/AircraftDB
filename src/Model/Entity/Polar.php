<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

class Polar extends Entity {
    private  $polar = [
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


    public function recordPolarPlot($bucket, $compDir) {
        $this->polar[$bucket][$compDir]['hits']++;
    }

    public function getPolarPlot() {
        foreach($this->polar as &$polarData) {
            foreach ($polarData as &$row) {
                if (empty($row['hits'])) {
                    $row['color'] = 'rgba(255,255,255,0)';
                } else {
                    $row['color'] = 'rgba(100,190,241, 0.7)';
                }
            }
        }
        return $this->polar;
    }
}