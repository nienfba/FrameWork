<?php

namespace Nienfba\Framework;

abstract class Model
{
    /**
     * @var iDataStorage $data;
     */
    protected $data;

    /**
     * @var string $entity full qualified name of Entity Class;
     */
    protected $entity;

    /**
     * @var string $table table on storage
     */
    protected $table;

    /**
     * @var string $prefixe prefixe for row in Storage
     */
    protected $prefixe;

    public function __construct()
    {
        $this->data = Application::getDataStorage();

        $this->entity = $this->getEntityName();

        $this->prefixe = '';

        $this->setTableName();
    }

    /** Must return Entity class Name for Model 
     * @return string Entity class full qualified name
    */
    abstract public function getEntityName(): string;


    /** Defined name of data Table an prefixe of Row
     * 
     * @param void
     * @return void
     */
    private function setTableName() {
        $entityPart = explode('\\', $this->entity);
        $this->table = strtolower($entityPart[count($entityPart)-1]);
        if(DB_PREFIXE)
            $this->prefixe = $this->table[0].'_';
    }

    /** Find one element in Data and return Entity Object
     * 
     * @param int $id id (primary Key) of element
     * @return Entity instance of Entity 
     */
    public function find(?int $id): ?Entity
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->prefixe}id=:id";
        return $this->data->selectOne($this->entity, $sql, ['id' => $id]);
    }

    /** Find one element by specific row
     * 
     * @param array $fields fields to find by 
     * @return Entity instance of Entity 
     */
    public function findBy(array $fields): ?Entity
    {
        $where = '';
        $sql = "SELECT * FROM {$this->table} WHERE ";

        $i = 0;
        foreach($fields as $field=>$value) {
            $where = "{$this->prefixe}{$field}=:{$field}";
            if (++$i < count($fields))
                $where .= ' AND ';
        }

        $sql .= $where;

        return $this->data->selectOne($this->entity, $sql, $fields);
    }

    /** Find one element in Data and return Entity Object
     * 
     * @param array $params all params condition for request ['title'=>'The title to find', 'name' => 'Name to find']
     * @param array $orders all order field and order for request ['title'=>'DESC']
     * @param int $limit max number of elements
     * @param int $offset start find lax number of element at this offset position
     * 
     * @return Entity instance of Entity 
     */
    public function findAll(array $params = [], array $orders = [], int $limit=null, int $offset=null) : EntityCollection
    {
        $sql = 'SELECT * FROM ' . $this->table;

        /** Add params */
        $where = '';
        $i = 0;
        foreach($params as $prop=>$value) {
            $where .= "{$this->prefixe}$prop = :$prop";
            if (++$i < count($params))
                $where .= ' AND ';
        }

        /** Add Order */
        $order = '';
        $j= 0;
        foreach ($orders as $prop => $sens) {
            $order .= "{$this->prefixe}$prop $sens";
            if (++$j < count($orders))
                $order .= ',';
        }
        

        if(!empty($where))
            $sql .= " WHERE $where";
            
        if(!empty($order))
            $sql .= " ORDER BY $order";

        if (!empty($limit)) {
            $sql .= " LIMIT $limit";
            if (!empty($offset))
                $sql .= " OFFSET $offset";
        }

        return $this->data->selectAll($this->entity, $sql, $params);
    }

    /** Save entity in DATA 
     * @param Entity $entity the entity object to save on DATA
     * @return void
     * 
     * @todo implement saveChilren
     */
    public function save(Entity $entity): ?int
    {
        // Récupération des colonne et propriétés de notre Entité
        $properties = $entity->getPropertyHydrateData($this);

        // On parcours toutes les propriétés pour les adaptés à la Data

        $listCols = '';
        $listTokens = '';
        $listColsTokens = '';
        $i = 0;
        foreach ($properties as $colonne => $value) {
            
            // DATETIME convert to string to write in Data !!
            if (strpos($colonne, 'At')) {
                unset($properties[$colonne]);
                $colonne = str_replace('At', '_at', $colonne);
                if($value != null)
                    $properties[$colonne] = $value->format('Y-m-d H:i');
                else
                    $properties[$colonne] = $value;
            }

            //ENTITY : getting ID to write in Data !!
            if (gettype($value) == 'object' && is_subclass_of($value, 'Nienfba\Framework\Entity')) {

                unset($properties[$colonne]);
                $colonne = $colonne . '_id';
                $properties[$colonne] = $value->getId();
                
            }

            // ENTITY COLLECTION : don't do anything at this time !
            if (gettype($value) == 'object' && get_class($value) == 'Nienfba\Framework\EntityCollection') {
                unset($properties[$colonne]);
                continue; 
            }


            // CREATING STRING FOR DATA REQUEST 
            $listCols .= $colonne;
            $listTokens .= ':' . $colonne;
            $listColsTokens .= "{$colonne} = :{$colonne}";
            if (++$i < count($properties)) {
                $listCols .= ',';
                $listTokens .= ',';
                $listColsTokens .= ',';
            }
        
        }

        if ($entity->getId() == null) {
            $sql = "INSERT INTO {$this->table} ({$listCols}) VALUES ({$listTokens})";
        } else {
            $sql = "UPDATE {$this->table} SET $listColsTokens WHERE {$this->prefixe}id = :{$this->prefixe}id";
        }

        return $this->data->insert($sql, $properties);
    }

    /**
     * Get $prefixe prefixe for row in Storage
     */
    public function getPrefixe(): string
    {
        return $this->prefixe;
    }

    /**
     * Set $prefixe prefixe for row in Storage
     */
    public function setPrefixe(string $prefixe): self
    {
        $this->prefixe = $prefixe;

        return $this;
    }
}
