<?php

namespace App\Core\ORM\Repositories;

use App\Core\ORM\DB;
use App\Core\ORM\Entities\ProductEntity;
use App\Core\ORM\Repositories\AbstractRepository;
use App\Core\Services\CacheService;

class ProductsRepository extends AbstractRepository
{
    private static string $table = "Products";

    public function __construct(DB $db, CacheService $cacheService)
    {
        parent::__construct($db, $cacheService);
    }

    public function getTotalProductTodayByUserId(int $userId): int
    {
        $sql = "SELECT COUNT(*) AS total FROM " . self::$table . " WHERE shared_by_user_id = ? AND DATE(datetime) = CURDATE()";
        return $this->db->query($sql, $userId)->fetch()["total"] ?? 10;
    }

    public function saveProduct(ProductEntity $product): void
    {
        $sql = "INSERT INTO " . self::$table . " SET url = ?, asin = ?, shared_by_user_id = ?";
        $this->db->query($sql, $product->getUrl(), $product->getAsin(), $product->getSharedByUserId());
        $product->setId($this->db->getLastInsertId());
    }

    public function checkAsinToday(string $asin): bool
    {
        $sql = "SELECT COUNT(*) as tot FROM " . self::$table . " WHERE asin = ? AND DATE(datetime) = CURDATE()";
        $res = $this->db->query($sql, $asin)->fetch()['tot'] ?? 0;
        return (bool)$res;
    }


}