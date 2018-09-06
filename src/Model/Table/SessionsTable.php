<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class SessionsTable extends Table
{
    public function initialize(array $config)
    {
        $this->hasOne('Locations', ['foreignKey' => 'LocationID', 'bindingKey' => 'LocationID']);
    }
}