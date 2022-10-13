<?php

namespace Nienfba\Framework;

class Renderer
{
    const DEFAULT_LAYOUT = 'layout.front';
    const VIEW_EXTENSION = '.phtml';

    protected $view;

    protected $layout;

    protected $app;


    public function __construct(Application $app)
    {
        $this->layout = self::DEFAULT_LAYOUT;

        $this->app = $app;
    }


    /**
     * Render an HTML Response
     *
     * @param string $view la vue utilisée
     * @param array $params les paramètres (var) passés à la vue
     * 
     * @return void
     * 
     */
    public function render(string $view, array $params = [])
    {
        extract($params);

        // Creating shortcut var for APPLICATION
        $app = $this->app;

   

        /** Start buffering view */
        ob_start();

        require(PATH_VIEWS.$view);

        /** Getting view content to include un Layout */
        $viewContent = ob_get_clean();

        /** Getting extended Layout */
        if(isset($extends) && $extends !== false)
            require(PATH_VIEWS.$extends);
        else
            echo $viewContent;
 
        exit();
    }

    /**
     * Render an Json Response
     *
     * @param array $params
     * 
     * @return void
     * 
     */
    public function renderJson(array $params)
    {
        header('Content-Type: application/json');
        echo json_encode($params);
        exit();
    }

    /** Setter du layout
     * @param string $layout le nom du layout
     * @return self
     */
    public function setLayout(string $layout):self
    {
        $this->layout = $layout;

        return $this;
    }

}