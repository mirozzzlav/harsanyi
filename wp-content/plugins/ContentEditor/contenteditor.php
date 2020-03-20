<?php
/**
 * Plugin Name: Harsanyi Content Editor
 * Plugin URI: 
 * Description: Edit texts and options of some parts of the Harsanyi website (mainly homepage)
 * Version: 1.0
 * Author: Mirozlav
 * Author URI: http://www.sloncompany.com
 */

require_once \WP_PLUGIN_DIR . '/constants.php';

function _blockeditor_off() {
    global $post;
    if (empty($post) || $post->post_name !== CONTENT_SETTINGS_PG_NAME) {
        return true;
    }
    
    return false;
}
add_filter('use_block_editor_for_post_type', '_blockeditor_off');


function custom_editor_settings( $settings, $editor_id ){
    $settings['_content_editor_dfw'] = false;
    $settings['media_buttons'] = false;
    $settings['tinymce'] = false;
    $settings['quicktags'] = false;
    return $settings;
}
add_filter( 'wp_editor_settings', 'custom_editor_settings', 10, 2 );

function plg_register_plugin_scripts() {
    wp_enqueue_script( 'json_editor', plugin_dir_url( __FILE__ ) . 'jquery.json-editor.min.js', [], '', true);
    wp_enqueue_script( 'script', plugin_dir_url( __FILE__ ) . 'script.js?'.rand(), ['json_editor'], '', true );
}
add_action('admin_init','plg_register_plugin_scripts');
