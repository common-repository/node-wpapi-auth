<?php

// step 1: Create a function that contains the menu-building code
function easy_js_wpapi_plugin_menu() {
    // create an options level sub menu
    add_options_page(' EASY-JS-WPAPI-HANDLE | Basic WP-Authentication', 'Basic WP-Authentication',
        'manage_options', 'easy-js-wpapi', 'easy_js_wpapi_plugin_options');
    // call register_settings function
    add_action('admin_init', 'register_easy_js_wpapi_plugin_options');
}


function register_easy_js_wpapi_plugin_options() {
    // register plugin settings
    register_setting('easy_js_wpapi_plugin_options_group', 'admin_username');
    register_setting('easy_js_wpapi_plugin_options_group', 'admin_password');
    register_setting('easy_js_wpapi_plugin_options_group', 'admin_state');
}

// step 2: Register the menu function using the admin_menu action hook
add_action('admin_menu', 'easy_js_wpapi_plugin_menu');

// step 3: Create the HTML Output for the page(screen) displayed when the menu item is clicked
function easy_js_wpapi_plugin_options() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
    }

    // Now display the settings editing screen header

    // Check if user is logged in and pass the result of
    // to the 'admin_state' settings option
    if ( is_user_logged_in() ) {
        $output = "true";
    } else {
        $output = "false";
    }

    update_option( 'admin_state', $output );

    // settings form
    ?>

    <div class="wrap" id="opt-wrap">
        <h2 id='easy-js-wpapi_title'><i class="fa fa-universal-access fa-fw faa-spin animated"></i>
            EASY-JS-WPAPI Client HANDLE |<span>Basic WP-Authentication</span></h2>

        <form name="form1" action='options.php' method='post'>

            <?php settings_fields( 'easy_js_wpapi_plugin_options_group' ); ?>

            <?php do_settings_sections( 'easy_js_wpapi_plugin_options_group' ); ?>

            <table id="opt-table">
                <tr>
                    <td id="bauth"><i id="lock" class="fa fa-lock fa-fw faa-pulse animated"></i>
                        <i id="unlock" class="fa fa-unlock fa-fw faa-pulse animated"></i>
                    </td>
                    <td id="usr_pw"><p id='easy-js-wpapi_usr'>
                            <input placeholder="admin username" type="text" name="admin_username" id="admin_usr"
                                   value="<?php echo esc_attr( get_option( 'admin_username' ) ) ?>"
                            />
                        </p>
                        <p id='easy-js-wpapi_pw'>
                            <input placeholder="admin password" type="text" name="admin_password"
                                   value="<?php echo esc_attr( get_option( 'admin_password' ) ) ?>"
                            />
                        </p>
                    </td>
                </tr>
            </table>

            <p>
                <input id="admin_state" type="hidden" disabled="disabled" name="admin_state"
                       value="<?php echo esc_attr( get_option( 'admin_state' ) ) ?>"/>
            </p>

            <div id='easy-js-wpapi_submit'>
                <?php submit_button( __( 'Save' ), 'opt_btn', 'save', 'true' ); ?>
            </div>
        </form>

    </div>

    <?php

}


