<?php

namespace App\Core\ORM\Repositories;

use App\Core\ORM\Repositories\AbstractRepository;

class ScoreRepository extends AbstractRepository
{
    private static string $table = 'Scores';

    public function voteByProductId(int $score, int $productId, int $userId): ?\PDOStatement
    {
        $sql = "DELETE FROM " . self::$table . " WHERE product_id = ? AND user_id = ?";
        $this->db->query($sql, $productId, $userId);

        $sql = "INSERT INTO " . self::$table . " SET user_id = ?, product_id = ?, value = ?";
        return $this->db->query($sql, $userId, $productId, $score);
    }

    public function getTopOfWeek(): bool|array
    {
        $sql = "SELECT SUM(value) as total_score, user_id FROM " . self::$table . " WHERE datetime >= NOW() - INTERVAL 7 DAY GROUP by user_id ORDER by total_score DESC";
        $res = $this->db->query($sql);
        return $res->fetchAll();
    }

    public function getScoreOfWeekByUserId(int $userId): int
    {
        $sql = "SELECT SUM(value) as total_score FROM " . self::$table . " WHERE user_id = ? AND datetime >= NOW() - INTERVAL 7 DAY";
        $res = $this->db->query($sql, $userId);
        return $res->fetch()['total_score'] ?? 0;
    }
}