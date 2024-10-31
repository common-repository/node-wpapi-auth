/**
 * Author: Oluwasegun Somefun
 * Author URI: http://github.com/somefunagba
 *
 * Access the wpapi.js functionality through WPAPI global
 * Make 'wp' globally accessible for use in all WP scripts to access the WP REST-API
 **/

// This script will be localized by WP when it is enqueued

// Localization is used to inject a NONCE(unique number) into the script
// which is then used to authenticate with WP using a normal login cookie
// You will be logged in to WP within this script

// Basic Authentication uses the admin's username and password

var wp = function() {

    // get reply to know if the admin user is logged in

    var reply = WP_API_Settings.userstate;
    //console.log(reply);


    if (reply === 'true') {
        wp = new WPAPI({
            endpoint: WP_API_Settings.root,
            nonce: WP_API_Settings.nonce,
            auth: true
        });

    } else {
        wp = new WPAPI({
            endpoint: WP_API_Settings.root,
            username: WP_API_Settings.username,
            password: WP_API_Settings.password,
            auth: true
        });
    }

    return wp;

}();

wp.users().me().then(function (me) {
    console.log(me.name + '! ' + 'I am set to access the WP REST-API!');
});// You are now authenticated globally and can, read and write private data!

