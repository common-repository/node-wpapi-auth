/**
 * Author: Oluwasegun Somefun
 * Author URI: http://github.com/somefunagba
 *
 * Access the wpapi.js functionality through its global object 'WPAPI' as variable 'wp'
 * Make 'wp' globally accessible for use in all .js scripts in WordPress to access the WP REST-API
 **/

// This script will be localized by WordPress when it is enqueued

// Localization is used to inject a NONCE(one-time unique number) into the script
// which is then used to authenticate with WP using a normal login cookie

// Basic Authentication uses a dummy admin's username and password

var curr_user_role = WP_API_Settings.currUserRole;

var wp = function() {

    // get reply to know if the admin user is logged in

    var reply = WP_API_Settings.userstate;
    //console.log(reply);
    //console.log(curr_user_role);
    //console.log(WP_API_Settings.root + " " +  WP_API_Settings.username + " " + WP_API_Settings.password);


    if(reply === 'true' && curr_user_role === 'false'){ // when non-admin is logged in
        console.log("notadmin");
        //console.log(WP_API_Settings.root + " " +  WP_API_Settings.nonce);
        wp = new WPAPI({
            endpoint: WP_API_Settings.root,
            username: WP_API_Settings.username,
            password: WP_API_Settings.password,
            auth: true
        });
    } else {
        if (reply === 'true') {
            console.log("admin");
            wp = new WPAPI({// when admin is logged in
                endpoint: WP_API_Settings.root,
                nonce: WP_API_Settings.nonce,
                auth: true
            });
        } else if (reply === 'false') { // when all users are logged out
            console.log("client");
            wp = new WPAPI({
                endpoint: WP_API_Settings.root,
                username: WP_API_Settings.username,
                password: WP_API_Settings.password,
                auth: true
            });
        }
    }

    return wp;

}();

wp.users().me().then(function (me) {
    //console.log('Hi!' + me.name + '! ' + 'I am set to fully access the WP REST-API!');
    console.log("Hi! I am all set to talk with the WP REST-API.\nBest luck! on your WordPress Projects.");
});// You are now authenticated globally and can, read and write private data!

wp.settings().get().then(function (data) {
    // do something with returned data
    console.log("Site Name: " + data.title);
}).catch(function (err) {
    console.error(err);
});