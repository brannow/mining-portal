<?php declare(strict_types=1);


namespace Src\Domain\Repository;


use Src\Domain\Model\User;

class UserRepository extends BaseRepository
{
    /**
     * @param string $key
     * @return int
     */
    public function findUserIdFromRigKey(string $key): int
    {
        $result = $this->getConnection()->fetchQuery(
            'SELECT t1.`id`
                            FROM `user` t1
                            WHERE
                            t1.`rig_key`=?
                            LIMIT 1',
            $key
        );
        
        if ($result && !empty($result[0]['id'])) {
            return (int)$result[0]['id'];
        }
        
        return 0;
    }
    
    /**
     * @param string $username
     * @return bool
     */
    public function existUsername(string $username): bool
    {
        $result = $this->getConnection()->fetchQuery(
            'SELECT t1.`id`
                            FROM `user` t1
                            WHERE
                            t1.`username`=?
                            LIMIT 1',
            $username
        );
        
        return !empty($result);
    }
    
    /**
     * @param User $user
     */
    public function deleteUser(User $user): void
    {
        $this->getConnection()->updateQuery('DELETE FROM `user` WHERE `id` = ? ', $user->getId());
    }
    
    /**
     * @return array
     */
    public function findAll(): array
    {
        return $this->selectModel(
            User::class,
            'user'
        );
    }
    
    /**
     * @param int $id
     * @return null|User
     */
    public function findUserById(int $id): ?User
    {
        $user = $this->selectModel(
            User::class,
            'user',
            [],
            ['id' => $id],
            [],
            1
        );
    
        if ($user) {
            return $user[0];
        }
    
        return null;
    }
    
    /**
     * @param User[] ...$users
     * @return bool
     */
    public function updateUser(User ...$users) :bool
    {
        $split = $this->splitIntoInsertUpdate(...$users);
        if ($split[self::UPDATES]) {
            $this->updateModel(
                'user',
                ['email', 'level', 'password'],
                ['id'],
                ...$split[self::UPDATES]
            );
        }
        
        if ($split[self::INSERTS]) {
            $this->insertModel(
                'user',
                ['level', 'email', 'password', 'username', 'encryption_key', 'app_token', 'rig_key'],
                ...$split[self::INSERTS]
            );
        }
        
        return true;
    }
    
    /**
     * @param string $username
     * @return array
     */
    public function findUserHashByUsername(string $username): array
    {
        $result = $this->getConnection()->fetchQuery(
            'SELECT t1.`id`, t1.`password` FROM `user` t1
            WHERE t1.`username` = ?
            LIMIT 1;',
            $username
        );
    
        if ($result) {
            return $result[0];
        }
    
        return [];
    }
}