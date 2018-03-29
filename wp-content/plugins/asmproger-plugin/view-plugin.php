<?php
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Class ViewHelper
 */
class ViewHelper
{
    private static $_instance = null;

    private function __construct()
    {

    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new ViewHelper();
        }
        return self::$_instance;
    }

    public function getView(array $params = [], $view = '')
    {
        if (!$params) {
            die('invalid params');
        }
        if (!$view) {
            die('invalid template name');
        }

        $viewFile = plugin_dir_path(__FILE__) . "/views/{$view}.php";

        if (file_exists($viewFile)) {
            ob_start();
            include($viewFile);
            $content = ob_get_clean();

            foreach ($params as $k => $v) {
                $k = '{' . $k . '}';
                $content = str_replace($k, $v, $content);
            }

            return $content;
        } else {
            die('invalid name');
        }
    }
}