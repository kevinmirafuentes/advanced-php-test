<?php 
namespace App\Repositories;

class PlayerStatRepository extends Repository
{
    public function getPlayerStats($search)
    {
        $where = [];
        $conds = [];

        if ($search->has('playerId')) $conds['roster.id'] = $search['playerId'];
        if ($search->has('player')) $conds['roster.name'] = $search['player'];
        if ($search->has('team')) $conds['roster.team_code'] = $search['team'];
        if ($search->has('position')) $conds['roster.pos'] = $search['position'];
        if ($search->has('country')) $conds['roster.nationality'] = $search['country'];
        
        foreach ($conds as $field => $value) {
            $where[] = "{$field}=?";
        }

        $where = implode(' AND ', $where);
        $sql = "
            SELECT roster.name, player_totals.*, 
                (3pt * 3) + (2pt * 2) + free_throws as total_points, 
                if(field_goals_attempted, concat(round(field_goals/field_goals_attempted, 2) * 100, '%' ), 0) as field_goals_pct,
                if (3pt_attempted, concat(round(3pt/3pt_attempted, 2) * 100, '%'), 0) as 3pt_pct,
                if (2pt_attempted, concat(round(2pt/2pt_attempted, 2) * 100, '%'), 0) as 2pt_pct,
                if (free_throws_attempted, concat(round(free_throws/free_throws_attempted, 2) * 100, '%'), 0) as total_rebounds,
                offensive_rebounds + defensive_rebounds as total_rebounds
            FROM player_totals
                INNER JOIN roster ON (roster.id = player_totals.player_id)
            WHERE $where";
        
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

        return collect($data);
    }
}
