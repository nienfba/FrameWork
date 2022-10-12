<?php

namespace Nienfba\Framework;

use Nienfba\Framework\Exception\ControllerNotFoundException;


class Application {

    /**
     * @var Routeur Le routeur 
     */
    private $routeur;

    /**
     * @var String Le nom qualifié du contoller a appelé fourni par le routeur
     */
    private $controller;

    private $response;

    /**
     * @var iDataStorage L'objet DataStorage utilisé dans l'application pour la persistence des données
     */
    private static $db = null;

    /** Constructeur */
    public function __construct() {
       
        /** On charge la classe pour gérer la persistence */
        if (!empty(STORAGE)) {
            $storage = 'Nienfba\Framework\DataStorage'.ucfirst(STORAGE);
            self::$db = new $storage();
        }
        
        try {
            /** Appel du routeur */
            $this->router = new Router();

            /** On exécute l'application */
            $this->run();
        } catch (ControllerNotFoundException $e) {
            http_response_code(500);
            echo 'Error 500 :' . $e->getMessage();
            echo $e->getTraceAsString();
        } catch (\Exception $e) {
            http_response_code(500);
            echo 'Error 500 :' . $e->getMessage();
        }
    }

    /** Chargement du controller et de la méthode 
     * @param void
     * @return void 
    */
    private function run() {
       
        // Excecute le controller 
        $controller = $this->router->getController();
        $this->controller = new $controller($this);

        // Excecute la méthode et récupère la réponse
        $method = $this->router->getAction();

        $this->response = $this->controller->$method(...$this->router->getParams());

        // Sending response to Buffer output
        $this->sendResponse();
       
    }

    /** Shortcut to Router path méthod
     * @param void
     * @return void 
     */
    public function path(?string $controller = null, ?string $action = null, ?array $params = null) : string {
        return $this->router->path($controller, $action, $params);
    }

    /** Envoie la réponse en fonction du type */
    private function sendResponse() {
        echo $this->response;
    }

    /** Fourni la statique contenant le DataStorage
     * 
     */
    public static function getDataStorage() {
        return self::$db;
    }


}