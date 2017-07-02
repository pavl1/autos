<?php

namespace App;

/**
 * Theme customizer
 */
add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {
    // Add postMessage support
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->selective_refresh->add_partial('blogname', [
        'selector' => '.brand',
        'render_callback' => function () {
            bloginfo('name');
        }
    ]);
});

/**
 * Customizer JS
 */
add_action('customize_preview_init', function () {
    wp_enqueue_script('sage/customizer.js', asset_path('scripts/customizer.js'), ['customize-preview'], null, true);
});

add_action('admin_menu', function() {
    add_menu_page('Autos', 'Autos title', 'administrator', 'autos.php', function() { ?>
        <div class="wrap">
        <h2>Your Plugin Name</h2>

        <form method="post" action="options.php">
            <?php settings_fields( 'baw-settings-group' ); ?>
            <table class="form-table">
                <tr valign="top">
                <th scope="row">New Option Name</th>
                <td><input type="text" name="new_option_name" value="<?php echo get_option('new_option_name'); ?>" /></td>
                </tr>

                <tr valign="top">
                <th scope="row">Some Other Option</th>
                <td><input type="text" name="some_other_option" value="<?php echo get_option('some_other_option'); ?>" /></td>
                </tr>

                <tr valign="top">
                <th scope="row">Options, Etc.</th>
                <td><input type="text" name="option_etc" value="<?php echo get_option('option_etc'); ?>" /></td>
                </tr>
            </table>

            <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
            </p>

        </form>
        </div>
    <?php
    });
});
