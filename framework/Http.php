<?php
namespace Nienfba\Framework;

class Http
{
    public static function get($index)
    {
        return filter_input(INPUT_GET,$index, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /** Get all Element indide my $_GET array
     * @param array $excludes excludes index
     * 
     * @return array array of values 
     */
    public static function getAll(array $excludes = []) : array
    {
        $params = [];
        
        foreach ($_GET as $param=>$value) {
            if (!in_array($param, $excludes) && !empty($value))
                $params[$param] = filter_input(INPUT_GET, $param, FILTER_SANITIZE_SPECIAL_CHARS);
        }

        return $params;
    }

    public static function post($index)
    {
        return filter_input(INPUT_POST, $index, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    public static function redirectToRoute(string $controller, string $action = null, array $params = array())
    {
        $app = new Application();
        header('Location: '.$app->path($controller, $action, $params));
        exit();
    }

    public static function redirect(string $url)
    {
        header('Location: ' . $url);
        exit();
    }
}