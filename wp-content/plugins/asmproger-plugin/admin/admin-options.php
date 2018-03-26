<form method="POST" action="options.php">
    <?php
        settings_fields( 'asmp_options' );  // название группы опций - register_setting( $option_group )
        do_settings_sections( 'asmp_options' ); // slug страницы на которой выводится форма
        submit_button();
    ?>
</form>