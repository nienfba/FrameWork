<?php

namespace Nienfba\Framework;

use Nienfba\Framework\Application;


/**
 * Classe controller : comportement commun des controller
 */
class Controller {

    /**
     * @var Renderer $db data access 
     */
    protected $renderer;

    /** 
     * @var Applicaton $app Accès à l'application
     */
    protected $app;


    /**
     * Constructeur
     */
    public function __construct(Application $app) {
        $this->renderer = new Renderer($app);

        $this->app = $app;
    }

    /** Shortcut to renderer render méthode for all Controller
     * @param string $view la vue utilisée
     * @param array $params les paramètres (var) passés à la vue
     * @param boo $noLayout si on utilise un layout pour le rendu ou directement la vue
     * 
     * @return void
     */
    public function render(string $view, array $params = []) {
        $this->renderer->render($view, $params);
    }

    /**
     * Shortcut to renderer Render an Json Response
     *
     * @param array $params
     * 
     * @return mixed
     * 
     */
    public function renderJson(array $params)
    {
       $this->renderer->renderJson($params);
    }

}