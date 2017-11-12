/*
 * jQuery File Upload Plugin JS Example 7.0
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global $, window, document */

xoops_smallworld(function () {
    'use strict';

    // Initialize the jQuery File Upload widget:
    xoops_smallworld('#fileupload').fileupload({
        // Uncomment the following to send cross-domain cookies:
        //xhrFields: {withCredentials: true},
        url: 'imgupload.php'
    });

    // Enable iframe cross-domain access via redirect option:
    xoops_smallworld('#fileupload').fileupload(
        'option',
        'redirect',
        window.location.href.replace(
            /\/[^\/]*$/,
            '/cors/result.html?%s'
        )
    );

    if (window.location.hostname === 'blueimp.github.com') {
        // Demo settings:
        xoops_smallworld('#fileupload').fileupload('option', {
            url: 'imgupload.php',
            maxFileSize: 5000000,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|JPE?G)$/i,
            process: [
                {
                    action: 'load',
                    fileTypes: /^image\/(gif|jpeg|png|JPE?G)$/,
                    maxFileSize: 20000000 // 20MB
                },
                {
                    action: 'resize',
                    maxWidth: 1440,
                    maxHeight: 900
                },
                {
                    action: 'save'
                }
            ]
        });
        // Upload server status check for browsers with CORS support:
        if (xoops_smallworld.support.cors) {
            xoops_smallworld.ajax({
                url: 'imgupload.php',
                type: 'HEAD'
            }).fail(function () {
                xoops_smallworld('<span class="alert alert-error"/>')
                    .text('Upload server currently unavailable - ' +
                        new Date())
                    .appendTo('#fileupload');
            });
        }
    } else {
        // Load existing files:
        xoops_smallworld.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: xoops_smallworld('#fileupload').fileupload('option', 'url'),
            dataType: 'json',
            context: xoops_smallworld('#fileupload')[0]
        }).done(function (result) {
            xoops_smallworld(this).fileupload('option', 'done')
                .call(this, null, {result: result});
        });
    }

});
