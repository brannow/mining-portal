<?php declare(strict_types=1);


namespace Src\Domain\Model;


abstract class BaseModel
{
    /**
     * @var int
     */
    private $id = 0;
    
    private $snapshotId = 0;
    
    /**
     * BaseModel constructor.
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        $this->id = $id;
    }
    
    /**
     * @param int $id
     */
    public function __setId(int $id): void
    {
        $this->id = $id;
    }
    
    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    
    /**
     * @return bool
     */
    public function compareSnapshotId(): bool
    {
        return ($this->snapshotId === $this->generateSnapshotId());
    }
    
    /**
     *
     */
    public function updateSnapshotId(): void
    {
        $this->snapshotId = $this->generateSnapshotId();
    }
    
    /**
     *
     */
    public function preDatabaseHook()
    {
    
    }
    
    /**
     *
     */
    public function postDatabaseHook()
    {
    
    }
    
    /**
     * @return int
     */
    private function generateSnapshotId(): int
    {
        $methodNames = get_class_methods(static::class);
        $values = [];
        foreach ($methodNames as $methodName) {
            if(strpos($methodName, 'get') === 0) {
                $var = $this->$methodName();
                if ($var === null || is_scalar($var) || (is_object($var) && method_exists($var, '__toString'))) {
                    $values[] = crc32((string)$var . '//' . $methodName);
                }
            }
        }
    
        return crc32(json_encode($values));
    }
}