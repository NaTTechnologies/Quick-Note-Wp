<?php
// Verificar si el usuario ha iniciado sesión
function quicknotes_wp_check_user_logged_in()
{
    return is_user_logged_in();
}

// Generar HTML para la barra flotante y el panel de notas
function quicknotes_wp_output()
{
    if (!quicknotes_wp_check_user_logged_in())
        return;
    ?>

    <div class="quicknotes-wrapper">
        <div class="quicknotes-float-btn">
            <span class="dashicons dashicons-book" style="font-size: 24px;"></span>
        </div>
        <div class="quicknotes-modal">
            <strong class="quicknotes-title">Notas rápidas</strong>
            <div class="quicknotes-add-note-form" style="display: none;">
                <input type="text" class="quicknotes-note-title" placeholder="Título de la nota">
                <textarea class="quicknotes-note-content" contenteditable="true" placeholder="Contenido de la nota..." data-maxlength="1000"></textarea>
                <button class="quicknotes-save-note">Guardar nota</button>
            </div>

            <button class="quicknotes-add-note" style="margin-top: 10px;">Agregar nota</button>
            
            <div class="quicknotes-list">
                <?php
                // Aquí debes obtener las notas del usuario actual desde la base de datos y mostrarlas en una lista.
                
                ?>
            </div>
            
           
        </div>
    </div>
    <?php
}


add_action('wp_footer', 'quicknotes_wp_output');

function quicknotes_wp_load_quicknotes()
{
    global $wpdb;

    // Comprobar si el usuario está conectado
    if (!is_user_logged_in()) {
        //wp_send_json_error('Debe iniciar sesión para ver sus notas.');
        return;
    }

    $user_id = get_current_user_id();
    $table_name = $wpdb->prefix . "quicknotes";
    $results = $wpdb->get_results($wpdb->prepare("SELECT id, user_id, title, content, DATE_FORMAT(created_at, '%%e de %%M de %%Y') AS created_at, updated_at FROM {$table_name} WHERE user_id = %d order by updated_at desc", $user_id));

    wp_send_json_success($results);
}

add_action('wp_ajax_load_quicknotes', 'quicknotes_wp_load_quicknotes');
add_action('wp_ajax_nopriv_load_quicknotes', 'quicknotes_wp_load_quicknotes');

function quicknotes_wp_add_quicknote()
{
    global $wpdb;

    // Comprobar si el usuario está conectado
    if (!is_user_logged_in()) {
        wp_send_json_error('Debe iniciar sesión para agregar notas.');
    }

    $user_id = get_current_user_id();
    $title = sanitize_text_field($_POST['title']);
    $content = sanitize_textarea_field($_POST['content']);

    $table_name = $wpdb->prefix . "quicknotes";
    $result = $wpdb->insert($table_name, array(
        'user_id' => $user_id,
        'title' => $title,
        'content' => $content
    )
    );

    if ($result) {
        wp_send_json_success('Nota agregada correctamente.');
    } else {
        wp_send_json_error('Error al agregar la nota.');
    }
}

// AJAX action hooks
add_action('wp_ajax_add_quicknote', 'quicknotes_wp_add_quicknote');
add_action('wp_ajax_nopriv_add_quicknote', 'quicknotes_wp_add_quicknote');


// Implementar funciones CRUD y AJAX para gestionar notas
function quicknotes_wp_create_note()
{
    // Aquí puedes agregar el código para crear una nota en la base de datos
}

add_action('wp_ajax_quicknotes_wp_create_note', 'quicknotes_wp_create_note');

//

