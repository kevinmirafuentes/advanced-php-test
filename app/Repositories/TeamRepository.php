<?php 
namespace App\Repositories;

use App\Repositories\Repository;

class TeamRepository extends Repository
{
    public function getTeams()
    {
        return $this->fetch('SELECT * FROM team');
    }
}
