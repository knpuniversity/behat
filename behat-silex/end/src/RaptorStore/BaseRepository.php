<?php

namespace RaptorStore;

use Doctrine\DBAL\Connection;

abstract class BaseRepository
{
    const DATE_FORMAT = 'Y-m-d H:i:s';

    protected $conn;

    abstract protected function getTableName();

    abstract function arrayToObject(array $result, $obj = null);

    abstract function objectToArray($obj);

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * @return array
     */
    public function findAll()
    {
        return $this->findBy(array());
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->findOneBy(array(
            'id' => $id
        ));
    }

    /**
     * Inserts an object
     *
     * @param $obj
     */
    public function insert($obj)
    {
        $data = $this->objectToArray($obj);
        unset($data['id']);
        $this->conn->insert($this->getTableName(), $data);

        $obj->id = $this->conn->lastInsertId();
    }

    /**
     * Updates an object
     *
     * @param $obj
     */
    public function update($obj)
    {
        $data = $this->objectToArray($obj);
        unset($data['id']);

        $this->conn->update(
            $this->getTableName(),
            $data,
            array('id' => $obj->id)
        );
    }

    /**
     * Empties the data in the table
     */
    public function emptyTable()
    {
        $this->conn->executeQuery(sprintf('DELETE FROM %s', $this->getTableName()));
    }

    /**
     * Return all objects matching this criteria
     *
     * @param array $criteria
     * @return array
     */
    public function findBy(array $criteria)
    {
        $sql = $this->buildQuery($criteria);

        $results = $this->conn->fetchAll($sql);

        $objects = array();
        foreach ($results as $result) {
            $objects[] = $this->arrayToObject($result);
        }

        return $objects;
    }

    /**
     * Returns one object matching the criteria
     *
     * @param array $criteria
     * @return mixed
     */
    public function findOneBy(array $criteria)
    {
        $sql = $this->buildQuery($criteria);

        $result = $this->conn->fetchAssoc($sql);
        if (!$result) {
            return false;
        }

        return $this->arrayToObject($result);
    }

    /**
     * Builds the SQL query
     *
     * @param array $criteria
     * @return string
     */
    private function buildQuery(array $criteria)
    {
        $sql = 'SELECT * FROM '.$this->getTableName();
        if (count($criteria)) {
            $expr = $this->conn->getExpressionBuilder()->andX();
            foreach ($criteria as $column => $value) {
                $expr->add($this->conn->getExpressionBuilder()->eq($column, $value));
            }

            $sql .= sprintf(' WHERE %s', $expr);
        }

        return $sql;
    }
}