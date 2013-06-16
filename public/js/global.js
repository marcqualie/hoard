window.app = {};

/**
 * Fix Bootstrap
 */
 $(document).ready(function () {
    $('.dropdown .dropdown-menu').click(function (e) {
        e.stopPropagation();
    });
    $(document).tooltip({
        'selector': 'a[data-toggle=tooltip]'
    });
});

/**
 * Extenders
 */
$.fn.serializeObject = function () {
    var json = {};
    jQuery.map($(this).serializeArray(), function(n, i) {
        json[n['name']] = n['value'];
    });
    return json;
};
String.prototype.replaceAll = function (org, rep) {
    var str = this;
    var cnt = 0;
    while (str.indexOf(org) > -1 && cnt++ < 50) {
        str = str.replace(org, rep);
    }
    return str;
}
$.strPadLeft = function(i, l, s) {
    var o = i.toString();
    if ( ! s) {
        s = '0';
    }
    while (o.length < l) {
        o = s + o;
    }
    return o;
};

/**
 * Main viewer code
 */
$(document).ready(function () {

    /**
     * Viewer
     */
    if ($('#viewer-form')) {

        var renderView = function (j) {

            var viewContent = $('#viewer-content');

            // Display API Errors
            if (j.hasOwnProperty('error')) {
                viewContent.html('<div class="alert alert-danger"><b>Error</b> ' + j.error + '</div>');
                return;
            }

            //
            if (j.length > 0) {
                viewContent.html('');
                $(j).each(function (index, arr) {

                    var el = $(document.createElement('div'));
                    var data = {};
                    for (var key in arr['d']) {
                        data[key] = arr['d'][key];
                    }

                    // Fields
                    var fields = [];
                    for (var field in arr['d']) {
                        fields.push(
                            '"' + field + '": "' + arr['d'][field] + '"'
                        );
                    }

                    // Time
                    var _date = new Date(arr['t']['sec'] * 1000);
                    var _dateToday = new Date();
                    if (_dateToday.getMonth() + _dateToday.getDay() == _date.getMonth() + _date.getDay()) {
                        var _hour = _date.getHours(); if (_hour < 10) _hour = '0' + _hour;
                        var _min = _date.getMinutes(); if (_min < 10) _min = '0' + _min;
                        var _sec = _date.getSeconds(); if (_sec < 10) _sec = '0' + _sec;
                        var timeText = _hour + ':' + _min + '<span style="opacity:.5">:' + _sec + '</span>';
                    } else {
                        var _month = _date.getMonth(); if (_month < 10) _month = '0' + _month;
                        var _day = _date.getDate(); if (_day < 10) _day = '0' + _day;
                        var timeText = _month + '/' + _day;
                    }

                    // Parse JSON
                    var json_full = JSON.stringify(data, undefined, 2);
//                  json_full = json_full.replaceAll('{"', '{<br/>&nbsp;&nbsp;"');
//                  json_full = json_full.replaceAll('"}', '"<br/>}');
//                  json_full = json_full.replaceAll('",', "\"<span style='color:#999'>,</span><br/>&nbsp;&nbsp;");

                    // Render
//                  console.log(data._id, data, arr);
                    el.html('<div class="doc-object">'
                        + '<div class="doc-oneline clearfix">'
                            + '<div class="doc-date">' + timeText + '</div>'
                            + '<div class="doc-snippet">'
                                + (_hoardRequestData['event'] ? '' : '<span class="doc-event">' + arr['e'] + '</span> ')
                                + '{ ' + fields.join(', ') + ' }'
                            + '</div>'
                            + '<div class="doc-id hide">' + arr['_id'] + '</div>'
                        + "</div>"
                        + '<div class="doc-full hide">'
                            + "<pre>" + json_full + "</pre>"
                        + '</div>'
                    + '</div>');
                    viewContent.append(el);

                    // Events
                    el.find('.doc-snippet').click(function () {
                        var doc = el.find('.doc-full');
                        if (doc.hasClass('hide'))
                        {
                            console.log('do open');
                            doc.removeClass('hide').stop().slideUp(0).slideDown(400);
                        }
                        else
                        {
                            console.log('do close');
                            doc.addClass('hide').stop().slideUp(400);
                        }
                    })

                });
            }

            // No events
            else {
                viewContent.html('No results');
            }


        }

        /**
         * Select Box
         */
        var appSelector = $('[name=bucket]');
        var viewerForm = $('#viewer-form');
        var currentHash = location.hash;
        appSelector.change(function () {
            location.href = '#bucket=' + appSelector.val();
        });
        if (location.hash) {
            var bucketId = location.hash.replace('#', '').split('=')[1];
            appSelector.val(bucketId);
        }
        setInterval(function () {
            if (location.hash != currentHash) {
                currentHash = location.hash;
                var split = currentHash.replace('#', '').split('&');
                var _get = {};
                for (var i = 0; i < split.length; i++) {
                    var s = split[i].split('=');
                    var k = s[0];
                    var v = s[1];
                    _get[k] = v;
                }
                var bucketId = _get['bucket'];
                viewerForm.submit();
            }
        }, 500);

        /**
         * Request
         */
        var _hoardRequestData = '';
        viewerForm.submit(function () {
            _hoardRequestData = $(this).serializeObject();
            sendHoardRequest();
        }).submit();
        function sendHoardRequest () {
            var _data = _hoardRequestData;
//          var _event = _hoardRequestData['event'] || '';
            $.post('/find/' + _hoardRequestData['bucket'], _hoardRequestData, function (json) {
                renderView(json);
            }, 'json');
        }

    }

});
