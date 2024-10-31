<?php
/**
 * Plugin Name: EASY-JS-WPAPI Client HANDLE for the WordPress REST-API
 * Author: Oluwasegun Somefun
 * Description: Easily talk with the WordPress REST-API to access and display your WordPress (model) data without rest-authentication errors when creating Javascript Themes for consistent views by all users/clients accessing your WordPress site.
 * Author URI: https://somefunagba.github.io/easyjs-wpapi-rest-client-handle/
 * Version: 2.0
 * License: GPL2+
 **/

// includes the options and functions section of this plugin.
require_once(dirname(__FILE__) . '/functions.php');
require_once(dirname(__FILE__) . '/options.php');


// Add Settings link to this plugin's Pane on Plugin Dashboard
function easy_js_wpapi_plugin_add_settings_link( $links ) {
    $settings_link = '<a href="options-general.php?page=easy-js-wpapi">' . __( 'Settings' ) . '</a>';
    //array_push( $links, $settings_link );
    array_unshift($links, $settings_link);
    return $links;
}

$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'easy_js_wpapi_plugin_add_settings_link' );


/**
 * Start: Modified WordPress HTTP Basic Authentication.
 **/

function the_wp_basic_auth_handler( $user ) {
    global $basic_wp_auth_error;

    $basic_wp_auth_error = null;


    // Don't authenticate twice
/*    if ( ! empty( $user ) ) {
        return $user;
    }*/

    // We want to authenticate again if user is not an admin, so we uncommented the immediate 'if' block above

    // Note that setting the current user does not log in that user.
    // Here we will only reset the current user to an admin and not log the admin in.
    $usr = get_user_by('login', get_option( 'admin_username' ));
    $user_id = $usr->ID;

    $new_user = get_user_by( 'id', $user_id );

    if( $new_user ) {
        wp_set_current_user( $user_id, $user->user_login );
        // to sign in the set current user : uncomment the next two lines
        //wp_set_auth_cookie( $user_id ); // sets a new login cookie
        //do_action( 'wp_login', $n_user->user_login ); // signs the new current user in
    }

    // Confirm that our current user to be authenticated is set
    if ( !isset( $_SERVER['PHP_AUTH_USER']) ) {
        return $user;
    }

    $username = get_option('admin_username');
    $password = get_option('admin_password');

    /**
     * In multi-site WP installations, wp_authenticate_spam_check filter uses authentication and calls
     * get_currentuserinfo which in turn calls the determine_current_user filter. This leads to infinite
     * recursion and a stack overflow unless we do this:
     */

    // remove this current function from the determine_current_user filter used during authentication
    remove_filter( 'determine_current_user', 'the_wp_basic_auth_handler', 20 );

    $user = wp_authenticate( $username, $password );

    // add this function to the filter again
    add_filter( 'determine_current_user', 'the_wp_basic_auth_handler', 20 );

    if ( is_wp_error( $user ) ) {
        $basic_wp_auth_error = $user;
        return null;
    }

    $basic_wp_auth_error = true;

    return $user->ID;
}
add_filter( 'determine_current_user', 'the_wp_basic_auth_handler', 20 );
// The default filters use this to determine the current user from the request’s cookies, if available.
// Returning a value of false will effectively short-circuit setting the current user.
// Parameter: $user_id(int|bool) User ID if one has been determined, false otherwise.

function basic_auth_error($error) {

    // Pass-through other errors. $error = null
    if ( ! empty( $error ) ) {
        return $error;
    }

    global $basic_wp_auth_error;

    return $basic_wp_auth_error;
}
add_filter( 'rest_authentication_errors', 'basic_auth_error' );
// This is used to pass a WP_Error from an authentication method back to the API.
// (WP_Error|null|bool) WP_Error if authentication error, null if authentication method wasn't used,
// true if authentication succeeded.

/**
 * End: Modified JSON Basic Authentication.
 **/

// -----------------------------------------------------------------------


/**
 * Register and Enqueue the NODE-WPAPI pre-built wpapi.js script;
 * Register, Localize and Enqueue the first-party script
 **/

// gets the role of the current signed in user
function curr_userAuth() {
    $current_user = wp_get_current_user();
    $user_roles = $current_user->roles[0];
    if ($user_roles == "administrator"){
        return TRUE;
    }
    return FALSE;
}

add_action('wp_enqueue_scripts', function () {

    // Tell WordPress about the location of the wpapi script
    // Identify it as "wpapi"

    wp_register_script('wpapi',
        plugin_dir_url(__FILE__) . 'wpapi.min.js',
        array(), false, true
    );

    // Register our own 'easy-js-wpapi.js' first-party script which depends on 'wpapi'
    wp_register_script('easy-js-wpapi', plugin_dir_url(__FILE__) . 'easy-js-wpapi.js',
        array('wpapi', 'jquery'), false, true);

    wp_localize_script( 'easy-js-wpapi', 'WP_API_Settings',
        array(
            'userstate' => is_user_logged_in() ? get_option('admin_state') : 'false',
            'root'     => esc_url_raw( rest_url() ),
            'nonce' => wp_create_nonce('wp_rest'),
            'username' => get_option('admin_username'),
            'password' => get_option('admin_password'),
            'currUserRole' => curr_userAuth() ? 'true': 'false'
        )
    );

    // Enqueue our easy-js-wpapi.js script
    wp_enqueue_script('easy-js-wpapi');

});