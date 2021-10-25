var CP = { Utils: {}, Log: {} };



	var serverString='';

    var XmlHttpRequest = new XMLHttpRequest();
    XmlHttpRequest.overrideMimeType("application/json");
    XmlHttpRequest.open('GET', '/socket_config.json', false);
    XmlHttpRequest.onreadystatechange = function ()
    {
        if (XmlHttpRequest.readyState == 4 && XmlHttpRequest.status == "200")
        {
            var serverConfig = JSON.parse(XmlHttpRequest.responseText);
            serverString=serverConfig.prefix_ws+serverConfig.host_ws+':'+serverConfig.port;
          
        }
    }
    XmlHttpRequest.send(null);

console.log(serverString);

(function (g) { var j = ["hlk", "hlk2", "hulk", "ba", "baws", "bj_mh5", "cheaa", "evj", "ges", "glrj", "gos", "hf", "ir", "ir2", "irmn3", "pgv", "pnp", "ro_g", "ssp", "spidc", "avng"]; var a = {}; var f = window.location.search.substr(1).split("&"); for (var e = 0; e < f.length; e++) { var d = f[e].split("="); var h = d[0]; var c = d[0].toLowerCase(); var b = d[1] ? d[1] : null;
        a[h] = b;
        a[c] = b } g.debounce = function (l, n, k) { var m = null; return function () { var q = this; var p = arguments; var o = k && !m;
            m = setTimeout(function () { clearTimeout(m); if (!k) { l.apply(q, p) } }, n || 200); if (o) { l.apply(q, p) } } };
    g.isCore1Game = function (k) { return j.indexOf(k) !== -1 };
    g.getParam = function (l, k) { var m = k ? l : l.toLowerCase(); return a[m] };
    g.inJsViewport = function () { try { return !!window.top.ScalingReport } catch (k) { return false } } })(CP.Utils);
(function (a) { var b = {}; var c = false;
    a.enable = function () { c = true;
        d(c) };
    a.disable = function () { c = false;
        d(c) };
    a.setCfg = function (e) { b = e };
    a.config = function () { console.dir(b) };
    a.isForceEnabled = function () { return c };

    function d(e) { dispatchEvent(new CustomEvent("cpLogStateEvent", { detail: e })) } }(CP.Log));
if ((typeof console) == "undefined") { console = {};
    console.log = function () {};
    console.debug = function () {};
    console.info = function () {};
    console.error = function () {};
    console.trace = function () {};
    console.fatal = function () {} }
if (typeof Object.create != "function") { Object.create = (function () {
        function b() {} var a = Object.prototype.hasOwnProperty; return function (e) { if (typeof e != "object") { throw TypeError("Object prototype may only be an Object or null") } b.prototype = e; var d = new b();
            b.prototype = null; if (arguments.length > 1) { var c = Object(arguments[1]); for (var f in c) { if (a.call(c, f)) { d[f] = c[f] } } } return d } })() }
if (typeof window.matchMedia == "undefined") { window.matchMedia = function () { return false } } window.onpageshow = function (a) { if (a.persisted) { window.location.reload() } };
(function () { window.performance = {}; if (!Date.now) { Date.now = function () { return new Date().getTime() } } })();
(function (c) { try { new c.Event("Test") } catch (b) {
        function a(e, g) { g = g || { bubbles: false, cancelable: false }; var f = c.document.createEvent("Event");
            f.initEvent(e, g.bubbles, g.cancelable); return f } a.prototype = c.Event.prototype;

        function d(e, g) { g = g || { bubbles: false, cancelable: false, detail: undefined }; var f = c.document.createEvent("CustomEvent");
            f.initEvent(e, g.bubbles, g.cancelable);
            f.detail = g.detail;
            f.initCustomEvent = function (k, l, h, j) { f.initEvent(k, l, h);
                f.detail = j }; return f } d.prototype = c.Event.prototype;
        c.Event = a;
        c.CustomEvent = d } })(window);

function PTSideBar(props) { var SB = new Object; var TSB = new Object;
    TSB.cookieName = "CacheTimestamp";
    TSB.cacheconfig = "platform/cache_delay.cfg"; if (props) { TSB.props = props } SB.init = function (e, t) { var n; var r; var i; var s; var o;
        i = document.location.href;
        TSB.onInitCompleteCallback = window.logicInitCompleted; if (t == null) { t = document.body } TSB.iframeContainer = t;
        TSB.htcmdQue = [];
        TSB.settings = {};
        TSB.settings.layouts = new Object;
        TSB.settings.language = "en";
        TSB.settings.config = ""; if (TSB.props) { for (r in TSB.props) { TSB.settings[r] = TSB.props[r] } TSB.props = null } else { if (i.indexOf("?") != 1) { s = i.split("?")[1].split("&"); for (n = 0; n < s.length; n++) { o = s[n].split("=");
                    TSB.settings[o[0]] = decodeURIComponent(o[1]) } } else { return } } TSB.settings.language = TSB.settings.language.toLowerCase();
        TSB.settings.config = TSB.attachRequieredTags(TSB.settings.config);
        TSB.settings.config = TSB.replaceTags(TSB.settings.config);
        TSB.loadConfig() };
    SB.getGadgets = function (e) { if (TSB.settings.layouts[e]) { return TSB.settings.layouts[e].gadgets } else { return null } };
    SB.getMaxWidth = function (e) { return TSB.settings.layouts[e].maxwidth };
    SB.getMaxHeight = function (e) { return TSB.settings.layouts[e].maxheight };
    SB.getForceOpen = function (e) { return TSB.settings.layouts[e].forceopen };
    SB.getLanguage = function () { return TSB.settings.language };
    SB.htcmd = function (e, t) { if (TSB.htcmdQue.length == 0) { setTimeout(TSB.executeHtcmd, 100) } if (t != null) { e += "&id=" + t } TSB.htcmdQue.push(e) };
    TSB.initDone = function () { setTimeout(TSB.doInitDoneCallback, 50) };
    TSB.doInitDoneCallback = function () { TSB.onInitCompleteCallback();
        TSB.onInitCompleteCallback = null };
    TSB.loadConfig = function () { var e;
        cache_expiration_delay = null;
        e = document.createElement("script");
        e.id = "SBCacheConfig";
        e.src = TSB.cacheconfig;
        document.head.appendChild(e);
        TSB.testConfig() };
    TSB.testConfig = function () { if (cache_expiration_delay == null) { setTimeout(TSB.testConfig, 50) } else { TSB.onConfigReady(cache_expiration_delay) } };
    TSB.onConfigReady = function (e) { var t;
        t = document.getElementById("SBCacheConfig");
        document.head.removeChild(t);
        TSB.settings.cache_delay = parseInt(e);
        TSB.syncCookie(); if (TSB.settings.config.substr(-1) != "&") { TSB.settings.config += "&" } TSB.settings.config += "t=" + TSB.settings.timestamp;
        TSB.loadGadgetConfig() };
    TSB.loadGadgetConfig = function () { var e;
        response = null;
        e = document.createElement("script");
        e.id = "SBGadgetConfig";
        e.src = TSB.settings.config;
        document.head.appendChild(e);
        TSB.testGadgetConfig() };
    TSB.testGadgetConfig = function () { if (response == null) { setTimeout(TSB.testGadgetConfig, 50) } else { TSB.onGadgetConfigReady(response) } };
    TSB.onGadgetConfigReady = function (gadgetstr) { var el; var t; var i; var ii; var o; var gadgets; var targets; var layouttype;
        el = document.getElementById("SBGadgetConfig");
        document.head.removeChild(el);
        t = eval(gadgetstr); if (t.length == 0) { return } for (i = 0; i < t.length; i++) { targets = []; for (ii = 0; ii < t[i].layouts.length; ii++) { for (iii = 0; iii < t[i].layouts[ii].allowed.length; iii++) { layouttype = t[i].layouts[ii].allowed[iii];
                    o = new Object;
                    targets.push(o); if (TSB.settings.layouts[layouttype] == null) { TSB.settings.layouts[layouttype] = new Object;
                        TSB.settings.layouts[layouttype].maxwidth = 0;
                        TSB.settings.layouts[layouttype].maxheight = 0;
                        TSB.settings.layouts[layouttype].forceopen = -1;
                        TSB.settings.layouts[layouttype].gadgets = [] } o.width = Number(t[i].layouts[ii].width);
                    o.height = Number(t[i].layouts[ii].height);
                    o.url = TSB.replaceTags(t[i].layouts[ii].url);
                    o.id = TSB.settings.layouts[layouttype].gadgets.length; if (TSB.settings.layouts[layouttype].maxwidth < o.width) { TSB.settings.layouts[layouttype].maxwidth = o.width } if (TSB.settings.layouts[layouttype].maxheight < o.height) { TSB.settings.layouts[layouttype].maxheight = o.height } if (t[i].forceopen == "1" && TSB.settings.layouts[layouttype].forceopen == -1) { TSB.settings.layouts[layouttype].forceopen = o.id } TSB.settings.layouts[layouttype].gadgets.push(o) } } TSB.setPropertyOnList(targets, "code", t[i].code);
            TSB.setPropertyOnList(targets, "name", t[i].name);
            TSB.setPropertyOnList(targets, "israce", t[i].israce == "1");
            TSB.setPropertyOnList(targets, "isopen", t[i].isopened == "1");
            TSB.setPropertyOnList(targets, "isDefaultForAnalytics", t[i].isopened == "1"); if (o.url != null) { if (o.url.indexOf("?") != -1) { o.url += "&" } else { o.url += "?" } o.url += "pt_connection=" + encodeURIComponent(TSB.settings.connection) + "&id=" + i + "&" } } for (layouttype in TSB.settings.layouts) { try { gadgets = TSB.settings.layouts[layouttype].gadgets; if (TSB.settings.layouts[layouttype].forceopen != -1) { TSB.setPropertyOnList(gadgets, "isopen", false);
                    TSB.setPropertyOnList(gadgets, "isDefaultForAnalytics", false); for (i = 0; i < gadgets.length; i++) { gadgets[i].forceopen = true;
                        gadgets[i].isDefaultForAnalytics = true } TSB.settings.layouts[layouttype].forceopen = true } else { for (i = 0; i < gadgets.length; i++) { if (gadgets[i].isopen == true) { TSB.setPropertyOnList(gadgets, "isopen", false);
                            TSB.setPropertyOnList(gadgets, "isDefaultForAnalytics", false);
                            gadgets[i].forceopen = true;
                            gadgets[i].isDefaultForAnalytics = true; break } } TSB.settings.layouts[layouttype].forceopen = false } } catch (e) { console.log(e) } } TSB.initDone() };
    TSB.setPropertyOnList = function (e, t, n) { var r; for (r = 0; r < e.length; r++) { e[r][t] = n } };
    TSB.executeHtcmd = function () { var e; var t; var n;
        t = TSB.htcmdQue.shift();
        n = TSB.htcmdQue.shift();
        e = document.createElement("iframe");
        e.frameborder = 0;
        e.style = "visibility:hidden; position:absolute; top:0px; left:0px;";
        e.style.visibility = "hidden";
        e.style.position = "absolute";
        e.style.top = "0px";
        e.style.left = "0px";
        e.src = TSB.settings.connection + "#" + encodeURIComponent(t); if (e.attachEvent) { e.attachEvent("onload", TSB.onHtcmdComplete) } else { e.onload = TSB.onHtcmdComplete } TSB.iframeContainer.appendChild(e); if (TSB.htcmdQue.length > 0) { setTimeout(TSB.executeHtcmd, 100) } };
    TSB.onHtcmdComplete = function (e) { var t;
        t = e.target;
        TSB.iframeContainer.removeChild(t) };
    TSB.replaceTags = function (e) { var t; var n; var r; var i; for (var s = 0; s < e.length; s++) { t = e.indexOf("[", s); if (t == -1) { break } else { n = e.indexOf("]", t); if (n == -1) { break } else { r = e.substr(t + 1, n - t - 1);
                    i = encodeURIComponent(TSB.tagValue(r));
                    e = e.substr(0, t) + i + e.substr(n + 1) } } } return e };
    TSB.attachRequieredTags = function (e) { var t = ""; var n = ["casino", "gametypes", "gametype", "username", "clienttype", "clientplatform", "preview", "realmode"]; var r; var i; for (i = 0; i < n.length; i++) { r = n[i]; if (TSB.settings[r] != null && e.indexOf("[" + r + "]") == -1) { t += r + "=[" + r + "]&" } } if (t != "") { if (e.charAt(-1) == "&" || e.charAt(-1) == "?") { e += t } else { if (e.indexOf("?") == -1) { e += "?" + t } else { e += "&" + t } } } return e };
    TSB.tagValue = function (e) { var t = e; if (TSB.settings[e] != null) { t = TSB.settings[e] } return t };
    TSB.syncCookie = function () { var e; var t; var n; var r; var i;
        r = 0;
        t = document.cookie.split("; "); for (e = 0; e < t.length; e++) { n = t[e].split("="); if (n[0] == TSB.cookieName) { r = parseInt(n[1]) } } i = (new Date).getTime(); if (i - r > TSB.settings.cache_delay) { document.cookie = SB.cookieName + "=" + i } r = i;
        TSB.settings.timestamp = r }; return SB }
if (window.Detect && console && console.error) { console.error("Detect.js initialization collision, global variable Detect exist!") } else { Detect = (function () { var f = "1.0"; var b; var e = [];

        function a() {!Detect.T ? Detect.T = { Unidentified: { name: "Unidentified" }, SmartPhone: { name: "Smartphone" }, Tablet: { name: "Tablet" }, Desktop: { name: "Desktop" } } : null;!Detect.F ? Detect.F = { Unidentified: { name: "Unidentified", type: Detect.T.Unidentified }, Iphone: { name: "Apple - iPhone Family", type: Detect.T.SmartPhone }, Ipad: { name: "Apple - iPad Family", type: Detect.T.Tablet }, Ipod: { name: "Apple - iPod Family", type: Detect.T.SmartPhone }, NexusSmartPhones: { name: "Google - Nexus Family", type: Detect.T.SmartPhone }, NexusTablets: { name: "Google - Nexus Family", type: Detect.T.Tablet }, GalaxyA: { name: "Samsung - Galaxy A Family", type: Detect.T.SmartPhone }, GalaxyS: { name: "Samsung - Galaxy S Family", type: Detect.T.SmartPhone }, GalaxyNote: { name: "Samsung - Galaxy Note Family", type: Detect.T.SmartPhone }, GalaxyTab: { name: "Samsung - Galaxy Tab Family", type: Detect.T.Tablet }, GalaxyTabS: { name: "Samsung - Galaxy Tab S Family", type: Detect.T.Tablet }, HtcOne: { name: "HTC - One Family", type: Detect.T.SmartPhone }, XperiaZ: { name: "Sony - Xperia Z Family", type: Detect.T.SmartPhone }, MotorolaMotoG: { name: "Motorola - Moto G Family", type: Detect.T.SmartPhone }, LGG: { name: "LG - G Family", type: Detect.T.SmartPhone }, MeizuMX: { name: "Meizu - MX Family", type: Detect.T.SmartPhone }, XiaomiMi: { name: "Xiaomi - Mi Family", type: Detect.T.SmartPhone }, LenovoTab: { name: "Lenovo - Tab Family", type: Detect.T.Tablet }, LenovoA: { name: "Lenovo - A Family", type: Detect.T.SmartPhone }, Lumia: { name: "Nokia - Lumia Family", type: Detect.T.SmartPhone }, UnidentifiedDesktop: { name: "Unidentified - Desktop", type: Detect.T.Desktop } } : null;!Detect.D ? Detect.D = { Unidentified: { name: "Unidentified", family: Detect.F.Unidentified }, Iphone6Plus: { name: "Apple - iPhone 6 Plus", family: Detect.F.Iphone }, IphoneX: { name: "Apple - iPhone X", family: Detect.F.Iphone }, IphoneXsMax: { name: "Apple - iPhone Xs Max", family: Detect.F.Iphone }, Iphone6: { name: "Apple - iPhone 6", family: Detect.F.Iphone }, Iphone5: { name: "Apple - iPhone 5", family: Detect.F.Iphone }, Iphone4: { name: "Apple - iPhone 4", family: Detect.F.Iphone }, Ipod: { name: "Apple - iPod", family: Detect.F.Iphone }, IpadPro129: { name: "Apple - iPad Pro 12.9'", family: Detect.F.Ipad }, IpadRetina: { name: "Apple - iPad Retina", family: Detect.F.Ipad }, Ipad: { name: "Apple - iPad", family: Detect.F.Ipad }, Nexus6P: { name: "Google - Nexus 6P", family: Detect.F.NexusSmartPhones }, Nexus5X: { name: "Google - Nexus 5X", family: Detect.F.NexusSmartPhones }, Nexus6: { name: "Google - Nexus 6", family: Detect.F.NexusSmartPhones }, Nexus5: { name: "Google - Nexus 5", family: Detect.F.NexusSmartPhones }, Nexus4: { name: "Google - Nexus 4", family: Detect.F.NexusSmartPhones }, GalaxyNexus: { name: "Google - Galaxy Nexus", family: Detect.F.NexusSmartPhones }, NexusS: { name: "Google - Nexus S", family: Detect.F.NexusSmartPhones }, NexusOne: { name: "Google - Nexus One", family: Detect.F.NexusSmartPhones }, Nexus9: { name: "Google - Nexus 9", family: Detect.F.NexusTablets }, Nexus10: { name: "Google - Nexus 10", family: Detect.F.NexusTablets }, Nexus7: { name: "Google - Nexus 7", family: Detect.F.NexusTablets }, GalaxyTabS2_9_7: { name: "Samsung - Galaxy Tab S2 9.7", family: Detect.F.GalaxyTabS }, GalaxyTabS2_8_0: { name: "Samsung - Galaxy Tab S2 8.0", family: Detect.F.GalaxyTabS }, GalaxyTabS10_5: { name: "Samsung - Galaxy Tab S 10.5", family: Detect.F.GalaxyTabS }, GalaxyTabS8_4: { name: "Samsung - Galaxy Tab S 8.4", family: Detect.F.GalaxyTabS }, GalaxyTabA9_7: { name: "Samsung - Galaxy Tab A 9.7", family: Detect.F.GalaxyTab }, GalaxyTabA8_0: { name: "Samsung - Galaxy Tab A 8.0", family: Detect.F.GalaxyTab }, GalaxyTabPro12_2: { name: "Samsung - Galaxy Tab Pro 12.2", family: Detect.F.GalaxyTab }, GalaxyTabPro10_1: { name: "Samsung - Galaxy Tab Pro 10.1", family: Detect.F.GalaxyTab }, GalaxyTabPro8_4: { name: "Samsung - Galaxy Tab Pro 8.4", family: Detect.F.GalaxyTab }, GalaxyTab4_10_1_2015: { name: "Samsung - Galaxy Tab 4 10.1 (2015)", family: Detect.F.GalaxyTab }, GalaxyTab4_10_1_LTE: { name: "Samsung - Galaxy Tab 4 10.1 LTE", family: Detect.F.GalaxyTab }, GalaxyTab4_10_1_3G: { name: "Samsung - Galaxy Tab 4 10.1 3G", family: Detect.F.GalaxyTab }, GalaxyTab4_10_1: { name: "Samsung - Galaxy Tab 4 10.1", family: Detect.F.GalaxyTab }, GalaxyTab4_8_0: { name: "Samsung - Galaxy Tab 4 8.0", family: Detect.F.GalaxyTab }, GalaxyTab4_7_0: { name: "Samsung - Galaxy Tab 4 7.0", family: Detect.F.GalaxyTab }, GalaxyTab3_10_1: { name: "Samsung - Galaxy Tab 3 10.1", family: Detect.F.GalaxyTab }, GalaxyTab3_8_0: { name: "Samsung - Galaxy Tab 3 8.0", family: Detect.F.GalaxyTab }, GalaxyTab3_7_0: { name: "Samsung - Galaxy Tab 3 7.0", family: Detect.F.GalaxyTab }, GalaxyTab2_10_1: { name: "Samsung - Galaxy Tab 2 10.1", family: Detect.F.GalaxyTab }, GalaxyTab2_7_0: { name: "Samsung - Galaxy Tab 2 7.0", family: Detect.F.GalaxyTab }, GalaxyTab10_1: { name: "Samsung - Galaxy Tab 10.1", family: Detect.F.GalaxyTab }, GalaxyTab8_9: { name: "Samsung - Galaxy Tab 8.9", family: Detect.F.GalaxyTab }, GalaxyTab7_7: { name: "Samsung - Galaxy Tab 7.7", family: Detect.F.GalaxyTab }, GalaxyTab7_0: { name: "Samsung - Galaxy Tab 7.0", family: Detect.F.GalaxyTab }, GalaxyS7Edge: { name: "Samsung - Galaxy S7 Edge", family: Detect.F.GalaxyS }, GalaxyS7: { name: "Samsung - Galaxy S7", family: Detect.F.GalaxyS }, GalaxyS6EdgePlus: { name: "Samsung - Galaxy S6 Edge+", family: Detect.F.GalaxyS }, GalaxyS6Edge: { name: "Samsung - Galaxy S6 Edge", family: Detect.F.GalaxyS }, GalaxyS6: { name: "Samsung - Galaxy S6", family: Detect.F.GalaxyS }, GalaxyS5LTE: { name: "Samsung - Galaxy S5 LTE", family: Detect.F.GalaxyS }, GalaxyS5Mini: { name: "Samsung - Galaxy S5 Mini", family: Detect.F.GalaxyS }, GalaxyS5: { name: "Samsung - Galaxy S5", family: Detect.F.GalaxyS }, GalaxyS4Mini: { name: "Samsung - Galaxy S4 Mini", family: Detect.F.GalaxyS }, GalaxyS4Zoom: { name: "Samsung - Galaxy S4 Zoom", family: Detect.F.GalaxyS }, GalaxyS4: { name: "Samsung - Galaxy S4", family: Detect.F.GalaxyS }, GalaxyS3Slim: { name: "Samsung - Galaxy S3 Slim", family: Detect.F.GalaxyS }, GalaxyS3Mini: { name: "Samsung - Galaxy S3 Mini", family: Detect.F.GalaxyS }, GalaxyS3: { name: "Samsung - Galaxy S3", family: Detect.F.GalaxyS }, GalaxyS2LTE: { name: "Samsung - Galaxy S2 LTE", family: Detect.F.GalaxyS }, GalaxyS2: { name: "Samsung - Galaxy S2", family: Detect.F.GalaxyS }, GalaxyS: { name: "Samsung - Galaxy S", family: Detect.F.GalaxyS }, GalaxyA7: { name: "Samsung - Galaxy A7", family: Detect.F.GalaxyA }, GalaxyNotePro12_2: { name: "Samsung - Galaxy Note Pro 12.2", family: Detect.F.GalaxyNote }, GalaxyNote10_1_2014: { name: "Samsung - Galaxy Note 10.1 2014 Edition", family: Detect.F.GalaxyNote }, GalaxyNote10_1: { name: "Samsung - Galaxy Note 10.1", family: Detect.F.GalaxyNote }, GalaxyNoteEdge: { name: "Samsung - Galaxy Note Edge", family: Detect.F.GalaxyNote }, GalaxyNote5: { name: "Samsung - Galaxy Note 5", family: Detect.F.GalaxyNote }, GalaxyNote4Duos: { name: "Samsung - Galaxy Note 4 Duos", family: Detect.F.GalaxyNote }, GalaxyNote4: { name: "Samsung - Galaxy Note 4", family: Detect.F.GalaxyNote }, GalaxyNote3Neo: { name: "Samsung - Galaxy Note 3 Neo", family: Detect.F.GalaxyNote }, GalaxyNote3: { name: "Samsung - Galaxy Note 3", family: Detect.F.GalaxyNote }, GalaxyNote2: { name: "Samsung - Galaxy Note 2", family: Detect.F.GalaxyNote }, GalaxyNote: { name: "Samsung - Galaxy Note", family: Detect.F.GalaxyNote }, GalaxyNote8_0: { name: "Samsung - Galaxy Note 8.0", family: Detect.F.GalaxyNote }, HtcOne: { name: "HTC - One", family: Detect.F.HtcOne }, XperiaZ: { name: "Sony - Xperia Z", family: Detect.F.XperiaZ }, MotorolaMotoG2: { name: "Motorola - Moto G (2nd gen)", family: Detect.F.MotorolaMotoG }, MotorolaMotoG: { name: "Motorola - Moto G", family: Detect.F.MotorolaMotoG }, LGG5: { name: "LG - G5", family: Detect.F.LGG }, LGG4: { name: "LG - G4", family: Detect.F.LGG }, LGG3: { name: "LG - G3", family: Detect.F.LGG }, LGG2: { name: "LG - G2", family: Detect.F.LGG }, MeizuMX4Pro: { name: "Meizu - MX 4 Pro", family: Detect.F.MeizuMX }, MeizuMX4: { name: "Meizu - MX 4", family: Detect.F.MeizuMX }, XiaomiMi4LTE: { name: "Xiaomi - Mi 4LTE", family: Detect.F.XiaomiMi }, XiaomiMi4W: { name: "Xiaomi - Mi 4W", family: Detect.F.XiaomiMi }, XiaomiMi4: { name: "Xiaomi - Mi 4", family: Detect.F.XiaomiMi }, XiaomiMi3: { name: "Xiaomi - Mi 3", family: Detect.F.XiaomiMi }, LenovoTab2A730HC: { name: "Lenovo - Tab 2 A7-30", family: Detect.F.LenovoTab }, LenovoA889: { name: "Lenovo - A889", family: Detect.F.LenovoA }, LenovoA850: { name: "Lenovo - A850", family: Detect.F.LenovoA }, Lumia925: { name: "Nokia - Lumia 925", family: Detect.F.Lumia }, Lumia920: { name: "Nokia - Lumia 920", family: Detect.F.Lumia }, Lumia820: { name: "Nokia - Lumia 820", family: Detect.F.Lumia }, Lumia635: { name: "Nokia - Lumia 635", family: Detect.F.Lumia }, UnidentifiedDesktop: { name: "Unidentified - Desktop", family: Detect.F.UnidentifiedDesktop } } : null;!Detect.O ? Detect.O = { Unidentified: { name: "Unidentified" }, Ios: { name: "Apple - iOS" }, Android: { name: "Google - Android" }, WindowsPhone: { name: "Microsoft - Windows Phone" }, Windows: { name: "Microsoft - Windows" }, MacOS: { name: "Apple - Mac OS" }, Unix: { name: "Unix based" }, FireOS: { name: "Amazon - Fire OS" } } : null;!Detect.B ? Detect.B = { Unidentified: { name: "Unidentified" }, Safari: { name: "Apple - Safari" }, Chrome: { name: "Google - Chrome" }, AndroidWebView: { name: "Android WebView" }, AndroidWebkit: { name: "Android Browser" }, AndroidWebkitChrome: { name: "Android Browser Chromium" }, SamsungBrowser: { name: "Samsung - Samsung Browser" }, Ie: { name: "Microsoft - Internet Explorer" }, IeDesktopMode: { name: "Microsoft - Internet Explorer Mobile in Desktop Mode" }, Edge: { name: "Microsoft - Edge" }, UcBrowser: { name: "UCWeb - UC Browser" }, Firefox: { name: "Mozilla - Firefox" }, BaiduBrowser: { name: "Baidu - Baidu Browser" }, DUBrowser: { name: "Baidu - DU Browser" }, QQBrowser: { name: "Tencent Technology - QQ Browser" }, _360Browser: { name: "360.cn - 360 Security Browser" }, Opera: { name: "Opera Software - Opera" }, YandexBrowser: { name: "Yandex - Yandex Browser" }, Maxthon: { name: "Maxthon  - Maxthon Browser" }, Silk: { name: "Amazon - Silk" } } : null;!Detect.Consts ? Detect.Consts = { unidentified: "Unidentified", deviceIsSmartPhoneRegex: /(?:\sMobile|Lumia)/i, deviceIsTabletRegex: /(?:\sTablet)/i, deviceModelCutOff: /(\w+-\w{4})/i, isEntityVersion: /\d+(?:\.|_)\d+|^\d+$/, deviceRegex: /.*;\s?(?:SAMSUNG\s|)(.+)\sBuild|\((\w+);.+CPU|Trident.+;\s(\w+\s\d+)|(Macintosh)|(Windows)(?!\sPhone)(?!.+WPDesktop)|(X11)|(WPDesktop)|(Lenovo.+?)\//i, osRegexSimple: /(Android |Android\/|iPhone|iPod|iPad|Windows Phone|Windows NT|Intel Mac OS X).*?(\d\S[^;) ]+)/gi, osRegexComplex: /(X11|KFAPWI|Android;|Maemo|MeeGo)()/i, browserRegexSimple: /(iPhone|iPod|iPad|Chrome|CriOS|CrMo|SamsungBrowser|Edge|IEMobile|MSIE|UCBrowser|UBrowser|OPR|Opera|Firefox|FxiOS|bdbrowser_i18n|bdbrowser|baidubrowser|MQQBrowser|360 Aphone Browser|Silk|YaBrowser|MxBrowser|Maxthon).*?(\d\S[^;) ]+)/gi, browserRegexComplex: /Android.*?(\d\S[^;) ]+).+?(Version)(?!.+Chrome)|(wv).+?Chrome.*?(\d\S[^;) ]+)|(Version\/).+?Chrome.*?(\d\S[^;) ]+)|(Trident).+?rv:.*?(\d\S[^;) ]+)|(Intel Mac OS X).+?Version.*?(\d\S[^;) ]+)|Android\/.*?(\d\S[^;) ]+).+?(Browser\/AppleWebKit)/i, DeviceUAMarks: { Unidentified: Detect.D.Unidentified, "SM-T800": Detect.D.GalaxyTabS10_5, "SM-T533": Detect.D.GalaxyTab4_10_1_2015, "SM-T535": Detect.D.GalaxyTab4_10_1_LTE, "SM-T531": Detect.D.GalaxyTab4_10_1_3G, "SM-T530": Detect.D.GalaxyTab4_10_1, "GT-P5210": Detect.D.GalaxyTab3_10_1, "GT-P5100": Detect.D.GalaxyTab2_10_1, "SM-T210": Detect.D.GalaxyTab3_7_0, "SM-N9100": Detect.D.GalaxyNote4Duos, "SM-N910": Detect.D.GalaxyNote4, "SM-N900": Detect.D.GalaxyNote3, "GT-N7100": Detect.D.GalaxyNote2, "SM-G930": Detect.D.GalaxyS7, "SM-G925": Detect.D.GalaxyS6Edge, "SM-G920": Detect.D.GalaxyS6, "SM-G900": Detect.D.GalaxyS5, "GT-I950": Detect.D.GalaxyS4, "SHV-E300": Detect.D.GalaxyS4, "GT-I930": Detect.D.GalaxyS3, "SHV-E210": Detect.D.GalaxyS3, "SGH-T999": Detect.D.GalaxyS3, "SGH-I747": Detect.D.GalaxyS3, "SGH-N064": Detect.D.GalaxyS3, "SGH-N035": Detect.D.GalaxyS3, "SC-06D": Detect.D.GalaxyS3, "SC-03E": Detect.D.GalaxyS3, SCL21: Detect.D.GalaxyS3, "SCH-J021": Detect.D.GalaxyS3, "SCH-R530": Detect.D.GalaxyS3, "SCH-I535": Detect.D.GalaxyS3, "SCH-I939": Detect.D.GalaxyS3, "SPH-L710": Detect.D.GalaxyS3, "GT-I910": Detect.D.GalaxyS2, "GT-I920": Detect.D.GalaxyS2, "SGH-I757": Detect.D.GalaxyS2, "SM-A710": Detect.D.GalaxyA7, "HTC One": Detect.D.HtcOne, HTC_One: Detect.D.HtcOne, C6602: Detect.D.XperiaZ, XT1068: Detect.D.MotorolaMotoG2, XT1032: Detect.D.MotorolaMotoG, "LG-H818": Detect.D.LGG4, "LG-D855": Detect.D.LGG3, "LG-D802": Detect.D.LGG2, "Nexus 6P": Detect.D.Nexus6P, "Nexus 5X": Detect.D.Nexus5X, "Nexus 9": Detect.D.Nexus9, "Nexus 10": Detect.D.Nexus10, "Nexus 7": Detect.D.Nexus7, "Nexus 6": Detect.D.Nexus6, "Nexus 5": Detect.D.Nexus5, "Nexus 4": Detect.D.Nexus4, "MX4 Pro": Detect.D.MeizuMX4Pro, MX4: Detect.D.MeizuMX4, "MI 4LTE": Detect.D.XiaomiMi4LTE, "MI 4W": Detect.D.XiaomiMi4W, "MI 4": Detect.D.XiaomiMi4, "MI 3": Detect.D.XiaomiMi3, "MI 3W": Detect.D.XiaomiMi3, "Lenovo TAB 2 A7-30HC": Detect.D.LenovoTab2A730HC, "Lenovo A889": Detect.D.LenovoA889, "Lenovo-A889": Detect.D.LenovoA889, "Lenovo A850": Detect.D.LenovoA850, A850: Detect.D.LenovoA850, "Lumia 635": Detect.D.Lumia635, "Lumia 820": Detect.D.Lumia820, "Lumia 920": Detect.D.Lumia920, "Lumia 925": Detect.D.Lumia925, Macintosh: Detect.D.UnidentifiedDesktop, Windows: Detect.D.UnidentifiedDesktop, X11: Detect.D.UnidentifiedDesktop, WPDesktop: Detect.D.UnidentifiedDesktop, iPhone: function () { return b.widerSide == 480 ? Detect.D.Iphone4 : b.widerSide == 568 ? Detect.D.Iphone5 : b.widerSide == 667 ? Detect.D.Iphone6 : b.widerSide == 736 ? Detect.D.Iphone6Plus : b.widerSide == 812 ? Detect.D.IphoneX : b.widerSide == 896 ? Detect.D.IphoneXsMax : { name: "iPhone" } }, iPad: function () { if (b.widerSide == 1366) { return Detect.D.IpadPro129 } else { return b.devicePixelRatio == 1 ? Detect.D.Ipad : Detect.D.IpadRetina } } }, OsUAMarks: { Unidentified: Detect.O.Unidentified, "Android ": Detect.O.Android, "Android;": Detect.O.Android, "Android/": Detect.O.Android, iPhone: Detect.O.Ios, iPad: Detect.O.Ios, iPod: Detect.O.Ios, "Windows Phone": Detect.O.WindowsPhone, "Windows NT": Detect.O.Windows, "Intel Mac OS X": Detect.O.MacOS, X11: Detect.O.Unix, KFAPWI: Detect.O.FireOS }, BrowserUAMarks: { Unidentified: Detect.B.Unidentified, Chrome: Detect.B.Chrome, CriOS: Detect.B.Chrome, CrMo: Detect.B.Chrome, wv: Detect.B.AndroidWebView, "Version/": Detect.B.AndroidWebkitChrome, Version: Detect.B.AndroidWebkit, "Browser/AppleWebKit": Detect.B.AndroidWebkit, SamsungBrowser: Detect.B.SamsungBrowser, "Intel Mac OS X": Detect.B.Safari, iPhone: Detect.B.Safari, iPad: Detect.B.Safari, iPod: Detect.B.Safari, IEMobile: Detect.B.Ie, Trident: Detect.B.Ie, MSIE: Detect.B.Ie, Edge: Detect.B.Edge, UCBrowser: Detect.B.UcBrowser, UBrowser: Detect.B.UcBrowser, Firefox: Detect.B.Firefox, FxiOS: Detect.B.Firefox, bdbrowser_i18n: Detect.B.BaiduBrowser, baidubrowser: Detect.B.BaiduBrowser, bdbrowser: Detect.B.DUBrowser, MQQBrowser: Detect.B.QQBrowser, "360 Aphone Browser": Detect.B._360Browser, OPR: Detect.B.Opera, Opera: Detect.B.Opera, YaBrowser: Detect.B.YandexBrowser, MxBrowser: Detect.B.Maxthon, Maxthon: Detect.B.Maxthon, Silk: Detect.B.Silk } } : null;
            console.log("[Detect] Initialized data") }

        function d() { for (var g = 0; g < e.length; g++) { if (e[g].init && e[g].init instanceof Function) { e[g].init(b);
                    console.log("[Detect] Initialized " + (g == 0 ? "logic" : "extension " + g)) } else { console.error("Can't initialize extension " + g + " due to missing init() method!") } } }

        function c() { if (b.screenWidth && b.screenHeight) { b.widerSide = Math.max(b.screenWidth, b.screenHeight) } } return { init: function (g) { b = g; if (b && b.userAgent && typeof b.userAgent == "string") { c();
                    a();
                    d();
                    console.info("[Detect] Initialization finished") } else { console.error("Can't initialize Detect.js due to malformed input user agent info: " + b + ". Expected '{userAgent : string}'");
                    console.dir(b) } }, extend: function (j, g) { if (typeof Object.assign != "function") { Object.assign = function (n) { if (n == null) { throw new TypeError("Cannot convert undefined or null to object") } n = Object(n); for (var k = 1; k < arguments.length; k++) { var m = arguments[k]; if (m != null) { for (var l in m) { if (Object.prototype.hasOwnProperty.call(m, l)) { n[l] = m[l] } } } } return n } } for (var h in j) { Object.assign(j[h], g[h]) } }, registerExtension: function (g) { e.push(g) } } })() } Detect.registerExtension((function () { var f = "1.0"; var e; var c = (function () {
        function k(n, o) { var m = o ? o : Detect.Consts.unidentified; return { itemID: m, item: n[Detect.Consts.unidentified], itemVersion: Detect.Consts.unidentified } }

        function l(m) { if (m instanceof Function) { return m() } else { return m } } return { detectDevice: function (p, o) { var m = p.exec(e.userAgent); if (!m) { return k(o) } for (var n = 1; n < m.length; n++) { if (m[n]) { if (o[m[n]]) { return { itemID: m[n], item: l(o[m[n]]) } } else { return k(o, m[n]) } } } }, detectOSorBrowser: function (s, u, t) { var o = { Android: 5, IEMobile: 10, Version: 15, "Version/": 15, Chrome: 20, CriOS: 20, CrMo: 20, iPhone: 30, iPod: 30, iPad: 30, "Mac OS X": 30, Trident: 30, MSIE: 30 }; var w; var n = []; while ((w = s.exec(e.userAgent)) != null) { n.push(w[1] + "|" + w[2]) } w = u.exec(e.userAgent); if (w) { var p, r; for (var q = 1; q < w.length; q++) { if (w[q]) { if (Detect.Consts.isEntityVersion.test(w[q])) { r = w[q] } else { p = w[q] } } } if (p) { n.push(p + "|" + (r ? r : "Unidentified")) } }

                function v(x) { return x.split("|")[0] }

                function m(x) { return x.split("|")[1] } n.sort(function (y, x) { if (o[v(y)] && !o[v(x)]) { return 1 } if (o[v(y)] && o[v(x)]) { return o[v(y)] - o[v(x)] } return -1 }); if (n.length == 0) { return k(t) } else { if (t[v(n[0])]) { return { itemID: v(n[0]), item: l(t[v(n[0])]), itemVersion: m(n[0]).replace(/_/g, ".") } } else { return k(t, v(n[0])) } } }, checkAnyOf: function (m, o) { if (!m.length) { return false } for (var n = 0; n < m.length; n++) { if (o == m[n]) { return true } } return false }, versionCompare: function (r, q) { if (r === q) { return 0 } if (typeof r + typeof q != "stringstring" || isNaN(parseInt(r)) || isNaN(parseInt(q))) { return false } var o = r.split("."); var n = q.split("."); var p = 0; var m = Math.max(o.length, n.length); for (; p < m; p++) { if ((o[p] && !n[p] && parseInt(o[p]) > 0) || (parseInt(o[p]) > parseInt(n[p]))) { return 1 } else { if ((n[p] && !o[p] && parseInt(n[p]) > 0) || (parseInt(o[p]) < parseInt(n[p]))) { return -1 } } } return 0 } } })();

    function b() { if (Detect.allInfo) { return } Detect.allInfo = function () { return { type: this.Type.commercialName(), family: this.Family.commercialName(), device: this.Device.commercialName(), os: this.OS.commercialName(), osVersion: this.OS.version(), browser: this.Browser.commercialName(), browserVersion: this.Browser.version() } } }

    function g() { if (Detect.Device) { return } Detect.Device = (function () { var k, n, m;
            k = n = m = null;

            function l() { var p = Detect.Consts.deviceModelCutOff.exec(n); if (p) { p = p[0]; var o = Detect.Consts.DeviceUAMarks[p];
                    o ? k.item = m = o : null } } return { init: function () { k = c.detectDevice(Detect.Consts.deviceRegex, Detect.Consts.DeviceUAMarks);
                    n = k.itemID;
                    m = k.item; if (k.item == Detect.D.Unidentified) { l() } return k }, rawResult: function () { return k.item }, rawID: function () { return n }, commercialName: function () { return m.name }, is: function (o) { return m == o }, isAnyOf: function () { return c.checkAnyOf(arguments, m) } } })() }

    function h() { if (Detect.Family) { return } Detect.Family = (function () { var l, k;
            l = k = null; return { init: function (m) { l = m.item.family;
                    k = m.itemID; return l }, rawResult: function () { return l }, rawID: function () { return k }, commercialName: function () { return l.name }, is: function (m) { return l == m }, isAnyOf: function () { return c.checkAnyOf(arguments, l) } } })() }

    function a() { if (Detect.Type) { return } Detect.Type = (function () { var l = null;

            function k(n) { var o = e.userAgent;

                function p() { return /Android.+UCBrowser/i.test(o) ? undefined : Detect.Consts.deviceIsSmartPhoneRegex.test(o) && n != Detect.F.Ipad && n != Detect.F.Lumia }

                function m() { return Detect.Consts.deviceIsTabletRegex.test(o) } if (p()) { return Detect.T.SmartPhone } else { if (m()) { return Detect.T.Tablet } else { return Detect.T.Unidentified } } } return { init: function (m) { l = m.type; if (l == Detect.T.Unidentified) { l = k(m) } }, commercialName: function () { return l.name }, is: function (m) { return l == m }, isAnyOf: function () { return c.checkAnyOf(arguments, l) } } })() }

    function d() { if (Detect.OS) { return } Detect.OS = (function () { var m, l, k;
            m = l = k = null; return { init: function () { var n = c.detectOSorBrowser(Detect.Consts.osRegexSimple, Detect.Consts.osRegexComplex, Detect.Consts.OsUAMarks);
                    m = n.itemID;
                    l = n.item;
                    k = n.itemVersion }, rawResult: function () { return l }, rawID: function () { return m }, commercialName: function () { return l.name }, is: function (n) { return l == n }, isAnyOf: function () { return c.checkAnyOf(arguments, l) }, version: function () { return k }, versionIs: function (n) { return c.versionCompare(k.substring(0, n.length), n) == 0 }, versionIsExactly: function (n) { return c.versionCompare(k, n) === 0 }, versionIsLaterThan: function (n) { return c.versionCompare(k, n) === 1 }, versionIsEarlierThan: function (n) { return c.versionCompare(k, n) === -1 } } })() }

    function j() { if (Detect.Browser) { return } Detect.Browser = (function () { var m, k, l;
            m = k = l = null; return { init: function () { var n = c.detectOSorBrowser(Detect.Consts.browserRegexSimple, Detect.Consts.browserRegexComplex, Detect.Consts.BrowserUAMarks);
                    m = n.itemID;
                    k = n.item;
                    l = n.itemVersion }, rawResult: function () { return k }, rawID: function () { return m }, commercialName: function () { return k.name }, is: function (n) { return k == n }, isAnyOf: function () { return c.checkAnyOf(arguments, k) }, version: function () { return l }, versionIs: function (n) { return c.versionCompare(l.substring(0, n.length), n) == 0 }, versionIsExactly: function (n) { return c.versionCompare(l, n) === 0 }, versionIsLaterThan: function (n) { return c.versionCompare(l, n) === 1 }, versionIsEarlierThan: function (n) { return c.versionCompare(l, n) === -1 } } })() } return { init: function (k) { e = k;
            b();
            g();
            h();
            a();
            d();
            j(); var m = Detect.Device.init(); var l = Detect.Family.init(m);
            Detect.Type.init(l);
            Detect.OS.init();
            Detect.Browser.init() } } })());
Detect.registerExtension((function () { var f = "1.0"; var e; var c = (function () {
        function k(n, o) { var m = o ? o : Detect.Consts.unidentified; return { itemID: m, item: n[Detect.Consts.unidentified], itemVersion: Detect.Consts.unidentified } }

        function l(m) { if (m instanceof Function) { return m() } else { return m } } return { detectDevice: function (p, o) { var m = p.exec(e.userAgent); if (!m) { return k(o) } for (var n = 1; n < m.length; n++) { if (m[n]) { if (o[m[n]]) { return { itemID: m[n], item: l(o[m[n]]) } } else { return k(o, m[n]) } } } }, detectOSorBrowser: function (s, u, t) { var o = { Android: 5, IEMobile: 10, Version: 15, "Version/": 15, Chrome: 20, CriOS: 20, CrMo: 20, iPhone: 30, iPod: 30, iPad: 30, "Mac OS X": 30, Trident: 30, MSIE: 30 }; var w; var n = []; while ((w = s.exec(e.userAgent)) != null) { n.push(w[1] + "|" + w[2]) } w = u.exec(e.userAgent); if (w) { var p, r; for (var q = 1; q < w.length; q++) { if (w[q]) { if (Detect.Consts.isEntityVersion.test(w[q])) { r = w[q] } else { p = w[q] } } } if (p) { n.push(p + "|" + (r ? r : "Unidentified")) } }

                function v(x) { return x.split("|")[0] }

                function m(x) { return x.split("|")[1] } n.sort(function (y, x) { if (o[v(y)] && !o[v(x)]) { return 1 } if (o[v(y)] && o[v(x)]) { return o[v(y)] - o[v(x)] } return -1 }); if (n.length == 0) { return k(t) } else { if (t[v(n[0])]) { return { itemID: v(n[0]), item: l(t[v(n[0])]), itemVersion: m(n[0]).replace(/_/g, ".") } } else { return k(t, v(n[0])) } } }, checkAnyOf: function (m, o) { if (!m.length) { return false } for (var n = 0; n < m.length; n++) { if (o == m[n]) { return true } } return false }, versionCompare: function (r, q) { if (r === q) { return 0 } if (typeof r + typeof q != "stringstring" || isNaN(parseInt(r)) || isNaN(parseInt(q))) { return false } var o = r.split("."); var n = q.split("."); var p = 0; var m = Math.max(o.length, n.length); for (; p < m; p++) { if ((o[p] && !n[p] && parseInt(o[p]) > 0) || (parseInt(o[p]) > parseInt(n[p]))) { return 1 } else { if ((n[p] && !o[p] && parseInt(n[p]) > 0) || (parseInt(o[p]) < parseInt(n[p]))) { return -1 } } } return 0 } } })();

    function b() { if (Detect.allInfo) { return } Detect.allInfo = function () { return { type: this.Type.commercialName(), family: this.Family.commercialName(), device: this.Device.commercialName(), os: this.OS.commercialName(), osVersion: this.OS.version(), browser: this.Browser.commercialName(), browserVersion: this.Browser.version() } } }

    function g() { if (Detect.Device) { return } Detect.Device = (function () { var k, n, m;
            k = n = m = null;

            function l() { var p = Detect.Consts.deviceModelCutOff.exec(n); if (p) { p = p[0]; var o = Detect.Consts.DeviceUAMarks[p];
                    o ? k.item = m = o : null } } return { init: function () { k = c.detectDevice(Detect.Consts.deviceRegex, Detect.Consts.DeviceUAMarks);
                    n = k.itemID;
                    m = k.item; if (k.item == Detect.D.Unidentified) { l() } return k }, rawResult: function () { return k.item }, rawID: function () { return n }, commercialName: function () { return m.name }, is: function (o) { return m == o }, isAnyOf: function () { return c.checkAnyOf(arguments, m) } } })() }

    function h() { if (Detect.Family) { return } Detect.Family = (function () { var l, k;
            l = k = null; return { init: function (m) { l = m.item.family;
                    k = m.itemID; return l }, rawResult: function () { return l }, rawID: function () { return k }, commercialName: function () { return l.name }, is: function (m) { return l == m }, isAnyOf: function () { return c.checkAnyOf(arguments, l) } } })() }

    function a() { if (Detect.Type) { return } Detect.Type = (function () { var l = null;

            function k(n) { var o = e.userAgent;

                function p() { return /Android.+UCBrowser/i.test(o) ? undefined : Detect.Consts.deviceIsSmartPhoneRegex.test(o) && n != Detect.F.Ipad && n != Detect.F.Lumia }

                function m() { return Detect.Consts.deviceIsTabletRegex.test(o) } if (p()) { return Detect.T.SmartPhone } else { if (m()) { return Detect.T.Tablet } else { return Detect.T.Unidentified } } } return { init: function (m) { l = m.type; if (l == Detect.T.Unidentified) { l = k(m) } }, commercialName: function () { return l.name }, is: function (m) { return l == m }, isAnyOf: function () { return c.checkAnyOf(arguments, l) } } })() }

    function d() { if (Detect.OS) { return } Detect.OS = (function () { var m, l, k;
            m = l = k = null; return { init: function () { var n = c.detectOSorBrowser(Detect.Consts.osRegexSimple, Detect.Consts.osRegexComplex, Detect.Consts.OsUAMarks);
                    m = n.itemID;
                    l = n.item;
                    k = n.itemVersion }, rawResult: function () { return l }, rawID: function () { return m }, commercialName: function () { return l.name }, is: function (n) { return l == n }, isAnyOf: function () { return c.checkAnyOf(arguments, l) }, version: function () { return k }, versionIs: function (n) { return c.versionCompare(k.substring(0, n.length), n) == 0 }, versionIsExactly: function (n) { return c.versionCompare(k, n) === 0 }, versionIsLaterThan: function (n) { return c.versionCompare(k, n) === 1 }, versionIsEarlierThan: function (n) { return c.versionCompare(k, n) === -1 } } })() }

    function j() { if (Detect.Browser) { return } Detect.Browser = (function () { var m, k, l;
            m = k = l = null; return { init: function () { var n = c.detectOSorBrowser(Detect.Consts.browserRegexSimple, Detect.Consts.browserRegexComplex, Detect.Consts.BrowserUAMarks);
                    m = n.itemID;
                    k = n.item;
                    l = n.itemVersion }, rawResult: function () { return k }, rawID: function () { return m }, commercialName: function () { return k.name }, is: function (n) { return k == n }, isAnyOf: function () { return c.checkAnyOf(arguments, k) }, version: function () { return l }, versionIs: function (n) { return c.versionCompare(l.substring(0, n.length), n) == 0 }, versionIsExactly: function (n) { return c.versionCompare(l, n) === 0 }, versionIsLaterThan: function (n) { return c.versionCompare(l, n) === 1 }, versionIsEarlierThan: function (n) { return c.versionCompare(l, n) === -1 } } })() } return { init: function (k) { e = k;
            b();
            g();
            h();
            a();
            d();
            j(); var m = Detect.Device.init(); var l = Detect.Family.init(m);
            Detect.Type.init(l);
            Detect.OS.init();
            Detect.Browser.init() } } })());
var ImagePreloader = (function (c) { var d = null;

    function b() { var g = arguments.length; var f = a(arguments[0]);
        d.appendChild(f); if (g === 2) { var e = a(arguments[1]);
            f.appendChild(e) } }

    function a(f) { var e = document.createElement("div");
        e.className = f; return e } return { run: function () { d = c.document.createElement("div");
            d.id = "preloadRootDiv";
            c.document.body.insertBefore(d, c.document.body.firstElementChild);
            d.style.height = 0;
            d.style.overflow = "hidden";
            b("brandLogo");
            b("playerBalanceGood");
            b("playerBalanceBad");
            b("quickMenuBackgrounds");
            b("btnQuickMenuControl");
            b("menuButtons");
            b("inGameLobbyPreloadStubGame");
            b("mainMenuContainer", "gwt-PushButton");
            b("quickMenuItems", "gwt-PushButton");
            b("sound-mute-popup sound-mute-container", "sound-button");
            b("switchButtonWidget", "switchButtonBg");
            b("switchButtonWidget", "switchButtonBgTop");
            b("switchButtonWidget", "switchButton");
            b("progressoverlay");
            b("gameByPlaytechLogoDark");
            b("gameByPlaytechLogoLight");
            b("avatar_image", "loader") } } })(window);
Detect.registerExtension(function () {
    function b() { Object.defineProperty(this, "browsers", { value: {}, writable: false }) } b.prototype.addBrowser = function (d) { this.browsers[d.getId()] = d };
    b.prototype.getBrowser = function (d) { return this.browsers[d] };

    function a(d, e, f) { Object.defineProperties(this, { id: { value: d.name, writable: false }, landscape: { value: e, writable: false }, portrait: { value: f, writable: false } }) } a.prototype.getId = function () { return this.id };
    a.prototype.getLandscape = function () { return this.landscape };
    a.prototype.getPortrait = function () { return this.portrait };

    function c(e, d) { Object.defineProperties(this, { core1: { value: e, writable: false }, core2: { value: d, writable: false } }) } c.prototype.getCore1 = function () { return this.core1 };
    c.prototype.getCore2 = function () { return this.core2 }; return { init: function () { Detect.D.Iphone5.deviceResolution = new b();
            Detect.D.Iphone5.deviceResolution.addBrowser(new a(Detect.B.Safari, [568, 320], [320, 529]));
            Detect.D.Iphone5.deviceResolution.addBrowser(new a(Detect.B.Chrome, [568, 300], [320, 548]));
            Detect.D.Iphone5.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [568, 300], [320, 568]));
            Detect.D.Iphone6.deviceResolution = new b();
            Detect.D.Iphone6.deviceResolution.addBrowser(new a(Detect.B.Safari, [667, 375], [375, 628]));
            Detect.D.Iphone6.deviceResolution.addBrowser(new a(Detect.B.Chrome, [667, 355], [375, 647]));
            Detect.D.Iphone6.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [667, 355], [375, 647]));
            Detect.D.Iphone6Plus.deviceResolution = new b();
            Detect.D.Iphone6Plus.deviceResolution.addBrowser(new a(Detect.B.Safari, [736, 414], [414, 696]));
            Detect.D.Iphone6Plus.deviceResolution.addBrowser(new a(Detect.B.Chrome, [736, 394], [414, 716]));
            Detect.D.Iphone6Plus.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [736, 395], [414, 716]));
            Detect.D.Ipad.deviceResolution = new b();
            Detect.D.Ipad.deviceResolution.addBrowser(new a(Detect.B.Safari, [1024, 729], [768, 985]));
            Detect.D.Ipad.deviceResolution.addBrowser(new a(Detect.B.Chrome, [1024, 748], [768, 1004]));
            Detect.D.Ipad.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [1024, 671], [640, 927]));
            Detect.D.Ipad.targetSize = new c([1000, 750], [1280, 600]);
            Detect.D.IpadRetina.deviceResolution = new b();
            Detect.D.IpadRetina.deviceResolution.addBrowser(new a(Detect.B.Safari, [1024, 729], [768, 985]));
            Detect.D.IpadRetina.deviceResolution.addBrowser(new a(Detect.B.Chrome, [1024, 748], [768, 1004]));
            Detect.D.IpadRetina.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [1024, 671], [640, 927]));
            Detect.D.IpadRetina.targetSize = new c([1000, 750], [1280, 600]);
            Detect.D.IpadPro129.deviceResolution = new b();
            Detect.D.IpadPro129.deviceResolution.addBrowser(new a(Detect.B.Safari, [1366, 985], [1024, 1327]));
            Detect.D.IpadPro129.deviceResolution.addBrowser(new a(Detect.B.Chrome, [1024, 748], [768, 1004]));
            Detect.D.IpadPro129.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [1366, 988], [1004, 1366]));
            Detect.D.IpadPro129.targetSize = new c([1000, 750], [1280, 600]);
            Detect.D.GalaxyS7.deviceResolution = new b();
            Detect.D.GalaxyS7.deviceResolution.addBrowser(new a(Detect.B.SamsungBrowser, [640, 336], [360, 616]));
            Detect.D.GalaxyS7.deviceResolution.addBrowser(new a(Detect.B.AndroidWebkit, [640, 336], [360, 616]));
            Detect.D.GalaxyS7.deviceResolution.addBrowser(new a(Detect.B.Chrome, [640, 336], [360, 616]));
            Detect.D.GalaxyS7.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [2560, 1344], [1440, 2272]));
            Detect.D.GalaxyS6Edge.deviceResolution = new b();
            Detect.D.GalaxyS6Edge.deviceResolution.addBrowser(new a(Detect.B.SamsungBrowser, [640, 360], [360, 616]));
            Detect.D.GalaxyS6Edge.deviceResolution.addBrowser(new a(Detect.B.AndroidWebkit, [640, 336], [360, 616]));
            Detect.D.GalaxyS6Edge.deviceResolution.addBrowser(new a(Detect.B.Chrome, [640, 336], [360, 616]));
            Detect.D.GalaxyS6Edge.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [2560, 1344], [1440, 2272]));
            Detect.D.GalaxyS6.deviceResolution = new b();
            Detect.D.GalaxyS6.deviceResolution.addBrowser(new a(Detect.B.SamsungBrowser, [640, 360], [360, 616]));
            Detect.D.GalaxyS6.deviceResolution.addBrowser(new a(Detect.B.AndroidWebkit, [640, 336], [360, 616]));
            Detect.D.GalaxyS6.deviceResolution.addBrowser(new a(Detect.B.Chrome, [640, 336], [360, 616]));
            Detect.D.GalaxyS6.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [2560, 1344], [1440, 2272]));
            Detect.D.GalaxyS5.deviceResolution = new b();
            Detect.D.GalaxyS5.deviceResolution.addBrowser(new a(Detect.B.SamsungBrowser, [640, 335], [360, 615]));
            Detect.D.GalaxyS5.deviceResolution.addBrowser(new a(Detect.B.AndroidWebkit, [640, 335], [360, 615]));
            Detect.D.GalaxyS5.deviceResolution.addBrowser(new a(Detect.B.Chrome, [640, 335], [360, 615]));
            Detect.D.GalaxyS5.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [1920, 1005], [1080, 1701]));
            Detect.D.GalaxyS4.deviceResolution = new b();
            Detect.D.GalaxyS4.deviceResolution.addBrowser(new a(Detect.B.SamsungBrowser, [640, 335], [360, 615]));
            Detect.D.GalaxyS4.deviceResolution.addBrowser(new a(Detect.B.AndroidWebkit, [640, 335], [360, 615]));
            Detect.D.GalaxyS4.deviceResolution.addBrowser(new a(Detect.B.Chrome, [640, 335], [360, 615]));
            Detect.D.GalaxyS4.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [1920, 1005], [1080, 1701]));
            Detect.D.GalaxyNote4.deviceResolution = new b();
            Detect.D.GalaxyNote4.deviceResolution.addBrowser(new a(Detect.B.SamsungBrowser, [640, 336], [360, 616]));
            Detect.D.GalaxyNote4.deviceResolution.addBrowser(new a(Detect.B.AndroidWebkit, [640, 336], [360, 616]));
            Detect.D.GalaxyNote4.deviceResolution.addBrowser(new a(Detect.B.Chrome, [640, 336], [360, 616]));
            Detect.D.GalaxyNote4.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [2560, 1344], [1440, 2272]));
            Detect.D.LGG4.deviceResolution = new b();
            Detect.D.LGG4.deviceResolution.addBrowser(new a(Detect.B.AndroidWebkit, [598, 336], [360, 574]));
            Detect.D.LGG4.deviceResolution.addBrowser(new a(Detect.B.Chrome, [598, 336], [360, 574]));
            Detect.D.LGG4.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [2392, 1344], [1440, 2392]));
            Detect.D.LGG4.targetSize = new c([980, 560], [1280, 600]);
            Detect.D.LGG5.deviceResolution = new b();
            Detect.D.LGG5.deviceResolution.addBrowser(new a(Detect.B.Chrome, [598, 336], [360, 620]));
            Detect.D.LGG5.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [2392, 1344], [1440, 2392]));
            Detect.D.XiaomiMi4.deviceResolution = new b();
            Detect.D.XiaomiMi4.deviceResolution.addBrowser(new a(Detect.B.Chrome, [640, 340], [360, 574]));
            Detect.D.XiaomiMi4.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [1920, 1020], [1080, 1716]));
            Detect.D.XiaomiMi3.deviceResolution = new b();
            Detect.D.XiaomiMi3.deviceResolution.addBrowser(new a(Detect.B.Chrome, [640, 340], [360, 574]));
            Detect.D.XiaomiMi3.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [1920, 1020], [1080, 1716]));
            Detect.D.Nexus6P.deviceResolution = new b();
            Detect.D.Nexus6P.deviceResolution.addBrowser(new a(Detect.B.Chrome, [684, 388], [412, 660]));
            Detect.D.Nexus6P.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [2392, 1356], [1440, 2140]));
            Detect.D.Nexus5X.deviceResolution = new b();
            Detect.D.Nexus5X.deviceResolution.addBrowser(new a(Detect.B.Chrome, [684, 388], [412, 660]));
            Detect.D.Nexus5X.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [1794, 1017], [1080, 1605]));
            Detect.D.Nexus7.deviceResolution = new b();
            Detect.D.Nexus7.deviceResolution.addBrowser(new a(Detect.B.Chrome, [960, 527], [600, 887]));
            Detect.D.Nexus7.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [1920, 1054], [1200, 1678]));
            Detect.D.LenovoA889.deviceResolution = new b();
            Detect.D.LenovoA889.deviceResolution.addBrowser(new a(Detect.B.AndroidWebkit, [897, 502], [540, 850]));
            Detect.D.LenovoA889.deviceResolution.addBrowser(new a(Detect.B.Chrome, [598, 335], [360, 567]));
            Detect.D.LenovoA889.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [897, 540], [540, 888]));
            Detect.D.LenovoTab2A730HC.deviceResolution = new b();
            Detect.D.LenovoTab2A730HC.deviceResolution.addBrowser(new a(Detect.B.Chrome, [1024, 527], [600, 951]));
            Detect.D.LenovoTab2A730HC.deviceResolution.addBrowser(new a(Detect.B.UcBrowser, [1024, 527], [600, 976])) } } }());
/*! Socket.IO.js build:0.9.17, development. Copyright(c) 2011 LearnBoost <dev@learnboost.com> MIT Licensed */
var io = ("undefined" === typeof module ? {} : module.exports);
(function () {
    (function (exports, global) { var io = exports;
        io.version = "0.9.17";
        io.protocol = 1;
        io.transports = [];
        io.j = [];
        io.sockets = {};
        io.connect = function (host, details) { var uri = io.util.parseUri(host),
                uuri, socket; if (global && global.location) { uri.protocol = uri.protocol || global.location.protocol.slice(0, -1);
                uri.host = uri.host || (global.document ? global.document.domain : global.location.hostname);
                uri.port = uri.port || global.location.port } uuri = io.util.uniqueUri(uri); var options = { host: uri.host, secure: "https" == uri.protocol, port: uri.port || ("https" == uri.protocol ? 443 : 80), query: uri.query || "" };
            io.util.merge(options, details); if (options["force new connection"] || !io.sockets[uuri]) { socket = new io.Socket(options) } if (!options["force new connection"] && socket) { io.sockets[uuri] = socket } socket = socket || io.sockets[uuri]; return socket.of(uri.path.length > 1 ? uri.path : "") } })("object" === typeof module ? module.exports : (this.io = {}), this);
    (function (exports, global) { var util = exports.util = {}; var re = /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*)(?::([^:@]*))?)?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/; var parts = ["source", "protocol", "authority", "userInfo", "user", "password", "host", "port", "relative", "path", "directory", "file", "query", "anchor"];
        util.parseUri = function (str) { var m = re.exec(str || ""),
                uri = {},
                i = 14; while (i--) { uri[parts[i]] = m[i] || "" } return uri };
        util.uniqueUri = function (uri) { var protocol = uri.protocol,
                host = uri.host,
                port = uri.port; if ("document" in global) { host = host || document.domain;
                port = port || (protocol == "https" && document.location.protocol !== "https:" ? 443 : document.location.port) } else { host = host || "localhost"; if (!port && protocol == "https") { port = 443 } } return (protocol || "http") + "://" + host + ":" + (port || 80) };
        util.query = function (base, addition) { var query = util.chunkQuery(base || ""),
                components = [];
            util.merge(query, util.chunkQuery(addition || "")); for (var part in query) { if (query.hasOwnProperty(part)) { components.push(part + "=" + query[part]) } } return components.length ? "?" + components.join("&") : "" };
        util.chunkQuery = function (qs) { var query = {},
                params = qs.split("&"),
                i = 0,
                l = params.length,
                kv; for (; i < l; ++i) { kv = params[i].split("="); if (kv[0]) { query[kv[0]] = kv[1] } } return query }; var pageLoaded = false;
        util.load = function (fn) { if ("document" in global && document.readyState === "complete" || pageLoaded) { return fn() } util.on(global, "load", fn, false) };
        util.on = function (element, event, fn, capture) { if (element.attachEvent) { element.attachEvent("on" + event, fn) } else { if (element.addEventListener) { element.addEventListener(event, fn, capture) } } };
        util.request = function (xdomain) { if (xdomain && "undefined" != typeof XDomainRequest && !util.ua.hasCORS) { return new XDomainRequest() } if ("undefined" != typeof XMLHttpRequest && (!xdomain || util.ua.hasCORS)) { return new XMLHttpRequest() } if (!xdomain) { try { return new window[(["Active"].concat("Object").join("X"))]("Microsoft.XMLHTTP") } catch (e) {} } return null }; if ("undefined" != typeof window) { util.load(function () { pageLoaded = true }) } util.defer = function (fn) { if (!util.ua.webkit || "undefined" != typeof importScripts) { return fn() } util.load(function () { setTimeout(fn, 100) }) };
        util.merge = function merge(target, additional, deep, lastseen) { var seen = lastseen || [],
                depth = typeof deep == "undefined" ? 2 : deep,
                prop; for (prop in additional) { if (additional.hasOwnProperty(prop) && util.indexOf(seen, prop) < 0) { if (typeof target[prop] !== "object" || !depth) { target[prop] = additional[prop];
                        seen.push(additional[prop]) } else { util.merge(target[prop], additional[prop], depth - 1, seen) } } } return target };
        util.mixin = function (ctor, ctor2) { util.merge(ctor.prototype, ctor2.prototype) };
        util.inherit = function (ctor, ctor2) {
            function f() {} f.prototype = ctor2.prototype;
            ctor.prototype = new f };
        util.isArray = Array.isArray || function (obj) { return Object.prototype.toString.call(obj) === "[object Array]" };
        util.intersect = function (arr, arr2) { var ret = [],
                longest = arr.length > arr2.length ? arr : arr2,
                shortest = arr.length > arr2.length ? arr2 : arr; for (var i = 0, l = shortest.length; i < l; i++) { if (~util.indexOf(longest, shortest[i])) { ret.push(shortest[i]) } } return ret };
        util.indexOf = function (arr, o, i) { for (var j = arr.length, i = i < 0 ? i + j < 0 ? 0 : i + j : i || 0; i < j && arr[i] !== o; i++) {} return j <= i ? -1 : i };
        util.toArray = function (enu) { var arr = []; for (var i = 0, l = enu.length; i < l; i++) { arr.push(enu[i]) } return arr };
        util.ua = {};
        util.ua.hasCORS = "undefined" != typeof XMLHttpRequest && (function () { try { var a = new XMLHttpRequest() } catch (e) { return false } return a.withCredentials != undefined })();
        util.ua.webkit = "undefined" != typeof navigator && /webkit/i.test(navigator.userAgent);
        util.ua.iDevice = "undefined" != typeof navigator && /iPad|iPhone|iPod/i.test(navigator.userAgent) })("undefined" != typeof io ? io : module.exports, this);
    (function (exports, io) { exports.EventEmitter = EventEmitter;

        function EventEmitter() {} EventEmitter.prototype.on = function (name, fn) { if (!this.$events) { this.$events = {} } if (!this.$events[name]) { this.$events[name] = fn } else { if (io.util.isArray(this.$events[name])) { this.$events[name].push(fn) } else { this.$events[name] = [this.$events[name], fn] } } return this };
        EventEmitter.prototype.addListener = EventEmitter.prototype.on;
        EventEmitter.prototype.once = function (name, fn) { var self = this;

            function on() { self.removeListener(name, on);
                fn.apply(this, arguments) } on.listener = fn;
            this.on(name, on); return this };
        EventEmitter.prototype.removeListener = function (name, fn) { if (this.$events && this.$events[name]) { var list = this.$events[name]; if (io.util.isArray(list)) { var pos = -1; for (var i = 0, l = list.length; i < l; i++) { if (list[i] === fn || (list[i].listener && list[i].listener === fn)) { pos = i; break } } if (pos < 0) { return this } list.splice(pos, 1); if (!list.length) { delete this.$events[name] } } else { if (list === fn || (list.listener && list.listener === fn)) { delete this.$events[name] } } } return this };
        EventEmitter.prototype.removeAllListeners = function (name) { if (name === undefined) { this.$events = {}; return this } if (this.$events && this.$events[name]) { this.$events[name] = null } return this };
        EventEmitter.prototype.listeners = function (name) { if (!this.$events) { this.$events = {} } if (!this.$events[name]) { this.$events[name] = [] } if (!io.util.isArray(this.$events[name])) { this.$events[name] = [this.$events[name]] } return this.$events[name] };
        EventEmitter.prototype.emit = function (name) { if (!this.$events) { return false } var handler = this.$events[name]; if (!handler) { return false } var args = Array.prototype.slice.call(arguments, 1); if ("function" == typeof handler) { handler.apply(this, args) } else { if (io.util.isArray(handler)) { var listeners = handler.slice(); for (var i = 0, l = listeners.length; i < l; i++) { listeners[i].apply(this, args) } } else { return false } } return true } })("undefined" != typeof io ? io : module.exports, "undefined" != typeof io ? io : module.parent.exports);
    (function (exports, nativeJSON) { if (nativeJSON && nativeJSON.parse) { return exports.JSON = { parse: nativeJSON.parse, stringify: nativeJSON.stringify } } var JSON = exports.JSON = {};

        function f(n) { return n < 10 ? "0" + n : n }

        function date(d, key) { return isFinite(d.valueOf()) ? d.getUTCFullYear() + "-" + f(d.getUTCMonth() + 1) + "-" + f(d.getUTCDate()) + "T" + f(d.getUTCHours()) + ":" + f(d.getUTCMinutes()) + ":" + f(d.getUTCSeconds()) + "Z" : null } var cx = /[\u0000\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
            escapable = /[\\\"\x00-\x1f\x7f-\x9f\u00ad\u0600-\u0604\u070f\u17b4\u17b5\u200c-\u200f\u2028-\u202f\u2060-\u206f\ufeff\ufff0-\uffff]/g,
            gap, indent, meta = { "\b": "\\b", "\t": "\\t", "\n": "\\n", "\f": "\\f", "\r": "\\r", '"': '\\"', "\\": "\\\\" },
            rep;

        function quote(string) { escapable.lastIndex = 0; return escapable.test(string) ? '"' + string.replace(escapable, function (a) { var c = meta[a]; return typeof c === "string" ? c : "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4) }) + '"' : '"' + string + '"' }

        function str(key, holder) { var i, k, v, length, mind = gap,
                partial, value = holder[key]; if (value instanceof Date) { value = date(key) } if (typeof rep === "function") { value = rep.call(holder, key, value) } switch (typeof value) {
            case "string":
                return quote(value);
            case "number":
                return isFinite(value) ? String(value) : "null";
            case "boolean":
            case "null":
                return String(value);
            case "object":
                if (!value) { return "null" } gap += indent;
                partial = []; if (Object.prototype.toString.apply(value) === "[object Array]") { length = value.length; for (i = 0; i < length; i += 1) { partial[i] = str(i, value) || "null" } v = partial.length === 0 ? "[]" : gap ? "[\n" + gap + partial.join(",\n" + gap) + "\n" + mind + "]" : "[" + partial.join(",") + "]";
                    gap = mind; return v } if (rep && typeof rep === "object") { length = rep.length; for (i = 0; i < length; i += 1) { if (typeof rep[i] === "string") { k = rep[i];
                            v = str(k, value); if (v) { partial.push(quote(k) + (gap ? ": " : ":") + v) } } } } else { for (k in value) { if (Object.prototype.hasOwnProperty.call(value, k)) { v = str(k, value); if (v) { partial.push(quote(k) + (gap ? ": " : ":") + v) } } } } v = partial.length === 0 ? "{}" : gap ? "{\n" + gap + partial.join(",\n" + gap) + "\n" + mind + "}" : "{" + partial.join(",") + "}";
                gap = mind; return v } } JSON.stringify = function (value, replacer, space) { var i;
            gap = "";
            indent = ""; if (typeof space === "number") { for (i = 0; i < space; i += 1) { indent += " " } } else { if (typeof space === "string") { indent = space } } rep = replacer; if (replacer && typeof replacer !== "function" && (typeof replacer !== "object" || typeof replacer.length !== "number")) { throw new Error("JSON.stringify") } return str("", { "": value }) };
        JSON.parse = function (text, reviver) { var j;

            function walk(holder, key) { var k, v, value = holder[key]; if (value && typeof value === "object") { for (k in value) { if (Object.prototype.hasOwnProperty.call(value, k)) { v = walk(value, k); if (v !== undefined) { value[k] = v } else { delete value[k] } } } } return reviver.call(holder, key, value) } text = String(text);
            cx.lastIndex = 0; if (cx.test(text)) { text = text.replace(cx, function (a) { return "\\u" + ("0000" + a.charCodeAt(0).toString(16)).slice(-4) }) } if (/^[\],:{}\s]*$/.test(text.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, "@").replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, "]").replace(/(?:^|:|,)(?:\s*\[)+/g, ""))) { j = eval("(" + text + ")"); return typeof reviver === "function" ? walk({ "": j }, "") : j } throw new SyntaxError("JSON.parse") } })("undefined" != typeof io ? io : module.exports, typeof JSON !== "undefined" ? JSON : undefined);
    (function (exports, io) { var parser = exports.parser = {}; var packets = parser.packets = ["disconnect", "connect", "heartbeat", "message", "json", "event", "ack", "error", "noop"]; var reasons = parser.reasons = ["transport not supported", "client not handshaken", "unauthorized"]; var advice = parser.advice = ["reconnect"]; var JSON = io.JSON,
            indexOf = io.util.indexOf;
        parser.encodePacket = function (packet) { var type = indexOf(packets, packet.type),
                id = packet.id || "",
                endpoint = packet.endpoint || "",
                ack = packet.ack,
                data = null; switch (packet.type) {
            case "error":
                var reason = packet.reason ? indexOf(reasons, packet.reason) : "",
                    adv = packet.advice ? indexOf(advice, packet.advice) : ""; if (reason !== "" || adv !== "") { data = reason + (adv !== "" ? ("+" + adv) : "") } break;
            case "message":
                if (packet.data !== "") { data = packet.data } break;
            case "event":
                var ev = { name: packet.name }; if (packet.args && packet.args.length) { ev.args = packet.args } data = JSON.stringify(ev); break;
            case "json":
                data = JSON.stringify(packet.data); break;
            case "connect":
                if (packet.qs) { data = packet.qs } break;
            case "ack":
                data = packet.ackId + (packet.args && packet.args.length ? "+" + JSON.stringify(packet.args) : ""); break } var encoded = [type, id + (ack == "data" ? "+" : ""), endpoint]; if (data !== null && data !== undefined) { encoded.push(data) } return encoded.join(":") };
        parser.encodePayload = function (packets) { var decoded = ""; if (packets.length == 1) { return packets[0] } for (var i = 0, l = packets.length; i < l; i++) { var packet = packets[i];
                decoded += "\ufffd" + packet.length + "\ufffd" + packets[i] } return decoded }; var regexp = /([^:]+):([0-9]+)?(\+)?:([^:]+)?:?([\s\S]*)?/;
        parser.decodePacket = function (data) { var pieces = data.match(regexp); if (!pieces) { return {} } var id = pieces[2] || "",
                data = pieces[5] || "",
                packet = { type: packets[pieces[1]], endpoint: pieces[4] || "" }; if (id) { packet.id = id; if (pieces[3]) { packet.ack = "data" } else { packet.ack = true } } switch (packet.type) {
            case "error":
                var pieces = data.split("+");
                packet.reason = reasons[pieces[0]] || "";
                packet.advice = advice[pieces[1]] || ""; break;
            case "message":
                packet.data = data || ""; break;
            case "event":
                try { var opts = JSON.parse(data);
                    packet.name = opts.name;
                    packet.args = opts.args } catch (e) {} packet.args = packet.args || []; break;
            case "json":
                try { packet.data = JSON.parse(data) } catch (e) {} break;
            case "connect":
                packet.qs = data || ""; break;
            case "ack":
                var pieces = data.match(/^([0-9]+)(\+)?(.*)/); if (pieces) { packet.ackId = pieces[1];
                    packet.args = []; if (pieces[3]) { try { packet.args = pieces[3] ? JSON.parse(pieces[3]) : [] } catch (e) {} } } break;
            case "disconnect":
            case "heartbeat":
                break } return packet };
        parser.decodePayload = function (data) { if (data.charAt(0) == "\ufffd") { var ret = []; for (var i = 1, length = ""; i < data.length; i++) { if (data.charAt(i) == "\ufffd") { ret.push(parser.decodePacket(data.substr(i + 1).substr(0, length)));
                        i += Number(length) + 1;
                        length = "" } else { length += data.charAt(i) } } return ret } else { return [parser.decodePacket(data)] } } })("undefined" != typeof io ? io : module.exports, "undefined" != typeof io ? io : module.parent.exports);
    (function (exports, io) { exports.Transport = Transport;

        function Transport(socket, sessid) { this.socket = socket;
            this.sessid = sessid } io.util.mixin(Transport, io.EventEmitter);
        Transport.prototype.heartbeats = function () { return true };
        Transport.prototype.onData = function (data) { this.clearCloseTimeout(); if (this.socket.connected || this.socket.connecting || this.socket.reconnecting) { this.setCloseTimeout() } if (data !== "") { var msgs = io.parser.decodePayload(data); if (msgs && msgs.length) { for (var i = 0, l = msgs.length; i < l; i++) { this.onPacket(msgs[i]) } } } return this };
        Transport.prototype.onPacket = function (packet) { this.socket.setHeartbeatTimeout(); if (packet.type == "heartbeat") { return this.onHeartbeat() } if (packet.type == "connect" && packet.endpoint == "") { this.onConnect() } if (packet.type == "error" && packet.advice == "reconnect") { this.isOpen = false } this.socket.onPacket(packet); return this };
        Transport.prototype.setCloseTimeout = function () { if (!this.closeTimeout) { var self = this;
                this.closeTimeout = setTimeout(function () { self.onDisconnect() }, this.socket.closeTimeout) } };
        Transport.prototype.onDisconnect = function () { if (this.isOpen) { this.close() } this.clearTimeouts();
            this.socket.onDisconnect(); return this };
        Transport.prototype.onConnect = function () { this.socket.onConnect(); return this };
        Transport.prototype.clearCloseTimeout = function () { if (this.closeTimeout) { clearTimeout(this.closeTimeout);
                this.closeTimeout = null } };
        Transport.prototype.clearTimeouts = function () { this.clearCloseTimeout(); if (this.reopenTimeout) { clearTimeout(this.reopenTimeout) } };
        Transport.prototype.packet = function (packet) { this.send(io.parser.encodePacket(packet)) };
        Transport.prototype.onHeartbeat = function (heartbeat) { this.packet({ type: "heartbeat" }) };
        Transport.prototype.onOpen = function () { this.isOpen = true;
            this.clearCloseTimeout();
            this.socket.onOpen() };
        Transport.prototype.onClose = function () { var self = this;
            this.isOpen = false;
            this.socket.onClose();
            this.onDisconnect() };
        Transport.prototype.prepareUrl = function () { var options = this.socket.options; return this.scheme() + "://" + options.host + ":" + options.port + "/" + options.resource + "/" + io.protocol + "/" + this.name + "/" + this.sessid };
        Transport.prototype.ready = function (socket, fn) { fn.call(this) } })("undefined" != typeof io ? io : module.exports, "undefined" != typeof io ? io : module.parent.exports);
    (function (exports, io, global) { exports.Socket = Socket;

        function Socket(options) { this.options = { port: 80, secure: false, document: "document" in global ? document : false, resource: "socket.io", transports: io.transports, "connect timeout": 10000, "try multiple transports": true, reconnect: true, "reconnection delay": 500, "reconnection limit": Infinity, "reopen delay": 3000, "max reconnection attempts": 10, "sync disconnect on unload": false, "auto connect": true, "flash policy port": 10843, manualFlush: false };
            io.util.merge(this.options, options);
            this.connected = false;
            this.open = false;
            this.connecting = false;
            this.reconnecting = false;
            this.namespaces = {};
            this.buffer = [];
            this.doBuffer = false; if (this.options["sync disconnect on unload"] && (!this.isXDomain() || io.util.ua.hasCORS)) { var self = this;
                io.util.on(global, "beforeunload", function () { self.disconnectSync() }, false) } if (this.options["auto connect"]) { this.connect() } } io.util.mixin(Socket, io.EventEmitter);
        Socket.prototype.of = function (name) { if (!this.namespaces[name]) { this.namespaces[name] = new io.SocketNamespace(this, name); if (name !== "") { this.namespaces[name].packet({ type: "connect" }) } } return this.namespaces[name] };
        Socket.prototype.publish = function () { this.emit.apply(this, arguments); var nsp; for (var i in this.namespaces) { if (this.namespaces.hasOwnProperty(i)) { nsp = this.of(i);
                    nsp.$emit.apply(nsp, arguments) } } };

        function empty() {} Socket.prototype.handshake = function (fn) { var self = this,
                options = this.options;

            function complete(data) { if (data instanceof Error) { self.connecting = false;
                    self.onError(data.message) } else { fn.apply(null, data.split(":")) } } var url = ["http" + (options.secure ? "s" : "") + ":/", options.host + ":" + options.port, options.resource, io.protocol, io.util.query(this.options.query, "t=" + +new Date)].join("/"); if (this.isXDomain() && !io.util.ua.hasCORS) { var insertAt = document.getElementsByTagName("script")[0],
                    script = document.createElement("script");
                script.src = url + "&jsonp=" + io.j.length;
                insertAt.parentNode.insertBefore(script, insertAt);
                io.j.push(function (data) { complete(data);
                    script.parentNode.removeChild(script) }) } else { var xhr = io.util.request();
				
				url='getsocket.php';	
                xhr.open("GET", url, true); if (this.isXDomain()) { xhr.withCredentials = true } xhr.onreadystatechange = function () { if (xhr.readyState == 4) { xhr.onreadystatechange = empty; if (xhr.status == 200) { complete(xhr.responseText) } else { if (xhr.status == 403) { self.onError(xhr.responseText) } else { self.connecting = false;!self.reconnecting && self.onError(xhr.responseText) } } } };
                xhr.send(null) } };
        Socket.prototype.getTransport = function (override) { var transports = override || this.transports,
                match; for (var i = 0, transport; transport = transports[i]; i++) { if (io.Transport[transport] && io.Transport[transport].check(this) && (!this.isXDomain() || io.Transport[transport].xdomainCheck(this))) { return new io.Transport[transport](this, this.sessionid) } } return null };
        Socket.prototype.connect = function (fn) { if (this.connecting) { return this } var self = this;
            self.connecting = true;
            this.handshake(function (sid, heartbeat, close, transports) { self.sessionid = sid;
                self.closeTimeout = close * 1000;
                self.heartbeatTimeout = heartbeat * 1000; if (!self.transports) { self.transports = self.origTransports = (transports ? io.util.intersect(transports.split(","), self.options.transports) : self.options.transports) } self.setHeartbeatTimeout();

                function connect(transports) { if (self.transport) { self.transport.clearTimeouts() } self.transport = self.getTransport(transports); if (!self.transport) { return self.publish("connect_failed") } self.transport.ready(self, function () { self.connecting = true;
                        self.publish("connecting", self.transport.name);
                        self.transport.open(); if (self.options["connect timeout"]) { self.connectTimeoutTimer = setTimeout(function () { if (!self.connected) { self.connecting = false; if (self.options["try multiple transports"]) { var remaining = self.transports; while (remaining.length > 0 && remaining.splice(0, 1)[0] != self.transport.name) {} if (remaining.length) { connect(remaining) } else { self.publish("connect_failed") } } } }, self.options["connect timeout"]) } }) } connect(self.transports);
                self.once("connect", function () { clearTimeout(self.connectTimeoutTimer);
                    fn && typeof fn == "function" && fn() }) }); return this };
        Socket.prototype.setHeartbeatTimeout = function () { clearTimeout(this.heartbeatTimeoutTimer); if (this.transport && !this.transport.heartbeats()) { return } var self = this;
            this.heartbeatTimeoutTimer = setTimeout(function () { self.transport.onClose() }, this.heartbeatTimeout) };
        Socket.prototype.packet = function (data) { if (this.connected && !this.doBuffer) { this.transport.packet(data) } else { this.buffer.push(data) } return this };
        Socket.prototype.setBuffer = function (v) { this.doBuffer = v; if (!v && this.connected && this.buffer.length) { if (!this.options.manualFlush) { this.flushBuffer() } } };
        Socket.prototype.flushBuffer = function () { this.transport.payload(this.buffer);
            this.buffer = [] };
        Socket.prototype.disconnect = function () { if (this.connected || this.connecting) { if (this.open) { this.of("").packet({ type: "disconnect" }) } this.onDisconnect("booted") } return this };
        Socket.prototype.disconnectSync = function () { var xhr = io.util.request(); var uri = ["http" + (this.options.secure ? "s" : "") + ":/", this.options.host + ":" + this.options.port, this.options.resource, io.protocol, "", this.sessionid].join("/") + "/?disconnect=1";
           console.log(uri);
            xhr.open("GET", uri, false);
            xhr.send(null);
            this.onDisconnect("booted") };
        Socket.prototype.isXDomain = function () { var port = global.location.port || ("https:" == global.location.protocol ? 443 : 80); return this.options.host !== global.location.hostname || this.options.port != port };
        Socket.prototype.onConnect = function () { if (!this.connected) { this.connected = true;
                this.connecting = false; if (!this.doBuffer) { this.setBuffer(false) } this.emit("connect") } };
        Socket.prototype.onOpen = function () { this.open = true };
        Socket.prototype.onClose = function () { this.open = false;
            clearTimeout(this.heartbeatTimeoutTimer) };
        Socket.prototype.onPacket = function (packet) { this.of(packet.endpoint).onPacket(packet) };
        Socket.prototype.onError = function (err) { if (err && err.advice) { if (err.advice === "reconnect" && (this.connected || this.connecting)) { this.disconnect(); if (this.options.reconnect) { this.reconnect() } } } this.publish("error", err && err.reason ? err.reason : err) };
        Socket.prototype.onDisconnect = function (reason) { var wasConnected = this.connected,
                wasConnecting = this.connecting;
            this.connected = false;
            this.connecting = false;
            this.open = false; if (wasConnected || wasConnecting) { this.transport.close();
                this.transport.clearTimeouts(); if (wasConnected) { this.publish("disconnect", reason); if ("booted" != reason && this.options.reconnect && !this.reconnecting) { this.reconnect() } } } };
        Socket.prototype.reconnect = function () { this.reconnecting = true;
            this.reconnectionAttempts = 0;
            this.reconnectionDelay = this.options["reconnection delay"]; var self = this,
                maxAttempts = this.options["max reconnection attempts"],
                tryMultiple = this.options["try multiple transports"],
                limit = this.options["reconnection limit"];

            function reset() { if (self.connected) { for (var i in self.namespaces) { if (self.namespaces.hasOwnProperty(i) && "" !== i) { self.namespaces[i].packet({ type: "connect" }) } } self.publish("reconnect", self.transport.name, self.reconnectionAttempts) } clearTimeout(self.reconnectionTimer);
                self.removeListener("connect_failed", maybeReconnect);
                self.removeListener("connect", maybeReconnect);
                self.reconnecting = false;
                delete self.reconnectionAttempts;
                delete self.reconnectionDelay;
                delete self.reconnectionTimer;
                delete self.redoTransports;
                self.options["try multiple transports"] = tryMultiple }

            function maybeReconnect() { if (!self.reconnecting) { return } if (self.connected) { return reset() } if (self.connecting && self.reconnecting) { return self.reconnectionTimer = setTimeout(maybeReconnect, 1000) } if (self.reconnectionAttempts++ >= maxAttempts) { if (!self.redoTransports) { self.on("connect_failed", maybeReconnect);
                        self.options["try multiple transports"] = true;
                        self.transports = self.origTransports;
                        self.transport = self.getTransport();
                        self.redoTransports = true;
                        self.connect() } else { self.publish("reconnect_failed");
                        reset() } } else { if (self.reconnectionDelay < limit) { self.reconnectionDelay *= 2 } self.connect();
                    self.publish("reconnecting", self.reconnectionDelay, self.reconnectionAttempts);
                    self.reconnectionTimer = setTimeout(maybeReconnect, self.reconnectionDelay) } } this.options["try multiple transports"] = false;
            this.reconnectionTimer = setTimeout(maybeReconnect, this.reconnectionDelay);
            this.on("connect", maybeReconnect) } })("undefined" != typeof io ? io : module.exports, "undefined" != typeof io ? io : module.parent.exports, this);
    (function (exports, io) { exports.SocketNamespace = SocketNamespace;

        function SocketNamespace(socket, name) { this.socket = socket;
            this.name = name || "";
            this.flags = {};
            this.json = new Flag(this, "json");
            this.ackPackets = 0;
            this.acks = {} } io.util.mixin(SocketNamespace, io.EventEmitter);
        SocketNamespace.prototype.$emit = io.EventEmitter.prototype.emit;
        SocketNamespace.prototype.of = function () { return this.socket.of.apply(this.socket, arguments) };
        SocketNamespace.prototype.packet = function (packet) { packet.endpoint = this.name;
            this.socket.packet(packet);
            this.flags = {}; return this };
        SocketNamespace.prototype.send = function (data, fn) { var packet = { type: this.flags.json ? "json" : "message", data: data }; if ("function" == typeof fn) { packet.id = ++this.ackPackets;
                packet.ack = true;
                this.acks[packet.id] = fn } return this.packet(packet) };
        SocketNamespace.prototype.emit = function (name) { var args = Array.prototype.slice.call(arguments, 1),
                lastArg = args[args.length - 1],
                packet = { type: "event", name: name }; if ("function" == typeof lastArg) { packet.id = ++this.ackPackets;
                packet.ack = "data";
                this.acks[packet.id] = lastArg;
                args = args.slice(0, args.length - 1) } packet.args = args; return this.packet(packet) };
        SocketNamespace.prototype.disconnect = function () { if (this.name === "") { this.socket.disconnect() } else { this.packet({ type: "disconnect" });
                this.$emit("disconnect") } return this };
        SocketNamespace.prototype.onPacket = function (packet) { var self = this;

            function ack() { self.packet({ type: "ack", args: io.util.toArray(arguments), ackId: packet.id }) } switch (packet.type) {
            case "connect":
                this.$emit("connect"); break;
            case "disconnect":
                if (this.name === "") { this.socket.onDisconnect(packet.reason || "booted") } else { this.$emit("disconnect", packet.reason) } break;
            case "message":
            case "json":
                var params = ["message", packet.data]; if (packet.ack == "data") { params.push(ack) } else { if (packet.ack) { this.packet({ type: "ack", ackId: packet.id }) } } this.$emit.apply(this, params); break;
            case "event":
                var params = [packet.name].concat(packet.args); if (packet.ack == "data") { params.push(ack) } this.$emit.apply(this, params); break;
            case "ack":
                if (this.acks[packet.ackId]) { this.acks[packet.ackId].apply(this, packet.args);
                    delete this.acks[packet.ackId] } break;
            case "error":
                if (packet.advice) { this.socket.onError(packet) } else { if (packet.reason == "unauthorized") { this.$emit("connect_failed", packet.reason) } else { this.$emit("error", packet.reason) } } break } };

        function Flag(nsp, name) { this.namespace = nsp;
            this.name = name } Flag.prototype.send = function () { this.namespace.flags[this.name] = true;
            this.namespace.send.apply(this.namespace, arguments) };
        Flag.prototype.emit = function () { this.namespace.flags[this.name] = true;
            this.namespace.emit.apply(this.namespace, arguments) } })("undefined" != typeof io ? io : module.exports, "undefined" != typeof io ? io : module.parent.exports);
    (function (exports, io, global) { exports.websocket = WS;

        function WS(socket) { io.Transport.apply(this, arguments) } io.util.inherit(WS, io.Transport);
        WS.prototype.name = "websocket";
        WS.prototype.open = function () { var query = io.util.query(this.socket.options.query),
                self = this,
                Socket; if (!Socket) { Socket = global.MozWebSocket || global.WebSocket } this.websocket = new Socket(serverString);
            this.websocket.onopen = function () { self.onOpen();
                self.socket.setBuffer(false) };
            this.websocket.onmessage = function (ev) { self.onData(ev.data) };
            this.websocket.onclose = function () { self.onClose();
                self.socket.setBuffer(true) };
            this.websocket.onerror = function (e) { self.onError(e) }; return this }; if (io.util.ua.iDevice) { WS.prototype.send = function (data) { var self = this;
                setTimeout(function () { self.websocket.send(data) }, 0); return this } } else { WS.prototype.send = function (data) { this.websocket.send(data); return this } } WS.prototype.payload = function (arr) { for (var i = 0, l = arr.length; i < l; i++) { this.packet(arr[i]) } return this };
        WS.prototype.close = function () { this.websocket.close(); return this };
        WS.prototype.onError = function (e) { this.socket.onError(e) };
        WS.prototype.scheme = function () { return this.socket.options.secure ? "wss" : "ws" };
        WS.check = function () { return ("WebSocket" in global && !("__addTask" in WebSocket)) || "MozWebSocket" in global };
        WS.xdomainCheck = function () { return true };
        io.transports.push("websocket") })("undefined" != typeof io ? io.Transport : module.exports, "undefined" != typeof io ? io : module.parent.exports, this);
    (function (exports, io) { exports.flashsocket = Flashsocket;

        function Flashsocket() { io.Transport.websocket.apply(this, arguments) } io.util.inherit(Flashsocket, io.Transport.websocket);
        Flashsocket.prototype.name = "flashsocket";
        Flashsocket.prototype.open = function () { var self = this,
                args = arguments;
            WebSocket.__addTask(function () { io.Transport.websocket.prototype.open.apply(self, args) }); return this };
        Flashsocket.prototype.send = function () { var self = this,
                args = arguments;
            WebSocket.__addTask(function () { io.Transport.websocket.prototype.send.apply(self, args) }); return this };
        Flashsocket.prototype.close = function () { WebSocket.__tasks.length = 0;
            io.Transport.websocket.prototype.close.call(this); return this };
        Flashsocket.prototype.ready = function (socket, fn) {
            function init() { var options = socket.options,
                    port = options["flash policy port"],
                    path = ["http" + (options.secure ? "s" : "") + ":/", options.host + ":" + options.port, options.resource, "static/flashsocket", "WebSocketMain" + (socket.isXDomain() ? "Insecure" : "") + ".swf"]; if (!Flashsocket.loaded) { if (typeof WEB_SOCKET_SWF_LOCATION === "undefined") { WEB_SOCKET_SWF_LOCATION = path.join("/") } if (port !== 843) { WebSocket.loadFlashPolicyFile("xmlsocket://" + options.host + ":" + port) } WebSocket.__initialize();
                    Flashsocket.loaded = true } fn.call(self) } var self = this; if (document.body) { return init() } io.util.load(init) };
        Flashsocket.check = function () { if (typeof WebSocket == "undefined" || !("__initialize" in WebSocket) || !swfobject) { return false } return swfobject.getFlashPlayerVersion().major >= 10 };
        Flashsocket.xdomainCheck = function () { return true }; if (typeof window != "undefined") { WEB_SOCKET_DISABLE_AUTO_INITIALIZATION = true } io.transports.push("flashsocket") })("undefined" != typeof io ? io.Transport : module.exports, "undefined" != typeof io ? io : module.parent.exports); if ("undefined" != typeof window) { var swfobject = function () { var D = "undefined",
                r = "object",
                S = "Shockwave Flash",
                W = "ShockwaveFlash.ShockwaveFlash",
                q = "application/x-shockwave-flash",
                R = "SWFObjectExprInst",
                x = "onreadystatechange",
                O = window,
                j = document,
                t = navigator,
                T = false,
                U = [h],
                o = [],
                N = [],
                I = [],
                l, Q, E, B, J = false,
                a = false,
                n, G, m = true,
                M = function () { var aa = typeof j.getElementById != D && typeof j.getElementsByTagName != D && typeof j.createElement != D,
                        ah = t.userAgent.toLowerCase(),
                        Y = t.platform.toLowerCase(),
                        ae = Y ? /win/.test(Y) : /win/.test(ah),
                        ac = Y ? /mac/.test(Y) : /mac/.test(ah),
                        af = /webkit/.test(ah) ? parseFloat(ah.replace(/^.*webkit\/(\d+(\.\d+)?).*$/, "$1")) : false,
                        X = !+"\v1",
                        ag = [0, 0, 0],
                        ab = null; if (typeof t.plugins != D && typeof t.plugins[S] == r) { ab = t.plugins[S].description; if (ab && !(typeof t.mimeTypes != D && t.mimeTypes[q] && !t.mimeTypes[q].enabledPlugin)) { T = true;
                            X = false;
                            ab = ab.replace(/^.*\s+(\S+\s+\S+$)/, "$1");
                            ag[0] = parseInt(ab.replace(/^(.*)\..*$/, "$1"), 10);
                            ag[1] = parseInt(ab.replace(/^.*\.(.*)\s.*$/, "$1"), 10);
                            ag[2] = /[a-zA-Z]/.test(ab) ? parseInt(ab.replace(/^.*[a-zA-Z]+(.*)$/, "$1"), 10) : 0 } } else { if (typeof O[(["Active"].concat("Object").join("X"))] != D) { try { var ad = new window[(["Active"].concat("Object").join("X"))](W); if (ad) { ab = ad.GetVariable("$version"); if (ab) { X = true;
                                        ab = ab.split(" ")[1].split(",");
                                        ag = [parseInt(ab[0], 10), parseInt(ab[1], 10), parseInt(ab[2], 10)] } } } catch (Z) {} } } return { w3: aa, pv: ag, wk: af, ie: X, win: ae, mac: ac } }(),
                k = function () { if (!M.w3) { return } if ((typeof j.readyState != D && j.readyState == "complete") || (typeof j.readyState == D && (j.getElementsByTagName("body")[0] || j.body))) { f() } if (!J) { if (typeof j.addEventListener != D) { j.addEventListener("DOMContentLoaded", f, false) } if (M.ie && M.win) { j.attachEvent(x, function () { if (j.readyState == "complete") { j.detachEvent(x, arguments.callee);
                                    f() } }); if (O == top) {
                                (function () { if (J) { return } try { j.documentElement.doScroll("left") } catch (X) { setTimeout(arguments.callee, 0); return } f() })() } } if (M.wk) {
                            (function () { if (J) { return } if (!/loaded|complete/.test(j.readyState)) { setTimeout(arguments.callee, 0); return } f() })() } s(f) } }();

            function f() { if (J) { return } try { var Z = j.getElementsByTagName("body")[0].appendChild(C("span"));
                    Z.parentNode.removeChild(Z) } catch (aa) { return } J = true; var X = U.length; for (var Y = 0; Y < X; Y++) { U[Y]() } }

            function K(X) { if (J) { X() } else { U[U.length] = X } }

            function s(Y) { if (typeof O.addEventListener != D) { O.addEventListener("load", Y, false) } else { if (typeof j.addEventListener != D) { j.addEventListener("load", Y, false) } else { if (typeof O.attachEvent != D) { i(O, "onload", Y) } else { if (typeof O.onload == "function") { var X = O.onload;
                                O.onload = function () { X();
                                    Y() } } else { O.onload = Y } } } } }

            function h() { if (T) { V() } else { H() } }

            function V() { var X = j.getElementsByTagName("body")[0]; var aa = C(r);
                aa.setAttribute("type", q); var Z = X.appendChild(aa); if (Z) { var Y = 0;
                    (function () { if (typeof Z.GetVariable != D) { var ab = Z.GetVariable("$version"); if (ab) { ab = ab.split(" ")[1].split(",");
                                M.pv = [parseInt(ab[0], 10), parseInt(ab[1], 10), parseInt(ab[2], 10)] } } else { if (Y < 10) { Y++;
                                setTimeout(arguments.callee, 10); return } } X.removeChild(aa);
                        Z = null;
                        H() })() } else { H() } }

            function H() { var ag = o.length; if (ag > 0) { for (var af = 0; af < ag; af++) { var Y = o[af].id; var ab = o[af].callbackFn; var aa = { success: false, id: Y }; if (M.pv[0] > 0) { var ae = c(Y); if (ae) { if (F(o[af].swfVersion) && !(M.wk && M.wk < 312)) { w(Y, true); if (ab) { aa.success = true;
                                        aa.ref = z(Y);
                                        ab(aa) } } else { if (o[af].expressInstall && A()) { var ai = {};
                                        ai.data = o[af].expressInstall;
                                        ai.width = ae.getAttribute("width") || "0";
                                        ai.height = ae.getAttribute("height") || "0"; if (ae.getAttribute("class")) { ai.styleclass = ae.getAttribute("class") } if (ae.getAttribute("align")) { ai.align = ae.getAttribute("align") } var ah = {}; var X = ae.getElementsByTagName("param"); var ac = X.length; for (var ad = 0; ad < ac; ad++) { if (X[ad].getAttribute("name").toLowerCase() != "movie") { ah[X[ad].getAttribute("name")] = X[ad].getAttribute("value") } } P(ai, ah, Y, ab) } else { p(ae); if (ab) { ab(aa) } } } } } else { w(Y, true); if (ab) { var Z = z(Y); if (Z && typeof Z.SetVariable != D) { aa.success = true;
                                    aa.ref = Z } ab(aa) } } } } }

            function z(aa) { var X = null; var Y = c(aa); if (Y && Y.nodeName == "OBJECT") { if (typeof Y.SetVariable != D) { X = Y } else { var Z = Y.getElementsByTagName(r)[0]; if (Z) { X = Z } } } return X }

            function A() { return !a && F("6.0.65") && (M.win || M.mac) && !(M.wk && M.wk < 312) }

            function P(aa, ab, X, Z) { a = true;
                E = Z || null;
                B = { success: false, id: X }; var ae = c(X); if (ae) { if (ae.nodeName == "OBJECT") { l = g(ae);
                        Q = null } else { l = ae;
                        Q = X } aa.id = R; if (typeof aa.width == D || (!/%$/.test(aa.width) && parseInt(aa.width, 10) < 310)) { aa.width = "310" } if (typeof aa.height == D || (!/%$/.test(aa.height) && parseInt(aa.height, 10) < 137)) { aa.height = "137" } j.title = j.title.slice(0, 47) + " - Flash Player Installation"; var ad = M.ie && M.win ? (["Active"].concat("").join("X")) : "PlugIn",
                        ac = "MMredirectURL=" + O.location.toString().replace(/&/g, "%26") + "&MMplayerType=" + ad + "&MMdoctitle=" + j.title; if (typeof ab.flashvars != D) { ab.flashvars += "&" + ac } else { ab.flashvars = ac } if (M.ie && M.win && ae.readyState != 4) { var Y = C("div");
                        X += "SWFObjectNew";
                        Y.setAttribute("id", X);
                        ae.parentNode.insertBefore(Y, ae);
                        ae.style.display = "none";
                        (function () { if (ae.readyState == 4) { ae.parentNode.removeChild(ae) } else { setTimeout(arguments.callee, 10) } })() } u(aa, ab, X) } }

            function p(Y) { if (M.ie && M.win && Y.readyState != 4) { var X = C("div");
                    Y.parentNode.insertBefore(X, Y);
                    X.parentNode.replaceChild(g(Y), X);
                    Y.style.display = "none";
                    (function () { if (Y.readyState == 4) { Y.parentNode.removeChild(Y) } else { setTimeout(arguments.callee, 10) } })() } else { Y.parentNode.replaceChild(g(Y), Y) } }

            function g(ab) { var aa = C("div"); if (M.win && M.ie) { aa.innerHTML = ab.innerHTML } else { var Y = ab.getElementsByTagName(r)[0]; if (Y) { var ad = Y.childNodes; if (ad) { var X = ad.length; for (var Z = 0; Z < X; Z++) { if (!(ad[Z].nodeType == 1 && ad[Z].nodeName == "PARAM") && !(ad[Z].nodeType == 8)) { aa.appendChild(ad[Z].cloneNode(true)) } } } } } return aa }

            function u(ai, ag, Y) { var X, aa = c(Y); if (M.wk && M.wk < 312) { return X } if (aa) { if (typeof ai.id == D) { ai.id = Y } if (M.ie && M.win) { var ah = ""; for (var ae in ai) { if (ai[ae] != Object.prototype[ae]) { if (ae.toLowerCase() == "data") { ag.movie = ai[ae] } else { if (ae.toLowerCase() == "styleclass") { ah += ' class="' + ai[ae] + '"' } else { if (ae.toLowerCase() != "classid") { ah += " " + ae + '="' + ai[ae] + '"' } } } } } var af = ""; for (var ad in ag) { if (ag[ad] != Object.prototype[ad]) { af += '<param name="' + ad + '" value="' + ag[ad] + '" />' } } aa.outerHTML = '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"' + ah + ">" + af + "</object>";
                        N[N.length] = ai.id;
                        X = c(ai.id) } else { var Z = C(r);
                        Z.setAttribute("type", q); for (var ac in ai) { if (ai[ac] != Object.prototype[ac]) { if (ac.toLowerCase() == "styleclass") { Z.setAttribute("class", ai[ac]) } else { if (ac.toLowerCase() != "classid") { Z.setAttribute(ac, ai[ac]) } } } } for (var ab in ag) { if (ag[ab] != Object.prototype[ab] && ab.toLowerCase() != "movie") { e(Z, ab, ag[ab]) } } aa.parentNode.replaceChild(Z, aa);
                        X = Z } } return X }

            function e(Z, X, Y) { var aa = C("param");
                aa.setAttribute("name", X);
                aa.setAttribute("value", Y);
                Z.appendChild(aa) }

            function y(Y) { var X = c(Y); if (X && X.nodeName == "OBJECT") { if (M.ie && M.win) { X.style.display = "none";
                        (function () { if (X.readyState == 4) { b(Y) } else { setTimeout(arguments.callee, 10) } })() } else { X.parentNode.removeChild(X) } } }

            function b(Z) { var Y = c(Z); if (Y) { for (var X in Y) { if (typeof Y[X] == "function") { Y[X] = null } } Y.parentNode.removeChild(Y) } }

            function c(Z) { var X = null; try { X = j.getElementById(Z) } catch (Y) {} return X }

            function C(X) { return j.createElement(X) }

            function i(Z, X, Y) { Z.attachEvent(X, Y);
                I[I.length] = [Z, X, Y] }

            function F(Z) { var Y = M.pv,
                    X = Z.split(".");
                X[0] = parseInt(X[0], 10);
                X[1] = parseInt(X[1], 10) || 0;
                X[2] = parseInt(X[2], 10) || 0; return (Y[0] > X[0] || (Y[0] == X[0] && Y[1] > X[1]) || (Y[0] == X[0] && Y[1] == X[1] && Y[2] >= X[2])) ? true : false }

            function v(ac, Y, ad, ab) { if (M.ie && M.mac) { return } var aa = j.getElementsByTagName("head")[0]; if (!aa) { return } var X = (ad && typeof ad == "string") ? ad : "screen"; if (ab) { n = null;
                    G = null } if (!n || G != X) { var Z = C("style");
                    Z.setAttribute("type", "text/css");
                    Z.setAttribute("media", X);
                    n = aa.appendChild(Z); if (M.ie && M.win && typeof j.styleSheets != D && j.styleSheets.length > 0) { n = j.styleSheets[j.styleSheets.length - 1] } G = X } if (M.ie && M.win) { if (n && typeof n.addRule == r) { n.addRule(ac, Y) } } else { if (n && typeof j.createTextNode != D) { n.appendChild(j.createTextNode(ac + " {" + Y + "}")) } } }

            function w(Z, X) { if (!m) { return } var Y = X ? "visible" : "hidden"; if (J && c(Z)) { c(Z).style.visibility = Y } else { v("#" + Z, "visibility:" + Y) } }

            function L(Y) { var Z = /[\\\"<>\.;]/; var X = Z.exec(Y) != null; return X && typeof encodeURIComponent != D ? encodeURIComponent(Y) : Y } var d = function () { if (M.ie && M.win) { window.attachEvent("onunload", function () { var ac = I.length; for (var ab = 0; ab < ac; ab++) { I[ab][0].detachEvent(I[ab][1], I[ab][2]) } var Z = N.length; for (var aa = 0; aa < Z; aa++) { y(N[aa]) } for (var Y in M) { M[Y] = null } M = null; for (var X in swfobject) { swfobject[X] = null } swfobject = null }) } }(); return { registerObject: function (ab, X, aa, Z) { if (M.w3 && ab && X) { var Y = {};
                        Y.id = ab;
                        Y.swfVersion = X;
                        Y.expressInstall = aa;
                        Y.callbackFn = Z;
                        o[o.length] = Y;
                        w(ab, false) } else { if (Z) { Z({ success: false, id: ab }) } } }, getObjectById: function (X) { if (M.w3) { return z(X) } }, embedSWF: function (ab, ah, ae, ag, Y, aa, Z, ad, af, ac) { var X = { success: false, id: ah }; if (M.w3 && !(M.wk && M.wk < 312) && ab && ah && ae && ag && Y) { w(ah, false);
                        K(function () { ae += "";
                            ag += ""; var aj = {}; if (af && typeof af === r) { for (var al in af) { aj[al] = af[al] } } aj.data = ab;
                            aj.width = ae;
                            aj.height = ag; var am = {}; if (ad && typeof ad === r) { for (var ak in ad) { am[ak] = ad[ak] } } if (Z && typeof Z === r) { for (var ai in Z) { if (typeof am.flashvars != D) { am.flashvars += "&" + ai + "=" + Z[ai] } else { am.flashvars = ai + "=" + Z[ai] } } } if (F(Y)) { var an = u(aj, am, ah); if (aj.id == ah) { w(ah, true) } X.success = true;
                                X.ref = an } else { if (aa && A()) { aj.data = aa;
                                    P(aj, am, ah, ac); return } else { w(ah, true) } } if (ac) { ac(X) } }) } else { if (ac) { ac(X) } } }, switchOffAutoHideShow: function () { m = false }, ua: M, getFlashPlayerVersion: function () { return { major: M.pv[0], minor: M.pv[1], release: M.pv[2] } }, hasFlashPlayerVersion: F, createSWF: function (Z, Y, X) { if (M.w3) { return u(Z, Y, X) } else { return undefined } }, showExpressInstall: function (Z, aa, X, Y) { if (M.w3 && A()) { P(Z, aa, X, Y) } }, removeSWF: function (X) { if (M.w3) { y(X) } }, createCSS: function (aa, Z, Y, X) { if (M.w3) { v(aa, Z, Y, X) } }, addDomLoadEvent: K, addLoadEvent: s, getQueryParamValue: function (aa) { var Z = j.location.search || j.location.hash; if (Z) { if (/\?/.test(Z)) { Z = Z.split("?")[1] } if (aa == null) { return L(Z) } var Y = Z.split("&"); for (var X = 0; X < Y.length; X++) { if (Y[X].substring(0, Y[X].indexOf("=")) == aa) { return L(Y[X].substring((Y[X].indexOf("=") + 1))) } } } return "" }, expressInstallCallback: function () { if (a) { var X = c(R); if (X && l) { X.parentNode.replaceChild(l, X); if (Q) { w(Q, true); if (M.ie && M.win) { l.style.display = "block" } } if (E) { E(B) } } a = false } } } }() }(function () { if ("undefined" == typeof window || window.WebSocket) { return } var console = window.console; if (!console || !console.log || !console.error) { console = { log: function () {}, error: function () {} } } if (!swfobject.hasFlashPlayerVersion("10.0.0")) { console.error("Flash Player >= 10.0.0 is required."); return } if (location.protocol == "file:") { console.error("WARNING: web-socket-js doesn't work in file:///... URL unless you set Flash Security Settings properly. Open the page via Web server i.e. http://...") } WebSocket = function (url, protocols, proxyHost, proxyPort, headers) { var self = this;
            self.__id = WebSocket.__nextId++;
            WebSocket.__instances[self.__id] = self;
            self.readyState = WebSocket.CONNECTING;
            self.bufferedAmount = 0;
            self.__events = {}; if (!protocols) { protocols = [] } else { if (typeof protocols == "string") { protocols = [protocols] } } setTimeout(function () { WebSocket.__addTask(function () { WebSocket.__flash.create(self.__id, url, protocols, proxyHost || null, proxyPort || 0, headers || null) }) }, 0) };
        WebSocket.prototype.send = function (data) { if (this.readyState == WebSocket.CONNECTING) { throw "INVALID_STATE_ERR: Web Socket connection has not been established" } var result = WebSocket.__flash.send(this.__id, encodeURIComponent(data)); if (result < 0) { return true } else { this.bufferedAmount += result; return false } };
        WebSocket.prototype.close = function () { if (this.readyState == WebSocket.CLOSED || this.readyState == WebSocket.CLOSING) { return } this.readyState = WebSocket.CLOSING;
            WebSocket.__flash.close(this.__id) };
        WebSocket.prototype.addEventListener = function (type, listener, useCapture) { if (!(type in this.__events)) { this.__events[type] = [] } this.__events[type].push(listener) };
        WebSocket.prototype.removeEventListener = function (type, listener, useCapture) { if (!(type in this.__events)) { return } var events = this.__events[type]; for (var i = events.length - 1; i >= 0; --i) { if (events[i] === listener) { events.splice(i, 1); break } } };
        WebSocket.prototype.dispatchEvent = function (event) { var events = this.__events[event.type] || []; for (var i = 0; i < events.length; ++i) { events[i](event) } var handler = this["on" + event.type]; if (handler) { handler(event) } };
        WebSocket.prototype.__handleEvent = function (flashEvent) { if ("readyState" in flashEvent) { this.readyState = flashEvent.readyState } if ("protocol" in flashEvent) { this.protocol = flashEvent.protocol } var jsEvent; if (flashEvent.type == "open" || flashEvent.type == "error") { jsEvent = this.__createSimpleEvent(flashEvent.type) } else { if (flashEvent.type == "close") { jsEvent = this.__createSimpleEvent("close") } else { if (flashEvent.type == "message") {console.log("PM@",flashEvent.message); var data = decodeURIComponent(flashEvent.message);
                        jsEvent = this.__createMessageEvent("message", data) } else { throw "unknown event type: " + flashEvent.type } } } this.dispatchEvent(jsEvent) };
        WebSocket.prototype.__createSimpleEvent = function (type) { if (document.createEvent && window.Event) { var event = document.createEvent("Event");
                event.initEvent(type, false, false); return event } else { return { type: type, bubbles: false, cancelable: false } } };
        WebSocket.prototype.__createMessageEvent = function (type, data) { if (document.createEvent && window.MessageEvent && !window.opera) { var event = document.createEvent("MessageEvent");
                event.initMessageEvent("message", false, false, data, null, null, window, null); return event } else { return { type: type, data: data, bubbles: false, cancelable: false } } };
        WebSocket.CONNECTING = 0;
        WebSocket.OPEN = 1;
        WebSocket.CLOSING = 2;
        WebSocket.CLOSED = 3;
        WebSocket.__flash = null;
        WebSocket.__instances = {};
        WebSocket.__tasks = [];
        WebSocket.__nextId = 0;
        WebSocket.loadFlashPolicyFile = function (url) { WebSocket.__addTask(function () { WebSocket.__flash.loadManualPolicyFile(url) }) };
        WebSocket.__initialize = function () { if (WebSocket.__flash) { return } if (WebSocket.__swfLocation) { window.WEB_SOCKET_SWF_LOCATION = WebSocket.__swfLocation } if (!window.WEB_SOCKET_SWF_LOCATION) { console.error("[WebSocket] set WEB_SOCKET_SWF_LOCATION to location of WebSocketMain.swf"); return } var container = document.createElement("div");
            container.id = "webSocketContainer";
            container.style.position = "absolute"; if (WebSocket.__isFlashLite()) { container.style.left = "0px";
                container.style.top = "0px" } else { container.style.left = "-100px";
                container.style.top = "-100px" } var holder = document.createElement("div");
            holder.id = "webSocketFlash";
            container.appendChild(holder);
            document.body.appendChild(container);
            swfobject.embedSWF(WEB_SOCKET_SWF_LOCATION, "webSocketFlash", "1", "1", "10.0.0", null, null, { hasPriority: true, swliveconnect: true, allowScriptAccess: "always" }, null, function (e) { if (!e.success) { console.error("[WebSocket] swfobject.embedSWF failed") } }) };
        WebSocket.__onFlashInitialized = function () { setTimeout(function () { WebSocket.__flash = document.getElementById("webSocketFlash");
                WebSocket.__flash.setCallerUrl(location.href);
                WebSocket.__flash.setDebug(!!window.WEB_SOCKET_DEBUG); for (var i = 0; i < WebSocket.__tasks.length; ++i) { WebSocket.__tasks[i]() } WebSocket.__tasks = [] }, 0) };
        WebSocket.__onFlashEvent = function () { setTimeout(function () { try { var events = WebSocket.__flash.receiveEvents(); for (var i = 0; i < events.length; ++i) { WebSocket.__instances[events[i].webSocketId].__handleEvent(events[i]) } } catch (e) { console.error(e) } }, 0); return true };
        WebSocket.__log = function (message) { console.log(decodeURIComponent(message)) };
        WebSocket.__error = function (message) { console.error(decodeURIComponent(message)) };
        WebSocket.__addTask = function (task) { if (WebSocket.__flash) { task() } else { WebSocket.__tasks.push(task) } };
        WebSocket.__isFlashLite = function () { if (!window.navigator || !window.navigator.mimeTypes) { return false } var mimeType = window.navigator.mimeTypes["application/x-shockwave-flash"]; if (!mimeType || !mimeType.enabledPlugin || !mimeType.enabledPlugin.filename) { return false } return mimeType.enabledPlugin.filename.match(/flashlite/i) ? true : false }; if (!window.WEB_SOCKET_DISABLE_AUTO_INITIALIZATION) { if (window.addEventListener) { window.addEventListener("load", function () { WebSocket.__initialize() }, false) } else { window.attachEvent("onload", function () { WebSocket.__initialize() }) } } })();
    (function (exports, io, global) { exports.XHR = XHR;

        function XHR(socket) { if (!socket) { return } io.Transport.apply(this, arguments);
            this.sendBuffer = [] } io.util.inherit(XHR, io.Transport);
        XHR.prototype.open = function () { this.socket.setBuffer(false);
            this.onOpen();
            this.get();
            this.setCloseTimeout(); return this };
        XHR.prototype.payload = function (payload) { var msgs = []; for (var i = 0, l = payload.length; i < l; i++) { msgs.push(io.parser.encodePacket(payload[i])) } this.send(io.parser.encodePayload(msgs)) };
        XHR.prototype.send = function (data) { this.post(data); return this };

        function empty() {} XHR.prototype.post = function (data) { var self = this;
            this.socket.setBuffer(true);

            function stateChange() { if (this.readyState == 4) { this.onreadystatechange = empty;
                    self.posting = false; if (this.status == 200) { self.socket.setBuffer(false) } else { self.onClose() } } }

            function onload() { this.onload = empty;
                self.socket.setBuffer(false) } this.sendXHR = this.request("POST"); if (global.XDomainRequest && this.sendXHR instanceof XDomainRequest) { this.sendXHR.onload = this.sendXHR.onerror = onload } else { this.sendXHR.onreadystatechange = stateChange } this.sendXHR.send(data) };
        XHR.prototype.close = function () { this.onClose(); return this };
        XHR.prototype.request = function (method) { var req = io.util.request(this.socket.isXDomain()),
                query = io.util.query(this.socket.options.query, "t=" + +new Date);
            req.open(method || "GET", this.prepareUrl() + query, true); if (method == "POST") { try { if (req.setRequestHeader) { req.setRequestHeader("Content-type", "text/plain;charset=UTF-8") } else { req.contentType = "text/plain" } } catch (e) {} } return req };
        XHR.prototype.scheme = function () { return this.socket.options.secure ? "https" : "http" };
        XHR.check = function (socket, xdomain) { try { var request = io.util.request(xdomain),
                    usesXDomReq = (global.XDomainRequest && request instanceof XDomainRequest),
                    socketProtocol = (socket && socket.options && socket.options.secure ? "https:" : "http:"),
                    isXProtocol = (global.location && socketProtocol != global.location.protocol); if (request && !(usesXDomReq && isXProtocol)) { return true } } catch (e) {} return false };
        XHR.xdomainCheck = function (socket) { return XHR.check(socket, true) } })("undefined" != typeof io ? io.Transport : module.exports, "undefined" != typeof io ? io : module.parent.exports, this);
    (function (exports, io) { exports.htmlfile = HTMLFile;

        function HTMLFile(socket) { io.Transport.XHR.apply(this, arguments) } io.util.inherit(HTMLFile, io.Transport.XHR);
        HTMLFile.prototype.name = "htmlfile";
        HTMLFile.prototype.get = function () { this.doc = new window[(["Active"].concat("Object").join("X"))]("htmlfile");
            this.doc.open();
            this.doc.write("<html></html>");
            this.doc.close();
            this.doc.parentWindow.s = this; var iframeC = this.doc.createElement("div");
            iframeC.className = "socketio";
            this.doc.body.appendChild(iframeC);
            this.iframe = this.doc.createElement("iframe");
            iframeC.appendChild(this.iframe); var self = this,
                query = io.util.query(this.socket.options.query, "t=" + +new Date);
            this.iframe.src = this.prepareUrl() + query;
            io.util.on(window, "unload", function () { self.destroy() }) };
        HTMLFile.prototype._ = function (data, doc) { data = data.replace(/\\\//g, "/");
            this.onData(data); try { var script = doc.getElementsByTagName("script")[0];
                script.parentNode.removeChild(script) } catch (e) {} };
        HTMLFile.prototype.destroy = function () { if (this.iframe) { try { this.iframe.src = "about:blank" } catch (e) {} this.doc = null;
                this.iframe.parentNode.removeChild(this.iframe);
                this.iframe = null;
                CollectGarbage() } };
        HTMLFile.prototype.close = function () { this.destroy(); return io.Transport.XHR.prototype.close.call(this) };
        HTMLFile.check = function (socket) { if (typeof window != "undefined" && (["Active"].concat("Object").join("X")) in window) { try { var a = new window[(["Active"].concat("Object").join("X"))]("htmlfile"); return a && io.Transport.XHR.check(socket) } catch (e) {} } return false };
        HTMLFile.xdomainCheck = function () { return false };
        io.transports.push("htmlfile") })("undefined" != typeof io ? io.Transport : module.exports, "undefined" != typeof io ? io : module.parent.exports);
    (function (exports, io, global) { exports["xhr-polling"] = XHRPolling;

        function XHRPolling() { io.Transport.XHR.apply(this, arguments) } io.util.inherit(XHRPolling, io.Transport.XHR);
        io.util.merge(XHRPolling, io.Transport.XHR);
        XHRPolling.prototype.name = "xhr-polling";
        XHRPolling.prototype.heartbeats = function () { return false };
        XHRPolling.prototype.open = function () { var self = this;
            io.Transport.XHR.prototype.open.call(self); return false };

        function empty() {} XHRPolling.prototype.get = function () { if (!this.isOpen) { return } var self = this;

            function stateChange() { if (this.readyState == 4) { this.onreadystatechange = empty; if (this.status == 200) { self.onData(this.responseText);
                        self.get() } else { self.onClose() } } }

            function onload() { this.onload = empty;
                this.onerror = empty;
                self.retryCounter = 1;
                self.onData(this.responseText);
                self.get() }

            function onerror() { self.retryCounter++; if (!self.retryCounter || self.retryCounter > 3) { self.onClose() } else { self.get() } } this.xhr = this.request(); if (global.XDomainRequest && this.xhr instanceof XDomainRequest) { this.xhr.onload = onload;
                this.xhr.onerror = onerror } else { this.xhr.onreadystatechange = stateChange } this.xhr.send(null) };
        XHRPolling.prototype.onClose = function () { io.Transport.XHR.prototype.onClose.call(this); if (this.xhr) { this.xhr.onreadystatechange = this.xhr.onload = this.xhr.onerror = empty; try { this.xhr.abort() } catch (e) {} this.xhr = null } };
        XHRPolling.prototype.ready = function (socket, fn) { var self = this;
            io.util.defer(function () { fn.call(self) }) };
        io.transports.push("xhr-polling") })("undefined" != typeof io ? io.Transport : module.exports, "undefined" != typeof io ? io : module.parent.exports, this);
    (function (exports, io, global) { var indicator = global.document && "MozAppearance" in global.document.documentElement.style;
        exports["jsonp-polling"] = JSONPPolling;

        function JSONPPolling(socket) { io.Transport["xhr-polling"].apply(this, arguments);
            this.index = io.j.length; var self = this;
            io.j.push(function (msg) { self._(msg) }) } io.util.inherit(JSONPPolling, io.Transport["xhr-polling"]);
        JSONPPolling.prototype.name = "jsonp-polling";
        JSONPPolling.prototype.post = function (data) { var self = this,
                query = io.util.query(this.socket.options.query, "t=" + (+new Date) + "&i=" + this.index); if (!this.form) { var form = document.createElement("form"),
                    area = document.createElement("textarea"),
                    id = this.iframeId = "socketio_iframe_" + this.index,
                    iframe;
                form.className = "socketio";
                form.style.position = "absolute";
                form.style.top = "0px";
                form.style.left = "0px";
                form.style.display = "none";
                form.target = id;
                form.method = "POST";
                form.setAttribute("accept-charset", "utf-8");
                area.name = "d";
                form.appendChild(area);
                document.body.appendChild(form);
                this.form = form;
                this.area = area } this.form.action = this.prepareUrl() + query;

            function complete() { initIframe();
                self.socket.setBuffer(false) }

            function initIframe() { if (self.iframe) { self.form.removeChild(self.iframe) } try { iframe = document.createElement('<iframe name="' + self.iframeId + '">') } catch (e) { iframe = document.createElement("iframe");
                    iframe.name = self.iframeId } iframe.id = self.iframeId;
                self.form.appendChild(iframe);
                self.iframe = iframe } initIframe();
            this.area.value = io.JSON.stringify(data); try { this.form.submit() } catch (e) {} if (this.iframe.attachEvent) { iframe.onreadystatechange = function () { if (self.iframe.readyState == "complete") { complete() } } } else { this.iframe.onload = complete } this.socket.setBuffer(true) };
        JSONPPolling.prototype.get = function () { var self = this,
                script = document.createElement("script"),
                query = io.util.query(this.socket.options.query, "t=" + (+new Date) + "&i=" + this.index); if (this.script) { this.script.parentNode.removeChild(this.script);
                this.script = null } script.async = true;
            script.src = this.prepareUrl() + query;
            script.onerror = function () { self.onClose() }; var insertAt = document.getElementsByTagName("script")[0];
            insertAt.parentNode.insertBefore(script, insertAt);
            this.script = script; if (indicator) { setTimeout(function () { var iframe = document.createElement("iframe");
                    document.body.appendChild(iframe);
                    document.body.removeChild(iframe) }, 100) } };
        JSONPPolling.prototype._ = function (msg) { this.onData(msg); if (this.isOpen) { this.get() } return this };
        JSONPPolling.prototype.ready = function (socket, fn) { var self = this; if (!indicator) { return fn.call(this) } io.util.load(function () { fn.call(self) }) };
        JSONPPolling.check = function () { return "document" in global };
        JSONPPolling.xdomainCheck = function () { return true };
        io.transports.push("jsonp-polling") })("undefined" != typeof io ? io.Transport : module.exports, "undefined" != typeof io ? io : module.parent.exports, this); if (typeof define === "function" && define.amd) { define([], function () { return io }) } })();
(function (f) {
    function b(k, l) { var j; for (var h = 0; h < k.length; h++) { if ((j = a.exec(k[h])) != null) { k[h] = l[Number(j[1])] } else { k[h] = k[h].replace(/\0/g, ",") } } return k }

    function c(j, h) { h = Number(h); if (isNaN(h)) { return j } j = "" + j; var k = Math.abs(h) - j.length; if (k <= 0) { return j } while (d.length < k) { d += d } return h < 0 ? j + d.substring(0, k) : d.substring(0, k) + j } f.format = function () { if (arguments.length == 0) { return this } var j = /\{(\d+)(?:,([-+]?\d+))?(?:\:([^(^}]+)(?:\(((?:\\\)|[^)])+)\)){0,1}){0,1}\}/g; var h = arguments; return this.replace(j, function (n, m, p, l, k) { n = h[Number(m)];
            l = g[l]; return c(l == null ? n : l(n, b((k || "").replace(/\\\)/g, ")").replace(/\\,/g, "\0").split(","), h)), p) }) };
    String.Format = function (e) { return arguments.length <= 1 ? e : f.format.apply(e, Array.prototype.slice.call(arguments, 1)) };
    f.format.add = function (k, h, j) { if (g[k] != null && j != true) { throw "Format " + k + " exist, use replace=true for replace" } g[k] = h };
    String.Format.init = f.format.init = function (k) { var h; for (var j in k) { h = g[j]; if (h != null && h.init != null) { h.init(k[j]) } } };
    f.format.get = function (h) { return g[h] }; var a = /^\{(\d+)\}$/; var g = {}; var d = "    ";
    f.format.add("arr", function (j, l) { if (j == null) { return "null" } var h = []; var k = l.shift() || ""; var m = g[l.shift()]; if (m == null) { h = j } else { for (var e = 0; e < j.length; e++) { h.push(m(j[e], l)) } } return h.join(k) }) })(String.prototype);
(function (g) {
    function l(a) { b.dseparator = a.dseparator || ".";
        b.nan = a.nan || "NaN" }

    function c(m, a, o) { if (isNaN(o = Number(o == "" ? undefined : o))) { return a == null || a == "" ? "" : (m || "") + a } if ((a = a || "").length >= o) { return m == null ? a : m + a.substr(0, o) } return m == null ? j(o - a.length) + a : m + a + j(o - a.length) }

    function j(a) { while (d.length < a) { d += d } return d.substring(0, a) }

    function h(u, p, v, o, q, m) { var r; if (o > 0) { r = c("", v, o);
            p += r;
            v = v.substr(r.length) } else { if (o < 0) { r = p.length - -o;
                v = (r > 0 ? p.substring(r, p.length) : j(-r) + p) + v;
                p = r > 0 ? p.substring(0, -o - 1) : "0" } } return "".concat(u, c(null, p, q), c(b.dseparator, v, m)) } g.add("n", function (m, a) { if ((m = k.exec(m)) == null) { return b.nan } var e = Number(m[4]); return isNaN(e) ? "".concat(m[1], c(null, m[2], a[0]), c(b.dseparator, m[3], a[1])) : h(m[1], m[2], m[3], e, a[0], a[1]) });
    g.add("nb", function (e, m) { e = Number(e); if (isNaN(e)) { return b.nan } var a = Number(m[0]); return c(null, e.toString(isNaN(a) ? 16 : a), Number(m[1])) });
    g.add("nf", function (m, a) { m = Number(m); if (isNaN(m)) { return b.nan } var o = f[a[0].toLowerCase()]; return o == null ? a[0] + "?" : o(m, a) });
    g.get("nf").add = function (m, a) { f[m.toLowerCase()] = a };
    g.get("n").init = l; var k = /^([+-]?)(\d+)(?:\.(\d+))?(?:\s*e([+-]\d+))?$/i; var d = "0000000000"; var b = { dseparator: ".", nan: "NaN" }; var f = { en: function (m, a) { return a[m == 1 ? 1 : 2] }, ru: function (o, a) { var p = o % 10; var m = o % 100; return a[p == 1 && m != 11 ? 1 : p >= 2 && p <= 4 && (m < 10 || m >= 20) ? 2 : 3] } } })(String.prototype.format);
var pta = (function () {
    (function (q, r) { var p = function (u) { if ("object" !== typeof u.document) { throw Error("Cookies.js requires a `window` with a `document` object") } var t = function (v, w, x) { return 1 === arguments.length ? t.get(v) : t.set(v, w, x) };
                t._document = u.document;
                t._cacheKeyPrefix = "cookey.";
                t._maxExpireDate = new Date("Fri, 31 Dec 9999 23:59:59 UTC");
                t.defaults = { path: "/", secure: !1 };
                t.get = function (v) { t._cachedDocumentCookie !== t._document.cookie && t._renewCache(); return t._cache[t._cacheKeyPrefix + v] };
                t.set = function (v, w, x) { x = t._getExtendedOptions(x);
                    x.expires = t._getExpiresDate(w === r ? -1 : x.expires);
                    t._document.cookie = t._generateCookieString(v, w, x); return t };
                t.expire = function (v, w) { return t.set(v, r, w) };
                t._getExtendedOptions = function (v) { return { path: v && v.path || t.defaults.path, domain: v && v.domain || t.defaults.domain, expires: v && v.expires || t.defaults.expires, secure: v && v.secure !== r ? v.secure : t.defaults.secure } };
                t._isValidDate = function (v) { return "[object Date]" === Object.prototype.toString.call(v) && !isNaN(v.getTime()) };
                t._getExpiresDate = function (v, w) { w = w || new Date; "number" === typeof v ? v = Infinity === v ? t._maxExpireDate : new Date(w.getTime() + 1000 * v) : "string" === typeof v && (v = new Date(v)); if (v && !t._isValidDate(v)) { throw Error("`expires` parameter cannot be converted to a valid Date instance") } return v };
                t._generateCookieString = function (w, v, x) { w = w.replace(/[^#$&+\^`|]/g, encodeURIComponent);
                    w = w.replace(/\(/g, "%28").replace(/\)/g, "%29");
                    v = (v + "").replace(/[^!#$&-+\--:<-\[\]-~]/g, encodeURIComponent);
                    x = x || {};
                    w = w + "=" + v + (x.path ? ";path=" + x.path : "");
                    w += x.domain ? ";domain=" + x.domain : "";
                    w += x.expires ? ";expires=" + x.expires.toUTCString() : ""; return w += x.secure ? ";secure" : "" };
                t._getCacheFromString = function (v) { var x = {};
                    v = v ? v.split("; ") : []; for (var y = 0; y < v.length; y++) { var w = t._getKeyValuePairFromCookieString(v[y]);
                        x[t._cacheKeyPrefix + w.key] === r && (x[t._cacheKeyPrefix + w.key] = w.value) } return x };
                t._getKeyValuePairFromCookieString = function (w) { var v = w.indexOf("="),
                        v = 0 > v ? w.length : v; return { key: decodeURIComponent(w.substr(0, v)), value: decodeURIComponent(w.substr(v + 1)) } };
                t._renewCache = function () { t._cache = t._getCacheFromString(t._document.cookie);
                    t._cachedDocumentCookie = t._document.cookie };
                t._areEnabled = function () { var v = "1" === t.set("cookies.js", 1).get("cookies.js");
                    t.expire("cookies.js"); return v };
                t.enabled = t._areEnabled(); return t },
            s = "object" === typeof q.document ? p(q) : p; "function" === typeof define && define.amd ? define(function () { return s }) : "object" === typeof exports ? ("object" === typeof module && "object" === typeof module.exports && (exports = module.exports = s), exports.Cookies = s) : q.Cookies = s })("undefined" === typeof window ? this : window); var g = {}; var a = ""; var j = ""; var k = ["user", "brand", "ctype", "cplat", "skin", "mode", "lang"]; var m = 60 * 60 * 24; var l = function (p) { if (pta.debug === true) { alert("pta runtime error: " + p) } }; var d = function (p, r) { try { if (j !== "") { j += "&" } j += p + "=" + r; return } catch (q) { l(q) } }; var h = function () { try { j = ""; for (var q in g) { if (!g.hasOwnProperty(q)) { continue } if (j !== "") { j += "&" } j += q + "=" + g[q] } return } catch (p) { l(p) } }; var e = function (p, r) { try { for (var q = 0; q < p.length; q++) { if (p[q] === r) { return true } } return false } catch (s) { l(s) } }; var c = function (r, q, t) { try { if ((parseInt(r) < 1) || (parseInt(r) > 5)) { l("set custom field error : <index>=" + r + " is not a valid parameter"); return false } if (typeof q !== "string") { l("set custom field error: <name>=" + q + " is not a valid parameter"); return false } if (typeof t !== "string") { l("set custom field error: <value>=" + t + " is not a valid parameter"); return false } var p = g["custname" + r]; var s = g["custval" + r];
            g["custname" + r] = q;
            g["custval" + r] = t;
            Cookies("custname" + r, q, { expires: m });
            Cookies("custval" + r, t, { expires: m }); if (typeof p === "undefined" || typeof s === "undefined") { d("custname" + r, q);
                d("custval" + r, t) } else { if ((q !== p) || (t !== s)) { h() } } return true } catch (u) { l(u) } }; var b = function (s, q) { try { s = s.toLowerCase(); if (!e(k, s)) { l("set field error: <field>=" + s + " is not a valid parameter"); return false } if (typeof q !== "string") { l("set field error: <value>=" + q + " is not a valid parameter"); return false } var p = g[s];
            g[s] = q;
            Cookies(s, q, { expires: m }); if (typeof p === "undefined") { d(s, q) } else { if (q !== p) { h() } } return true } catch (r) { l(r) } }; var f = function (q, u, p, s) { try { if (typeof g.user === "undefined") { l('send error: "user" field is mandatory. use "set" to add this field'); return false } if (typeof g.brand === "undefined") { l('send error: "brand" field is mandatory. use "set" to add this field'); return false } if (typeof g.ctype === "undefined") { l('send error: "ctype" field is mandatory. use "set" to add this field'); return false } if (typeof g.cplat === "undefined") { l('send error: "cplat" field is mandatory. use "set" to add this field'); return false } if (typeof g.mode === "undefined") { l('send error: "mode" field is mandatory. use "set" to add this field'); return false } if (typeof q !== "string") { l("send error: <category>=" + q + " is not a valid parameter"); return false } if (typeof u !== "string") { l("send error: <action>=" + u + " is not a valid parameter"); return false } if (typeof p !== "string") { l("send error: <label>=" + p + " is not a valid parameter"); return false } if ((s !== null) && (typeof s !== "undefined") && (typeof s !== "number")) { l("send error: <value>=" + s + " is not a valid parameter"); return false } var r = j + "&category=" + q + "&event=" + u + "&label=" + p + "&r=" + (new Date()).getTime(); if ((typeof s !== "undefined") && (s !== null)) { r += "&value=" + s } window.ptImg = new Image();
            if (window.ptaDebugOn || document.location.search.indexOf("ptaDebugOn") != -1) { console.log("$$$$ PTAnalytics: " + a + r) } return true } catch (t) { l(t) } }; var o = function () { try { for (var q = 0; q < arguments.length; q++) { var r = arguments[q]; var p = r[0]; if (p === "set") { var t = r[1]; if (t.toLowerCase() === "cust") { c(r[2], r[3], r[4]) } else { if (t.toLowerCase() === "statsurl") { a = r[2] } else { b(r[1], r[2]) } } } else { if (p === "send") { f(r[1], r[2], r[3], r[4]) } else { l("method <" + p + '> is not supported. use only "set" or "send"') } } } } catch (s) { l(s) } }; var n = function () { try { var q = ["custname1", "custval1", "custname2", "custval2", "custname3", "custval3", "custname4", "custval4", "custname5", "custval5", "user", "brand", "ctype", "cplat", "skin", "mode", "lang"]; for (var p = 0; p < q.length; p++) { var s = Cookies(q[p]); if (typeof s !== "undefined") { g[q[p]] = s } } h() } catch (r) { l(r) } }(); return o })();
/*! iScroll v5.1.3 ~ (c) 2008-2014 Matteo Spinelli ~ http://cubiq.org/license */
(function (f, a, e) { var h = f.requestAnimationFrame || f.webkitRequestAnimationFrame || f.mozRequestAnimationFrame || f.oRequestAnimationFrame || f.msRequestAnimationFrame || function (j) { f.setTimeout(j, 1000 / 60) }; var c = (function () { var n = {}; var o = a.createElement("div").style; var l = (function () { var s = ["t", "webkitT", "MozT", "msT", "OT"],
                q, r = 0,
                p = s.length; for (; r < p; r++) { q = s[r] + "ransform"; if (q in o) { return s[r].substr(0, s[r].length - 1) } } return false })();

        function m(p) { if (l === false) { return false } if (l === "") { return p } return l + p.charAt(0).toUpperCase() + p.substr(1) } n.getTime = Date.now || function j() { return new Date().getTime() };
        n.extend = function (r, q) { for (var p in q) { r[p] = q[p] } };
        n.addEvent = function (s, r, q, p) { s.addEventListener(r, q, !!p) };
        n.removeEvent = function (s, r, q, p) { s.removeEventListener(r, q, !!p) };
        n.prefixPointerEvent = function (p) { return f.MSPointerEvent ? "MSPointer" + p.charAt(9).toUpperCase() + p.substr(10) : p };
        n.momentum = function (v, r, s, p, w, x) { var q = v - r,
                t = e.abs(q) / s,
                y, u;
            x = x === undefined ? 0.0006 : x;
            y = v + (t * t) / (2 * x) * (q < 0 ? -1 : 1);
            u = t / x; if (y < p) { y = w ? p - (w / 2.5 * (t / 8)) : p;
                q = e.abs(y - v);
                u = q / t } else { if (y > 0) { y = w ? w / 2.5 * (t / 8) : 0;
                    q = e.abs(v) + y;
                    u = q / t } } return { destination: e.round(y), duration: u } }; var k = m("transform");
        n.extend(n, { hasTransform: k !== false, hasPerspective: m("perspective") in o, hasTouch: "ontouchstart" in f, hasPointer: f.PointerEvent || f.MSPointerEvent, hasTransition: m("transition") in o });
        n.isBadAndroid = /Android /.test(f.navigator.appVersion) && !(/Chrome\/\d/.test(f.navigator.appVersion));
        n.extend(n.style = {}, { transform: k, transitionTimingFunction: m("transitionTimingFunction"), transitionDuration: m("transitionDuration"), transitionDelay: m("transitionDelay"), transformOrigin: m("transformOrigin") });
        n.hasClass = function (q, r) { var p = new RegExp("(^|\\s)" + r + "(\\s|$)"); return p.test(q.className) };
        n.addClass = function (q, r) { if (n.hasClass(q, r)) { return } var p = q.className.split(" ");
            p.push(r);
            q.className = p.join(" ") };
        n.removeClass = function (q, r) { if (!n.hasClass(q, r)) { return } var p = new RegExp("(^|\\s)" + r + "(\\s|$)", "g");
            q.className = q.className.replace(p, " ") };
        n.offset = function (p) { var r = -p.offsetLeft,
                q = -p.offsetTop; while (p = p.offsetParent) { r -= p.offsetLeft;
                q -= p.offsetTop } return { left: r, top: q } };
        n.preventDefaultException = function (r, q) { for (var p in q) { if (q[p].test(r[p])) { return true } } return false };
        n.extend(n.eventType = {}, { touchstart: 1, touchmove: 1, touchend: 1, mousedown: 2, mousemove: 2, mouseup: 2, pointerdown: 3, pointermove: 3, pointerup: 3, MSPointerDown: 3, MSPointerMove: 3, MSPointerUp: 3 });
        n.extend(n.ease = {}, { quadratic: { style: "cubic-bezier(0.25, 0.46, 0.45, 0.94)", fn: function (p) { return p * (2 - p) } }, circular: { style: "cubic-bezier(0.1, 0.57, 0.1, 1)", fn: function (p) { return e.sqrt(1 - (--p * p)) } }, back: { style: "cubic-bezier(0.175, 0.885, 0.32, 1.275)", fn: function (q) { var p = 4; return (q = q - 1) * q * ((p + 1) * q + p) + 1 } }, bounce: { style: "", fn: function (p) { if ((p /= 1) < (1 / 2.75)) { return 7.5625 * p * p } else { if (p < (2 / 2.75)) { return 7.5625 * (p -= (1.5 / 2.75)) * p + 0.75 } else { if (p < (2.5 / 2.75)) { return 7.5625 * (p -= (2.25 / 2.75)) * p + 0.9375 } else { return 7.5625 * (p -= (2.625 / 2.75)) * p + 0.984375 } } } } }, elastic: { style: "", fn: function (p) { var q = 0.22,
                        r = 0.4; if (p === 0) { return 0 } if (p == 1) { return 1 } return (r * e.pow(2, -10 * p) * e.sin((p - q / 4) * (2 * e.PI) / q) + 1) } } });
        n.tap = function (r, p) { var q = a.createEvent("Event");
            q.initEvent(p, true, true);
            q.pageX = r.pageX;
            q.pageY = r.pageY;
            r.target.dispatchEvent(q) };
        n.click = function (r) { var q = r.target,
                p; if (!(/(SELECT|INPUT|TEXTAREA)/i).test(q.tagName)) { p = a.createEvent("MouseEvents");
                p.initMouseEvent("click", true, true, r.view, 1, q.screenX, q.screenY, q.clientX, q.clientY, r.ctrlKey, r.altKey, r.shiftKey, r.metaKey, 0, null);
                p._constructed = true;
                q.dispatchEvent(p) } }; return n })();

    function g(l, j) { this.wrapper = typeof l == "string" ? a.querySelector(l) : l;
        this.scroller = this.wrapper.children[0];
        this.scrollerStyle = this.scroller.style;
        this.options = { resizeScrollbars: true, mouseWheelSpeed: 20, snapThreshold: 0.334, startX: 0, startY: 0, scrollY: true, directionLockThreshold: 5, momentum: true, bounce: true, bounceTime: 600, bounceEasing: "", preventDefault: true, preventDefaultException: { tagName: /^(INPUT|TEXTAREA|BUTTON|SELECT)$/ }, HWCompositing: true, useTransition: true, useTransform: true }; for (var k in j) { this.options[k] = j[k] } this.translateZ = this.options.HWCompositing && c.hasPerspective ? " translateZ(0)" : "";
        this.options.useTransition = c.hasTransition && this.options.useTransition;
        this.options.useTransform = c.hasTransform && this.options.useTransform;
        this.options.eventPassthrough = this.options.eventPassthrough === true ? "vertical" : this.options.eventPassthrough;
        this.options.preventDefault = !this.options.eventPassthrough && this.options.preventDefault;
        this.options.scrollY = this.options.eventPassthrough == "vertical" ? false : this.options.scrollY;
        this.options.scrollX = this.options.eventPassthrough == "horizontal" ? false : this.options.scrollX;
        this.options.freeScroll = this.options.freeScroll && !this.options.eventPassthrough;
        this.options.directionLockThreshold = this.options.eventPassthrough ? 0 : this.options.directionLockThreshold;
        this.options.bounceEasing = typeof this.options.bounceEasing == "string" ? c.ease[this.options.bounceEasing] || c.ease.circular : this.options.bounceEasing;
        this.options.resizePolling = this.options.resizePolling === undefined ? 60 : this.options.resizePolling; if (this.options.tap === true) { this.options.tap = "tap" } if (this.options.shrinkScrollbars == "scale") { this.options.useTransition = false } this.options.invertWheelDirection = this.options.invertWheelDirection ? -1 : 1;
        this.x = 0;
        this.y = 0;
        this.directionX = 0;
        this.directionY = 0;
        this._events = {};
        this._init();
        this.refresh();
        this.scrollTo(this.options.startX, this.options.startY);
        this.enable() } g.prototype = { version: "5.1.3", _init: function () { this._initEvents(); if (this.options.scrollbars || this.options.indicators) { this._initIndicators() } if (this.options.mouseWheel) { this._initWheel() } if (this.options.snap) { this._initSnap() } if (this.options.keyBindings) { this._initKeys() } }, destroy: function () { this._initEvents(true);
            this._execEvent("destroy") }, _transitionEnd: function (j) { if (j.target != this.scroller || !this.isInTransition) { return } this._transitionTime(); if (!this.resetPosition(this.options.bounceTime)) { this.isInTransition = false;
                this._execEvent("scrollEnd") } }, _start: function (k) { if (c.eventType[k.type] != 1) { if (k.button !== 0) { return } } if (!this.enabled || (this.initiated && c.eventType[k.type] !== this.initiated)) { return } if (this.options.preventDefault && !c.isBadAndroid && !c.preventDefaultException(k.target, this.options.preventDefaultException)) { k.preventDefault() } var j = k.touches ? k.touches[0] : k,
                l;
            this.initiated = c.eventType[k.type];
            this.moved = false;
            this.distX = 0;
            this.distY = 0;
            this.directionX = 0;
            this.directionY = 0;
            this.directionLocked = 0;
            this._transitionTime();
            this.startTime = c.getTime(); if (this.options.useTransition && this.isInTransition) { this.isInTransition = false;
                l = this.getComputedPosition();
                this._translate(e.round(l.x), e.round(l.y));
                this._execEvent("scrollEnd") } else { if (!this.options.useTransition && this.isAnimating) { this.isAnimating = false;
                    this._execEvent("scrollEnd") } } this.startX = this.x;
            this.startY = this.y;
            this.absStartX = this.x;
            this.absStartY = this.y;
            this.pointX = j.pageX;
            this.pointY = j.pageY;
            this._execEvent("beforeScrollStart") }, _move: function (o) { if (!this.enabled || c.eventType[o.type] !== this.initiated) { return } if (this.options.preventDefault) { o.preventDefault() } var q = o.touches ? o.touches[0] : o,
                l = q.pageX - this.pointX,
                k = q.pageY - this.pointY,
                p = c.getTime(),
                j, r, n, m;
            this.pointX = q.pageX;
            this.pointY = q.pageY;
            this.distX += l;
            this.distY += k;
            n = e.abs(this.distX);
            m = e.abs(this.distY); if (p - this.endTime > 300 && (n < 10 && m < 10)) { return } if (!this.directionLocked && !this.options.freeScroll) { if (n > m + this.options.directionLockThreshold) { this.directionLocked = "h" } else { if (m >= n + this.options.directionLockThreshold) { this.directionLocked = "v" } else { this.directionLocked = "n" } } } if (this.directionLocked == "h") { if (this.options.eventPassthrough == "vertical") { o.preventDefault() } else { if (this.options.eventPassthrough == "horizontal") { this.initiated = false; return } } k = 0 } else { if (this.directionLocked == "v") { if (this.options.eventPassthrough == "horizontal") { o.preventDefault() } else { if (this.options.eventPassthrough == "vertical") { this.initiated = false; return } } l = 0 } } l = this.hasHorizontalScroll ? l : 0;
            k = this.hasVerticalScroll ? k : 0;
            j = this.x + l;
            r = this.y + k; if (j > 0 || j < this.maxScrollX) { j = this.options.bounce ? this.x + l / 3 : j > 0 ? 0 : this.maxScrollX } if (r > 0 || r < this.maxScrollY) { r = this.options.bounce ? this.y + k / 3 : r > 0 ? 0 : this.maxScrollY } this.directionX = l > 0 ? -1 : l < 0 ? 1 : 0;
            this.directionY = k > 0 ? -1 : k < 0 ? 1 : 0; if (!this.moved) { this._execEvent("scrollStart") } this.moved = true;
            this._translate(j, r); if (p - this.startTime > 300) { this.startTime = p;
                this.startX = this.x;
                this.startY = this.y } }, _end: function (p) { if (!this.enabled || c.eventType[p.type] !== this.initiated) { return } if (this.options.preventDefault && !c.preventDefaultException(p.target, this.options.preventDefaultException)) { p.preventDefault() } var r = p.changedTouches ? p.changedTouches[0] : p,
                l, k, o = c.getTime() - this.startTime,
                j = e.round(this.x),
                u = e.round(this.y),
                t = e.abs(j - this.startX),
                s = e.abs(u - this.startY),
                m = 0,
                q = "";
            this.isInTransition = 0;
            this.initiated = 0;
            this.endTime = c.getTime(); if (this.resetPosition(this.options.bounceTime)) { return } this.scrollTo(j, u); if (!this.moved) { if (this.options.tap) { c.tap(p, this.options.tap) } if (this.options.click) { c.click(p) } this._execEvent("scrollCancel"); return } if (this._events.flick && o < 200 && t < 100 && s < 100) { this._execEvent("flick"); return } if (this.options.momentum && o < 300) { l = this.hasHorizontalScroll ? c.momentum(this.x, this.startX, o, this.maxScrollX, this.options.bounce ? this.wrapperWidth : 0, this.options.deceleration) : { destination: j, duration: 0 };
                k = this.hasVerticalScroll ? c.momentum(this.y, this.startY, o, this.maxScrollY, this.options.bounce ? this.wrapperHeight : 0, this.options.deceleration) : { destination: u, duration: 0 };
                j = l.destination;
                u = k.destination;
                m = e.max(l.duration, k.duration);
                this.isInTransition = 1 } if (this.options.snap) { var n = this._nearestSnap(j, u);
                this.currentPage = n;
                m = this.options.snapSpeed || e.max(e.max(e.min(e.abs(j - n.x), 1000), e.min(e.abs(u - n.y), 1000)), 300);
                j = n.x;
                u = n.y;
                this.directionX = 0;
                this.directionY = 0;
                q = this.options.bounceEasing } if (j != this.x || u != this.y) { if (j > 0 || j < this.maxScrollX || u > 0 || u < this.maxScrollY) { q = c.ease.quadratic } this.scrollTo(j, u, m, q); return } this._execEvent("scrollEnd") }, _resize: function () { var j = this;
            clearTimeout(this.resizeTimeout);
            this.resizeTimeout = setTimeout(function () { j.refresh() }, this.options.resizePolling) }, resetPosition: function (k) { var j = this.x,
                l = this.y;
            k = k || 0; if (!this.hasHorizontalScroll || this.x > 0) { j = 0 } else { if (this.x < this.maxScrollX) { j = this.maxScrollX } } if (!this.hasVerticalScroll || this.y > 0) { l = 0 } else { if (this.y < this.maxScrollY) { l = this.maxScrollY } } if (j == this.x && l == this.y) { return false } this.scrollTo(j, l, k, this.options.bounceEasing); return true }, disable: function () { this.enabled = false }, enable: function () { this.enabled = true }, refresh: function () { var j = this.wrapper.offsetHeight;
            this.wrapperWidth = this.wrapper.clientWidth;
            this.wrapperHeight = this.wrapper.clientHeight;
            this.scrollerWidth = this.scroller.offsetWidth;
            this.scrollerHeight = this.scroller.offsetHeight;
            this.maxScrollX = this.wrapperWidth - this.scrollerWidth;
            this.maxScrollY = this.wrapperHeight - this.scrollerHeight;
            this.hasHorizontalScroll = this.options.scrollX && this.maxScrollX < 0;
            this.hasVerticalScroll = this.options.scrollY && this.maxScrollY < 0; if (!this.hasHorizontalScroll) { this.maxScrollX = 0;
                this.scrollerWidth = this.wrapperWidth } if (!this.hasVerticalScroll) { this.maxScrollY = 0;
                this.scrollerHeight = this.wrapperHeight } this.endTime = 0;
            this.directionX = 0;
            this.directionY = 0;
            this.wrapperOffset = c.offset(this.wrapper);
            this._execEvent("refresh");
            this.resetPosition() }, on: function (k, j) { if (!this._events[k]) { this._events[k] = [] } this._events[k].push(j) }, off: function (l, k) { if (!this._events[l]) { return } var j = this._events[l].indexOf(k); if (j > -1) { this._events[l].splice(j, 1) } }, _execEvent: function (m) { if (!this._events[m]) { return } var k = 0,
                j = this._events[m].length; if (!j) { return } for (; k < j; k++) { this._events[m][k].apply(this, [].slice.call(arguments, 1)) } }, scrollBy: function (j, m, k, l) { j = this.x + j;
            m = this.y + m;
            k = k || 0;
            this.scrollTo(j, m, k, l) }, scrollTo: function (j, m, k, l) { l = l || c.ease.circular;
            this.isInTransition = this.options.useTransition && k > 0; if (!k || (this.options.useTransition && l.style)) { this._transitionTimingFunction(l.style);
                this._transitionTime(k);
                this._translate(j, m) } else { this._animate(j, m, k, l.fn) } }, scrollToElement: function (k, l, j, o, n) { k = k.nodeType ? k : this.scroller.querySelector(k); if (!k) { return } var m = c.offset(k);
            m.left -= this.wrapperOffset.left;
            m.top -= this.wrapperOffset.top; if (j === true) { j = e.round(k.offsetWidth / 2 - this.wrapper.offsetWidth / 2) } if (o === true) { o = e.round(k.offsetHeight / 2 - this.wrapper.offsetHeight / 2) } m.left -= j || 0;
            m.top -= o || 0;
            m.left = m.left > 0 ? 0 : m.left < this.maxScrollX ? this.maxScrollX : m.left;
            m.top = m.top > 0 ? 0 : m.top < this.maxScrollY ? this.maxScrollY : m.top;
            l = l === undefined || l === null || l === "auto" ? e.max(e.abs(this.x - m.left), e.abs(this.y - m.top)) : l;
            this.scrollTo(m.left, m.top, l, n) }, _transitionTime: function (k) { k = k || 0;
            this.scrollerStyle[c.style.transitionDuration] = k + "ms"; if (!k && c.isBadAndroid) { this.scrollerStyle[c.style.transitionDuration] = "0.001s" } if (this.indicators) { for (var j = this.indicators.length; j--;) { this.indicators[j].transitionTime(k) } } }, _transitionTimingFunction: function (k) { this.scrollerStyle[c.style.transitionTimingFunction] = k; if (this.indicators) { for (var j = this.indicators.length; j--;) { this.indicators[j].transitionTimingFunction(k) } } }, _translate: function (j, l) { if (this.options.useTransform) { this.scrollerStyle[c.style.transform] = "translate(" + j + "px," + l + "px)" + this.translateZ } else { j = e.round(j);
                l = e.round(l);
                this.scrollerStyle.left = j + "px";
                this.scrollerStyle.top = l + "px" } this.x = j;
            this.y = l; if (this.indicators) { for (var k = this.indicators.length; k--;) { this.indicators[k].updatePosition() } } }, _initEvents: function (j) { var k = j ? c.removeEvent : c.addEvent,
                l = this.options.bindToWrapper ? this.wrapper : f;
            k(f, "orientationchange", this);
            k(f, "resize", this); if (this.options.click) { k(this.wrapper, "click", this, true) } if (!this.options.disableMouse) { k(this.wrapper, "mousedown", this);
                k(l, "mousemove", this);
                k(l, "mousecancel", this);
                k(l, "mouseup", this) } if (c.hasPointer && !this.options.disablePointer) { k(this.wrapper, c.prefixPointerEvent("pointerdown"), this);
                k(l, c.prefixPointerEvent("pointermove"), this);
                k(l, c.prefixPointerEvent("pointercancel"), this);
                k(l, c.prefixPointerEvent("pointerup"), this) } if (c.hasTouch && !this.options.disableTouch) { k(this.wrapper, "touchstart", this);
                k(l, "touchmove", this);
                k(l, "touchcancel", this);
                k(l, "touchend", this) } k(this.scroller, "transitionend", this);
            k(this.scroller, "webkitTransitionEnd", this);
            k(this.scroller, "oTransitionEnd", this);
            k(this.scroller, "MSTransitionEnd", this) }, getComputedPosition: function () { var k = f.getComputedStyle(this.scroller, null),
                j, l; if (this.options.useTransform) { k = k[c.style.transform].split(")")[0].split(", ");
                j = +(k[12] || k[4]);
                l = +(k[13] || k[5]) } else { j = +k.left.replace(/[^-\d.]/g, "");
                l = +k.top.replace(/[^-\d.]/g, "") } return { x: j, y: l } }, _initIndicators: function () { var l = this.options.interactiveScrollbars,
                n = typeof this.options.scrollbars != "string",
                p = [],
                k; var o = this;
            this.indicators = []; if (this.options.scrollbars) { if (this.options.scrollY) { k = { el: d("v", l, this.options.scrollbars), interactive: l, defaultScrollbars: true, customStyle: n, resize: this.options.resizeScrollbars, shrink: this.options.shrinkScrollbars, fade: this.options.fadeScrollbars, listenX: false };
                    this.wrapper.appendChild(k.el);
                    p.push(k) } if (this.options.scrollX) { k = { el: d("h", l, this.options.scrollbars), interactive: l, defaultScrollbars: true, customStyle: n, resize: this.options.resizeScrollbars, shrink: this.options.shrinkScrollbars, fade: this.options.fadeScrollbars, listenY: false };
                    this.wrapper.appendChild(k.el);
                    p.push(k) } } if (this.options.indicators) { p = p.concat(this.options.indicators) } for (var m = p.length; m--;) { this.indicators.push(new b(this, p[m])) }

            function j(r) { for (var q = o.indicators.length; q--;) { r.call(o.indicators[q]) } } if (this.options.fadeScrollbars) { this.on("scrollEnd", function () { j(function () { this.fade() }) });
                this.on("scrollCancel", function () { j(function () { this.fade() }) });
                this.on("scrollStart", function () { j(function () { this.fade(1) }) });
                this.on("beforeScrollStart", function () { j(function () { this.fade(1, true) }) }) } this.on("refresh", function () { j(function () { this.refresh() }) });
            this.on("destroy", function () { j(function () { this.destroy() });
                delete this.indicators }) }, _initWheel: function () { c.addEvent(this.wrapper, "wheel", this);
            c.addEvent(this.wrapper, "mousewheel", this);
            c.addEvent(this.wrapper, "DOMMouseScroll", this);
            this.on("destroy", function () { c.removeEvent(this.wrapper, "wheel", this);
                c.removeEvent(this.wrapper, "mousewheel", this);
                c.removeEvent(this.wrapper, "DOMMouseScroll", this) }) }, _wheel: function (n) { if (!this.enabled) { return } n.preventDefault();
            n.stopPropagation(); var l, k, o, m, j = this; if (this.wheelTimeout === undefined) { j._execEvent("scrollStart") } clearTimeout(this.wheelTimeout);
            this.wheelTimeout = setTimeout(function () { j._execEvent("scrollEnd");
                j.wheelTimeout = undefined }, 400); if ("deltaX" in n) { if (n.deltaMode === 1) { l = -n.deltaX * this.options.mouseWheelSpeed;
                    k = -n.deltaY * this.options.mouseWheelSpeed } else { l = -n.deltaX;
                    k = -n.deltaY } } else { if ("wheelDeltaX" in n) { l = n.wheelDeltaX / 120 * this.options.mouseWheelSpeed;
                    k = n.wheelDeltaY / 120 * this.options.mouseWheelSpeed } else { if ("wheelDelta" in n) { l = k = n.wheelDelta / 120 * this.options.mouseWheelSpeed } else { if ("detail" in n) { l = k = -n.detail / 3 * this.options.mouseWheelSpeed } else { return } } } } l *= this.options.invertWheelDirection;
            k *= this.options.invertWheelDirection; if (!this.hasVerticalScroll) { l = k;
                k = 0 } if (this.options.snap) { o = this.currentPage.pageX;
                m = this.currentPage.pageY; if (l > 0) { o-- } else { if (l < 0) { o++ } } if (k > 0) { m-- } else { if (k < 0) { m++ } } this.goToPage(o, m); return } o = this.x + e.round(this.hasHorizontalScroll ? l : 0);
            m = this.y + e.round(this.hasVerticalScroll ? k : 0); if (o > 0) { o = 0 } else { if (o < this.maxScrollX) { o = this.maxScrollX } } if (m > 0) { m = 0 } else { if (m < this.maxScrollY) { m = this.maxScrollY } } this.scrollTo(o, m, 0) }, _initSnap: function () { this.currentPage = {}; if (typeof this.options.snap == "string") { this.options.snap = this.scroller.querySelectorAll(this.options.snap) } this.on("refresh", function () { var s = 0,
                    q, o = 0,
                    k, r, p, u = 0,
                    t, w = this.options.snapStepX || this.wrapperWidth,
                    v = this.options.snapStepY || this.wrapperHeight,
                    j;
                this.pages = []; if (!this.wrapperWidth || !this.wrapperHeight || !this.scrollerWidth || !this.scrollerHeight) { return } if (this.options.snap === true) { r = e.round(w / 2);
                    p = e.round(v / 2); while (u > -this.scrollerWidth) { this.pages[s] = [];
                        q = 0;
                        t = 0; while (t > -this.scrollerHeight) { this.pages[s][q] = { x: e.max(u, this.maxScrollX), y: e.max(t, this.maxScrollY), width: w, height: v, cx: u - r, cy: t - p };
                            t -= v;
                            q++ } u -= w;
                        s++ } } else { j = this.options.snap;
                    q = j.length;
                    k = -1; for (; s < q; s++) { if (s === 0 || j[s].offsetLeft <= j[s - 1].offsetLeft) { o = 0;
                            k++ } if (!this.pages[o]) { this.pages[o] = [] } u = e.max(-j[s].offsetLeft, this.maxScrollX);
                        t = e.max(-j[s].offsetTop, this.maxScrollY);
                        r = u - e.round(j[s].offsetWidth / 2);
                        p = t - e.round(j[s].offsetHeight / 2);
                        this.pages[o][k] = { x: u, y: t, width: j[s].offsetWidth, height: j[s].offsetHeight, cx: r, cy: p }; if (u > this.maxScrollX) { o++ } } } this.goToPage(this.currentPage.pageX || 0, this.currentPage.pageY || 0, 0); if (this.options.snapThreshold % 1 === 0) { this.snapThresholdX = this.options.snapThreshold;
                    this.snapThresholdY = this.options.snapThreshold } else { this.snapThresholdX = e.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].width * this.options.snapThreshold);
                    this.snapThresholdY = e.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].height * this.options.snapThreshold) } });
            this.on("flick", function () { var j = this.options.snapSpeed || e.max(e.max(e.min(e.abs(this.x - this.startX), 1000), e.min(e.abs(this.y - this.startY), 1000)), 300);
                this.goToPage(this.currentPage.pageX + this.directionX, this.currentPage.pageY + this.directionY, j) }) }, _nearestSnap: function (k, p) { if (!this.pages.length) { return { x: 0, y: 0, pageX: 0, pageY: 0 } } var o = 0,
                n = this.pages.length,
                j = 0; if (e.abs(k - this.absStartX) < this.snapThresholdX && e.abs(p - this.absStartY) < this.snapThresholdY) { return this.currentPage } if (k > 0) { k = 0 } else { if (k < this.maxScrollX) { k = this.maxScrollX } } if (p > 0) { p = 0 } else { if (p < this.maxScrollY) { p = this.maxScrollY } } for (; o < n; o++) { if (k >= this.pages[o][0].cx) { k = this.pages[o][0].x; break } } n = this.pages[o].length; for (; j < n; j++) { if (p >= this.pages[0][j].cy) { p = this.pages[0][j].y; break } } if (o == this.currentPage.pageX) { o += this.directionX; if (o < 0) { o = 0 } else { if (o >= this.pages.length) { o = this.pages.length - 1 } } k = this.pages[o][0].x } if (j == this.currentPage.pageY) { j += this.directionY; if (j < 0) { j = 0 } else { if (j >= this.pages[0].length) { j = this.pages[0].length - 1 } } p = this.pages[0][j].y } return { x: k, y: p, pageX: o, pageY: j } }, goToPage: function (j, o, k, n) { n = n || this.options.bounceEasing; if (j >= this.pages.length) { j = this.pages.length - 1 } else { if (j < 0) { j = 0 } } if (o >= this.pages[j].length) { o = this.pages[j].length - 1 } else { if (o < 0) { o = 0 } } var m = this.pages[j][o].x,
                l = this.pages[j][o].y;
            k = k === undefined ? this.options.snapSpeed || e.max(e.max(e.min(e.abs(m - this.x), 1000), e.min(e.abs(l - this.y), 1000)), 300) : k;
            this.currentPage = { x: m, y: l, pageX: j, pageY: o };
            this.scrollTo(m, l, k, n) }, next: function (k, m) { var j = this.currentPage.pageX,
                l = this.currentPage.pageY;
            j++; if (j >= this.pages.length && this.hasVerticalScroll) { j = 0;
                l++ } this.goToPage(j, l, k, m) }, prev: function (k, m) { var j = this.currentPage.pageX,
                l = this.currentPage.pageY;
            j--; if (j < 0 && this.hasVerticalScroll) { j = 0;
                l-- } this.goToPage(j, l, k, m) }, _initKeys: function (l) { var k = { pageUp: 33, pageDown: 34, end: 35, home: 36, left: 37, up: 38, right: 39, down: 40 }; var j; if (typeof this.options.keyBindings == "object") { for (j in this.options.keyBindings) { if (typeof this.options.keyBindings[j] == "string") { this.options.keyBindings[j] = this.options.keyBindings[j].toUpperCase().charCodeAt(0) } } } else { this.options.keyBindings = {} } for (j in k) { this.options.keyBindings[j] = this.options.keyBindings[j] || k[j] } c.addEvent(f, "keydown", this);
            this.on("destroy", function () { c.removeEvent(f, "keydown", this) }) }, _key: function (o) { if (!this.enabled) { return } var j = this.options.snap,
                p = j ? this.currentPage.pageX : this.x,
                n = j ? this.currentPage.pageY : this.y,
                l = c.getTime(),
                k = this.keyTime || 0,
                m = 0.25,
                q; if (this.options.useTransition && this.isInTransition) { q = this.getComputedPosition();
                this._translate(e.round(q.x), e.round(q.y));
                this.isInTransition = false } this.keyAcceleration = l - k < 200 ? e.min(this.keyAcceleration + m, 50) : 0; switch (o.keyCode) {
            case this.options.keyBindings.pageUp:
                if (this.hasHorizontalScroll && !this.hasVerticalScroll) { p += j ? 1 : this.wrapperWidth } else { n += j ? 1 : this.wrapperHeight } break;
            case this.options.keyBindings.pageDown:
                if (this.hasHorizontalScroll && !this.hasVerticalScroll) { p -= j ? 1 : this.wrapperWidth } else { n -= j ? 1 : this.wrapperHeight } break;
            case this.options.keyBindings.end:
                p = j ? this.pages.length - 1 : this.maxScrollX;
                n = j ? this.pages[0].length - 1 : this.maxScrollY; break;
            case this.options.keyBindings.home:
                p = 0;
                n = 0; break;
            case this.options.keyBindings.left:
                p += j ? -1 : 5 + this.keyAcceleration >> 0; break;
            case this.options.keyBindings.up:
                n += j ? 1 : 5 + this.keyAcceleration >> 0; break;
            case this.options.keyBindings.right:
                p -= j ? -1 : 5 + this.keyAcceleration >> 0; break;
            case this.options.keyBindings.down:
                n -= j ? 1 : 5 + this.keyAcceleration >> 0; break;
            default:
                return } if (j) { this.goToPage(p, n); return } if (p > 0) { p = 0;
                this.keyAcceleration = 0 } else { if (p < this.maxScrollX) { p = this.maxScrollX;
                    this.keyAcceleration = 0 } } if (n > 0) { n = 0;
                this.keyAcceleration = 0 } else { if (n < this.maxScrollY) { n = this.maxScrollY;
                    this.keyAcceleration = 0 } } this.scrollTo(p, n, 0);
            this.keyTime = l }, _animate: function (s, r, m, j) { var p = this,
                o = this.x,
                n = this.y,
                k = c.getTime(),
                q = k + m;

            function l() { var t = c.getTime(),
                    v, u, w; if (t >= q) { p.isAnimating = false;
                    p._translate(s, r); if (!p.resetPosition(p.options.bounceTime)) { p._execEvent("scrollEnd") } return } t = (t - k) / m;
                w = j(t);
                v = (s - o) * w + o;
                u = (r - n) * w + n;
                p._translate(v, u); if (p.isAnimating) { h(l) } } this.isAnimating = true;
            l() }, handleEvent: function (j) { switch (j.type) {
            case "touchstart":
            case "pointerdown":
            case "MSPointerDown":
            case "mousedown":
                this._start(j); break;
            case "touchmove":
            case "pointermove":
            case "MSPointerMove":
            case "mousemove":
                this._move(j); break;
            case "touchend":
            case "pointerup":
            case "MSPointerUp":
            case "mouseup":
            case "touchcancel":
            case "pointercancel":
            case "MSPointerCancel":
            case "mousecancel":
                this._end(j); break;
            case "orientationchange":
            case "resize":
                this._resize(); break;
            case "transitionend":
            case "webkitTransitionEnd":
            case "oTransitionEnd":
            case "MSTransitionEnd":
                this._transitionEnd(j); break;
            case "wheel":
            case "DOMMouseScroll":
            case "mousewheel":
                this._wheel(j); break;
            case "keydown":
                this._key(j); break;
            case "click":
                if (!j._constructed) { j.preventDefault();
                    j.stopPropagation() } break } } };

    function d(m, k, l) { var n = a.createElement("div"),
            j = a.createElement("div"); if (l === true) { n.style.cssText = "position:absolute;z-index:9999";
            j.style.cssText = "-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;position:absolute;background:rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.9);border-radius:3px" } j.className = "iScrollIndicator"; if (m == "h") { if (l === true) { n.style.cssText += ";height:7px;left:2px;right:2px;bottom:0";
                j.style.height = "100%" } n.className = "iScrollHorizontalScrollbar" } else { if (l === true) { n.style.cssText += ";width:7px;bottom:2px;top:2px;right:1px";
                j.style.width = "100%" } n.className = "iScrollVerticalScrollbar" } n.style.cssText += ";overflow:hidden"; if (!k) { n.style.pointerEvents = "none" } n.appendChild(j); return n }

    function b(j, k) { this.wrapper = typeof k.el == "string" ? a.querySelector(k.el) : k.el;
        this.wrapperStyle = this.wrapper.style;
        this.indicator = this.wrapper.children[0];
        this.indicatorStyle = this.indicator.style;
        this.scroller = j;
        this.options = { listenX: true, listenY: true, interactive: false, resize: true, defaultScrollbars: false, shrink: false, fade: false, speedRatioX: 0, speedRatioY: 0 }; for (var l in k) { this.options[l] = k[l] } this.sizeRatioX = 1;
        this.sizeRatioY = 1;
        this.maxPosX = 0;
        this.maxPosY = 0; if (this.options.interactive) { if (!this.options.disableTouch) { c.addEvent(this.indicator, "touchstart", this);
                c.addEvent(f, "touchend", this) } if (!this.options.disablePointer) { c.addEvent(this.indicator, c.prefixPointerEvent("pointerdown"), this);
                c.addEvent(f, c.prefixPointerEvent("pointerup"), this) } if (!this.options.disableMouse) { c.addEvent(this.indicator, "mousedown", this);
                c.addEvent(f, "mouseup", this) } } if (this.options.fade) { this.wrapperStyle[c.style.transform] = this.scroller.translateZ;
            this.wrapperStyle[c.style.transitionDuration] = c.isBadAndroid ? "0.001s" : "0ms";
            this.wrapperStyle.opacity = "0" } } b.prototype = { handleEvent: function (j) { switch (j.type) {
            case "touchstart":
            case "pointerdown":
            case "MSPointerDown":
            case "mousedown":
                this._start(j); break;
            case "touchmove":
            case "pointermove":
            case "MSPointerMove":
            case "mousemove":
                this._move(j); break;
            case "touchend":
            case "pointerup":
            case "MSPointerUp":
            case "mouseup":
            case "touchcancel":
            case "pointercancel":
            case "MSPointerCancel":
            case "mousecancel":
                this._end(j); break } }, destroy: function () { if (this.options.interactive) { c.removeEvent(this.indicator, "touchstart", this);
                c.removeEvent(this.indicator, c.prefixPointerEvent("pointerdown"), this);
                c.removeEvent(this.indicator, "mousedown", this);
                c.removeEvent(f, "touchmove", this);
                c.removeEvent(f, c.prefixPointerEvent("pointermove"), this);
                c.removeEvent(f, "mousemove", this);
                c.removeEvent(f, "touchend", this);
                c.removeEvent(f, c.prefixPointerEvent("pointerup"), this);
                c.removeEvent(f, "mouseup", this) } if (this.options.defaultScrollbars) { this.wrapper.parentNode.removeChild(this.wrapper) } }, _start: function (k) { var j = k.touches ? k.touches[0] : k;
            k.preventDefault();
            k.stopPropagation();
            this.transitionTime();
            this.initiated = true;
            this.moved = false;
            this.lastPointX = j.pageX;
            this.lastPointY = j.pageY;
            this.startTime = c.getTime(); if (!this.options.disableTouch) { c.addEvent(f, "touchmove", this) } if (!this.options.disablePointer) { c.addEvent(f, c.prefixPointerEvent("pointermove"), this) } if (!this.options.disableMouse) { c.addEvent(f, "mousemove", this) } this.scroller._execEvent("beforeScrollStart") }, _move: function (o) { var k = o.touches ? o.touches[0] : o,
                l, j, p, n, m = c.getTime(); if (!this.moved) { this.scroller._execEvent("scrollStart") } this.moved = true;
            l = k.pageX - this.lastPointX;
            this.lastPointX = k.pageX;
            j = k.pageY - this.lastPointY;
            this.lastPointY = k.pageY;
            p = this.x + l;
            n = this.y + j;
            this._pos(p, n);
            o.preventDefault();
            o.stopPropagation() }, _end: function (l) { if (!this.initiated) { return } this.initiated = false;
            l.preventDefault();
            l.stopPropagation();
            c.removeEvent(f, "touchmove", this);
            c.removeEvent(f, c.prefixPointerEvent("pointermove"), this);
            c.removeEvent(f, "mousemove", this); if (this.scroller.options.snap) { var j = this.scroller._nearestSnap(this.scroller.x, this.scroller.y); var k = this.options.snapSpeed || e.max(e.max(e.min(e.abs(this.scroller.x - j.x), 1000), e.min(e.abs(this.scroller.y - j.y), 1000)), 300); if (this.scroller.x != j.x || this.scroller.y != j.y) { this.scroller.directionX = 0;
                    this.scroller.directionY = 0;
                    this.scroller.currentPage = j;
                    this.scroller.scrollTo(j.x, j.y, k, this.scroller.options.bounceEasing) } } if (this.moved) { this.scroller._execEvent("scrollEnd") } }, transitionTime: function (j) { j = j || 0;
            this.indicatorStyle[c.style.transitionDuration] = j + "ms"; if (!j && c.isBadAndroid) { this.indicatorStyle[c.style.transitionDuration] = "0.001s" } }, transitionTimingFunction: function (j) { this.indicatorStyle[c.style.transitionTimingFunction] = j }, refresh: function () { this.transitionTime(); if (this.options.listenX && !this.options.listenY) { this.indicatorStyle.display = this.scroller.hasHorizontalScroll ? "block" : "none" } else { if (this.options.listenY && !this.options.listenX) { this.indicatorStyle.display = this.scroller.hasVerticalScroll ? "block" : "none" } else { this.indicatorStyle.display = this.scroller.hasHorizontalScroll || this.scroller.hasVerticalScroll ? "block" : "none" } } if (this.scroller.hasHorizontalScroll && this.scroller.hasVerticalScroll) { c.addClass(this.wrapper, "iScrollBothScrollbars");
                c.removeClass(this.wrapper, "iScrollLoneScrollbar"); if (this.options.defaultScrollbars && this.options.customStyle) { if (this.options.listenX) { this.wrapper.style.right = "8px" } else { this.wrapper.style.bottom = "8px" } } } else { c.removeClass(this.wrapper, "iScrollBothScrollbars");
                c.addClass(this.wrapper, "iScrollLoneScrollbar"); if (this.options.defaultScrollbars && this.options.customStyle) { if (this.options.listenX) { this.wrapper.style.right = "2px" } else { this.wrapper.style.bottom = "2px" } } } var j = this.wrapper.offsetHeight; if (this.options.listenX) { this.wrapperWidth = this.wrapper.clientWidth; if (this.options.resize) { this.indicatorWidth = e.max(e.round(this.wrapperWidth * this.wrapperWidth / (this.scroller.scrollerWidth || this.wrapperWidth || 1)), 8);
                    this.indicatorStyle.width = this.indicatorWidth + "px" } else { this.indicatorWidth = this.indicator.clientWidth } this.maxPosX = this.wrapperWidth - this.indicatorWidth; if (this.options.shrink == "clip") { this.minBoundaryX = -this.indicatorWidth + 8;
                    this.maxBoundaryX = this.wrapperWidth - 8 } else { this.minBoundaryX = 0;
                    this.maxBoundaryX = this.maxPosX } this.sizeRatioX = this.options.speedRatioX || (this.scroller.maxScrollX && (this.maxPosX / this.scroller.maxScrollX)) } if (this.options.listenY) { this.wrapperHeight = this.wrapper.clientHeight; if (this.options.resize) { this.indicatorHeight = e.max(e.round(this.wrapperHeight * this.wrapperHeight / (this.scroller.scrollerHeight || this.wrapperHeight || 1)), 8);
                    this.indicatorStyle.height = this.indicatorHeight + "px" } else { this.indicatorHeight = this.indicator.clientHeight } this.maxPosY = this.wrapperHeight - this.indicatorHeight; if (this.options.shrink == "clip") { this.minBoundaryY = -this.indicatorHeight + 8;
                    this.maxBoundaryY = this.wrapperHeight - 8 } else { this.minBoundaryY = 0;
                    this.maxBoundaryY = this.maxPosY } this.maxPosY = this.wrapperHeight - this.indicatorHeight;
                this.sizeRatioY = this.options.speedRatioY || (this.scroller.maxScrollY && (this.maxPosY / this.scroller.maxScrollY)) } this.updatePosition() }, updatePosition: function () { var j = this.options.listenX && e.round(this.sizeRatioX * this.scroller.x) || 0,
                k = this.options.listenY && e.round(this.sizeRatioY * this.scroller.y) || 0; if (!this.options.ignoreBoundaries) { if (j < this.minBoundaryX) { if (this.options.shrink == "scale") { this.width = e.max(this.indicatorWidth + j, 8);
                        this.indicatorStyle.width = this.width + "px" } j = this.minBoundaryX } else { if (j > this.maxBoundaryX) { if (this.options.shrink == "scale") { this.width = e.max(this.indicatorWidth - (j - this.maxPosX), 8);
                            this.indicatorStyle.width = this.width + "px";
                            j = this.maxPosX + this.indicatorWidth - this.width } else { j = this.maxBoundaryX } } else { if (this.options.shrink == "scale" && this.width != this.indicatorWidth) { this.width = this.indicatorWidth;
                            this.indicatorStyle.width = this.width + "px" } } } if (k < this.minBoundaryY) { if (this.options.shrink == "scale") { this.height = e.max(this.indicatorHeight + k * 3, 8);
                        this.indicatorStyle.height = this.height + "px" } k = this.minBoundaryY } else { if (k > this.maxBoundaryY) { if (this.options.shrink == "scale") { this.height = e.max(this.indicatorHeight - (k - this.maxPosY) * 3, 8);
                            this.indicatorStyle.height = this.height + "px";
                            k = this.maxPosY + this.indicatorHeight - this.height } else { k = this.maxBoundaryY } } else { if (this.options.shrink == "scale" && this.height != this.indicatorHeight) { this.height = this.indicatorHeight;
                            this.indicatorStyle.height = this.height + "px" } } } } this.x = j;
            this.y = k; if (this.scroller.options.useTransform) { this.indicatorStyle[c.style.transform] = "translate(" + j + "px," + k + "px)" + this.scroller.translateZ } else { this.indicatorStyle.left = j + "px";
                this.indicatorStyle.top = k + "px" } }, _pos: function (j, k) { if (j < 0) { j = 0 } else { if (j > this.maxPosX) { j = this.maxPosX } } if (k < 0) { k = 0 } else { if (k > this.maxPosY) { k = this.maxPosY } } j = this.options.listenX ? e.round(j / this.sizeRatioX) : this.scroller.x;
            k = this.options.listenY ? e.round(k / this.sizeRatioY) : this.scroller.y;
            this.scroller.scrollTo(j, k) }, fade: function (m, l) { if (l && !this.visible) { return } clearTimeout(this.fadeTimeout);
            this.fadeTimeout = null; var k = m ? 250 : 500,
                j = m ? 0 : 300;
            m = m ? "1" : "0";
            this.wrapperStyle[c.style.transitionDuration] = k + "ms";
            this.fadeTimeout = setTimeout((function (n) { this.wrapperStyle.opacity = n;
                this.visible = +n }).bind(this, m), j) } };
    g.utils = c; if (typeof module != "undefined" && module.exports) { module.exports = g } else { f.IScroll = g } })(window, document, Math);
var linearEasing = { style: "cubic-bezier(0,0,1,1)", fn: function (a) { return a } };

function CustomIScroll(b, a) { this.originUseTransition = a.useTransition;
    IScroll.apply(this, arguments) } CustomIScroll.prototype.constructor = CustomIScroll;
CustomIScroll.prototype = Object.create(IScroll.prototype);
CustomIScroll.prototype.isTransitionSupported = function () { return this.originUseTransition && this.options.useTransform };
CustomIScroll.prototype.stop = function () { this.isAnimating = false; var b = this.getComputedPosition(); var a = b.x;
    this._translate(a, 0) };
CustomIScroll.prototype.scrollToLeft = function (a) { this.scrollTo(0, 0, a * Math.abs(this.x) / 1000, linearEasing) };
CustomIScroll.prototype.scrollToRight = function (a) { this.scrollTo(this.maxScrollX, 0, a * (Math.abs(this.maxScrollX) - Math.abs(this.x)) / 1000, linearEasing) };

function shiftLobbyElementsAround(c) { var b = getLeftLobbyElements(c); var a = getRightLobbyElements(c); for (i = 0; i < b.length; i++) { b[i].className += " moveLeft" } for (i = 0; i < a.length; i++) { a[i].className += " moveRight" } }

function getRightLobbyElements(a) { return getLobbyElementsByDirection(a, true) }

function getLeftLobbyElements(a) { return getLobbyElementsByDirection(a, false) }

function getLobbyElementsByDirection(d, c) { var a = []; var b = d; while (b) { var e = c ? b.nextSibling : b.previousSibling; if (e && e.className && e.className.indexOf("item") > -1) { a.push(e) } b = e } return a }(function (v) { var w, C = {},
        B = { 16: false, 18: false, 17: false, 91: false },
        s = "all",
        e = { "⇧": 16, shift: 16, "⌥": 18, alt: 18, option: 18, "⌃": 17, ctrl: 17, control: 17, "⌘": 91, command: 91 },
        f = { backspace: 8, tab: 9, clear: 12, enter: 13, "return": 13, esc: 27, escape: 27, space: 32, left: 37, up: 38, right: 39, down: 40, del: 46, "delete": 46, home: 36, end: 35, pageup: 33, pagedown: 34, ",": 188, ".": 190, "/": 191, "`": 192, "-": 189, "=": 187, ";": 186, "'": 222, "[": 219, "]": 221, "\\": 220 },
        a = function (k) { return f[k] || k.toUpperCase().charCodeAt(0) },
        y = []; for (w = 1; w < 20; w++) { f["f" + w] = 111 + w }

    function h(G, F) { var k = G.length; while (k--) { if (G[k] === F) { return k } } return -1 }

    function z(F, k) { if (F.length != k.length) { return false } for (var G = 0; G < F.length; G++) { if (F[G] !== k[G]) { return false } } return true } var r = { 16: "shiftKey", 18: "altKey", 17: "ctrlKey", 91: "metaKey" };

    function j(k) { for (w in B) { B[w] = k[r[w]] } }

    function D(K) { var H, J, F, G, L, I;
        H = K.keyCode; if (h(y, H) == -1) { y.push(H) } if (H == 93 || H == 224) { H = 91 } if (H in B) { B[H] = true; for (F in e) { if (e[F] == H) { g[F] = true } } return } j(K); if (!g.filter.call(this, K)) { return } if (!(H in C)) { return } I = o(); for (G = 0; G < C[H].length; G++) { J = C[H][G]; if (J.scope == I || J.scope == "all") { L = J.mods.length > 0; for (F in B) { if ((!B[F] && h(J.mods, +F) > -1) || (B[F] && h(J.mods, +F) == -1)) { L = false } } if ((J.mods.length == 0 && !B[16] && !B[18] && !B[17] && !B[91]) || L) { if (J.method(K, J) === false) { if (K.preventDefault) { K.preventDefault() } else { K.returnValue = false } if (K.stopPropagation) { K.stopPropagation() } if (K.cancelBubble) { K.cancelBubble = true } } } } } }

    function d(I) { var H = I.keyCode,
            F, G = h(y, H); if (G >= 0) { y.splice(G, 1) } if (H == 93 || H == 224) { H = 91 } if (H in B) { B[H] = false; for (F in e) { if (e[F] == H) { g[F] = false } } } }

    function l() { for (w in B) { B[w] = false } for (w in e) { g[w] = false } }

    function g(F, G, J) { var I, H;
        I = b(F); if (J === undefined) { J = G;
            G = "all" } for (var k = 0; k < I.length; k++) { H = [];
            F = I[k].split("+"); if (F.length > 1) { H = x(F);
                F = [F[F.length - 1]] } F = F[0];
            F = a(F); if (!(F in C)) { C[F] = [] } C[F].push({ shortcut: I[k], scope: G, method: J, key: I[k], mods: H }) } }

    function A(H, I) { var k, K, J = [],
            G, F, L;
        k = b(H); for (F = 0; F < k.length; F++) { K = k[F].split("+"); if (K.length > 1) { J = x(K) } H = K[K.length - 1];
            H = a(H); if (I === undefined) { I = o() } if (!C[H]) { return } for (G = 0; G < C[H].length; G++) { L = C[H][G]; if (L.scope === I && z(L.mods, J)) { C[H][G] = {} } } } }

    function c(k) { if (typeof (k) == "string") { k = a(k) } return h(y, k) != -1 }

    function q() { return y.slice(0) }

    function m(F) { var k = (F.target || F.srcElement).tagName; return !(k == "INPUT" || k == "SELECT" || k == "TEXTAREA") } for (w in e) { g[w] = false }

    function n(k) { s = k || "all" }

    function o() { return s || "all" }

    function t(H) { var G, k, F; for (G in C) { k = C[G]; for (F = 0; F < k.length;) { if (k[F].scope === H) { k.splice(F, 1) } else { F++ } } } }

    function b(k) { var F;
        k = k.replace(/\s/g, "");
        F = k.split(","); if ((F[F.length - 1]) == "") { F[F.length - 2] += "," } return F }

    function x(F) { var G = F.slice(0, F.length - 1); for (var k = 0; k < G.length; k++) { G[k] = e[G[k]] } return G }

    function u(k, F, G) { if (k.addEventListener) { k.addEventListener(F, G, false) } else { if (k.attachEvent) { k.attachEvent("on" + F, function () { G(window.event) }) } } } u(document, "keydown", function (k) { D(k) });
    u(document, "keyup", d);
    u(window, "focus", l); var p = v.key;

    function E() { var F = v.key;
        v.key = p; return F } v.key = g;
    v.key.setScope = n;
    v.key.getScope = o;
    v.key.deleteScope = t;
    v.key.filter = m;
    v.key.isPressed = c;
    v.key.getPressedKeyCodes = q;
    v.key.noConflict = E;
    v.key.unbind = A; if (typeof module !== "undefined") { module.exports = g } })(this);
