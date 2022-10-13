<?php

namespace Nienfba\Framework;

/**
 * EntityCollection
 * 
 * Défini un objet itérable pour stocker et manipuler une collection d'entité
 */
class EntityCollection implements \Iterator, \Countable, \JsonSerializable  {

    /**
     * @var array current collection of entities (same entitites)
     */
    private $entities;


    /**
     * Constructeur
     */
    public function __construct(array $entities = []) {
        $this->setEntities($entities);
    }

    /**
     * Ajoute une entité à la collection
     *
     * @param Entity $entity
     * 
     * @return void
     * 
     */
    public function add(Entity $entity) {
        $key = spl_object_hash($entity);
        if(!array_key_exists($key, $this->entities))
            $this->entities[$key] = $entity;
    }

    /**
     * Remove une entité à la collection
     *
     * @param Entity $entity
     * 
     * @return void
     * 
     */
    public function remove(Entity $entity) {
        $key = spl_object_hash($entity);
        if (!array_key_exists($key, $this->entities))
            unset($this->entities[$key]);
    }

    /**
     * Load all entity inside this collection
     * 
     * @param string $model full qualified Model classe name
     * @param string $property property (Foreign Key) name to find all Entity of collection
     * @param int $value integer value for property (Foreign Key)
     * 
     * @return void
     * 
     */
    public function load(string $model, string $property, int $value) {
        $modelObject = new $model();
        $this->entities =  $modelObject->findAll(["{$property}_id"=> $value]);
        $this->reloadIndex();
    }

    /** Reload all index of entities collection to match with hash (use for add and remove) 
     * @param void
     * @return void
    */
    private function reloadIndex() {
        $entities = $this->entities;
        $this->entities = [];
        foreach($entities as $entity) {
            $this->entities[spl_object_hash($entity)] = $entity;
        }
    }

    public function current() :mixed {
        return current($this->entities);
    }

    public function key() :mixed {
        return key($this->entities);
    }

    public function next(): void {
        next($this->entities);
    }
    public function rewind(): void {
        reset($this->entities);
    }

    public function valid(): bool {
        return !is_null(key($this->entities));
    }

    public function count(): int {
        return count($this->entities);
    }

    /**
     * Get current collection of entities (same entitites)
     *
     * @return  array
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     * Set current collection of entities (same entitites)
     *
     * @param  array  $entities  current collection of entities (same entitites)
     *
     * @return  self
     */
    public function setEntities(array $entities)
    {
        $this->entities = $entities;

        return $this;
    }

    /**
     * Permet de sérialisé le collection en JSON
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {
        return $this->entities;
    }
}