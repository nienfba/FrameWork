<?php

namespace Nienfba\Framework;

use Nienfba\Framework\Entity;

interface iDataStorage {

    public function selectOne(string $entityName ,string $request = '', array $params = []): ?Entity;

    public function insert(string $request = '', array $params = []): ?int;

    public function delete(string $request = '', array $params = []);

    public function selectAll(string $entityName , string $request ='', array $params = []): EntityCollection;
}