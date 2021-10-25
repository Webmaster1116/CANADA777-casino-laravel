var playtechLocationData = {};
var _gls_localStorageKeys = {
    "lastRequestDateKey" : "_gls_lastRequestDate",
    "noPtLogoKey" : "_gls_noPtLogo"
};

(function () {
    var _locStorSupported = (function () {
        var test = 'test';
        try {
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch (e) {
            return false;
        }
    })();

    if (_locStorSupported && localStorage.getItem(_gls_localStorageKeys.lastRequestDateKey) !== null &&
        (new Date().getTime() - localStorage.getItem(_gls_localStorageKeys.lastRequestDateKey) < gls_config.minutesPerGeoLocationRequest * 60 * 1000)) {
        playtechLocationData.noPlaytechLogo = 'true' === localStorage.getItem(_gls_localStorageKeys.noPtLogoKey);
    } else {
        var docHead = document.head;
        var scriptTag = document.createElement('script');
        scriptTag.setAttribute('src', gls_config.path);
        docHead.appendChild(scriptTag);
        if (_locStorSupported) {
            localStorage.setItem(_gls_localStorageKeys.lastRequestDateKey, new Date().getTime());
        }
    }
})();
