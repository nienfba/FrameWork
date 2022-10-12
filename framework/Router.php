<?php

namespace Nienfba\Framework;

use Nienfba\Framework\Exception\ActionNotFoundException;
use Nienfba\Framework\Exception\ControllerNotFoundException;

class Router {

    /**
     * @var string controller short Name
     */
    private $controller;

    /**
     * @var string action (method) in controller
     */
    private $action;

    /**
     * @var array array of params to sending to action
     */
    private $params;

    /** Construct
     * 
     */
    public function __construct() {
        $this->controller = 'App\Controller\\'.ucfirst((Http::get('c') ?? DEFAULT_CONTROLLER)).'Controller';

        $this->action = Http::get('a') ?? DEFAULT_ACTION;

        $this->generateParams();

        $this->validateRoute();
    }

    private function generateParams() {
        $this->params = [];
        $paramsFromUrl = Http::getAll(['a','c']);

        if (isset($paramsFromUrl['params'])) {
            $this->params = explode('/', $paramsFromUrl['params']);
            unset($paramsFromUrl['params']);
        }
        
        $this->params = [...$this->params,...$paramsFromUrl];
    }

    /** Validate routing
     * 
     */
    private function validateRoute(string $controller = null, string $action = null) {
        $controller =  $controller ?? $this->controller;
        $action = $action ?? $this->action;
        if(!class_exists($this->controller))
            throw new ControllerNotFoundException("Le controller {$this->controller} n'a pas été trouvé !" );

        if (!method_exists($this->controller, $this->action))
            throw new ActionNotFoundException("Le controller {$this->controller} n'a pas de méthode {$this->action} !");
    }

    /** Generate Path route to Controller  / Action / param 
     * @param string|null controller 
     * @param string|null action
     * @param array paramètre de la route
     */
    public function path(?string $controller = null, ?string $action = null, ?array $params = []): string
    {
        if (USE_REWRITE) {
            $url = URL;
            if ($controller !== null) {
                $url .= $controller;
            } else {
                $url .= DEFAULT_CONTROLLER;
            }

            if ($action !== null) {
                $url .= '/' . $action;
            } else {
                $url .= '/' . DEFAULT_ACTION;
            }

            if ($params !== null) {
                foreach ($params as $index => $param) {
                    $url .= '/' . $param;
                }
            }
            //$url .= '/';
        } else {
            $url = 'index.php?c=';
            if ($controller !== null) {
                $url .= $controller;
            } else {
                $url .= DEFAULT_CONTROLLER;
            }

            if ($action !== null) {
                $url .= '&a=' . $action;
            } else {
                $url .= '&a=' . DEFAULT_ACTION;
            }

            if ($params !== null) {
                $i=0;
                foreach ($params as $index => $param) {
                    $url .= '&params=' . $param;
                    if (++$i < count($params))
                        $url .= '/';
                }
            }
        }

        return $url;
    }


    /**
     * Get controller short Name
     *
     * @return  string
     */
    public function getController()
    {
        return $this->controller;
    }

    /**
     * Set controller short Name
     *
     * @param  string  $controller  controller short Name
     *
     * @return  self
     */
    public function setController(string $controller)
    {
        $this->controller = $controller;

        return $this;
    }

    /**
     * Get action (method) in controller
     *
     * @return  string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set action (method) in controller
     *
     * @param  string  $action  action (method) in controller
     *
     * @return  self
     */
    public function setAction(string $action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get array of params to sending to action
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * Set array of params to sending to action
     */
    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }
}