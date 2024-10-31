<?php
/**
 * First Created by Oluwasegun Somefun
 * Date: 9/17/17
 * Time: 7:44 PM
 */

// Enqueue styling for this plugin's options page
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style( 'easy_js_wpapi_options', plugins_url( 'options.css', __FILE__ ) );
});


// Enqueue styling for css styles used by this plugin
add_action('admin_enqueue_scripts', function () {

    wp_enqueue_style( 'fa-easy_js_wpapi',
        plugins_url( 'font-awesome/css/font-awesome.min.css', __FILE__ ) );

    wp_enqueue_style( 'faa-easy_js_wpapi',
        plugins_url( 'font-awesome-animation/font-awesome-animation.min.css', __FILE__ ) );

});

// Check if user name and password entered in the options page is valid for a valid user
function basic_auth() {
   $check = wp_authenticate( get_option( 'admin_username' ), get_option( 'admin_password' ) );
   return is_wp_error($check);
}

// Check if the user is an administrator
function admin_auth() {
    $usr = get_user_by('login', get_option( 'admin_username' ));
    $usr_id = $usr->ID;
    $usrinfo = get_userdata($usr_id);
    $roles = implode(', ', $usrinfo->roles);
    if($roles == "administrator"){
        return TRUE;
    }
    return FALSE;
}

// Enqueue options.js script for our settings page: options.php
add_action('admin_enqueue_scripts', function () {

    wp_enqueue_script('jquery');

    // Register our 'options-js-wpapi.js'
    wp_register_script( 'options-js-wpapi', plugin_dir_url( __FILE__ ) . 'options.js',
        array( 'jquery'), false, true);

    wp_localize_script( 'options-js-wpapi', 'EasyJS_WPAPI_Options',
        array(
            'userState' => basic_auth() ? 'false' : 'true',
            'adminState' => admin_auth() ? 'true' : 'false'
        )
    );

    // Enqueue our options-js-wpapi.js script
    wp_enqueue_script( 'options-js-wpapi' );

});

/*function debug_console($data) {
    $out = $data;
    if( is_array($out)) {
        $out = implode(',', $out);
    }
    echo "<script>console.log('DebugPHPObjects:" . $out . " ');</script>";
}
debug_console("You");*/