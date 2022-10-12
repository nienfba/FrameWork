<?php

namespace Nienfba\Framework;

use Nienfba\Framework\Entity;

/**
 * Storage database
 */
class DataStorageDatabase implements iDataStorage {

    /**
     * Connexion PDO
     *
     * @var \PDO
     */
    private $pdo;


    /** Contruct a new DataStorageDatabase */
    public function __construct() {
        $defaults = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
        ];
        try {
            $this->pdo = new \PDO(DB_DSN, DB_USERNAME, DB_PASSWORD, $defaults);
        } catch (\PDOException $e) {
            // TODO return correct error/information page on PDOException
            echo $e->getMessage();
        }
        
    }

    /** Select an return one/first element from data
     * @param string $entityName entity name to hydrate
     * @param string $request Request to send to Data for retrive information
     * @param array $params Params array to send to Data for retrive information
     * 
     * @return Entity|null
     */
    public function selectOne(string $entityName, string $request = '', array $params = []): ?Entity {

        $sth = $this->pdo->prepare($request);

        $this->execute($sth, $params);

        $sth->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $entityName);
        
        $result = $sth->fetch();

        return empty($result)?null: $result;
    }

    /**
     * Select an return all elements from data.
     *
     * @param string $entityName
     * @param string $request
     * 
     * @return array collection of Entity
     * 
     */
    public function selectAll(string $entityName, string $request = '', array $params = []): EntityCollection
    {
        $sth = $this->pdo->prepare($request);

        $this->execute($sth, $params);

        $sth->setFetchMode(\PDO::FETCH_CLASS | \PDO::FETCH_PROPS_LATE, $entityName);

        $results = $sth->fetchAll();

        return empty($results) ? new EntityCollection() : new EntityCollection($results);
    }

    /**
     * Execute any SQL insert request and return last insert element id
     *
     * @param string $request
     * @param array $params
     * 
     * @return int|null
     * 
     */
    public function insert(string $request = '', array $params = []) : ?int 
    {
        $sth = $this->pdo->prepare($request);

        $this->execute($sth, $params);

        return $this->pdo->lastInsertId() ?? null;
    }

    /**
     * Execute any delete request
     *
     * @param string $request
     * @param array $params
     * 
     * @return void
     * 
     */
    public function delete(string $request = '', array $params = [])
    {
        $sth = $this->pdo->prepare($request);

        $this->execute($sth, $params);

    }

    /**
     * Execute any request on pdostatement object
     *
     * @param \PDOStatement $sth
     * @param array $params
     * 
     * @return void
     * 
     */
    private function execute(\PDOStatement $sth, array $params = []) {
        try {
            $sth->execute($params);
        } catch (\PDOException $e) {
            // TODO return correct information on PDOException
            echo $e->getMessage();
        }
    }

}