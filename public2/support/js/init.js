
/*
 * ==========================================================
 * INIT SCRIPT
 * ==========================================================
 *
 * Initialization script. [Deprecated from V 3.1.8. This file will be removed in January 2022.]
 * 
 */

'use strict';

(function ($) {
    var version = '3.1.7';
    var url_full = '';

    $(document).ready(function () {
        var url, xhr;
        if (typeof SB_INIT_URL != 'undefined') {
            if (SB_INIT_URL.indexOf('.js') < 0) {
                SB_INIT_URL += '/js/init.js?v=' + version;
            }
            url_full = SB_INIT_URL;
        } else {
            url_full = getScriptUrl();
        }
        var parameters = getScriptParameters(url_full);
        if ('url' in parameters) {
            url_full = parameters['url'];
        }
        if (typeof SB_TICKETS != 'undefined') {
            parameters['mode'] = 'tickets';
        }
        if (typeof SB_DISABLED != 'undefined' && SB_DISABLED) {
            return;
        }
        if (url_full.lastIndexOf('init.js') > 0) {
            url = url_full.substr(0, url_full.lastIndexOf('init.js') - 4);
        } else {
            url = url_full.substr(0, url_full.lastIndexOf('init.min.js') - 8);
        }
        xhr = sbCorsRequest('GET', url + '/include/init.php' + ('lang' in parameters ? '?lang=' + parameters['lang'] : '') + ('mode' in parameters ? ('lang' in parameters ? '&' : '?') + 'mode=' + parameters['mode'] : ''));
        if (!xhr) {
            console.log('Support Board: Init Error - CORS not supported.');
        } else {
            xhr.onload = function () {
                let target = 'body';
                let tickets = ('mode' in parameters && parameters['mode'] == 'tickets');
                if (tickets && $('#sb-tickets').length) {
                    target = '#sb-tickets';
                }
                $(target).append(xhr.responseText);

                var head = document.getElementsByTagName('head')[0];
                sbCreateStyle(url + '/css/min/' + (tickets ? 'tickets' : 'main') + '.min.css', head);
                sbCreateScript(url + '/js/main.js', head);

                if (tickets) {
                    sbCreateScript(url + '/apps/tickets/tickets.js', head);
                }
            };
            xhr.onerror = function () {
                console.log('Support Board: Init Error - CORS error.');
            };
            xhr.send();
        }
    });

    function sbCorsRequest(method, url) {
        var xhr = new XMLHttpRequest();
        if ('withCredentials' in xhr) {
            xhr.open(method, url, true);
        } else if (typeof XDomainRequest != 'undefined') {
            xhr = new XDomainRequest();
            xhr.open(method, url);
        } else {
            xhr = null;
        }
        return xhr;
    }

    function getScriptUrl(a) {
        var b = document.getElementsByTagName('script');
        for (var i = 0; i < b.length; i++) {
            if (b[i].id == 'sbinit' || b[i].src.indexOf('/supportboard/js/init.js') > -1 || b[i].src.indexOf('/supportboard/js/min/init.min.js') > -1) {
                return b[i].src;
            }
        }
        return '';
    }

    function getScriptParameters(url) {
        var c = url.split('?').pop().split('&');
        var p = {};
        for (var i = 0; i < c.length; i++) {
            var d = c[i].split('=');
            p[d[0]] = d[1]
        }
        return p;
    }

    function sbCreateStyle(url, head) {
        var link = document.createElement('link');
        link.id = 'support-board';
        link.rel = 'stylesheet';
        link.type = 'text/css';
        link.href = url + '?v=' + version;
        link.media = 'all';
        head.appendChild(link);
    }

    function sbCreateScript(url, head) {
        var script = document.createElement('script');
        script.src = url + '?v=' + version;
        head.appendChild(script);
    }
}(jQuery));