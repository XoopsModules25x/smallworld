/**
 * jQuery configs, selectors and functions for Xoops Smallworld
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * copyright       {@link https://xoops.org 2001-2017 XOOPS Project}
 * license         {@link http://www.fsf.org/copyleft/gpl.html GNU public license 2.0 or later}
 * author 2011     Culex - homepage.: http://culex.dk & email.: culex@culex.dk
 */
xoops_smallworld(function () {
    //Attach function for avatar
    Smallworld_attachAvatarOpen();

    // Get page url and page title (index.php)
    var smallworld_pageUrl = window.location.pathname;
    var smallworld_PageName = smallworld_pageUrl.substring(smallworld_pageUrl.lastIndexOf('/') + 1);

    // GET pop for statistics
    xoops_smallworld('#smallworld_statistics_lnk').on('click', function (e) {
        if (Smallworld_userHasProfile == 0) {
            alert(SmallworldDialogNotUser);
            return false;
        }
        e.preventDefault();
        if (xoops_smallworld('#smallworld_statistics_lnk').length) { // implies *not* zero
            xoops_smallworld('#smallworldStatsDiv').show();
            xoops_smallworld("#smallworldStatsDiv").load('stats.php');
            xoops_smallworld.colorbox({
                width: "75%",
                height: "28%",
                inline: true,
                onCleanup: function () {
                    xoops_smallworld('#smallworldStatsDiv').hide();
                },
                onClosed: function () {
                    xoops_smallworld('#smallworldStatsDiv').hide();
                },
                href: "#smallworldStatsDiv"
            });
        }
    });

    // GET pop for recentactivities
    xoops_smallworld('#smallworld_recentactivities').on('click', function (e) {
        if (Smallworld_userHasProfile <= 1) {
            alert(SmallworldDialogNotUser);
            return false;
        }
        e.preventDefault();
        var Smallworld_uname = xoops_smallworld(this).attr('rel');
        if (xoops_smallworld('#smallworld_recentactivities').length) { // implies *not* zero
            xoops_smallworld('#smallworld_recentactivitiesDiv').show();
            xoops_smallworld("#smallworld_recentactivitiesDiv").load('recentactivities.php?username=' + Smallworld_uname);
            xoops_smallworld.colorbox({
                width: "35%",
                height: "50%",
                inline: true,
                onCleanup: function () {
                    xoops_smallworld('#smallworld_recentactivitiesDiv').hide();
                },
                onClosed: function () {
                    xoops_smallworld('#smallworld_recentactivitiesDiv').hide();
                },
                href: "#smallworld_recentactivitiesDiv"
            });
        }
    });

    // Attach colorbox.js to selector in register.php and edit_profile.php
    // If other page / no presence of #smallworld_regform1 then exit function and continue
    xoops_smallworld(document).ready(function () {
        if (xoops_smallworld('#smallworld_regform1').length) { // implies *not* zero
            xoops_smallworld('#smallworld_regform1').show();
            xoops_smallworld("#smallworld_regform1").dialog({
                height: 'auto',
                width: 1150,
                modal: true,
                closeOnEscape: true,
                position: { my: "top", at: "center", of: window },
                open: function (event, ui) {
                    smallworld_DoValStart();
                    xoops_smallworld("input#realname").val();
                    xoops_smallworld(".ui-widget-overlay").css({
                        background: 'none repeat scroll 0 0 #222222',
                        opacity: 0.89
                    });
					xoops_smallworld('.ui-dialog').css({
						zIndex: '9999'
					});
                },
                beforeClose: function (event, ui) {
                    xoops_smallworld('#smallworld_regform1').hide();
                },
                close: function (event, ui) {
                    location.href = 'publicindex.php';
                }
            });
        }
    });

    // Function to make friend invitations form into ui dialog
    xoops_smallworld(function () {
        xoops_smallworld('#friendInvitations_box').css('display', 'none');
        if (Smallworld_hasmessages > 0) {
            if (xoops_smallworld('#friendInvitations_box').length) { // implies *not* zero
                xoops_smallworld('#friendInvitations_box').show();
                xoops_smallworld.colorbox({
                    width: "50%",
                    inline: true,
                    onCleanup: function () {
                        xoops_smallworld('#friendInvitations_box').hide();
                        xoops_smallworld('#friendInvitations_box').css('display', 'none');
                    },
                    href: "#friendInvitations_box",
                    onClosed: function () {
                        location.reload(true);
                    }
                });
            }
            xoops_smallworld('.smallworldrequestlink').on('click', function () {
                var smallworld_request_id = xoops_smallworld(this).prop('id');
                if (xoops_smallworld("tr[id^='smallworldfriendrequest_']").length === 0) {
                    xoops_smallworld('#friendInvitations_box').css('display', 'none');
                } else {
                    xoops_smallworld('tr#' + smallworld_request_id).remove();
                    if (xoops_smallworld("tr[id^='smallworldfriendrequest_']").length === 0) {
                        xoops_smallworld.colorbox.close();
                    }
                }
            });
        }
    });

    // Function to make Edit image descriptions form into ui dialog
    xoops_smallworld(document).ready(function () {
        if (xoops_smallworld('#smallworld_edit_imageform').length) { // implies *not* zero
            xoops_smallworld('#smallworld_edit_imageform').show();
            xoops_smallworld.colorbox({
                innerWidth: "1000px",
                height: "100%",
                inline: true,
                onCleanup: function () {
                    xoops_smallworld('#smallworld_edit_imageform').hide();
                },
                onClosed: function () {
                    location.href = 'index.php';
                },
                href: "#smallworld_edit_imageform"
            });
        }
    });

    // Function to make File upload form into ui dialog
    xoops_smallworld(document).ready(function () {
        if (xoops_smallworld('#file_upload').length) { // implies *not* zero
            xoops_smallworld('#file_upload').show();
            xoops_smallworld.colorbox({
                width: "auto",
                MaxHeight: "40%",
                inline: true,
                onComplete: function () {
                    xoops_smallworld("#file_upload").css("max-height", "200px");
                },
                onCleanup: function () {
                    xoops_smallworld('#file_upload').hide();
                },
                onClosed: function () {
                    location.href = 'editimages.php';
                },
                href: "#file_upload",
                onComplete: function () {
                    xoops_smallworld(this).colorbox.resize();
                }
            });
        }
    });

    // Function to make Div with id page into ui dialog
    xoops_smallworld(document).ready(function () {
        if (xoops_smallworld('div#page').length) { // implies *not* zero
            xoops_smallworld('div#page').show();
            xoops_smallworld.colorbox({
                innerWidth: "1000px",
                innerHeight: "90%",
                inline: true,
                onCleanup: function () {
                    xoops_smallworld('div#page').hide();
                },
                onClosed: function () {
                    //location.href = window.location.pathname; 
                    location.href = smallworld_urlReferer;
                },
                href: "div#page"
            });
        }
    });

    // If user does not have a profile in smallworld then goto register.
    // If user has already a profile then goto edit profile dialog
    if (Smallworld_userHasProfile <= 1) {
        var buttons = {};
        buttons[_smallworldContinueToReg] = function () {
            location.href = smallworld_url + 'register.php';
        };
        buttons[_smallworldCancel] = function () {
            xoops_smallworld(this).dialog("close");
            location.href = smallworld_url + 'publicindex.php';
        };
        buttons[_smallworldClose] = function () {
            xoops_smallworld(this).dialog("close");
            location.href = smallworld_url + 'publicindex.php';
        };

        xoops_smallworld('#smallworld_notyetusercontent').dialog({
            minWidth: 500,
            show: "blind",
            hide: "explode",
            width: "550px",
            close: function (event, ui) {
                location.href = smallworld_urlReferer;
            },
            buttons: buttons
        });
    }

    // Attach jquery-ui datepicker to form.
    xoops_smallworld("#birthday").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'dd-mm-yy',
        showOn: "button",
        buttonImage: "assets/images/calendar.gif",
        buttonImageOnly: true,
        onClose: function () {
            this.focus();
        },
        yearRange: '-100:+0'
    });

    // Attach jquery-ui datepicker to form.
    xoops_smallworld(".jobstart").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy',
        yearRange: '-100:+0'
    });

    // Attach jquery-ui datepicker to form.
    xoops_smallworld(".jobstop").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: 'yy',
        yearRange: '-100:+0'
    });

    // Steps for registration and submitbutton name using
    // formToWizzard.js
    if (xoops_smallworld("#smallworld_profileform").length > 0) {
        xoops_smallworld(function () {
            xoops_smallworld('#smallworld_profileform').stepy({
                backLabel: SmallworldValidationBackButton,
                block: true,
                errorImage: true,
                nextLabel: SmallworldValidationForwardButton,
                legend: true,
                description: true,
                titleClick: false
            });
        });
    }

    // Attach geolocate autocomplete to forms
    if (Smallworld_geocomplete == 1) {
        xoops_smallworld(document).ready(function () {
            if (xoops_smallworld("#birthplace").length > 0) {
				if (typeof xoops_smallworld('#birthplace').val() != "undefined") {
					// Additional check to not react on all input focus, check page url contains string
					if ((window.location.href.indexOf("editprofile.php") > -1) || (window.location.href.indexOf("register.php") > -1)){
					  xoops_smallworld('#birthplace').OsmLiveSearchBirth();
					}  
                }
            }
            
			if (xoops_smallworld("#present_city").length > 0) {
                if (typeof xoops_smallworld('#present_city').val() != "undefined") {
					// Additional check to not react on all input focus, check page url contains string
					if ((window.location.href.indexOf("editprofile.php") > -1) || (window.location.href.indexOf("register.php") > -1)){
						xoops_smallworld('#present_city').OsmLiveSearchNow();
					}
                    
                }
            }
			
        });
    }

    // Make Textareas elastic
    xoops_smallworld(function () {
        if (xoops_smallworld(".favourites").length > 0 ||
            xoops_smallworld(".smallworld_comment").length > 0 ||
            xoops_smallworld("#smallworld_update").length > 0 ||
            xoops_smallworld("#smallworld_update_profile").length > 0
        ) {
            xoops_smallworld(function () {
                xoops_smallworld('.favourites, .smallworld_comment, #smallworld_update, #smallworld_update_profile').elastic();
                xoops_smallworld('.smallworld_comment').css('max-width', xoops_smallworld('.smallworld_stcommentbody').outerWidth() - 90 + 'px');
                xoops_smallworld('#smallworld_update').css('max-width', xoops_smallworld('#smallworld_wall_container').outerWidth() - 30 + 'px');

            });
        }
    });

    // Toggle the form 'partner' if choosen status is married, in relationship, complicated
    xoops_smallworld(function () {
        if (xoops_smallworld("#relationship").val == '2') {
            xoops_smallworld('#partner').fadeOut('slow');
            xoops_smallworld('.partner').hide();
        }
        xoops_smallworld("#relationship").on('change', function () {
            var val = xoops_smallworld(this).val();
            if (val != '2') {
                xoops_smallworld('#partner').fadeIn('slow');
                xoops_smallworld('.partner').show();
            } else {
                xoops_smallworld('#partner').fadeOut('slow');
                xoops_smallworld('.partner').hide();
            }
        });
    });

// On focus to smallworld update textarea display tags & categories field
    xoops_smallworld('#smallworld_tagIMG').on('click', function () {
        xoops_smallworld('#tags_input').slideToggle(300);
        xoops_smallworld('#tags_input').focus().val("");
        xoops_smallworld("#tags_input").tagit({
            singleField: true,
            singleFieldNode: xoops_smallworld('#tags_input'),
            allowSpaces: true,
            minLength: 2,
            removeConfirmation: true,
            tagSource: function (request, response) {
                //console.log("1");
                xoops_smallworld.ajax({
                    url: smallworld_url + 'tags.php',
                    data: {term: request.term},
                    dataType: "json",
                    success: function (data) {
                        response(
                            xoops_smallworld.map(data, function (item) {
                                return {
                                    label: item.label + " (" + item.id + ")",
                                    value: item.value
                                }
                            }));
                    }
                });
            }
        });
    });

    xoops_smallworld('#tags_input').on("focus", function () {
        xoops_smallworld(this).show();
    });

// Autocomplete renders @username to scripted html code or Xcode
    function split(val) {
        return val.split(/@\s*/);
    }

    function split_(val) {
        return val.split(' ');
    }

    function extractLast(term) {
        return split(term).pop();
    }

// Attach '@' to comment and message for user tagging
    xoops_smallworld(document).ready(function () {
        xoops_smallworld("#smallworld_update, .smallworld_comment").bind("keydown", function (event) {
            // if TAB or autocomplete already is active
            if (event.keyCode === 9 && xoops_smallworld(this).data("autocomplete").menu.active) {
                event.preventDefault();
            }
            // if @ is pressed
            if (event.keyCode === 50) {
                xoops_smallworld(this).autocomplete({
                    disabled: false,
                    minLength: 1,
                    source: function (request, response) {
                        xoops_smallworld.ajax({
                            // basePath is used for defining contecxt-path of the url.
                            url: smallworld_url + 'search.php',
                            dataType: "json",
                            // data to be sent to the server:
                            data: {
                                term: extractLast(request.term)
                            },
                            success: function (data, type) {
                                //console.log( data);
                                items = data;
                                response(items);
                            },
                            error: function (data, type) {
                                console.log(type);
                            }
                        });
                    },
                    focus: function () {
                        return false;
                    },
                    open: function (event, ui) {
                        xoops_smallworld("ul.ui-autocomplete li a").each(function () {
                            var htmlString = xoops_smallworld(this).html().replace(/&lt;/g, '<');
                            htmlString = htmlString.replace(/&gt;/g, '>');
                            xoops_smallworld(this).html(htmlString);
                            xoops_smallworld('.ui-autocomplete.ui-menu').width(200);
                        });
                    },
                    select: function (event, ui) {
                        var terms = split_(this.value);
                        terms.pop();
                        //Add @ to username
                        ui.item.value = "@" + ui.item.value;
                        terms.push(ui.item.value);
                        terms.push("");
                        // Join last username with @
                        this.value = terms.join(" ");
                        // Exit autocomplete
                        xoops_smallworld(this).autocomplete('destroy');
                        xoops_smallworld('.ui-autocomplete-loading').removeClass("ui-autocomplete-loading");
                        return false;
                    }
                });
            } else {
                xoops_smallworld(this).autocomplete({disabled: true});
                xoops_smallworld(this).autocomplete("close");
            }
        });
    });

    // Search for partner in smallworld users or accept username
    xoops_smallworld(function () {
        xoops_smallworld("#partner").autocomplete({
            source: smallworld_url + 'search.php',
            minLength: 1,
            open: function (event, ui) {
                xoops_smallworld("ul.ui-autocomplete li a").each(function () {
                    var htmlString = xoops_smallworld(this).html().replace(/&lt;/g, '<');
                    htmlString = htmlString.replace(/&gt;/g, '>');
                    xoops_smallworld(this).html(htmlString);
                    xoops_smallworld('.ui-autocomplete.ui-menu').width(200);
                });
            },
            select: function (event, ui) {
                xoops_smallworld("input#partner").val(ui.item.value);
            }
        });
    });

    // Keep the searchform empty when clicked
    xoops_smallworld("input#smallworld_searchform").click(function (event) {
        event.preventDefault();
        // Erase text from inside textarea
        xoops_smallworld(this).val("");
        // Disable text erase
        xoops_smallworld(this).unbind(event);
    });

    // Search for user in smallworld users or test for new user
    xoops_smallworld(function () {
        //xoops_smallworld("#smallworld_searchform").focus().select();
        xoops_smallworld("#smallworld_searchform").autocomplete({
            source: smallworld_url + 'search.php',
            minLength: 1,
            select: function (event, ui) {
                location.href = smallworld_url + 'userprofile.php?username=' + ui.item.value;
            }
        });

        xoops_smallworld["ui"]["autocomplete"].prototype["_renderItem"] = function (ul, item) {
            return xoops_smallworld("<li></li>")
                .data("item.autocomplete", item)
                .append(xoops_smallworld("<a></a>").html(item.label))
                .appendTo(ul);
        };
    });

// Function to reset gender, relationshipstatus,politics an religion select:Selected in forms after send and in pagerefresh
    xoops_smallworld(function () {
        if (smallworld_PageName == 'register.php' || smallworld_PageName == 'editprofile.php') {
            var sw_data;
            xoops_smallworld.ajax({
                url: smallworld_url + "include/getSelects.php?" + Math.random(),
                cache: false,
                dataType: "json",
                success: function (sw_data) {
                    xoops_smallworld("select[name=gender] option[value=" + sw_data.gender + "]").attr("selected", true);
                    xoops_smallworld("select[name=relationship] option[value=" + sw_data.relat + "]").attr("selected", true);
                    if (sw_data.relat == 2) {
                        xoops_smallworld("#partner").hide();
                        xoops_smallworld("p.partner").hide();
                    }
                    if (sw_data.relat != 2) {
                        xoops_smallworld("#partner").show();
                        xoops_smallworld("p.partner").show();
                    }
                    xoops_smallworld("select[name=politic] option[value=" + sw_data.politic + "]").attr("selected", true);
                    xoops_smallworld("select[name=religion] option[value=" + sw_data.religion + "]").attr("selected", true);
                },
                error: function (xhr, status, thrown) {
                    alert(xhr + " " + status + " " + thrown);
                }
            });
        }
    });


    // Keep public as default checked in Update field
    xoops_smallworld(function () {
        xoops_smallworld("input[name=updatePublic][value=1]").prop("checked", true);
    });


    // Functions to add fields in screenname, email, education and jobs
    xoops_smallworld('#emailAdd').on('click', function (e) {
        e.preventDefault();
        // how many "duplicatable" input fields we currently have
        var num = xoops_smallworld('input[name="emailtype[]"]').length - 1;

        // the numeric ID of the new input field being added
        var newNum = (num + 1);
        xoops_smallworld('span#email:last').clone(true).insertBefore(this).find('input').val('').attr('id', 'email-' + newNum);
        xoops_smallworld('span#emailremove:last').clone(true).insertBefore(this);
    });

    /* Screen names for facebook etc */
    xoops_smallworld('#screennameAdd').on('click', function (e) {
        e.preventDefault();
        xoops_smallworld('span#screenname:last').clone(true).insertBefore(this).find('input').val('');
        xoops_smallworld('span#screennameremove:last').clone(true).insertBefore(this);
    });

    /* School */
    xoops_smallworld('#schoolAdd').on('click', function (e) {
        e.preventDefault();
        xoops_smallworld('div#school:last').clone(true).insertBefore(this).find('input').val('');
        xoops_smallworld('span#schoolremove:last').clone(true).insertBefore(this);
    });

    /* Jobs - also remove datepicker from :last and apply on NEW :last */
    xoops_smallworld('#jobAdd').on('click', function (e) {
        e.preventDefault();
        xoops_smallworld('div#job:last').clone(true).insertBefore(this).find('input').val('');
        xoops_smallworld('span#jobremove:last').clone(true).insertBefore(this);
        xoops_smallworld('.jobstart').removeClass("hasDatepicker").attr('id', "").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy',
            yearRange: '-100:+0'
        });
        xoops_smallworld('.jobstop').removeClass("hasDatepicker").attr('id', "").datepicker({
            changeMonth: true,
            changeYear: true,
            dateFormat: 'yy',
            yearRange: '-100:+0'
        });
    });


    // Registerform saveprofile
    xoops_smallworld(".smallworld_finish").click(function (e) {
        e.preventDefault();
        var dataString = xoops_smallworld("#smallworld_profileform").serialize();
        if (dataString == '') {
        } else {
            xoops_smallworld('.smallworld_finish').attr('disabled', true);
            xoops_smallworld.ajax({
                type: "POST",
                //dataType: 'json',
                cache: false,
                url: smallworld_url + "submit.php",
                data: dataString,
                success: function () {
                    alert(SmallworldSavedSuccesMsg);
                },
                complete: function () {
                    location.href = smallworld_url + 'index.php';
                }
            });
        }
    });

    // Save personal settings

    xoops_smallworld("body").on('click', '#smallworld_privsave', function (e) {
        e.preventDefault();
        if (xoops_smallworld('#posts').is(':checked')) {
            var posts = 1;
        } else {
            var posts = 0;
        }

        if (xoops_smallworld('#comments').is(':checked')) {
            var comments = 1;
        } else {
            var comments = 0;
        }

        if (xoops_smallworld('#notify').is(':checked')) {
            var notify = 1;
        } else {
            var notify = 0;
        }

        var dataString = 'posts=' + posts + '&comments=' + comments + '&notify=' + notify;

        if (dataString == '') {
        } else {
            xoops_smallworld('#smallworld_privsave').attr('disabled', true);
            xoops_smallworld.ajax({
                type: "POST",
                dataType: 'json',
                cache: false,
                url: smallworld_url + "settings.php",
                data: dataString,
                success: function () {

                },
                complete: function () {
                    location.href = smallworld_url + 'index.php';
                }
            });
        }
    });

    // Description for images save
    xoops_smallworld('#smallworld_edit_imagesavebtn').on('click', function () {
        var post = xoops_smallworld('#smallworld_edit_imgform').serialize();
        xoops_smallworld.post(smallworld_url + "image_edit_submit.php",
            post,
            function (data) {
                //alert(data);
                alert(SmallworldSavedSuccesMsg);
                location.href = smallworld_url + 'index.php';
            });
        return false;
    });

    // Description for images save
    xoops_smallworld('#smallworld_changeAvatar').on('click', function () {
        xoops_smallworld('.UploadNewAvatar').toggle();
        return false;
    });

    // Open avatar imagen in new window on click
    xoops_smallworld(function () {
        xoops_smallworld('#smallworld_user_img').css('cursor', 'pointer');
        xoops_smallworld('#smallworld_user_img').css('margin', '5px 0 2px');
        xoops_smallworld('#smallworld_user_img').css('display', 'block');
        xoops_smallworld('#smallworld_user_img').on('click', function (event) {
            var url = xoops_smallworld(this).attr('src');
            image = "<img src ='" + url + "' />";
            xoops_smallworld('<div id="lookingglassuseravatar">' + image + '</div>').appendTo('body');
            event.preventDefault();
            if (xoops_smallworld('#lookingglassuseravatar').length) { // implies *not* zero
                xoops_smallworld('#lookingglassuseravatar').show();
                xoops_smallworld.colorbox({
                    width: "auto",
                    height: "70%",
                    inline: true,
                    onCleanup: function () {
                        xoops_smallworld('#lookingglassuseravatar').hide();
                    },
                    onClosed: function () {
                        xoops_smallworld("#lookingglassuseravatar").remove();
                    },
                    onComplete: function () {
                        xoops_smallworld(this).colorbox.resize();
                    },
                    href: "#lookingglassuseravatar"
                });
            }
        }); //close click
    });

    // Attach on clik open education & work (..more) dialogues
    xoops_smallworld('#_smallworld_workmore').on('click', function (event) {
        event.preventDefault();
        if (xoops_smallworld('#workfull').length) { // implies *not* zero
            xoops_smallworld('#workfull').show();
            xoops_smallworld.colorbox({
                width: "450",
                height: "300",
                inline: true,
                onCleanup: function () {
                    xoops_smallworld('#workfull').hide();
                },
                onClosed: function () {
                },
                href: "#workfull"
            });
        }
    });

    // Show educations in dialog when clicked on more
    xoops_smallworld('#_smallworld_educationmore').on('click', function (event) {
        event.preventDefault();
        if (xoops_smallworld('#educationfull').length) { // implies *not* zero
            xoops_smallworld('#educationfull').show();
            xoops_smallworld.colorbox({
                width: "450",
                height: "300",
                inline: true,
                onCleanup: function () {
                    xoops_smallworld('#educationfull').hide();
                },
                onClosed: function () {
                },
                href: "#educationfull"
            });
        }
    });

    // Show more info in dialog
    xoops_smallworld('#_SMALLWORLD_MOREINFO').on('click', function (event) {
        event.preventDefault();
        if (xoops_smallworld('#interestsandmore').length) { // implies *not* zero
            xoops_smallworld('#interestsandmore').show();
            xoops_smallworld.colorbox({
                width: "600",
                height: "300",
                inline: true,
                onCleanup: function () {
                    xoops_smallworld('#interestsandmore').hide();
                },
                onClosed: function () {
                },
                href: "#interestsandmore"
            });
        }
    });

    // Show Privacy settings dialog
    xoops_smallworld('body').on('click', '#smallworld_changePersSettings', function (e) {
        e.preventDefault();
        if (xoops_smallworld('#smallworld_changePersSettings').length) { // implies *not* zero    
            xoops_smallworld('.smallworld_usersetings').show();
            xoops_smallworld.colorbox({
                inline: true,
                onCleanup: function () {
                    //xoops_smallworld('.smallworld_usersetings').hide();
                },
                onClosed: function () {
                    //xoops_smallworld('.smallworld_usersetings').hide();
                },
                href: "div .smallworld_usersetings",
            });
        }
    });

    //Function to show images in birthplace input
    xoops_smallworld('#_smallworld_birthplace_maplink').on('click', function (event) {
        event.preventDefault();
        if (xoops_smallworld('#_smallworld_birthplace_map').length) { // implies *not* zero
            if (Smallworld_geocomplete == 1) {
                xoops_smallworld('#_smallworld_birthplace_map').show();
                xoops_smallworld.colorbox({
                    innerWidth: "550px",
                    innerHeight: "550px",
                    inline: true,
                    onCleanup: function () {
                        xoops_smallworld('#_smallworld_birthplace_map').hide();
                    },
                    onComplete: function () {
                        //Smallworld_initialize_birthplace(smallworld_birthlng, smallworld_birthlatt);
						
                        xoops_smallworld('#_smallworld_birthplace_map').show();
						doMapBirth(smallworld_birthlatt, smallworld_birthlng);
                    },
                    title: function () {
                        var title = xoops_smallworld("#_smallworld_birthplace_map").attr('title');
                        return title != 'undefined' ? title : false;
                    },
                    href: "#_smallworld_birthplace_map",
                });
            }
        }
    });

	function doMapBirth(lat, lon) {
        var map = L.map('_smallworld_birthplace_map').setView([lat, lon], 13);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
        var marker = L.marker([lat, lon]).addTo(map);
        var popup = marker.bindPopup(cityname_birth);
    }
	
	function doMapNow(lat, lon) {
        var map = L.map('_smallworld_present_map').setView([lat, lon], 13);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
        var marker = L.marker([lat, lon]).addTo(map);
        var popup = marker.bindPopup(cityname_birth);
    }

    //Function to show images in present location input
    xoops_smallworld('#_smallworld_present_maplink').on('click', function (event) {
        event.preventDefault();
        if (xoops_smallworld('#_smallworld_present_map').length) { // implies *not* zero
            if (Smallworld_geocomplete == 1) {
                xoops_smallworld('#_smallworld_present_map').show();
                xoops_smallworld.colorbox({
                    innerWidth: "550px",
                    innerHeight: "550px",
                    inline: true,
                    onCleanup: function () {
                        xoops_smallworld('#_smallworld_present_map').hide();
                    },
                    onComplete: function () {
                        //Smallworld_initialize_currplace(smallworld_currlng, smallworld_currlatt);
						
                        xoops_smallworld('#_smallworld_present_map').show();
						doMapNow(smallworld_currlatt, smallworld_currlng);
                    },
                    title: function () {
                        var title = xoops_smallworld("#_smallworld_present_map").attr('title');
                        return title != 'undefined' ? title : false;
                    },
                    href: "#_smallworld_present_map"
                });
            }
        }
    });


    // Function to rewrite urls used in xoops core for directing to profile.php
    // Sets var Smallworld_uname = link text()
    // removes /userinfo.php?uid=#
    // replaces with /modules/smallworld/userprofile.php?username=Smallworld_uname
    // culex okt 2011
    if (smallworldTakeOverLinks != 0) {
        xoops_smallworld('a[href*="userinfo.php?"]').each(function () {
            var Smallworld_oldurl = xoops_smallworld(this).attr("href");
            var Smallworld_uname = xoops_smallworld(this).text();
            if (Smallworld_oldurl.match(/.*xoops.*/)) {
                return Smallworld_oldurl;
            } else {
                var Smallworld_tempArray = xoops_smallworld(this).attr("href").split("/");
                var Smallworld_baseURL = Smallworld_tempArray[0];
                this.href = this.href.replace(Smallworld_oldurl, smallworld_url + "userprofile.php?username=" + Smallworld_uname);
            }
        });
    }

    if (xoops_smallworld("#smallworld_stats_scroller").length > 0) {
        xoops_smallworld('#smallworld_stats_scroller').innerfade({
            animationtype: 'fade',
            speed: 1200,
            timeout: 10000,
            type: 'sequence',
            containerheight: '50px'
        });
    }

    // Return jSon with count of friends
    smallworld_getCountFriendMessagesEtcJS();

    // Sharing bookmarks defines
    xoops_smallworld(function () {
        xoops_smallworld('body').on('click', '.share', function (e) {
            e.preventDefault();
            var id = xoops_smallworld(this).attr('id');
            var ref = xoops_smallworld('span[name="' + id + '"]').attr('rel');
            var desc = xoops_smallworld('span[name="' + id + '"]').attr('rel1');
            var username = xoops_smallworld('span[name="' + id + '"]').attr('rel2');
            xoops_smallworld('[name="' + id + '"]').toggle();
            xoops_smallworld('span[name="' + id + '"]').bookmark({
                onSelect: Smallworld_customBookmark,
                url: ref,
                description: desc,
                title: username
            }).dialog();
            //xoops_smallworld(".ui-dialog-titlebar").hide();
        });
    });

    // Donation
    xoops_smallworld(function () {
        xoops_smallworld('body').on('click', '#smallworldDonate', function () {
            window.open('https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=WKFZBRBGMYKCA&lc=DK"&item_name=Culex%2edk&item_number=culex&currency_code=DKK&bn=PP%2dDonationsBF%3asp%2epng%3aNonHosted', 'blank',
                'width=600, height=400, menubar=no, toolbar=no, scrollbars=yes');
        });
    });


    smallworldCheckNumDivs();


    xoops_smallworld('a').each(function () {
        if (xoops_smallworld(this).attr('href') != undefined) {
            match = xoops_smallworld(this).attr('href').match(/\.(mp3)/);
            if (match != null) {
                href = xoops_smallworld(this).attr('href');
                text = xoops_smallworld(this).text();
                player =
                    '<object type="application/x-shockwave-flash" data="http://flash-mp3-player.net/medias/player_mp3_maxi.swf" width="200" height="20">' +
                    '<param name="movie" value="http://flash-mp3-player.net/medias/player_mp3_maxi.swf" />' +
                    '<param name="bgcolor" value="#ffffff" />' +
                    '<param name="FlashVars" value="mp3=' + href + '&amp;showstop=1&amp;showvolume=1" />' +
                    '</object>';

                p = xoops_smallworld(this).parent();
                // xoops_smallworld(this).remove();
                xoops_smallworld(p).append(player);
            }
        }
    });

    xoops_smallworld(".smallworld_big_face, .smallworld_small_face, .smallworldAttImg").each(function () {
        var image = xoops_smallworld(this);
        if (image.context.naturalWidth == 0 &&
            image.readyState == 'uninitialized') {
            xoops_smallworld(image).unbind("error").attr(
                "src", smallworld_url + "images/image_missing.png"
            );
        }
    });

// Function to remove spans in editprofile & profile.php
    xoops_smallworld('.smallworld_remove, .smallworld_remove2, .smallworld_remove3').on('click', function () {
        // shooter = link id to press
        // target = id of span to remove
        // counter = name of item to count items
        // Culex april 2011

        var shooter = xoops_smallworld(this).attr('id');
        var target = xoops_smallworld(this).prev().attr('id');
        if (target == 'email') {
            var i = xoops_smallworld("input[name='emailtype[]']").length;
        }
        if (target == 'screenname') {
            var i = xoops_smallworld("input[name='screenname_type[]']").length;
        }
        if (target == 'school') {
            var i = xoops_smallworld("input[name='school_type[]']").length;
        }
        if (target == 'job') {
            var i = xoops_smallworld("div#job").length;
        }
        if (i === 1) {
            return false;
        }
        if (i > 1) { // if you have at least 1 input on the form
            if (shooter === 'job') {
                xoops_smallworld(this).prev('div#job').remove();
                xoops_smallworld(this).remove();
            } else {
                xoops_smallworld(this).prev().remove();
                xoops_smallworld(this).remove();
            }
            i--; //deduct 1 from i so if i = 3, after i--, i will be i = 2
        }
        return false;
    });


}); // END OF DOCUMENT READY IN THE START

// function to apply "show more" show less if number of comments
// are more than 2
function smallworldCheckNumDivs() {
    xoops_smallworld(document).ready(function () {
        xoops_smallworld('.smallworld_commentcontainer').children('div').show();
        xoops_smallworld('.smallworld_CommentShowMoreSpan').remove();

        var showText = _smallworldCommentsMoreMore;
        var hideText = _smallworldCommentsMoreLess;

        xoops_smallworld('.smallworld_commentcontainer').each(function () {
            var hiddenElements = xoops_smallworld(this).children('div:gt(1)').hide();
            //var counts = xoops_smallworld(this).children().size();
            if (hiddenElements.size() > 0) {
                var showCaption = _smallworldCommentsMoreMore;
                xoops_smallworld(this).children('div:gt(1)').hide();
                xoops_smallworld(this).after('<div class="smallworld_CommentShowMoreSpan"><a href="javascript:void(0);" class="smallworld_CommentShowMore">' + showCaption + '</a></div>');
            }
        });

        xoops_smallworld('.smallworld_CommentShowMore').click(function (e) {
            e.preventDefault();
            if (xoops_smallworld(this).text() == showText) {
                xoops_smallworld(this).text(hideText);
            } else {
                xoops_smallworld(this).text(showText);
            }
            xoops_smallworld(this).parent().prev('div.smallworld_commentcontainer').children('div:gt(1)').slideToggle('slow');
        });
    });
}


// Open custom boomark window
function Smallworld_customBookmark(id, display, url) {
    window.open(url, '_blank',
        'width=600,height=400,menubar=no,toolbar=no,scrollbars=yes');
}

// Function to send invitation of friendship to userid
//get friends email ids and message to send invitation
function Smallworld_inviteFriends(friendID, myuid) {
    xoops_smallworld('#resultMsg').hide();
    var txtMsgModal = xoops_smallworld('#friendship').text().replace(/\t/g, '');
    var frNa = xoops_smallworld('#smallworld_capname').text();
    apprise(frNa + '... ' + txtMsgModal + '<br><br> ' + SmallworldConfirmBtnFI + ' ?', args, function (r) {
        if (r) {
            // user clicked 'Yes'
            xoops_smallworld.ajax({
                type: 'POST',
                url: smallworld_url + 'friendinvite.php?' + Math.random(),
                dataType: 'json',
                data: 'invitation=1&friend=' + friendID + '&myUid=' + myuid,
                success: function (response) {
                    xoops_smallworld('#resultMsg').html(response.msg);
                    xoops_smallworld('#friendship').html(response.msgChange);
                    return false;
                }
            });
        }
        else {
            // user clicked 'No'
            xoops_smallworld('#resultMsg').dialog('close');
            return false;
        }
    });
}

// function to follow / unfollow friends
function Smallworld_FollowFriend(friendID, myuid) {
    xoops_smallworld('#resultMsgFollow').hide();
    xoops_smallworld.ajax({
        type: 'POST',
        url: smallworld_url + 'friendinvite.php?' + Math.random(),
        dataType: 'json',
        data: 'invitation=2&friend=' + friendID + '&myUid=' + myuid,
        success: function (response) {
            xoops_smallworld('#followfriend').html(response.msgChange);
            xoops_smallworld('#resultMsgFollow').html(response.msg);
            xoops_smallworld('#resultMsgFollow').dialog({show: "size", hide: "scale"});
            return false;
        }
    });
}

// Set function to react on "more" button
function SmallworldGetMoreMsg() {
    xoops_smallworld('.smallworld_msg_counter:last').show();
    xoops_smallworld('.smallworld_msg_counter').on('click', function (e) {
        e.preventDefault();
        xoops_smallworld('#smallworld_moremsg_ajax').show();
        var lastmsg = xoops_smallworld(this).attr("id");
        var page = xoops_smallworld(this).attr("rel");
        var userid = xoops_smallworld(this).attr('rel2');

        if (lastmsg == '' || lastmsg == undefined) {
            xoops_smallworld('#smallworld_moremsg_ajax').remove();
            xoops_smallworld('.smallworld_msg_counter:last').remove();
        }
        if (lastmsg) {
            xoops_smallworld.ajax({
                type: "POST",
                url: "loadmore.php",
                data: "last=" + lastmsg + "&page=" + page + "&userid=" + userid,
                success: function (html) {
                    xoops_smallworld("#smallworld_content").append(html);
                    xoops_smallworld('#smallworld_moremsg_ajax:first').remove();
                    xoops_smallworld(".smallworld_msg_counter:first").remove();
                    if (xoops_smallworld.trim(html) == "") {
                        xoops_smallworld(".smallworld_msg_counter").remove();
                        xoops_smallworld('#smallworld_moremsg_ajax').remove();
                    }
                }
            });
        }
        else {

        }
        smallworldCheckNumDivs();
    });
}

// function to Accept / deny friendships
function Smallworld_AcceptDenyFriend(stat, friendID, myuid, targetID) {
    xoops_smallworld.ajax({
        type: 'POST',
        url: smallworld_url + 'friendinvite.php?' + Math.random(),
        dataType: 'json',
        data: 'invitation=3&friend=' + friendID + '&myUid=' + myuid + '&stat=' + stat,
        success: function (response) {
            //xoops_smallworld('#comnMsg').show();
            //alert (response.msg);
            xoops_smallworld('tr#' + targetID).hide();
            if (response != null && response != '') {
                xoops_smallworld('#friendship').html(response.msgChange);
            }
            return false;
        }
    });
}

// Function to refresh current page
function smallworldRefresh() {
    location.reload(true);
}

function smallworld_getCountFriendMessagesEtcJS() {
    xoops_smallworld.ajax({
        url: smallworld_url + "Get_Count.php?SmallworldGetUserMsgCount=1" + "&rndnum=" + Math.floor(Math.random() * 101),
        cache: false,
        dataType: "json",
        success: function (data) {
            if (data != null && data != '') {
                var newcnt = data.NewUserMsgCount;
                var diff = newcnt - smallworld_getFriendsMsgComCount;

                if (diff < 0) {
                    var text = smallworldDeletedMessages + diff;
                    xoops_smallworld('#smallworld_messagecounter_id').html(text);
                    xoops_smallworld('#smallworld_messagecounter_id').show();
                    //window.location.reload();
                }

                if (diff == 0) {
                    var text = '';
                    xoops_smallworld('#smallworld_messagecounter_id').empty();
                    xoops_smallworld('#smallworld_messagecounter_id').hide();
                }
                if (diff > 0) {
                    var text = smallworldNewMessages + diff;
                    xoops_smallworld('#smallworld_messagecounter_id').html(text);
                    xoops_smallworld('#smallworld_messagecounter_id').show();
                    //window.location.reload();
                }
            }
            return false;
        }
    });
    setTimeout('smallworld_getCountFriendMessagesEtcJS()', 10000);
}
function smallworld_DoValStart() {
    xoops_smallworld(document).ready(function () {
        // Attact validation to registeration parts in register form
        if (smallworldvalidationstrength != 0) {
            xoops_smallworld("#smallworld_profileform-next-0").hide();
            xoops_smallworld("#smallworld_profileform-next-1").hide();

            if (xoops_smallworld.inArray('realname', smallworlduseverification) > -1) {

                xoops_smallworld("input#realname").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: SmallworldValidationNameErrorMsg,
                });
            }

            if (xoops_smallworld.inArray('gender', smallworlduseverification) > -1) {
                xoops_smallworld("select#gender").validate({
                    expression: "if (VAL != 0) return true; else return false;",
                    message: SmallworldValidationGenderErrorMsg
                });
            }

            if (xoops_smallworld.inArray('interestedin', smallworlduseverification) > -1) {
                xoops_smallworld("#intingender").validate({
                    expression: "if (isChecked(SelfID)) return true; else return false;",
                    message: SmallworldValidationIntingenderErrorMsg
                });

            }

            if (xoops_smallworld.inArray('lookingfor', smallworlduseverification) > -1) {
                xoops_smallworld("#searchrelat").validate({
                    expression: "if (isChecked(SelfID)) return true; else return false;",
                    message: SmallworldValidationSearchrelatErrorMsg
                });
            }

            if (xoops_smallworld.inArray('emails', smallworlduseverification) > -1) {
                xoops_smallworld('input[name="emailtype[]"]').on('blur', function () {
                    var id = xoops_smallworld(this).attr('id');
                    xoops_smallworld("#" + id).validate({
                        expression: "if (VAL.match(/^[^\\W][a-zA-Z0-9\\_\\-\\.]+([a-zA-Z0-9\\_\\-\\.]+)*\\@[a-zA-Z0-9\\_\\-]+(\\.[a-zA-Z0-9\\_\\-]+)*\\.[a-zA-Z]{2,4}$/)) return true; else return false;",
                        message: "<img src='images/error.png' title='" + SmallworldValidationEmailTitleErrorMsg + "'/>"
                    });
                });
            }

            if (xoops_smallworld.inArray('birthday', smallworlduseverification) > -1) {
                xoops_smallworld("input[name='birthday']").validate({
                    expression: "if (!isValidDate(parseInt(VAL.split('-')[2],10), parseInt(VAL.split('-')[1],10), parseInt(VAL.split('-')[0],10))) return false; else return true;",
                    message: SmallworldValidationBirthdayErrorMsg
                });
            }

            if (xoops_smallworld.inArray('birthplace', smallworlduseverification) > -1) {
                xoops_smallworld("#birthplace").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: SmallworldValidationBirthplaceErrorMsg
                });
            }

            if (xoops_smallworld.inArray('birthplace', smallworlduseverification) > -1) {
                xoops_smallworld("input#adress").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: SmallworldValidationAdressErrorMsg
                });
            }

            if (xoops_smallworld.inArray('presentcity', smallworlduseverification) > -1) {
                xoops_smallworld("input#present_city").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: SmallworldValidationCityErrorMsg
                });
            }

            if (xoops_smallworld.inArray('country', smallworlduseverification) > -1) {
                xoops_smallworld("input#present_country").validate({
                    expression: "if (VAL) return true; else return false;",
                    message: SmallworldValidationCountryErrorMsg
                });
            }


            if (xoops_smallworld.inArray('website', smallworlduseverification) > -1) {
                xoops_smallworld("textarea#website").validate({
                    expression: "if (urlCheck(VAL)) return true; else return false;",
                    message: SmallworldValidationWebsiteErrorMsg
                });
            }


            // Activation of validations to be filled on initial load
            xoops_smallworld('fieldset:visible').find('input,select,textarea').each(function () {
                xoops_smallworld(this).blur();
            });

        }

        if (smallworlduseverification.length != 0) {
            xoops_smallworld('#smallworld_profileform-next-0').show();
            xoops_smallworld('#smallworld_profileform-next-1').show();
        }
    });
    return false;
}

function Smallworld_attachAvatarOpen() {
    // Open comment and update avatar imagen in new window on click
    xoops_smallworld(function () {
        xoops_smallworld('.smallworld_big_face, .smallworld_small_face, .smallworldAttImg').css('cursor', 'pointer');
        xoops_smallworld('.smallworld_big_face, .smallworld_small_face, .smallworldAttImg').on('click', function (event) {
            var url = xoops_smallworld(this).attr('src');
            image = "<img src ='" + url + "' />";
            xoops_smallworld('<div id="lookingglassuseravatar">' + image + '</div>').appendTo('body');
            event.preventDefault();
            if (xoops_smallworld('#lookingglassuseravatar').length) { // implies *not* zero
                xoops_smallworld('#lookingglassuseravatar').show();
                xoops_smallworld.colorbox({
                    width: "auto",
                    height: "70%",
                    innerHeight: "300px",
                    inline: true,
                    scalePhotos: true,
                    onCleanup: function () {
                        xoops_smallworld('#lookingglassuseravatar').remove();
                    },
                    onClosed: function () {
                        xoops_smallworld("#lookingglassuseravatar").remove();
                    },
                    onComplete: function () {
                        xoops_smallworld(this).colorbox.resize();
                    },
                    href: "#lookingglassuseravatar"
                });
            }
        }); //close click
    });
    return false;
}
