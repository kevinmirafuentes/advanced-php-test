<?php 
namespace App\Repositories;
use \mysqli;

class Repository 
{
    const DB_NAME = 'nba2019';
    const DB_HOST = 'mysql';
    const DB_USER = 'root';
    const DB_PASS = 'root';
    
    protected $db; 
    
    public function __construct()
    {
        $this->db = new mysqli(self::DB_HOST, self::DB_USER, self::DB_PASS, self::DB_NAME);    
    }

    public function fetch($sql)
    {
        $result = $this->db->query($sql);
        if (!is_object($result)) {
            return $result;
        }
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}
