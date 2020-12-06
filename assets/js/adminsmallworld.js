$(document).ready(function () {
    (function ($) {
        //xoops_smallworld('body').on('click', '.share', function(e)
        // CLICK ADD MORE TIME
        $("body").on('click', '#smallworld_addtime_img', function () {
            var user_rel = $(this).attr('rel');
            $(this).prev('#smallworld_addmoretime').toggle();
            $(this).next("#smallworld_addtime_img").toggle();
            $("#smallworld_addmoretime_options" + user_rel).click(function () {
                var userid = $(this).attr('rel');
                var value = $(this).val();
                var newvalue = value * 60;
                var dataString = 'type=addtime' + '&userid=' + userid + "&amount=" + newvalue;

                if (newvalue == 0 | newvalue == '' | userid == '' | value == '') {
                    $(this).next("#smallworld_addtime_img").toggle();
                    $(this).prev('#smallworld_addmoretime').toggle();
                    return false;
                } else {
                    $(this).prev("#smallworld_addmoretime").fadeIn(800).html('<img id="ajaxloader" src="../assets/images/ajaxloader.gif" alt=""/>');
                    $.ajax({
                        type: "POST",
                        url: "admintool.php",
                        data: dataString,
                        cache: false,
                        success: function (html) {
                            $(this).next("#ajaxloader").hide(2000);
                            $('#smallworld_admin_allusers').load('div_useradmin.php').fadeIn("slow");
                        }
                    });
                }
                return false;
            });
        });// END ADD MORE TIME


        // CLICK DELETE TIME
        $("body").on('click', '#smallworld_deletetime_img', function () {
            var id = $(this).attr('rel');
            var dataDelete = 'type=deletetime&deluserid=' + id;
            if (id != '') {
                $.ajax({
                    type: "POST",
                    url: "admintool.php",
                    data: dataDelete,
                    cache: false,
                    success: function (html) {
                        dataDelete = '';
                        id = '';
                        $('#smallworld_admin_allusers').load('div_useradmin.php').fadeIn("slow");

                    }
                });
            }
            return false;
        });// END DELETE TIME

        // CLICK DELETE User
        $("body").on('click', '#smallworld_accountdelete_img', function () {
            var userid = $(this).attr('rel');
            var username = $(this).attr('name');
            var dataDelete = 'type=deleteUser&deluserid=' + userid;
            if (userid != '') {
                if (confirm(SmallworldAdminSureDeleteUser + " " + username)) {
                    $.ajax({
                        type: "POST",
                        url: "admintool.php",
                        data: dataDelete,
                        cache: false,
                        success: function (html) {
                            $('#smallworld_admin_allusers').load('div_useradmin.php').fadeIn("slow");
                            alert(html);
                        }
                    });
                }
            }
            return false;
        });// END DELETE USER

        // Donation
        $(function () {
            $('body').on('click', '#smallworldDonate', function () {
                window.open('https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=WKFZBRBGMYKCA&lc=DK"&item_name=Culex%2edk&item_number=culex&currency_code=DKK&bn=PP%2dDonationsBF%3asp%2epng%3aNonHosted', 'blank',
                    'width=600, height=400, menubar=no, toolbar=no, scrollbars=yes');
            });
        });

    })(jQuery);
});

// On time reset in countdown reset div with updated data
function SmallworldCountdownliftOff() {
    jQuery('#smallworld_admin_allusers').load('div_useradmin.php').fadeIn("slow");
}


