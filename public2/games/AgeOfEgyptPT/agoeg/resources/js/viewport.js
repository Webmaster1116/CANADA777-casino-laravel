/**
 * This script contains all viewport-related logic. It scales viewport when <head> section is parsed. Also it contains
 * objects and methods for device/browser detections required in context of this script's logic.
 *
 * Script will perform it's logic only when launched in top frame.
 *
 * The main strategy for viewport scaling is to make all devices to scale and report real physical resolution, except
 * those which has hi-resolution screens (e.g. Full HD+, Retina+). For the latest different strategies applies (e.g.
 * for Retina iPad scaling applies to make it report same resolution as non-retina iPad (1024px),
 * for Androids with Full HD - to make them report 1280px resolution). This is business requirement
 * as old NGM Games don't have hi-res layouts for games...
 *
 * See ViewportManager.createViewport() method for strategy details.
 *
 * See also: https://confluence.playtech.corp/display/MOBUA/Universal+Viewport+Solution
 * Presentation: http://prezi.com/b7bukfhy8xyz/universal-viewport-solution/
 * To understand concepts of visual/layout/ideal viewports see: http://www.quirksmode.org/mobile/viewports.html
 *
 * @author Andriy Halyasovskyy
 * @version 16.06.06
 * @email thebit@i.ua
 *
 * =====================================================================================================================
 * Release notes:
 *
 * 17.03.10
 * - NGM-117710 - UC Chinese 11.4 incorrect scaling
 *
 * 16.06.06
 * - Fixed incorrect condition in Device.getDPR() for Nexus 6 and UC Browser, now should be scaled correctly
 *
 * 16.06.01
 * - Fixed Xiaomi Mi4 LTE Default browser (Du) incorrect scaling with disabled T5 engine (NGM-69700)
 *
 * 16.05.26
 * - Fixed UC and UC CN browsers scaling on Nexus 5x, 6, 6P
 *
 * 16.05.25
 * - Added support for next devices and browsers (NGM-55399/NGM-67147):
 *       * Nexus 5x, Nexus 6P Chrome browser
 *       * Xiaomi Mi Note (4G) Chrome, Default, Native Wrapper, Opera, FF, QQ, Baidu, Du, Du HD, Du Mini,
 *           UC, UC CN, 360 browsers
 *       * Oppo R7c Android 4 Default browser and Oppo R7 Android 5 Default browser
 *       * Lenovo A850 UC Browser
 *
 * - Fixed CP not loading if cookies disabled in browser settings by wrapping localStorage call with try-catch
 *
 * - Fixed cross-domain issue, see NGM-57791
 * - Fixed Block screen appear during resizing game window in desktop Edge, see NGM-56342
 * - Fixed other issues, see NGM-53991, NGM-34539, NGM-34494
 * - .header renamed to .debugHeader in debug widget due to Hub on Betfair hides .header in bundle.css
 * - Added URL trigger param "noVP" which will disable all logic of this script leaving HTML page without viewport metatag
 *
 * 16.02.04
 * - Turning off solution for Desktop, see NGM-46827 Wrong page scale on Desktop IE10
 *
 * 15.12.01
 * - Added iPad Pro support, see NGM-39349
 *       Added "iPadProCorrectScale" URL param which will make iPad Pro to scale to more correct 1366 resolution instead of 1024
 *
 * - Minor improvement to workaround for Safari Standalone on iPhone iOS 8.3+ incorrect scale after rotation (and
 *   after reload) - now workaround won't be applied for iOS 9.0.1+ due to Apple fixed the bug:
 *   https://confluence.playtech.corp/pages/viewpage.action?pageId=108748385
 *
 * - Now Debug Widget will update its values with extra delayed run after rotation/resize due to Lumia's slow redraw
 *
 * 15.09.25
 * - Improved workaround for Viewport scale problem after first open on Lumia 635/820/920 in IE and Edge,
 *   see: NGM-23366, NGM-15558. Now "double tap" overlay won't be triggered upon zoom-in to input field in IE 11.
 *
 * 15.09.14
 * - Added support for Samsung Galaxy Tab S 10.5 Native Wrapper and Microsoft Surface Pro 3 Edge
 *
 * - Improved workaround for Safari Standalone on iPhone iOS 8.3+ incorrect scale after rotation (and after reload),
 *       see: NGM-8202, NGM-25292, NGM-23878.
 *       Refactored to detect when scaling issue happens and to reset viewport to initial-scale = 1.0 afterwards.
 *       Plus now debug widget will update viewport content display on resize/orientation-change as well.
 *
 * 15.08.04
 * - Removed debug URL trigger param for Debug Widget to not be easily displayed on Production, also
 *       to not impact autotesters, see NGM-21152 and NGM-20903
 * - Adjusted workaround for Viewport scale problem after first open for Lumia 820/920, WP 8.0 (GDR 3), see: NGM-15558
 *
 * 15.07.29
 * - Added workaround for Viewport scale problem after first open on Lumia 635/820/920 in IE and Edge, see: NGM-15558
 * - Added debug and debugInVP URL trigger params which will now display Debug Widget with useful info
 * - Fixed UC Browser HD incorrect scaling in Galaxy Tab 3 10.1, 4 10.1 and S 10.1, see: NGM-19784
 *
 * 15.07.08
 * - Temporary exposing back all objects to window even if in frame due to NGM Core (turned-out) is using some of them
 * - Added Viewport.getViewportValue() back for backward compatibility (for Hub)
 * - ScalingReport will now work correctly when inside iFrame (it will try to access own mirror instance in top frame)
 * - Added workaroundsOff and workaroundsOffInVP URL trigger params handling which are meant to disable any workarounds
 *
 * 15.06.22
 * - Refactored in context of memory and CPU utilization and organized better namespace management
 *
 * - Added workaround for Safari Standalone on iPhone iOS 8.3+ incorrect scale after rotation,
 *       see: NGM-8202 (iOS 9.0 beta also affected).
 *       Fixed by resetting viewport to initial-scale = 1.0 during first rotation.
 *
 * - iPhone 6+ Zoom Mode support added, see https://support.apple.com/en-us/HT203073 and NGM-13399
 *
 * - Added support for next devices and browsers:
 *       * Nexus 6
 *       * Windows Phone OS (8 and 10 preview) for at least Lumia 820, 920, 925 and
 *           IE (10 [except desktop mode!], 11, Edge) and also UCBrowser on Windows Phone.
 *           Now resolution on these devices will correspond to real one instead of 1024px in width (for both L & P)
 *       * Meizu (MX4, MX4 Pro, M1 Note) Default browser
 *       * Xiaomi Mi4 Default browser
 *
 * - Added ScalingReport object which can be used for automated testing or other purposes
 *       (e.g. ScalingReport.multiplicationRatio())
 *
 * 15.01.15
 * - Added support for Native Wrapper at Samsung Galaxy S4 LTE (GT-I9505)
 * - Added support for old Chrome 25 version
 * - Added support for UCBrowser
 *
 * 14.10.21
 * - Added support for iPhone 6 and 6 Plus
 *
 * 14.07.11
 * - Added support for all Android Native Wrappers
 *
 * 14.07.04
 * - Complete re-architecture to switch from exceptional target-dpi map to exceptional device map
 * - Added support for Webkit browsers at LG G2 and Samsung Note 3 Android 4.3
 *
 * 13.03.22
 * - Initial version
 */

/**
 * @returns {boolean} true if script running inside iFrame (no matter in which level), else otherwise
 */
function inIframe() {
    try {
        return window.self !== window.top;
    } catch (e) {
        return true;
    }
}

/**
 * @returns {boolean} true if script running at least inside 2-nd level iFrame, else otherwise
 */
function inIframeDeeply() {
    try {
        return window.self !== window.top && window.parent !== window.top;
    } catch (e) {
        return true;
    }
}

window.MagicViewportContent = {
    content1 : "target-densitydpi=320, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0",
    content2 : "user-scalable=no, initial-scale=1.0, maximum-scale=0.5, minimum-scale=0.5",
    content3 : "user-scalable=no, initial-scale=1.0, maximum-scale=0.75, minimum-scale=0.75",
    content4 : "user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0",
    content5 : "user-scalable=no, initial-scale=1, maximum-scale=0.5765, minimum-scale=0.5765",
    content6 : "target-densitydpi=152, user-scalable=no",
    content7 : "user-scalable=no, initial-scale=1.0, maximum-scale=0.5457, minimum-scale=0.5457",
    content8 : "target-densitydpi=293, user-scalable=no"
};

/*
 NB: When using RegExp constructor function, the normal string escape rules (preceding special characters with \
 when included in a string) are necessary. For example, the following are equivalent:
 var re = /\w+/;
 var re = new RegExp("\\w+");
 https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/RegExp

 Also use these resources for developing/debugging regular expressions http://www.regexr.com and https://regex101.com
 */
window.Constants = {
    viewportSolutionVersion : "17.03.10",
    //Source: http://en.wikipedia.org/wiki/Comparison_of_Android_devices + iOS and Lumia devices
    // + 801 for Samsung Galaxy S & S2 as they report 534*1.5=801;
    // + 1281 for Samsung Galaxy Tab 3 10.1 UC Browser HD as it reports screen.width = 1281;
    // read about this constant in Screen.selectClosestResolution()
    mobileDevicesUniqueResolutions : [2732, 2560, 2160, 2048, 1920, 1440, 1334, 1281, 1280, 1136, 1024, 960, 854, 801, 800, 640, 480, 400, 320],
    highResScreenThreshold : 1920,
    maxSupportedResolution : 1280,
    maxIpadSupportedResolution : 1024,
    oldChromeVersionThreshold : 25, //read about this constant in Screen.convertFromDipsToPx()
    iPadProWidenessInDIPs : 1366, //screen width in DIPs
    iPadWidenessInDIPs : 1024, //screen width in DIPs
    iPhone6WidenessInPx : 1334, //physical screen wideness
    iPhone6PlusWidenessInPx : 1920, //physical screen wideness
    iPhone6PlusWidenessInDIPs : 736, //https://en.wikipedia.org/wiki/Device_independent_pixel, aka screen.width, aka ideal viewport width
    lumia920WidenessInPx : 1280, //IE 10 doesn't report window.devicePixelRatio, so this needed for Device.getDPR()
    lumia820WidenessInPx : 800,
    nexus6WidenessInPx : 2560,
    tab3_10_1WidenessInPx : 1280,
    tooStretchedAspectRatioThreshold : 4, //Exceeds 21:9 which is 2.33
    tooSquareAspectRatioThreshold : 1.3, //Exceeds 4:3 which is 1.33

    exceptionalDevices : {
        "(?:Windows(?!.+Phone)|WPDesktop|Macintosh|X11)" : "", //Disabling viewport for Desktop
        "Android 4\\.3.+SM-N900.+Version" : MagicViewportContent.content1, //Samsung Galaxy Note 3 Android 4.3 Webkit browser
        "GT-I9505.+Version(?!.+Chrome)" : MagicViewportContent.content1, //Samsung Galaxy S4 LTE Native Wrapper
        "HTC.One.+Version(?!.+Chrome)" : MagicViewportContent.content1, //HTC One Webkit browser
        "HTC.One.+Version.+Chrome" : MagicViewportContent.content2, //HTC One Wrapper
        "LG-D802.+Version" : MagicViewportContent.content2, //LG G2 Webkit browser
        "(?:MX4|m1 note).+Version" : MagicViewportContent.content1, //Meizu (MX4, MX4 Pro, M1 Note) Default browser
        "MI 4.+Version.+T5.+bdbrowser" : MagicViewportContent.content1, //Xiaomi Mi4 Default browser with T5 engine
        "MI NOTE LTE.+XiaoMi\/MiuiBrowser" : MagicViewportContent.content7, //XiaoMi Mi Note LTE Android 6.0.1 Default browser
        "MI NOTE LTE.+MQQBrowser" : MagicViewportContent.content8, //XiaoMi Mi Note LTE Android 6.0.1 QQ browser
        "MI NOTE LTE.+bdbrowser\/1" : MagicViewportContent.content8, //XiaoMi Mi Note LTE Android 6.0.1 Du HD browser
        "MI NOTE LTE.+bdbrowser" : MagicViewportContent.content7, //XiaoMi Mi Note LTE Android 6.0.1 Du browser
        "MI NOTE LTE.+baidubrowser" : MagicViewportContent.content7, //XiaoMi Mi Note LTE Android 6.0.1 Baidu browser
        "MI NOTE LTE.+Aphone browser" : MagicViewportContent.content7, //XiaoMi Mi Note LTE Android 6.0.1 Baidu browser
        "MI NOTE LTE.+Version.+Chrome" : MagicViewportContent.content7, //XiaoMi Mi Note LTE Android 6.0.1 Native wrapper
        "R7.+OppoBrowser" : MagicViewportContent.content2, //Oppo R7c, Android 4.4.4 Default browser
        "R7.+Version.+Chrome" : MagicViewportContent.content1, //Oppo R7, Android 5.1.1 Default browser
        "Nexus 6\\s" : MagicViewportContent.content5, //Nexus 6
        "Lenovo_A850.+UCBrowser" : MagicViewportContent.content1, //Lenovo A850 UC Browser
        "SM-T80.+Version.+Chrome" : MagicViewportContent.content4, //Galaxy Tab S 10.5 Wrapper and Android Webkit
        "Android(?!.+Nexus (?:7|10))(?=.+Version\/4.0.+Chrome)(?!.+UCBrowser)" : MagicViewportContent.content2, //Android Native Wrapper except Nexus 7 and 10
        "Android.+Nexus 7.+Version.+Chrome" : MagicViewportContent.content3, //Android Native Wrapper exact in Nexus 7 2013
        "GT-P52.+Chrome.+UCBrowser" : MagicViewportContent.content6 //Galaxy Tab 3 10.1 UC Browser HD
    },

    DOM : {
        viewport : "viewport", //ID to assign to viewport tag
        viewportStyleTag : "cssViewportStyleTag" //ID to assign to viewport style tag (for IE)
    },

    i18n : {
        doubleTap : {
            "EN" : "Please double tap to continue.",
            "BG" : "Докоснете двукратно, за да продължите.",
            "CH" : "請點選兩下以繼續。",
            "CS" : "Pro pokračování dvakrát klepněte, prosím.",
            "DA" : "Tap to gange for at fortsætte.",
            "DE" : "Doppelklick um fortzufahren.",
            "ES" : "Pulse dos veces para continuar.",
            "ES-MX" : "Toque dos veces para continuar.",
            "FI" : "Napauta kahdesti jatkaaksesi.",
            "IT" : "Effettua un doppio tocco per continuare.",
            "JA" : "続けるにはダブルタップしてください。",
            "KO" : "두 번 탭하여 계속하세요.",
            "MS" : "Sila ketik dua kali untuk meneruskan.",
            "NL" : "Dibbeltik om door te gaan.",
            "RU" : "Пожалуйста, щелкните два раза для продолжения.",
            "SK" : "Pokračujte dvojitým ťuknutím.",
            "SV" : "Dubbeltryck för att fortsätta.",
            "ZH-CN" : "请双击以继续。"
        }
    }
};

/**
 * Minimum memory resources instance to report results of work done by this library and other potentially useful info
 */
window.ScalingReport = (function() {
    var storage = {
        scaleFactor : null,
        actualDPR : null,
        deviceScreenWidth : null,
        deviceScreenHeight : null,
        deviceScreenWideness : null,
        deviceScreenNarrowness : null,
        deviceScreenIsHighResolution : null,
        browserVersionRaw : null,
        browserVersionMajor : null,
        browserVersionMinor : null,
        browserVersionRawBase : null,
        browserVersionMajorBase : null,
        browserVersionMinorBase : null
    };

    return {
        scaleFactor: function (value) {
            if (!arguments.length) return storage["scaleFactor"];
            storage.scaleFactor = value;
        },

        actualDPR: function (value) {
            if (!arguments.length) return storage["actualDPR"];
            storage.actualDPR = value;
        },

        multiplicationRatio: function () {
            return this.actualDPR() * this.scaleFactor();
        },

        deviceScreenWidth: function (value) {
            if (!arguments.length) return storage["deviceScreenWidth"];
            storage.deviceScreenWidth = value;
        },

        deviceScreenHeight: function (value) {
            if (!arguments.length) return storage["deviceScreenHeight"];
            storage.deviceScreenHeight = value;
        },

        deviceScreenWideness: function (value) {
            if (!arguments.length) return storage["deviceScreenWideness"];
            storage.deviceScreenWideness = value;
        },

        deviceScreenNarrowness: function (value) {
            if (!arguments.length) return storage["deviceScreenNarrowness"];
            storage.deviceScreenNarrowness = value;
        },

        deviceScreenIsHighResolution: function (value) {
            if (!arguments.length) return storage["deviceScreenIsHighResolution"];
            storage.deviceScreenIsHighResolution = value;
        },

        browserVersionRaw: function (value) {
            if (!arguments.length) return storage["browserVersionRaw"];
            storage.browserVersionRaw = value;
        },

        browserVersionMajor: function (value) {
            if (!arguments.length) return storage["browserVersionMajor"];
            storage.browserVersionMajor = value;
        },

        browserVersionMinor: function (value) {
            if (!arguments.length) return storage["browserVersionMinor"];
            storage.browserVersionMinor = value;
        },

        browserVersionRawBase: function (value) {
            if (!arguments.length) return storage["browserVersionRawBase"];
            storage.browserVersionRawBase = value;
        },

        browserVersionMajorBase: function (value) {
            if (!arguments.length) return storage["browserVersionMajorBase"];
            storage.browserVersionMajorBase = value;
        },

        browserVersionMinorBase: function (value) {
            if (!arguments.length) return storage["browserVersionMinorBase"];
            storage.browserVersionMinorBase = value;
        }
    }
})();

/**
 * Mini jQuery
 */
var DOM = (function() {
    function getEl(elem) {
        return typeof elem === "string" ? DOM.get(elem) : elem;
    }

    return {
        get: function(elemID) {
            return document.getElementById(elemID);
        },

        /**
         * Warning! Not safe! Sets display always to "block"!
         * @param elemID
         */
        show: function(elemID) {
            getEl(elemID).style.display = "block";
        },

        hide: function(elemID) {
            getEl(elemID).style.display = "none";
        },

        text: function(elemID, value) {
            if (arguments.length == 1) {
                return getEl(elemID).innerText;
            } else {
                getEl(elemID).innerText = value;
            }
        },

        html: function(elemID, value) {
            if (arguments.length == 1) {
                return getEl(elemID).innerHTML;
            } else {
                getEl(elemID).innerHTML = value;
            }
        },

        add: function(elem, elemID, content) {
            var el = document.createElement(elem);
            elemID ? el.id = elemID : null;
            content ? el.innerHTML = content : null;
            document.body.appendChild(el);
            return el;
        },

        addStyle: function(content) {
            var styleTag = document.createElement("style");
            styleTag.type = "text/css";
            styleTag.innerHTML = content;
            document.getElementsByTagName("head")[0].appendChild(styleTag);
            return styleTag;
        },

        data: function(elemID, dataName, value) {
            if (arguments.length == 2) {
                return getEl(elemID).getAttribute("data-" + dataName);
            } else {
                getEl(elemID).setAttribute("data-" + dataName, value);
            }
        },

        css: function(elemID, styleName, value) {
            if (arguments.length == 2) {
                return getEl(elemID).style[styleName];
            } else {
                getEl(elemID).style[styleName] = value;
            }
        }
    };
})();

/**
 * Utilities for working with URL params, local storage, i18n, etc.
 */
var Util = (function() {
    var languageParam = "language";
    var langParam = "lang";
    var localStorageParamPrefix = "_url-param_";
    var localStorageViewportPrefix = "_viewport_";

    return {
        isUrlTriggerParamPresent: function(name) {
            return new RegExp("[?&]" + name + "(?:$|=|&)", "i").test(location.search);
        },

        getUrlParamValue: function(name) {
            var result = new RegExp("[?&]" + name + "=(?:(.+)&|(.+)$(?!&))", "i").exec(location.search);

            if (result) {
                for (var i = 1; i < result.length; i++) {
                    if (result[i]) {
                        return decodeURIComponent(result[i]);
                    }
                }
            }

            return null;
        },

        getLocalized: function(constant) {
            var result = constant[Util.getLang()];
            return result ? result : constant.EN;
        },

        getLang: function() {
            var result;

            if (Util.getUrlParamValue(languageParam)) {
                result = Util.getUrlParamValue(languageParam);
            } else if (Util.getUrlParamValue(langParam)) {
                result = Util.getUrlParamValue(langParam);
            } else if (Util.recallUrlParam(languageParam)) {
                result = Util.recallUrlParam(languageParam);
            } else if (Util.recallUrlParam(langParam)) {
                result = Util.recallUrlParam(langParam);
            } else {
                result = "EN";
            }

            return result.toUpperCase();
        },

        recallUrlParam: function(paramName) {
            try {
                if (localStorage) {
                    return localStorage.getItem(localStorageParamPrefix + paramName);
                }
            } catch (e) {
                Logger.logError("Can't use local storage, most probably cookies disabled in browser settings! " + e);
            }
        },

        memoItem: function(itemName, itemValue) {
            try {
                if (localStorage) {
                    localStorage.setItem(localStorageViewportPrefix + itemName, itemValue);
                }
            } catch (e) {
                Logger.logError("Can't use local storage, most probably cookies disabled in browser settings! " + e);
            }
        },

        recallItem: function(itemName) {
            try {
                if (localStorage) {
                    return localStorage.getItem(localStorageViewportPrefix + itemName);
                }
            } catch (e) {
                Logger.logError("Can't use local storage, most probably cookies disabled in browser settings! " + e);
            }
        },

        /**
         * Run function with small delay so that for example internal browser layout redrawing finishes after rotation.
         * @param func pointer to function to call to after delay
         */
        safeRun: function(func) {
            setTimeout(func, 300);
        },

        /**
         * Run function with longer delay so that for example double tap overlay for Lumia and subsequent fix (zoom
         * back to normal) are finished.
         * @param func pointer to function to call to after delay
         */
        delayedRun: function(func) {
            setTimeout(func, 1000);
        }
    };
})();

/**
 * [Won't be released by GC as contains orientation change handler]
 * Handles workarounds
 */
window.WorkaroundManager = function() {
    /**
     * Bug introduces layout distortion when Safari Standalone in iPhone iOS 8.3+ gets rotated
     * (and/or after rotation web page reload happens). At this moment window dimensions becomes twice larger
     * (and thus it looks twice zoomed-out) and so it can be detected.
     *
     * It also turned-out that setting viewport to 1.0 scale - fixes the problem. Don't ask me, ask Apple...
     */
    var WorkaroundForIncorrectScaleInStandaloneMode = function() {
        function scalingIssueDetected() {
            return Math.max(window.innerWidth, window.innerHeight) > Constants.iPhone6WidenessInPx;
        }

        function fixScalingIssueIfDetected() {
            if (scalingIssueDetected()) {
                DOM.get(Constants.DOM.viewport).setAttribute("content" , MagicViewportContent.content4);
            }
        }

        function init() {
            window.addEventListener("orientationchange", fixScalingIssueIfDetected, false);
            window.addEventListener("load", fixScalingIssueIfDetected, false);
        }

        init();
    };

    /**
     * Bug introduces layout distortion when window viewport (window.innerWidth/innerHeight) falls-down into
     * default device-specific dimension (1024x768 in landscape and 1024x1554 in portrait), meanwhile document
     * viewport stays consistent with what specified in @-ms-viewport CSS rule. This allows to detect the bug itself
     * and display "Please double tap to continue overlay" which workarounds the bug.
     *
     * In IE 11 new behaviour was introduced: zoom to input field, which initially made double tap overlay to trigger
     * also. To eliminate this - smart logic had been added to scalingIssueDetected method: now it checks if aspect
     * ratio stays in "normal" range (not too stretched and not too square), otherwise this is the case with on screen
     * keyboard displayed and zoomed in to input field, so double tap overlay doesn't showing anymore.
     */
    var WorkaroundForIncorrectScaleAfterFirstOpeningInIEAndEdge = function() {
        var styleForBG = null;
        var doubleTapOverlayID = "doubleTapOverlay";
        var doubleTapLabelContainerID = "doubleTapLabelContainerID";

        var normalBGStyle =
            "body {" +
                "-ms-touch-action: none !important;" +
                "touch-action: none !important;" +
            "}";

        var doubleTappableBGStyle =
            "body {" +
                "-ms-touch-action: double-tap-zoom !important;" +
                "touch-action: double-tap-zoom !important;" +
            "}";

        /**
         * Magic Div was found accidentally and it makes the layout distortion automatically to zoom to correct scale.
         * And the "speed" of this zoom depends on left: parameter with -15px is the slowest and -XXXXXpx fast enough
         * to be not noticeable.
         *
         * But there is still bug when user keeps rotating device - after returning from landscape back to landscape -
         * page will be zoomed-in much, and so "Please double tap to continue" overlay will still be required.
         *
         * Don't ask me, ask Microsoft...
         */
        function createDoubleTapOverlay() {
            styleForBG = DOM.addStyle(doubleTappableBGStyle);

            DOM.addStyle(
                "html {" +
                    "overflow: inherit !important;" + //to override overflow:hidden in Hub which prevents magicDiv from fixing the bug
                "}" +

                ".magicDiv {" +
                    "position: absolute;" + //no matter which element or where placed, just anything in the DOM with position
                                            // shifted to at least 15px to the left of the screen
                    "left: -5000px;" + //this controls the speed of automatic resize/zoom-to-normal, with -15px is the slowest
                    "width: 1px;" + //should have some minimal dimensions
                "}" +

                "#" + doubleTapOverlayID + " {" +
                    "display: none;" +
                    "z-index: 10000;" +
                    "position: fixed;" +
                    "width: 100%;" +
                    "height: 100%;" +
                    "background-color: rgba(0, 0, 0, .6);" +
                    "color: white;" +
                    "font-size: 36px;" +
                "}" +

                "#" + doubleTapOverlayID + " .table {" +
                    "display: table;" +
                    "width: 100%;" +
                    "height: 100%;" +
                "}" +

                "#" + doubleTapOverlayID + " .table-cell {" +
                    "display: table-cell;" +
                    "vertical-align: middle;" +
                    "text-align: center;" +
                "}");

            DOM.add("div").className = "magicDiv";

            DOM.add("div", doubleTapOverlayID, "<div id='" + doubleTapLabelContainerID + "' class='table'>" +
                "<div class='table-cell'>" + Util.getLocalized(Constants.i18n.doubleTap) + "</div></div>");
        }

        /**
         * Checks if window viewport is not of same size as document viewport, taking into account aspect ratio, which
         * should stay in "normal" range (not too stretched and not too square).
         *
         * @returns {boolean} true if scaling issue detected, false otherwise
         */
        function scalingIssueDetected() {
            var widerSide = Math.max(window.innerWidth, window.innerHeight);
            var narrowerSide = Math.min(window.innerWidth, window.innerHeight);
            var aspectRatio = widerSide / narrowerSide;

            return window.innerWidth != document.documentElement.clientWidth &&
                aspectRatio < Constants.tooStretchedAspectRatioThreshold &&
                aspectRatio > Constants.tooSquareAspectRatioThreshold;
        }

        function checkIfToDisplayOverlay() {
            if (scalingIssueDetected()) {
                DOM.css(doubleTapLabelContainerID, "width" , window.innerWidth + "px");
                DOM.css(doubleTapLabelContainerID, "height" , window.innerHeight + "px");

                DOM.show(doubleTapOverlayID);
                toggleGameContainerVisibility(true);
                styleForBG.innerHTML = doubleTappableBGStyle;
            } else {
                DOM.hide(doubleTapOverlayID);
                toggleGameContainerVisibility(false);
                styleForBG.innerHTML = normalBGStyle;
            }
        }

        /**
         * Extra workaround for IE Mobile 10. For some reason Double Tap overlay is not double tapping when game is
         * displayed. So the solution is to hide it when overlay is on the screen.
         *
         * @param isToHide if true - set parent element of a canvas to display: none, display: block otherwise
         */
        function toggleGameContainerVisibility(isToHide) {
            //This whole workaround object is already for IE and Edge so no need to double check browser itself
            if (Device.Browser.Version.getMajor() != 10) return;

            var gameContainer;
            if (searchFirstElInFrame(window, "canvas")) {
                //Platform frame level
                gameContainer = searchFirstElInFrame(window, "canvas").parentElement;
            } else if (searchFirstElInFrame(window.frames[0], "canvas")) {
                //Hub frame level
                gameContainer = searchFirstElInFrame(window.frames[0], "canvas").parentElement;
            }

            if (gameContainer) {
                isToHide ? DOM.hide(gameContainer) : DOM.show(gameContainer);
            }
        }

        function searchFirstElInFrame(windowContext, elem) {
            for (var i = 0; i < windowContext.frames.length; i++) {
                if (windowContext.frames[i].document.getElementsByTagName(elem)[0]) {
                    return windowContext.frames[i].document.getElementsByTagName(elem)[0];
                }
            }
        }

        function init() {
            window.addEventListener("load", function() { createDoubleTapOverlay(); Util.safeRun(checkIfToDisplayOverlay); }, false);
            window.addEventListener("resize", function() { Util.safeRun(checkIfToDisplayOverlay); }, false);

            if(window.PointerEvent) {//For IE11 and Edge
                window.addEventListener("pointerdown", function() { Util.delayedRun(checkIfToDisplayOverlay); });
            } else if (window.MSPointerEvent) {//For IE10
                window.addEventListener("MSPointerDown", function() { Util.delayedRun(checkIfToDisplayOverlay); });
            }
        }

        init();
    };

    function isWorkaroundsDisabled() {
        return Util.isUrlTriggerParamPresent("workaroundsOff") || Util.isUrlTriggerParamPresent("workaroundsOffInVP");
    }

    return {
        /**
         * Workaround for NGM-8202, NGM-23878, NGM-25292 - "Invalid viewport scale on iPhone Safari iOS 8.3+ homescreen"
         */
        workaroundForIncorrectScaleInStandaloneMode: function() {
            if (isWorkaroundsDisabled()) return;
            new WorkaroundForIncorrectScaleInStandaloneMode();
        },

        /**
         * Workaround for NGM-15558 - "Viewport scale problem after first open on Lumia 635/820/920 in IE and Edge"
         */
        workaroundForIncorrectScaleAfterFirstOpeningInIEAndEdge: function() {
            if (isWorkaroundsDisabled()) return;
            new WorkaroundForIncorrectScaleAfterFirstOpeningInIEAndEdge();
        }
    };
};

window.DebugWidget = function() {
    var debugWidgetID = "debugWidget";
    var debugPanelContainerID = "debugPanelContainer";
    var debugPanelID = "debugPanel";
    var debugPanelSwitchSideBtnID = "debugPanelSwitchSideBtn";
    var debugPanelExpandCollapseBtnID = "debugPanelExpandCollapseBtn";
    var debugPanelCloseBtnID = "debugPanelCloseBtn";
    var viewportResolution = "viewportResolution";
    var viewportResolution2 = "viewportResolution2";
    var outerWH = "outerWH";
    var screenWH = "screenWH";
    var screenAvailWH = "screenAvailWH";
    var docOffsetWH = "docOffsetWH";
    var docClientWH = "docClientWH";
    var orientationID = "orientationID";
    var removeWidgetBtnID = "removeWidgetBtn";
    var emptyVPBtnID = "emptyVPBtn";
    var restoreVPBtnID = "restoreVPBtn";
    var setVPBtnID = "setVPBtn";
    var reloadBtnID = "reloadBtn";
    var refreshBtnID = "refreshBtn";
    var arrowLeftSymbol = "<";
    var arrowRightSymbol = ">";
    var arrowTopSymbol = "^";
    var arrowBottomSymbol = "_";
    var widgetRemoved = "widgetRemoved";
    var widgetPosition = "widgetPosition";
    var widgetCollapseState = "widgetCollapseState";
    var vpContentID = "vpContent";

    var widgetPosY = 150; //Optimal position to start right after CP's Main Menu button to not overlap with it

    if (inIframe()) {
        widgetPosY = 185; //Shift CP's widget below to not overlap with Hub's one
    }

    if (inIframeDeeply()) {
        widgetPosY = 220; //Shift Game's widget below to not overlap with CP's one
    }

    function getViewportContent() {
        if (DOM.get(Constants.DOM.viewport)) {
            return DOM.get(Constants.DOM.viewport).getAttribute("content");
        } else if (DOM.get(Constants.DOM.viewportStyleTag) != null) {
            return DOM.html(Constants.DOM.viewportStyleTag);
        } else {
            return "Viewport tag was not created!";
        }
    }

    function getViewportResolution() {
        return window.innerWidth  + " x " + window.innerHeight;
    }

    function getScreenWH() {
        return screen.width + " x " + screen.height;
    }

    function getScreenAvailWH() {
        return screen.availWidth + " x " + screen.availHeight;
    }

    function getDocOffsetWH() {
        return document.documentElement.offsetWidth + " x " + document.documentElement.offsetHeight;
    }

    function getDocClientWH() {
        return document.documentElement.clientWidth + " x " + document.documentElement.clientHeight;
    }

    function getOuterWH() {
        return window.outerWidth + " x " + window.outerHeight;
    }

    /**
     * jQuery you say? Hah!
     */
    function createAndDisplay() {
        DOM.addStyle(
            "#" + debugWidgetID + " {" +
                "display: " + (Util.recallItem(widgetRemoved) && Util.recallItem(widgetRemoved) === "true" ? "none" : "block") + ";" +
                "-moz-box-sizing: border-box;" +
                "-webkit-box-sizing: border-box;" +
                "box-sizing: border-box;" +
                "z-index: 20000;" +
                "position: fixed;" +
                "top: " + widgetPosY + "px;" +
                "width: 120px;" +
                "height: 30px;" +
                "line-height: normal;" +
                "background-color: rgba(0, 0, 0, .6);" +
                "color: white;" +
                "font-size: 20px;" +
                "text-align: center;" +
                "padding: 3px;" +
                "border: 1px solid white;" +
            "}" +

            "#" + debugPanelContainerID + " {" +
                "display: none;" +
                "z-index: 20000;" +
                "position: fixed;" +
                "top: 0px;" +
                "padding-top: " + widgetPosY + "px;" +
                "padding-bottom: 1px;" +
                "width: " + "480px;" +
                "height: 100%;" +
                "line-height: 16px;" +
                "-moz-box-sizing: border-box;" +
                "-webkit-box-sizing: border-box;" +
                "box-sizing: border-box;" +
            "}" +

            "#" + debugPanelContainerID + " * {" +
                "-moz-box-sizing: border-box;" +
                "-webkit-box-sizing: border-box;" +
                "box-sizing: border-box;" +
            "}" +

            "#" + debugPanelID + " {" +
                "height: 100%;" +
                "background-color: rgba(0, 0, 0, .6);" +
                "color: white;" +
                "border: 1px solid white;" +
            "}" +

            "#" + debugPanelID + " .debugHeader {" +
                "width: 100%;" +
                "background-color: rgba(0, 0, 0, .2);" +
                "position: absolute;" +
            "}" +

            "#" + debugPanelID + " .debugScroller {" +
                "overflow: auto;" +
                "height: 100%;" +
                "padding: 5px;" +
                "padding-top: 45px;" +
                "font-size: 16px;" +
                "line-height: 1.5;" +
            "}" +

            "#" + debugPanelID + " button {" +
                "width: 80px;" +
                "height: 40px;" +
                "font-size: 20px;" +
                "line-height: 1;" +
                "background-color: rgba(0, 0, 0, .2);" +
                "border: 1px solid white;" +
                "color: white;" +
            "}" +

            "#" + debugPanelID + " .debugScroller button {" +
                "width: 100px;" +
                "height: 42px;" +
                "font-size: 18px;" +
                "line-height: 1;" +
                "background-color: rgba(0, 0, 0, .2);" +
                "border: 1px solid white;" +
                "color: white;" +
                "margin: 5px;" +
            "}" +

            "#" + debugPanelID + " .debugScroller input {" +
                "width: 100px;" +
                "height: 42px;" +
                "margin: 5px;" +
            "}" +

            "#" + debugPanelID + " span {" +
                "font-size: 16px;" +
                "margin-left: 5px;" +
            "}" +

            "#" + debugPanelID + " p {" +
                "font-size: 20px;" +
                "line-height: 1;" +
                "margin: 0;" +
                "padding: 0;" +
            "}" +

            ".debugScroller pre {" +
                "white-space: pre-wrap;" +
                "margin: 0;" +
                "padding: 0;" +
            "}" +

            ".debugScroller td {" +
                "background-color: transparent;" +
            "}");

        DOM.add("div", debugWidgetID, getViewportResolution());

        DOM.add("div", debugPanelContainerID,
                "<div id='" + debugPanelID + "'>" +
                    "<div class='debugHeader'>" +
                        "<button id='" + debugPanelSwitchSideBtnID + "' data-state='at-left'>" + arrowRightSymbol + "</button>" +
                        "<button id='" + debugPanelExpandCollapseBtnID + "' data-state='collapsed'>" + arrowTopSymbol + "</button>" +
                        "<button id='" + debugPanelCloseBtnID + "'>X</button>" +
                        "<span>version: " + Constants.viewportSolutionVersion  + "</span>" +
                        "<span id='" + viewportResolution + "'>(" + getViewportResolution()  + ")</span>" +
                    "</div>" +

                    "<div class='debugScroller'>" +
                        "<button id='" + removeWidgetBtnID + "'>Remove this widget</button>" +
                        "<button id='" + emptyVPBtnID + "'>Clear viewport</button>" +
                        "<button id='" + restoreVPBtnID + "'>Restore viewport</button>" +
                        "<button id='" + setVPBtnID + "'>Custom viewport</button>" +
                        "<button id='" + reloadBtnID + "'>Reload</button>" +
                        "<button id='" + refreshBtnID + "'>Refresh</button>" +
                        "<input type='text'>" +

                        (!inIframe() ? "<p>[Top frame]</p><br>" : inIframeDeeply() ?
                            "<p>[2nd+ level frame]</p><br>" : "<p>[1st level frame]</p><br>") +

                        "<p>Viewport is set to:</p><pre id='" + vpContentID + "'>" + getViewportContent() + "</pre><br>" +
                        "<p>User-Agent is:</p><pre>" + navigator.userAgent + "</pre><br>" +

                        "<table>" +
                            "<tr><th colspan='2'>Browser properties:</th></tr>" +
                            "<tr><td>screen.width x H: </td><td id='" + screenWH + "'>" + getScreenWH() + "</td></tr>" +
                            "<tr><td>screen.availWidth x H: </td><td id='" + screenAvailWH + "'>" + getScreenAvailWH() + "</td></tr>" +
                            "<tr><td>doc.docEl.offsetWidth x H: </td><td id='" + docOffsetWH + "'>" + getDocOffsetWH() + "</td></tr>" +
                            "<tr><td>doc.docEl.clientWidth x H: </td><td id='" + docClientWH + "'>" + getDocClientWH() + "</td></tr>" +
                            "<tr><td>window.innerWidth x H: </td><td id='" + viewportResolution2 + "'>" + getViewportResolution() + "</td></tr>" +
                            "<tr><td>window.outerWidth x H: </td><td id='" + outerWH + "'>" + getOuterWH() + "</td></tr>" +
                            "<tr><td>window.navigator.standalone: </td><td>" + window.navigator.standalone + "</td></tr>" +
                            "<tr><td>window.devicePixelRatio: </td><td>" + window.devicePixelRatio + "</td></tr>" +
                            "<tr><td>window.orientation: </td><td id='" + orientationID + "'>" + window.orientation + "</td></tr>" +

                            "<tr><th colspan='2'>ScalingReport properties:</th></tr>" +
                            "<tr><td>Viewport.getViewportValue(): </td><td>" + Viewport.getViewportValue() + "</td></tr>" +
                            "<tr><td>ScalingReport.scaleFactor(): </td><td>" + ScalingReport.scaleFactor() + "</td></tr>" +
                            "<tr><td>ScalingReport.actualDPR(): </td><td>" + ScalingReport.actualDPR() + "</td></tr>" +
                            "<tr><td>ScalingReport.multiplicationRatio(): </td><td>" + ScalingReport.multiplicationRatio() + "</td></tr>" +
                            "<tr><td>ScalingReport.deviceScreenWidth(): </td><td>" + ScalingReport.deviceScreenWidth() + "</td></tr>" +
                            "<tr><td>ScalingReport.deviceScreenHeight(): </td><td>" + ScalingReport.deviceScreenHeight() + "</td></tr>" +
                            "<tr><td>ScalingReport.deviceScreenWideness(): </td><td>" + ScalingReport.deviceScreenWideness() + "</td></tr>" +
                            "<tr><td>ScalingReport.deviceScreenNarrowness(): </td><td>" + ScalingReport.deviceScreenNarrowness() + "</td></tr>" +
                            "<tr><td>ScalingReport.deviceScreenIsHighResolution(): </td><td>" + ScalingReport.deviceScreenIsHighResolution() + "</td></tr>" +
                        "</table>" +
                    "</div>" +
                "</div>"
        );
    }

    function updateValues() {
        DOM.text(debugWidgetID, getViewportResolution());
        DOM.text(viewportResolution, "(" + getViewportResolution() + ")");
        DOM.text(viewportResolution2, getViewportResolution());
        DOM.text(screenWH, getScreenWH());
        DOM.text(screenAvailWH, getScreenAvailWH());
        DOM.text(docOffsetWH, getDocOffsetWH());
        DOM.text(docClientWH, getDocClientWH());
        DOM.text(outerWH, getOuterWH());
        DOM.text(orientationID, window.orientation);
        DOM.text(vpContentID, getViewportContent());
    }

    var storedVPContent = ""; //for restore button

    /**
     * Assuming that DOM is ready
     */
    function initAndAssignHandlers() {
        storedVPContent = getViewportContent();

        window.addEventListener("resize", function () { updateValues(); Util.safeRun(updateValues); }, false);
        window.addEventListener("orientationchange", function () { updateValues(); Util.safeRun(updateValues); }, false);

        DOM.get(removeWidgetBtnID).addEventListener("click", function () {
            DOM.hide(debugWidgetID);
            DOM.hide(debugPanelContainerID);
            Util.memoItem(widgetRemoved, true);
        }, false);

        DOM.get(emptyVPBtnID).addEventListener("click", function () {
            if (DOM.get(Constants.DOM.viewport)) {
                DOM.get(Constants.DOM.viewport).setAttribute("content" , "");
            } else if (DOM.get(Constants.DOM.viewportStyleTag) != null) {
                DOM.html(Constants.DOM.viewportStyleTag, "");
            }
        }, false);

        DOM.get(restoreVPBtnID).addEventListener("click", function () {
            if (DOM.get(Constants.DOM.viewport)) {
                DOM.get(Constants.DOM.viewport).setAttribute("content" , storedVPContent);
            } else if (DOM.get(Constants.DOM.viewportStyleTag) != null) {
                DOM.html(Constants.DOM.viewportStyleTag, storedVPContent);
            }
        }, false);

        DOM.get(setVPBtnID).addEventListener("click", function () {
            var customVPContent = prompt("Type custom viewport content", "width=device-width, initial-scale=1.0");
            if (!customVPContent) return;

            if (DOM.get(Constants.DOM.viewport)) {
                DOM.get(Constants.DOM.viewport).setAttribute("content" , customVPContent);
            } else if (DOM.get(Constants.DOM.viewportStyleTag) != null) {
                DOM.html(Constants.DOM.viewportStyleTag, customVPContent);
            }
        }, false);

        DOM.get(reloadBtnID).addEventListener("click", function () {
            window.location.reload();
        }, false);

        DOM.get(refreshBtnID).addEventListener("click", function () {
            updateValues();
        }, false);

        DOM.get(debugWidgetID).addEventListener("click", function () {
            DOM.hide(debugWidgetID);
            DOM.show(debugPanelContainerID);
        }, false);

        DOM.get(debugPanelCloseBtnID).addEventListener("click", function () {
            DOM.hide(debugPanelContainerID);
            DOM.show(debugWidgetID);
        }, false);

        function stickToRight() {
            DOM.html(debugPanelSwitchSideBtnID, arrowLeftSymbol);
            DOM.css(debugPanelContainerID, "right", "0");
            DOM.css(debugWidgetID, "right", "0");
        }

        function stickToLeft() {
            DOM.html(debugPanelSwitchSideBtnID, arrowRightSymbol);
            DOM.css(debugPanelContainerID, "right", "");
            DOM.css(debugWidgetID, "right", "");
        }

        if (Util.recallItem(widgetPosition)) {
            DOM.data(debugPanelSwitchSideBtnID, "state", Util.recallItem(widgetPosition));

            if (Util.recallItem(widgetPosition) == "at-right") {
                stickToRight();
            } else {
                stickToLeft();
            }
        }

        DOM.get(debugPanelSwitchSideBtnID).addEventListener("click", function () {
            if (DOM.data(debugPanelSwitchSideBtnID, "state") == "at-left") {
                DOM.data(debugPanelSwitchSideBtnID, "state", "at-right");
                Util.memoItem(widgetPosition, "at-right");

                stickToRight();
            } else {
                DOM.data(debugPanelSwitchSideBtnID, "state", "at-left");
                Util.memoItem(widgetPosition, "at-left");

                stickToLeft();
            }
        }, false);

        function expand() {
            DOM.html(debugPanelExpandCollapseBtnID, arrowBottomSymbol);
            DOM.css(debugPanelContainerID, "paddingTop", "0");
            DOM.css(debugPanelContainerID, "width", "100%");
        }

        function collapse() {
            DOM.html(debugPanelExpandCollapseBtnID, arrowTopSymbol);
            DOM.css(debugPanelContainerID, "paddingTop", widgetPosY + "px");
            DOM.css(debugPanelContainerID, "width", "480px");
        }

        if (Util.recallItem(widgetCollapseState)) {
            DOM.data(debugPanelExpandCollapseBtnID, "state", Util.recallItem(widgetCollapseState));

            if (Util.recallItem(widgetCollapseState) == "expanded") {
                expand();
            } else {
                collapse();
            }
        }

        DOM.get(debugPanelExpandCollapseBtnID).addEventListener("click", function () {
            if (DOM.data(debugPanelExpandCollapseBtnID, "state") == "collapsed") {
                DOM.data(debugPanelExpandCollapseBtnID, "state", "expanded");
                Util.memoItem(widgetCollapseState, "expanded");

                expand();
            } else {
                DOM.data(debugPanelExpandCollapseBtnID, "state", "collapsed");
                Util.memoItem(widgetCollapseState, "collapsed");

                collapse();
            }
        }, false);
    }

    return {
        init: function() {
            window.addEventListener("load", function() {
                createAndDisplay();
                initAndAssignHandlers();
            }, false);
        }
    };
};

//Create anonymous scope-object and launch its constructor (which will create necessary objects in local scope and
//launch entry point) only if inside top frame. This is needed to simplify memory management.
var Logger = (function() {
    return {
        logDelimiterLine: function() {
            this.log("----------------------------------------------------------------");
        },

        log: function(message) {
            if (console && console.info) {console.info("[INFO] [viewport.js] " + message);}
        },

        logError: function(message) {
            if (console && console.error) {console.error("[ERROR] [viewport.js] " + message);}
        }
    };
})();

/**
 * Creates viewport. Contains private low-level method to create meta tag and add it to DOM and contains public hi-level
 * methods to create viewport with different required content.
 */
var Viewport = (function() {
    /**
     * Need to be called when <head> section is parsed as for iOS devices (at least) it don't take effect right away if
     * called onLoad (e.g. GWT EntryPoint.onModuleLoad())
     */
    function createViewport(content) {
        Logger.logDelimiterLine();
        Logger.log("Creating viewport with next content: " + content);
        Logger.logDelimiterLine();

        var viewportMetaTag = document.createElement("meta");
        viewportMetaTag.setAttribute("id", Constants.DOM.viewport);
        viewportMetaTag.setAttribute("name", Constants.DOM.viewport);
        viewportMetaTag.setAttribute("content", content);

        document.getElementsByTagName("head")[0].appendChild(viewportMetaTag);
    }

    /**
     * This particular implementation of dynamic styles creation might not be cross-browser and stable, but it is
     * confirmed to work at least on supported Window Phones.
     *
     * @param widthL width for landscape
     * @param widthP width for portrait
     */
    function createCSSViewport(widthL, widthP) {
        DOM.addStyle(
            "@media screen and (orientation: landscape) {"+
                "@-ms-viewport {"+
                    "width: " + widthL + "px;"+
                "}"+
            "}"+

            "@media screen and (orientation: portrait) {"+
                "@-ms-viewport {"+
                    "width: " + widthP + "px;"+
                "}"+
            "}")
            .id = Constants.DOM.viewportStyleTag;
    }

    return {
        /**
         * As Web-standards evolve this universal viewport settings should work correctly for major modern mobile OS's
         * and browsers, see: http://dev.w3.org/csswg/css-device-adapt/#viewport-meta
         */
        setViewport: function(scale) {
            this.scale = scale; //for backward compatibility with Hub
            createViewport("user-scalable=no, " +
                "initial-scale=" + scale + ", maximum-scale=" + scale + ", minimum-scale=" + scale);
        },

        /**
         * This method targeted for Androids with Android WebKit browser in which initial-scale works incorrectly.
         * Setting target-densitydpi=device-dpi makes viewport scaling to real physical resolution of device,
         * see: http://developer.android.com/guide/webapps/targeting.html#ViewportDensity
         * Setting initial, min, max-scale together with target-densitydpi is required to ensure user won't scale the
         * page manually as it was found that user-scalable=no doesn't work correctly on all devices...
         *
         * Support for Android specific target-densitydpi property has being removed from WebKit,
         * see: http://trac.webkit.org/changeset/119527 so this is only for old browsers.
         *
         * Also then NGM Core will rely on window.outerWidth/Height for Android to find-out resolution which is best
         * choice as it ignores browser address bar (as it will be hidden after game loaded with window.scrollTo(0, 1)).
         */
        setViewportAndroidWebkit: function(targetDpi) {
            var content = "target-densitydpi=" + targetDpi + ", user-scalable=no, " +
                "initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0";
            createViewport(content);
        },

        /**
         * Usual viewport meta tag has completely buggy and partial implementation so it can be treated as not supported
         * at all by Microsoft (only some sort of support looks like appearing in upcoming Edge browser):
         * http://blogs.msdn.com/b/iemobile/archive/2011/01/21/managing-the-browser-viewport-in-windows-phone-7.aspx
         * http://blogs.msdn.com/b/iemobile/archive/2010/11/22/the-ie-mobile-viewport-on-windows-phone-7.aspx
         *
         * But it turned-out that IE on Windows Phone is playing nicely with @viewport CSS at-rule:
         * https://msdn.microsoft.com/en-us/library/windows/apps/hh868813.aspx
         * https://developer.mozilla.org/en-US/docs/Web/CSS/@viewport
         *
         * But mainly only Microsoft supports it (partially) ATM:
         * http://caniuse.com/#search=%40viewport
         *
         * -ms-touch-action allows to disable double tap zoom and user manual zoom (but still buggy - can be still
         * zoomed during page scroll):
         * https://msdn.microsoft.com/en-us/library/windows/apps/hh767313.aspx
         *
         * @param widthL width for landscape
         * @param widthP width for portrait
         */
        setViewportIE: function (widthL, widthP) {
            createCSSViewport(widthL, widthP);
        },

        /**
         * This would be as first check before any logic of viewport.js. If device is in exceptional devices map - then
         * just take the viewport content from this map and apply. The end.
         */
        setViewportExceptionalDevice: function(content) {
            createViewport(content);
        },

        /**
         * For backward compatibility
         */
        getViewportValue: function () {
            return ScalingReport.scaleFactor();
        }
    };
})();

// --------------------------------------------------------------------------------------------------------------------

/**
 * Contains detection methods for different devices, os's, browsers, modes, screen characteristics, etc.
 */
var Device = (function() {
    return {
        testUserAgent: function(regExp) {
            return regExp.test(navigator.userAgent);
        },

        execUserAgent: function(regExp) {
            return regExp.exec(navigator.userAgent);
        },

        /**
         * Fail-safe getter for devicePixelRatio (it is not supported by IE).
         * window.devicePixelRatio is the ratio between physical pixels and device-independent pixels (DIPs) on device.
         * window.devicePixelRatio = physical pixels / dips.
         *
         * As iPhone 6 Plus reports incorrect (rounded) DPR = 3, this method workarounds the problem by calculating
         * real DPR for mentioned device, by dividing its real screen height (1920) to its reported screen.height (736)
         * which gives ~2.6 which should have being reported by this device instead of rounded value. All this is
         * required as later logic will try to find-out real device's screen resolution by taking DPR value and
         * multiplying it to reported screen.width or height (max value is used), so 736 * ~2.6 = 1920. Without this
         * workaround it would be 736 * 3 = 2208.
         *
         * @returns {number} window.devicePixelRatio if it exists, null otherwise
         */
        getDPR: function() {
            var idealViewportWidthInDIPs = Math.max(screen.width, screen.height);

            if (window.devicePixelRatio) {
                if (Device.isIphone6Plus()) {
                    if (idealViewportWidthInDIPs == Constants.iPhone6PlusWidenessInDIPs) {
                        return Constants.iPhone6PlusWidenessInPx / idealViewportWidthInDIPs; //2,608695652173913
                    } else {//then its "zoom mode" and Safari reports incorrect (hardcoded) DRP which have to be dynamic
                        return Constants.iPhone6WidenessInPx / idealViewportWidthInDIPs; //2
                    }
                } else if ((Device.isNexus6() || Device.isNexus6P()) && !Device.Browser.isUCBrowser()) {
                    return Constants.nexus6WidenessInPx / idealViewportWidthInDIPs; //3,497267759562842
                } else if (Device.isNexus5X() && !Device.Browser.isUCBrowser()) {
                    return Constants.highResScreenThreshold / idealViewportWidthInDIPs; //3,497267759562842
                } else if (Device.isTab3_10_1() && Device.Browser.isUCBrowser()) {
                    return Math.ceil(Constants.tab3_10_1WidenessInPx/idealViewportWidthInDIPs); //to fix 0.999 dpr
                } else if (Device.isXiaoMiNoteLTE() && !Device.Browser.isUCBrowser()) {
                    return Constants.highResScreenThreshold/idealViewportWidthInDIPs; //2,746781115879828 instead of 2.75
                } else {
                    return window.devicePixelRatio;
                }
            } else {
                if (Device.isLumia920() && Device.Browser.isIEMobile() && Device.Browser.Version.getMajor(true) == 10) {
                    return Constants.lumia920WidenessInPx / idealViewportWidthInDIPs; //~2.07
                } if (Device.isLumia820() && Device.Browser.isIEMobile() && Device.Browser.Version.getMajor(true) == 10) {
                    return Constants.lumia820WidenessInPx / idealViewportWidthInDIPs; //1 for IE 10 in GDR2 and ~1.349 in GDR3
                } else {
                    return null;
                }
            }
        },

        isIphone: function() {
            return Device.testUserAgent(/iPhone/i) && !Device.OS.isWindowsPhone();
        },

        /**
         * Warning!
         * As there is no good way to distinguish iPhones, the only possible way was used: to check if
         * window.devicePixelRatio is grater than 2, as all iPhones report it as 2 except iPhone 6 Plus which reports
         * it as 3. If future iPhones will need to have special treatment different from current 6 Plus treatment,
         * and if they will have same User-Agent string and report dpr as 3 - than it will be impossible to distinguish
         * new iPhone from iPhone 6 Plus and thus implement this treatment! God save us all...
         *
         * @returns {boolean} if device is iPhone and its device pixel ratio is higher than 2
         */
        isIphone6Plus: function() {
            return Device.isIphone() && window.devicePixelRatio > 2;
        },

        isIpod: function() {
            return Device.testUserAgent(/iPod/i);
        },

        isIpad: function() {
            return Device.testUserAgent(/iPad/i) && Math.max(screen.width, screen.height) == Constants.iPadWidenessInDIPs;
        },

        isIpadPro: function() {
            return Device.testUserAgent(/iPad/i) && Math.max(screen.width, screen.height) == Constants.iPadProWidenessInDIPs;
        },

        isLumia820: function() {
            return Device.testUserAgent(/Lumia 820/i);
        },

        isLumia920: function() {
            return Device.testUserAgent(/Lumia 920/i);
        },

        isNexus6: function() {
            return Device.testUserAgent(/Nexus 6\s/i);
        },

        isNexus6P: function() {
            return Device.testUserAgent(/Nexus 6P\s/i);
        },

        isNexus5X: function() {
            return Device.testUserAgent(/Nexus 5X/i);
        },

        isTab3_10_1: function() {
            return Device.testUserAgent(/GT-P52/i);
        },

        isXiaoMiNoteLTE: function() {
            return Device.testUserAgent(/MI NOTE LTE/i);
        },

        OS: (function() {
            return {
                isIos: function() {
                    return Device.isIphone() || Device.isIpod() || Device.isIpad();
                },

                isAndroid: function() {
                    return Device.testUserAgent(/Android/i) && !Device.OS.isWindowsPhone();
                },

                isWindowsPhone: function() {
                    return Device.testUserAgent(/Windows\sPhone/i);
                }
            };
        })(),

        Browser: (function() {
            return {
                /**
                 * @returns {boolean} true for Chrome and "new" Android Webkit browser (as there is no clear way how to
                 * distinguish these two), false otherwise.
                 */
                isChrome: function() {
                    return Device.testUserAgent(/(?:Chrome|CriOS|CrMo)(?!.+(?:Edge|UCBrowser))/i);
                },

                /**
                 * Such method is required in context of Device.Screen.isHighResolution()
                 * @returns {boolean} true for Chrome with version less than 25 (not including), false otherwise.
                 */
                isOldChrome: function() {
                    return this.isChrome() && this.Version.getMajor() < Constants.oldChromeVersionThreshold;
                },

                /**
                 * Checks if user agent contains "Android" AND "AppleWebKit" AND NOT "Chrome", ignoring case.
                 *
                 * @returns {boolean} return true only for "old" Android Webkit browser. The "new" one (appeared at
                 * least on Galaxy S4) is now on Chrome engine and so contains word "Chrome" in ua-string.
                 */
                isAndroidWebkit: function() {
                    return Device.testUserAgent(/(?:Android)(?=.+AppleWebKit)(?!.+(?:Chrome|UCBrowser))/i)  && !Device.Browser.isIEMobile();
                },

                /**
                 * UC Browser adds correspondent string everywhere except Windows Phone with IE 11
                 * @returns {boolean} true if UC Browser detected
                 */
                isUCBrowser: function() {
                    return Device.testUserAgent(/UCBrowser/i);
                },

                isIosSafari: function() {
                    return Device.OS.isIos() && !Device.Browser.isUCBrowser() && !Device.Browser.isChrome();
                },

                isIosSafariStandalone: function() {
                    return window.navigator.standalone;
                },

                /**
                 * Will match actually all Internet Explorers excluding desktop versions.
                 * @returns {true} if Internet Explorer detected
                 */
                isIEMobile: function () {
                    return Device.testUserAgent(/Windows\sPhone.+Trident/i);
                },

                /**
                 * Tries to identify Internet Explorer Mobile in "desktop mode"
                 * Settings -> Website preference -> desktop version
                 * @returns {boolean} true if IE Desktop mode detected
                 */
                isIEMobileDesktopMode: function () {
                    return Device.testUserAgent(/Trident.+WPDesktop/i);
                },

                isEdgeMobile: function () {
                    return Device.testUserAgent(/Windows\sPhone.+Edge/i);
                },

                /**
                 * Default browser found in Xiaomi Mi4
                 * @returns {boolean} true if default Xiaomi Mi4 browser detected
                 */
                isBdBrowser: function () {
                    return Device.testUserAgent(/bdbrowser/i);
                },

                Version: (function() {
                    var rawVersion = null;

                    /**
                     * "The returned array has the matched text as the first item, and then one item for each
                     * capturing parenthesis that matched containing the text that was captured."
                     * https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/RegExp/exec
                     *
                     * Also replaces iOS separators "_" to widely adopted "."
                     *
                     * @param array result of regexp is intended to be passed by this param
                     *
                     * @returns {string} captured version
                     */
                    function getVersionSafely(array) {
                        if (array) {
                            return array[1].replace(new RegExp("_", "g"), ".");
                        } else {
                            return null;
                        }
                    }

                    return {
                        /**
                         * Tries to recognize browser version.
                         *
                         * @param baseVersion indicates that caller is interested in version of the "underlying"
                         * browser, e.g. UC Browser for Windows Phone is build on top of IE
                         *
                         * @returns {string} raw version as recognized
                         */
                        getRaw: function(baseVersion) {
                            if (baseVersion === undefined) baseVersion = false;
                            if (rawVersion) return rawVersion;

                            if (Device.Browser.isEdgeMobile()) {
                                rawVersion = getVersionSafely(Device.execUserAgent(/Edge\/(\S+)/i));
                            } else if (!baseVersion && Device.Browser.isUCBrowser()) {
                                rawVersion = getVersionSafely(Device.execUserAgent(/UCBrowser\/(\S+)/i));
                            } else if (!baseVersion && Device.Browser.isBdBrowser()) {
                                rawVersion = getVersionSafely(Device.execUserAgent(/bdbrowser_i18n\/(\S+)/i));
                            } else if (Device.Browser.isChrome()) {
                                rawVersion = getVersionSafely(Device.execUserAgent(/(?:Chrome|CriOS|CrMo)\/(\S+)/i));
                            } else if (Device.Browser.isIEMobile()) {
                                rawVersion = getVersionSafely(Device.execUserAgent(/(?:rv:|IEMobile\/|MSIE\s)(\S[^);]+)/i));
                            } else if (Device.Browser.isAndroidWebkit()) {
                                rawVersion = getVersionSafely(Device.execUserAgent(/Version\/(\S+)/i));
                            } else if (Device.OS.isIos()) {
                                rawVersion = getVersionSafely(Device.execUserAgent(/OS\s(\d\S*)/i));
                            } else {
                                rawVersion = null;
                            }
                            return rawVersion;
                        },

                        getMajor: function(baseVersion) {
                            if (baseVersion === undefined) baseVersion = false;
                            if (this.getRaw(baseVersion)) {
                                return this.getRaw(baseVersion).split(".")[0];
                            } else {
                                return null;
                            }
                        },

                        getMinor: function(baseVersion) {
                            if (baseVersion === undefined) baseVersion = false;
                            if (this.getRaw(baseVersion)) {
                                return this.getRaw(baseVersion).split(".")[1];
                            } else {
                                return null;
                            }
                        },

                        /**
                         * Simply compares two string version values.
                         *
                         * Example:
                         * versionCompare('1.1', '1.2') => -1
                         * versionCompare('1.1', '1.1') =>  0
                         * versionCompare('1.2', '1.1') =>  1
                         * versionCompare('2.23.3', '2.22.3') => 1
                         *
                         * Returns:
                         * -1 = left is LOWER than right
                         *  0 = they are equal
                         *  1 = left is GREATER = right is LOWER
                         *  And FALSE if one of input versions are not valid
                         *
                         * @function
                         * @param {String} left  Version #1
                         * @param {String} right Version #2
                         * @return {Integer|Boolean}
                         * @author Alexey Bass (albass)
                         * @since 2011-07-14
                         */
                        versionCompare: function(left, right) {
                            if (typeof left + typeof right != 'stringstring')
                                return false;

                            var a = left.split('.');
                            var b = right.split('.');
                            var i = 0;
                            var len = Math.max(a.length, b.length);

                            for (; i < len; i++) {
                                if ((a[i] && !b[i] && parseInt(a[i]) > 0) || (parseInt(a[i]) > parseInt(b[i]))) {
                                    return 1;
                                } else if ((b[i] && !a[i] && parseInt(b[i]) > 0) || (parseInt(a[i]) < parseInt(b[i]))) {
                                    return -1;
                                }
                            }

                            return 0;
                        },

                        compareTo: function(anotherVersion, baseVersion) {
                            if (baseVersion === undefined) baseVersion = false;
                            return this.versionCompare(this.getRaw(baseVersion), anotherVersion);
                        }
                    };
                })()
            };
        })(),

        Screen: (function() {
            var resolutions = Constants.mobileDevicesUniqueResolutions; //shortcut

            /**
             * Converts value to pixels by multiplying it on DPR (Device Pixel Ratio).
             * Makes decision if multiplication is needed. Some browsers report values like screen.width/height
             * in pixels and some in DIPs (Device Independent Pixels). Latest Chrome and Safari report in DIPS,
             * Default Android browser and UCBrowser in pixels. Old Chrome reported in pixels, but somewhere between
             * 19 and 24 version Chrome began to report in DIPs, but unfortunately info about in which exact version
             * this happened is not available to the author of this file. It is confirmed that Chrome 18 reported in px
             * and Chrome 25 in DIPS.
             *
             * @param size adjusted by multiplying on DPR if its Chrome older than 25 or
             * new Default Android browser (on Chrome engine).
             *
             * @returns {number} size in pixels, previously converted from DIPS if it was needed.
             */
            function convertFromDipsToPx(size) {
                if (Device.Browser.isOldChrome() || Device.Browser.isAndroidWebkit() || Device.Browser.isBdBrowser() ||
                    (Device.OS.isAndroid() && Device.Browser.isUCBrowser() &&
                    Device.Browser.Version.getMajor() <= 11 && Device.Browser.Version.getMinor() < 4)) {
                    return size;
                } else {
                    return Math.floor(size * Device.getDPR());
                }
            }

            /**
             * Iterates over set of unique resolutions, divides each by input resolution to receive ratio.
             * If ratio less than 1 - returns previous resolution in the list as closest, meaning that iteration reached
             * the resolution which lower than input one. Relies on the facts that array of resolutions is sorted
             * descending and that browser may return resolution LOWER than device has in fact (not higher).
             *
             * WARNING! This method may work incorrectly if resolutions map will start to contain too much resolutions
             * which close to each other by value.
             *
             * @param inputResolution incorrect resolution returned by the browser
             * @returns {number} closest correct resolution
             */
            function selectClosestResolution(inputResolution) {
                for (var i = 0; i < resolutions.length; i++) {
                    if (resolutions[i] / inputResolution < 1) {
                        return resolutions[i - 1];
                    }
                }

                return resolutions[resolutions.length - 1];
            }

            /**
             * Rounds odd value to even grater by 1
             * @param value odd value
             * @returns {number} even value
             */
            function roundToEven(value) {
                return (value % 2 == 0) ? value : value + 1;
            }

            return {
                getWidth: function() {
                    return convertFromDipsToPx(screen.width);
                },

                getHeight: function() {
                    return convertFromDipsToPx(screen.height);
                },

                /**
                 * Chrome in new version (at least in 27) started to return incorrect values for
                 * screen.width/screen.height properties on devices with navigation bar taking space from the screen
                 * itself. Now these properties are reduced by height of this navigation panel (which can be different
                 * in landscape/portrait modes). This method tries to adjust this by using selectClosestResolution()
                 * method but due to its characteristics it used only on high resolution screens.
                 *
                 * @returns {number} size of wider side of the screen adjusted if its high resolution screen
                 */
                getWideness: function() {
                    return selectClosestResolution(Math.max(this.getWidth(), this.getHeight()));
                },

                /**
                 * @returns {number} side of narrower side of the screen rounded to even if odd (by adding +1 extra px)
                 */
                getNarrowness: function() {
                    return roundToEven(Math.min(this.getWidth(), this.getHeight()));
                },

                /**
                 * @returns {boolean} true if wider side size is more than threshold (1920px), false otherwise
                 */
                isHighResolution: function() {
                    return this.getWideness() >= Constants.highResScreenThreshold;
                }
            };
        })()
    };
})();

/**
 * Creates viewport when head section is parsed. Makes decision upon how exactly viewport will be created based on
 * device's screen size and browser. Applies workarounds if needed.
 */
var ViewportManager = (function() {
    var selectedViewport; //stored value between method calls
    var factor;
    var scaleFactor;

    /**
     * Iterates over Constants.exceptionalDevices and tests each key (which is device regexp)
     * upon navigator.userAgent. Stores viewport content selected from the map and returns true if current device is
     * found in the map.
     * Otherwise stores null and returns false.
     *
     * @returns {boolean} true if device found in the list, false otherwise
     */
    function isDeviceInExceptionalList() {
        for (var deviceKey in Constants.exceptionalDevices) {
            if (Device.testUserAgent(new RegExp(deviceKey, "i"))) {
                selectedViewport = Constants.exceptionalDevices[deviceKey];
                return true;
            }
        }

        selectedViewport = null;
        return false;
    }

    /**
     * @returns {String} stored value for already selected viewport content after isDeviceInExceptionalList() run,
     * if stored value undefined - this method will run isDeviceInExceptionalList() first and then retrieve again.
     */
    function getCorrespondentExceptionalViewPort() {
        if (selectedViewport != undefined) {
            return selectedViewport;
        } else {
            isDeviceInExceptionalList();
            return selectedViewport;
        }
    }

    /**
     * @returns {number} maximum supported resolution according to business requirements
     */
    function getMaxSupportedResolution() {
        if (Util.isUrlTriggerParamPresent("iPadProCorrectScale"))
            return Constants.iPadProWidenessInDIPs;

        if (Device.isIpad() || Device.isIpadPro()) {
            return Constants.maxIpadSupportedResolution;
        } else {
            return Constants.maxSupportedResolution;
        }
    }

    function calculateScaleFactor() {
        factor = Device.Screen.isHighResolution() ?
            Device.Screen.getWideness() / getMaxSupportedResolution() : 1;

        scaleFactor = factor/Device.getDPR();
    }

    function calculateScaledDimension(dimension) {
        return Math.round(dimension > getMaxSupportedResolution() ?
            dimension / (scaleFactor * Device.getDPR()) : dimension);
    }

    function fillInScalingReport() {
        //report potential scale factor (mainly for QA automation) even though it may not be applied
        ScalingReport.scaleFactor(scaleFactor);
        ScalingReport.actualDPR(Device.getDPR());
        ScalingReport.deviceScreenWidth(Device.Screen.getWidth());
        ScalingReport.deviceScreenHeight(Device.Screen.getHeight());
        ScalingReport.deviceScreenWideness(Device.Screen.getWideness());
        ScalingReport.deviceScreenNarrowness(Device.Screen.getNarrowness());
        ScalingReport.deviceScreenIsHighResolution(Device.Screen.isHighResolution());
        ScalingReport.browserVersionRaw(Device.Browser.Version.getRaw());
        ScalingReport.browserVersionMajor(Device.Browser.Version.getMajor());
        ScalingReport.browserVersionMinor(Device.Browser.Version.getMinor());
        ScalingReport.browserVersionRawBase(Device.Browser.Version.getRaw(true));
        ScalingReport.browserVersionMajorBase(Device.Browser.Version.getMajor(true));
        ScalingReport.browserVersionMinorBase(Device.Browser.Version.getMinor(true));
    }

    function createViewport() {
        if (inIframe() || Util.isUrlTriggerParamPresent("noVP")) return;

        if (isDeviceInExceptionalList()) {
            Viewport.setViewportExceptionalDevice(getCorrespondentExceptionalViewPort());
        } else if (Device.Browser.isAndroidWebkit()) {
            var targetDpi = Device.Screen.isHighResolution() ? "medium-dpi" : "device-dpi";
            Viewport.setViewportAndroidWebkit(targetDpi);
        } else if ((Device.Browser.isIEMobile() || Device.Browser.isEdgeMobile()) && !Device.Browser.isIEMobileDesktopMode()) {
            var scaledWideness = calculateScaledDimension(Device.Screen.getWideness());
            var scaledNarrowness = calculateScaledDimension(Device.Screen.getNarrowness());
            Viewport.setViewportIE(scaledWideness, scaledNarrowness);
        } else {
            Viewport.setViewport(scaleFactor); //will be called also for IE Desktop Mode causing it to
            // "incorrectly" scale to 1024px width which is better than 320px as alternative
        }
    }

    function applyWorkaroundsIfNeeded() {
        if (inIframe()) return;

        if (Device.isIphone() && Device.Browser.isIosSafariStandalone() &&
            Device.Browser.Version.compareTo("8.3") >= 0 && Device.Browser.Version.compareTo("9.0") <= 0) {
            new WorkaroundManager().workaroundForIncorrectScaleInStandaloneMode();
        }

        if (Device.Browser.isIEMobile() || Device.Browser.isEdgeMobile()) {
            new WorkaroundManager().workaroundForIncorrectScaleAfterFirstOpeningInIEAndEdge();
        }
    }

    function displayDebuggerIfNeeded() {
        if (Util.isUrlTriggerParamPresent("debugInVP") || Util.recallItem("debugInVP")) {
            Util.memoItem("debugInVP", "debugInVP"); //no sense in this value, just presence to trigger required behaviour
            new DebugWidget().init();
        }
    }

    return {
        init: function() {
            calculateScaleFactor();
            fillInScalingReport();
            createViewport();
            applyWorkaroundsIfNeeded();
            displayDebuggerIfNeeded();
        }
    };
})();

//Entry point
ViewportManager.init();