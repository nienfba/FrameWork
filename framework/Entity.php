<?php

namespace Nienfba\Framework;

use DateTime;
use Nienfba\Framework\Exception\ActionNotFoundException;


/**
 * Classe définissant les comportement commun des entités
 */
class Entity implements \JsonSerializable
{

    /**
     * @var int|null id 
     */
    private ?int $id = null;

    /**
     * @var array Property exclude to Json Serialize 
     */
    private $excludeSerializePropName = [];

    /**
     * Get id
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     * @param int|null $id
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }


    /**
     * Getter magique
     *
     * @param string $name
     * 
     * @return mixed
     * 
     */
    public function __get(string $name)
    {
        //return $this->$name;  ! interdit on respect le developpeur.. on passe par le getter :!!
        if (method_exists($this, 'get' . ucfirst($name)))
            return $this->{'get' . ucfirst($name)}();
        else
            throw new ActionNotFoundException('La méthode get' . ucfirst($name) . '() n\'existe pas !');
    }

    /** Setter magique. Sert à hydrater l'entité avec les data automatiquement en forçant l'appel au setter
     * Les colonnes dans la data sont préfixées avec la première lettre de l'entité. La propriété n'est donc pas trouvée
     * dans l'entité. __set et donc appelé... on retrouve la propriété en supprimant le prefixe !
     * On en profite pour convertir les données essentiel (DATE et relation).
     * @param string $name
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        //$this->$name = $value; ! interdit on respect le developpeur.. on passe par le setter :!!
        /** Pour ne pas développer des classe d'hydratation pour les entités DATA -> Entity
         * on met en place un système automatique en préfixant les colonnes en BDD
         * et en les suffixant avec _at pour les date et _id pour les relations.
         * Ce n'est pas "le plus modulable" mais ça fonctionne pour notre cas.
         */

        // On récupère le nom de la classe enfant qui utilise cette méthode
        $classPart = explode('\\', get_class($this));
        // la première lettre de la classe en minuscule est le préfixe utilisé pour les colonnes dans la Data
        $prefixe = lcfirst($classPart[count($classPart) - 1][0]) . '_';
        // on enlève le préfixe pour que le nom de la colonne corresponde au nom de la propriété de l'entité
        $name = str_replace($prefixe, '', $name);

        // Si on a un suffixe _at : on converti la valeur en objet DateTime pour le transmettre au setter
        if (strpos($name, '_at')) {
            $name = str_replace('_at', 'At', $name);
            if ($value !== null)
                $value = new \DateTime($value);
        }

        // ENTITY : load related entity. Si on a un suffixe _id, on charge l'entité associé pour la passer au setter
        if (strpos($name, '_id')) {
            $name = str_replace('_id', '', $name);

            // Load entity with modele
            $modelName = 'App\Model\\' . ucfirst($name) . 'Model';
            $model = new $modelName();
            $value = $model->find((int) $value);
        }

        // Si le setter existe on l'appel avec la valeur
        if (method_exists($this, 'set' . ucfirst($name)))
            $this->{'set' . ucfirst($name)}($value); // Exemple : setFirstname($value)
        //else
        //throw new ActionNotFoundException('La méthode set' . ucfirst($name) . '() n\'existe pas !');
    }

    /**
     * Permet de sérialisé l'entité en JSON !
     *
     * @return mixed
     */
    public function jsonSerialize(): mixed
    {

        $reflect = new \ReflectionClass($this);
        $props   = $reflect->getProperties();

        $array = ['id' => $this->getId()];

        $classPart = explode('\\', get_class($this));
        $className = lcfirst($classPart[count($classPart) - 1]);

        foreach ($props as $prop) {


            $getter = 'get' . ucfirst($prop->name);

            // Exclude propname that is define inside entity excludeSerializePropName
            if (in_array($prop->name, $this->excludeSerializePropName))
                continue;

            $value = $this->$getter();

            if (gettype($value) == 'object' && get_class($value) == 'Nienfba\Framework\EntityCollection')
                $value = URL . "get/{$prop->name}/{$className}/{$this->getId()}";

            if (gettype($value) == 'object' && is_subclass_of($value, 'Nienfba\Framework\Entity'))
                $value = URL . "get/{$prop->name}/{$value->getId()}";

            $array[$prop->name] = $value;
        }



        return $array;
    }

    /** Permet de renvoyer un tableau pour structurer une requête d'hydratation  des DATA
     * ['colonne'=>'value','colonne'=>'value']
     * @param void
     * @return array
     */
    public function getPropertyHydrateData(Model $model): array
    {

        /** Get Prefixe from model */
        $prefixe =  $model->getPrefixe();

        /** On utilise ReflectionClass pour lire toutes les propriétés */
        $reflect = new \ReflectionClass($this);
        $props   = $reflect->getProperties();

        $propsArray = [];

        // l'id est privée dans le parent on l'ajoute manuellement
        $propsArray[$prefixe . 'id'] = $this->getId();

        foreach ($props as $index => $prop) {

            $getter = "get" . ucfirst($prop->name);
            $value = $this->$getter();

            /** On ne retourne pas les EntityCollection */
            if (gettype($value) == 'object' && get_class($value) == 'Nienfba\Framework\EntityCollection')
                continue;


            $propsArray["{$prefixe}{$prop->name}"] = $value;
        }

        return $propsArray;
    }

    /** Save entity in DATA using Model 
     * It's a shortcut to model->save method
     * 
     * @param void
     * @return void
     */
    public function save()
    {
        // On récupère le nom de la classe enfant qui utilise cette méthode
        $classPart = explode('\\', get_class($this));

        $entityClassName = $classPart[count($classPart) - 1];

        /** On appel la méthode Save du modele associé */
        $modelName = 'App\Model\\' . $entityClassName . 'Model';
        $model = new $modelName();
        $model->save($this);
    }

    /**
     * Get property exclue to Json Serialize
     */
    public function getExcludeSerializePropName(): array
    {
        return $this->excludeSerializePropName;
    }

    /**
     * Set property exclue to Json Serialize
     */
    public function setExcludeSerializePropName(array $excludeSerializePropName): self
    {
        $this->excludeSerializePropName = $excludeSerializePropName;

        return $this;
    }
}
