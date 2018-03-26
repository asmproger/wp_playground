ASMPROGER PLUGIN ADMIN INDEX
<form method="post" action="options.php">
    <?php wp_nonce_field('update-options'); ?>

    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                New Option Name
            </th>
            <td>
                <input type="text" name="new_option_name" value="<?php echo get_option('new_option_name'); ?>"/>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Some Other Option</th>
            <td><input type="text" name="some_other_option" value="<?php echo get_option('some_other_option'); ?>"/>
            </td>
        </tr>
        <tr valign="top">
            <th scope="row">Options, Etc.</th>
            <td><input type="text" name="option_etc" value="<?php echo get_option('option_etc'); ?>"/></td>
        </tr>
    </table>
    <input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc"/>
    <input type="hidden" name="action" value="update"/>

    <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>"/>
    </p>
</form>