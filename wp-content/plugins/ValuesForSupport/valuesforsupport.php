<?php
/**
 * Plugin Name: Support (podpora) related values
 * Plugin URI: 
 * Description: This plugin is to change the support (podpora) related client values.
 * Version: 1.0
 * Author: Mirozlav
 * Author URI: http://www.sloncompany.com
 */

require_once \WP_PLUGIN_DIR . '/constants.php';
add_action('admin_menu','valuesforsupport_register_admin_page');


function valuesforsupport_register_admin_page() {
    add_menu_page(
        "Hodnoty pre čast podpora", 
        "Hodnoty pre čast podpora", 
        "manage_options", 
        SUPPORT_VALUES_PG_NAME,
        "change_support_values_pg"
    );     
}

function change_support_values_pg() { ?>
    <div class="wrap">
        <h1>Hodnoty pre časť podpora</h1>
        <form method="POST" action="options.php">
        <?php
        settings_fields("support");
        do_settings_sections(SUPPORT_VALUES_PG_NAME);
        submit_button();
        ?>

        </form>
        <?php settings_errors(); ?>
    </div>
<?php } ?>

<?php


function register_support_inputs() 
{
    
    add_settings_section('default', NULL, function() {}, SUPPORT_VALUES_PG_NAME);
    register_setting("support", SUPPORTED_PROJECTS_NAME,  
        [
            "sanitize_callback" => function($val) {
                if (!is_numeric($val) || $val < 0 || floor($val) != $val) {
                    add_settings_error(
                        SUPPORTED_PROJECTS_NAME,
                        NULL,
                        __('Zadajte "Počet podporených projektov" ako celé kladné číslo bez medzier.', 'wpse'),
                        'error',
                    );
                    return 0;
                }
                return floor($val);
            }
        ]
    );
    register_setting("support", SUPPORTED_PROJECTS_VALUE_NAME, 
        [
            "sanitize_callback" => function($val) {
                if (!is_numeric($val) || $val < 0) {
                    add_settings_error(
                        SUPPORTED_PROJECTS_VALUE_NAME,
                        NULL,
                        __('Zadajte "Hodnotu podporovaných projektov" ako kladné číslo bez medzier.', 'wpse'),
                        'error',
                    );
                    return 0;
                }
                return round($val,2);
            }
        ]
    );

    add_settings_field(
        SUPPORTED_PROJECTS_NAME, 
        "Počet podporených projektov", function() {
            echo "<input type='text' name='" . SUPPORTED_PROJECTS_NAME . "' value='". 
                get_option(SUPPORTED_PROJECTS_NAME). "' />";
        }, 
        SUPPORT_VALUES_PG_NAME
    );
    add_settings_field(
        SUPPORTED_PROJECTS_VALUE_NAME, 
        "Hodnota podporených projektov", function() {
            echo "<input type='text' name='" . SUPPORTED_PROJECTS_VALUE_NAME. "' ".
                "value='". get_option(SUPPORTED_PROJECTS_VALUE_NAME). "' />";
        }, 
        SUPPORT_VALUES_PG_NAME
    );
    
}
add_action("admin_init", "register_support_inputs");



