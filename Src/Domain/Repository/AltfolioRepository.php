<?php declare(strict_types=1);


namespace Src\Domain\Repository;


use Src\Domain\Model\Altfolio\Altfolio;

class AltfolioRepository extends BaseRepository
{
    /**
     * @param string $name
     * @return bool
     */
    public function existName(string $name): bool
    {
        $result = $this->getConnection()->fetchQuery(
            'SELECT t1.`id`
                            FROM `altfolio` t1
                            WHERE
                            t1.`name`=?
                            LIMIT 1',
            $name
        );
        
        return !empty($result);
    }
    
    /**
     * @param Altfolio $altfolio
     */
    public function deleteAltfolio(Altfolio $altfolio): void
    {
        $this->getConnection()->updateQuery('DELETE FROM `altfolio` WHERE `id` = ? ', $altfolio->getId());
    }
    
    /**
     * @param int $id
     * @param int $userId
     * @return null|Altfolio
     */
    public function findById(int $id, int $userId): ?Altfolio
    {
        $altfolio = $this->selectModel(
            Altfolio::class,
            'altfolio',
            [],
            ['id' => $id, 'user_id' => $userId],
            [],
            1
        );
    
        if ($altfolio) {
            return $altfolio[0];
        }
    
        return null;
    }
    
    /**
     * @param Altfolio[] ...$altfolios
     * @return bool
     */
    public function updateAltfolio(Altfolio ...$altfolios) :bool
    {
        $split = $this->splitIntoInsertUpdate(...$altfolios);
        if ($split[self::UPDATES]) {
            $this->updateModel(
                'altfolio',
                ['name'],
                ['id'],
                ...$split[self::UPDATES]
            );
        }
        
        if ($split[self::INSERTS]) {
            $this->insertModel(
                'altfolio',
                ['user_id', 'name'],
                ...$split[self::INSERTS]
            );
        }
        
        return true;
    }
    
    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->selectModel(
            Altfolio::class,
            'altfolio'
        );
    }
}