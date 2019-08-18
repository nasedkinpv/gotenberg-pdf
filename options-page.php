<?php
add_action('admin_menu', 'gotenberg_pdf_add_admin_menu');
add_action('admin_init', 'gotenberg_pdf_settings_init');


function gotenberg_pdf_add_admin_menu()
{

    add_options_page('Gotenberg Docker Settings', 'Gotenberg Docker Settings', 'manage_options', 'gotenberg_pdf', 'gotenberg_pdf_options_page');
}


function gotenberg_pdf_settings_init()
{

    register_setting('pluginPage', 'gotenberg_pdf_settings');

    add_settings_section(
        'gotenberg_pdf_pluginPage_section',
        __('Options', 'gotenberg'),
        'gotenberg_pdf_settings_section_callback',
        'pluginPage'
    );

    add_settings_field(
        'webserver_uri',
        __('Webserver uri', 'gotenberg'),
        'webserver_uri_render',
        'pluginPage',
        'gotenberg_pdf_pluginPage_section'
    );

    add_settings_field(
        'gotenberg_uri',
        __('Gotenberg container uri', 'gotenberg'),
        'gotenberg_uri_render',
        'pluginPage',
        'gotenberg_pdf_pluginPage_section'
    );

    add_settings_field(
        'query_for_print',
        __('Query for print view', 'gotenberg'),
        'query_for_print_render',
        'pluginPage',
        'gotenberg_pdf_pluginPage_section'
    );

    add_settings_field(
        'query_for_pdf',
        __('Query for pdf', 'gotenberg'),
        'query_for_pdf_render',
        'pluginPage',
        'gotenberg_pdf_pluginPage_section'
    );

    add_settings_field(
        'custom_post_type',
        __('Custom post type', 'gotenberg'),
        'custom_post_type_render',
        'pluginPage',
        'gotenberg_pdf_pluginPage_section'
    );
}


function webserver_uri_render()
{

    $options = get_option('gotenberg_pdf_settings');
    ?>
<input type='text' name='gotenberg_pdf_settings[webserver_uri]' value='<?php echo $options['webserver_uri']; ?>' placeholder="http://nginx:80/">
<?php

}


function gotenberg_uri_render()
{

    $options = get_option('gotenberg_pdf_settings');
    ?>
<input type='text' name='gotenberg_pdf_settings[gotenberg_uri]' value='<?php echo $options['gotenberg_uri']; ?>' placeholder="http://gotenberg:3000/">
<?php

}


function query_for_print_render()
{

    $options = get_option('gotenberg_pdf_settings');
    ?>
<input type='text' name='gotenberg_pdf_settings[query_for_print]' value='<?php echo $options['query_for_print']; ?>' placeholder="?print">
<?php

}


function query_for_pdf_render()
{

    $options = get_option('gotenberg_pdf_settings');
    ?>
<input type='text' name='gotenberg_pdf_settings[query_for_pdf]' value='<?php echo $options['query_for_pdf']; ?>' placeholder="?pdf">
<?php

}


function custom_post_type_render()
{

    $options = get_option('gotenberg_pdf_settings');
    ?>
<input type='text' name='gotenberg_pdf_settings[custom_post_type]' value='<?php echo $options['custom_post_type']; ?>' placeholder="yacht">
<?php

}


function gotenberg_pdf_settings_section_callback()
{

    echo __('For fully functional pdf creation you must have docker enviroment and gotenberg container running', 'gotenberg');
}


function gotenberg_pdf_options_page()
{

    ?>
<form action='options.php' method='post'>

    <h2>Gotenberg Docker Settings</h2>

    <?php
        settings_fields('pluginPage');
        do_settings_sections('pluginPage');
        submit_button();
        ?>

</form>
<?php

}
