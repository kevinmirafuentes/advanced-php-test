<?php
namespace App;

use App\Exporter;
use App\Repositories\PlayerRepository;
use App\Repositories\PlayerStatRepository;
use Illuminate\Support;  // https://laravel.com/docs/5.8/collections - provides the collect methods & collections class

class Controller 
{

    public function __construct($args) {
        $this->args = $args;
    }

    public function export($type, $format) {
        $data = [];
        $exporter = new Exporter();
        switch ($type) {
            case 'playerstats':
                $searchArgs = ['player', 'playerId', 'team', 'position', 'country'];
                $search = $this->args->filter(function($value, $key) use ($searchArgs) {
                    return in_array($key, $searchArgs);
                });
                $data = (new PlayerStatRepository)->getPlayerStats($search);
                break;
            case 'players':
                $searchArgs = ['player', 'playerId', 'team', 'position', 'country'];
                $search = $this->args->filter(function($value, $key) use ($searchArgs) {
                    return in_array($key, $searchArgs);
                });
                $data = (new PlayerRepository)->getPlayerStats($search);
                break;
        }
        if (!$data) {
            exit("Error: No data found!");
        }

        return $exporter->format($data, $format);
    }
}