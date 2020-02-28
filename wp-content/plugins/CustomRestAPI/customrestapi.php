<?php
/**
 * Plugin Name: Custom Rest Api
 * Plugin URI: 
 * Description: Adds some custom endpoints to wp-json REST API
 * Version: 1.0
 * Author: Mirozlav
 * Author URI: http://www.sloncompany.com
 */


/*ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);*/


spl_autoload_register( 'customrestapiAutoloader' );
function customrestapiAutoloader(String $className) 
{
    
    if (preg_match("/^CustomRestAPI\\\\(.+)/", $className, $matches)) {
        if (empty($matches[1])) {
            return;
        }
        $classFile = $matches[1];
        $classesDir = realpath(plugin_dir_path( __FILE__ )) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
        require_once $classesDir . $classFile . '.php';
    }
}

 add_action( 'rest_api_init', function () 
 {
    $api = new CustomRestAPI\RestController();
    $api->register_routes();
});