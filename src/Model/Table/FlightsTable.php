<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class FlightsTable extends Table
{
    public function initialize(array $config)
    {
        $this->hasOne('Sessions', ['foreignKey' => 'SessionID', 'bindingKey' => 'SessionID']);
    }
}