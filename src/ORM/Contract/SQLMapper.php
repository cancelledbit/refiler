<?php


namespace Refiler\ORM\Contract;


use Delight\Db\PdoDatabase;
use Psr\Container\ContainerInterface;
use Refiler\Model\Contract\BaseModel;
use Refiler\Model\Contract\ModelInterface;
use Refiler\Model\Factory\Contract\BaseModelFactory;

abstract  class SQLMapper extends BaseMapper implements MapperInterface
{
    protected string $table;
    protected PdoDatabase $database;

    public function __construct(ContainerInterface $container, PdoDatabase $database)
    {
        parent::__construct($container);
        $this->database = $database;
        $this->table = $this->getTableNameFromClass(static::class);

    }

    public function find(?string $id) : ModelInterface
    {
        $row = $this->database->selectRow("SELECT * FROM {$this->table} WHERE id = ?", [(int)$id]);
        /** @var BaseModel $model */
        $model = new $this->model((array)$row);
        $model->setMapper($this);
        if (count($row) > 0) {
            $model->setExisted();
        }
        return $model;
    }

    public function findBy(array $query, ?array $order = null)
    {
        // TODO: Implement findBy() method.
    }

    public function getIdQuery(?string $id)
    {
        // TODO: Implement getIdQuery() method.
    }

    public function save(BaseModel $model) : ModelInterface
    {
        /** @var BaseModel $model */
        $oldModel = $this->find($model->id);
        if ($oldModel->isExists()) {
            $this->database->update($this->table, $model->toArray(), ['id' => $model->id]);
        } else {
           $model = $this->create($model);
        }
        return $model;
    }

    protected function create(BaseModel $model): ModelInterface{
        $this->database->insert($this->table, $model->toArray());
        $model->id = $this->database->getLastInsertId();
        return $model;
    }

    public function remove(BaseModel $model)
    {
        // TODO: Implement remove() method.
    }

    protected function getTableNameFromClass(string $name): string {
        $name = explode('\\', $name);
        $name = array_pop($name);
        $namePositionEnd = strpos($name, 'Mapper');
        return strtolower(substr($name, 0, $namePositionEnd)).'s';
    }
}