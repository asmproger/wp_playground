<form method="POST" action="options.php">
    <?php
        settings_fields('asmp_options');  // название группы опций - register_setting( $option_group )
        do_settings_sections('asmp_options'); // slug страницы на которой выводится форма
    ?>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">Show ISBN?</th>
            <td>
                <input type="checkbox" name="asmp_settings_show_isbn"
                       <?php echo WP_Asmproger_Plugin::checkShowISBN() ? 'checked' : ''; ?> />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Show author prefix?</th>
            <td>
                <input type="checkbox" name="asmp_settings_show_prefix"
                    <?php echo WP_Asmproger_Plugin::checkShowPrefix() ? 'checked' : ''; ?> />
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Author prefix</th>
            <td>
                <input type="text" name="asmp_settings_author_prefix"
                       value="<?php echo esc_attr(WP_Asmproger_Plugin::getAdminPrefix()); ?>" />
            </td>
        </tr>
    </table>
    <?php submit_button(); ?>
</form>