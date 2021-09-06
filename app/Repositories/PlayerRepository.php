<?php 
namespace App\Repositories;

class PlayerRepository extends Repository
{
    public function getPlayerStats($search)
    {
        $where = [];
        $conds = [];

        if ($search->has('playerId')) $conds['roster.id'] = $search['playerId'];
        if ($search->has('player')) $conds['roster.name'] = $search['player'];;
        if ($search->has('team')) $conds['roster.team_code'] = $search['team'];
        if ($search->has('position')) $conds['roster.position'] = $search['position'];
        if ($search->has('country')) $conds['roster.nationality'] = $search['country'];
        
        foreach ($conds as $field => $value) {
            $where[] = "{$field}=?";
        }

        $where = implode(' AND ', $where);
        $sql = "SELECT roster.* FROM roster WHERE $where";
        
        $stmt = $this->db->prepare($sql);
        
        $query_params[] = implode('', array_map(function($i) { return 's'; }, array_keys($conds)));
        foreach ($conds as $c) {
            $query_params[] = $c;
        }

        call_user_func_array(array($stmt, 'bind_param'), $query_params);
        
        $stmt->execute();
        
        $result = $stmt->get_result();
        
        $data = [];
        
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return collect($data)
        ->map(function($item, $key) {
            unset($item['id']);
            return $item;
        });
    }   
}
