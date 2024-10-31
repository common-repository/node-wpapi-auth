(function ($) {

    // change the helper lock and unlock icons to
    // confirm if the username and password entered
    // is for a valid administrator user
    var auth = EasyJS_WPAPI_Options.userState;
    //console.log(auth);
    var vald = EasyJS_WPAPI_Options.adminState;
    //console.log(vald);
    if (auth === 'true' && vald === 'true') {
        $("#lock").hide(10);
        $("#unlock").show(10);
        $("#unlock").css('color', '#38bd38');
        console.log("Passed!");
        //console.log("ok");
    } else {
        $("#unlock").hide(10);
        $("#lock").show(10);
        $("#lock").css('color', '#f03636');
        console.log("Failed");
        //console.log("err");
    }

})(jQuery);