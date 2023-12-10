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
}