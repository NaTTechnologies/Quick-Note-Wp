<?php
/*
Plugin Name: QuickNotes WP
Description: Un plugin de notas rápidas para WordPress con barra flotante y operaciones CRUD utilizando AJAX.
Version: 1.0
Author: NaT Technologies
*/

// Activación y desactivación del plugin
register_activation_hook(__FILE__, 'quicknotes_wp_activate');
register_deactivation_hook(__FILE__, 'quicknotes_wp_deactivate');

function quicknotes_wp_activate() {
    global $wpdb;

    $table_name = $wpdb->prefix . "quicknotes";
    $charset_collate = $wpdb->get_charset_collate();

    // Comprobar si la tabla ya existe
    if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
        // Crear la tabla si no existe
        $sql = "CREATE TABLE {$table_name} (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) unsigned NOT NULL,
            title varchar(255) NOT NULL,
            content text NOT NULL,
            created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
        ) {$charset_collate};";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

function quicknotes_wp_deactivate() {
    // Aquí puedes agregar código para eliminar la tabla de la base de datos, si es necesario
}

// Enlazar archivos CSS y JavaScript
function quicknotes_wp_enqueue_scripts() {
    wp_enqueue_style('quicknotes-wp', plugin_dir_url(__FILE__) . 'assets/css/quicknotes-wp.css');
    //wp_enqueue_script('quicknotes-wp', plugin_dir_url(__FILE__) . 'assets/js/quicknotes-wp.js', array('jquery'), false, true);
    //wp_localize_script('quicknotes-wp', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));

    wp_register_script('quicknotes-wp-main', plugins_url('assets/js/quicknotes-wp.js', __FILE__), array('jquery'), '1.0.0', true);
    wp_enqueue_script('quicknotes-wp-main');

    // Agrega datos de AJAX al archivo JS
    wp_localize_script('quicknotes-wp-main', 'quicknotes_ajax_object', array(
        'ajax_url' => admin_url('admin-ajax.php')
    ));

    // Enlaza el archivo CSS de jQuery Rich Text
    wp_enqueue_style('jquery-rich-text', plugins_url('assets/css/jquery.richtext.min.css', __FILE__));

    // Enlaza el archivo JavaScript de jQuery Rich Text
    wp_enqueue_script('jquery-rich-text', plugins_url('assets/js/jquery.richtext.min.js', __FILE__), array('jquery'), '1.0.0', true);

}

add_action('wp_enqueue_scripts', 'quicknotes_wp_enqueue_scripts');

function quicknotes_wp_admin_menu() {
    add_menu_page(
        'QuickNotes',
        'QuickNotes',
        'manage_options',
        'quicknotes-admin',
        'quicknotes_wp_admin_page',
        'dashicons-welcome-write-blog',
        20
    );
}

function quicknotes_wp_admin_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . "quicknotes";

    // Si se seleccionó un usuario en el filtro, añade la condición a la consulta
    $user_id = isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0;
    $user_condition = $user_id > 0 ? $wpdb->prepare("WHERE user_id = %d", $user_id) : "";

    // Obtiene todas las notas de todos los usuarios o filtradas por usuario
    $notes = $wpdb->get_results("SELECT * FROM {$table_name} {$user_condition} ORDER BY created_at DESC");

    // Obtiene todos los usuarios para utilizar en el filtro
    $users = get_users(array('fields' => array('ID', 'display_name')));

    include(plugin_dir_path(__FILE__) . 'admin-page.php');
}

add_action('admin_menu', 'quicknotes_wp_admin_menu');


// Cargar archivos de funciones
require_once(plugin_dir_path(__FILE__) . 'includes/functions.php');
?>
