<?php declare(strict_types=1);


namespace Src\Domain\Repository;

use Src\Domain\Model\Altfolio\Wallet;

class WalletRepository extends BaseRepository
{
    /**
     * @param Wallet[] ...$wallets
     * @return bool
     */
    public function updateWallet(Wallet ...$wallets) :bool
    {
        $split = $this->splitIntoInsertUpdate(...$wallets);
        if ($split[self::UPDATES]) {
            $this->updateModel(
                'wallet',
                ['amount'],
                ['id'],
                ...$split[self::UPDATES]
            );
        }
        
        if ($split[self::INSERTS]) {
            $this->insertModel(
                'wallet',
                ['currency_id', 'altfolio_id', 'address', 'amount'],
                ...$split[self::INSERTS]
            );
        }
        
        return true;
    }
    
    /**
     * @param int $id
     * @return array
     */
    public function findByAltfolioId(int $id): array
    {
        return $this->selectModel(
            Wallet::class,
            'wallet',
            [],
            ['altfolio_id' => $id],
            ['currency_id' => 'ASC', 'amount' => 'DESC']
        );
    }
    
    /**
     * @param int $id
     * @param int $altfolioId
     * @return null|Wallet
     */
    public function findById(int $id, int $altfolioId): ?Wallet
    {
        $wallet = $this->selectModel(
            Wallet::class,
            'wallet',
            [],
            ['id' => $id, 'altfolio_id' => $altfolioId],
            [],
            1
        );
        
        if ($wallet) {
            return $wallet[0];
        }
        
        return null;
    }
    
    /**
     * @param Wallet $wallet
     */
    public function deleteWallet(Wallet $wallet): void
    {
        $this->getConnection()->updateQuery('DELETE FROM `wallet` WHERE `id` = ? ', $wallet->getId());
    }
    
    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->selectModel(
            Wallet::class,
            'wallet'
        );
    }
}