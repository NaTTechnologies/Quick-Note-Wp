<?php
// Verifica si el usuario actual tiene permisos para ver esta página
if (!current_user_can('manage_options')) {
    wp_die(__('No tienes permisos para acceder a esta página.'));
}
?>

<div class="wrap">
    <h1>QuickNotes</h1>

    <style>
        form{
            margin-bottom: 10px;
        }
    </style>

    <!-- Filtro de usuarios -->
    <form class="" method="GET" action="">
        <input type="hidden" name="page" value="<?php echo $_GET['page']; ?>">
        <select name="user_id" id="user_id">
            <option value="">Todos los usuarios</option>
            <?php foreach ($users as $user): ?>
                <option value="<?php echo $user->ID; ?>" <?php selected(isset($_GET['user_id']) ? $_GET['user_id'] : '', $user->ID); ?>>
                    <?php echo esc_html($user->display_name); ?>
                </option>

            <?php endforeach; ?>
        </select>
        <input type="submit" value="Filtrar" class="button">
    </form>

    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Contenido</th>
                <th>Usuario</th>
                <th>Fecha de creación</th>
                <th>Última actualización</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($notes as $note): ?>
                <tr>
                    <td>
                        <?php echo $note->id; ?>
                    </td>
                    <td>
                        <?php echo esc_html($note->title); ?>
                    </td>
                    <td>
                        <?php echo esc_html(wp_trim_words($note->content, 10, '...')); ?>
                    </td>
                    <td>
                        <?php echo esc_html(get_userdata($note->user_id)->display_name); ?>
                    </td>
                    <td>
                        <?php echo date_i18n(get_option('date_format'), strtotime($note->created_at)); ?>
                    </td>
                    <td>
                        <?php echo date_i18n(get_option('date_format'), strtotime($note->updated_at)); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Contenido</th>
                <th>Usuario</th>
                <th>Fecha de creación</th>
                <th>Última actualización</th>
            </tr>
        </tfoot>
    </table>

</div>