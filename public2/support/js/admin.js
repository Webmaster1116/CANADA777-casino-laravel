
/*
 * ==========================================================
 * ADMINISTRATION SCRIPT
 * ==========================================================
 *
 * Main Javascript admin file. © 2021 board.support. All rights reserved.
 * 
 */

'use strict';
(function ($) {

    // Global
    var admin;
    var header;

    // Conversation  
    var conversations = [];
    var conversations_area;
    var conversations_admin_list;
    var conversations_admin_list_ul;
    var saved_replies = false;
    var saved_replies_list = false;
    var woocommerce_products_box = false;
    var woocommerce_products_box_ul = false;
    var pagination = 1;
    var notes_panel;
    var attachments_panel;
    var direct_message_box;
    var dialogflow_intent_box;
    var suggestions_area;
    var pagination_end = false;

    // Users
    var users_area;
    var users_table;
    var users_table_menu;
    var users = {};
    var users_pagination = 1;
    var profile_box;
    var profile_edit_box;

    // Settings
    var settings_area;
    var articles_area;
    var automations_area;
    var automations_area_select;
    var automations_area_nav;
    var conditions_area;

    // Reports
    var reports_area;

    // Miscellaneus
    var upload_target;
    var upload_on_success;
    var language_switcher_target;
    var timeout;
    var alertOnConfirmation;
    var responsive = $(window).width() < 429;
    var scrolls = { last: 0, header: true, always_hidden: false };
    var localhost = location.hostname === 'localhost' || location.hostname === '127.0.0.1';
    var today = new Date();
    var is_busy = false;
    var agent_online = true;
    var active_interval = false;
    var pusher_timeout;
    var temp;
    var overlay;
    var SITE_URL;
    var ND = 'undefined';
    var wp_admin = false;

    /*
    * ----------------------------------------------------------
    * External plugins
    * ----------------------------------------------------------
    */

    // miniTip 1.5.3 | (c) 2011, James Simpson | Dual licensed under the MIT and GPL
    $.fn.miniTip = function (t) { var e = $.extend({ title: '', content: !1, delay: 300, anchor: 'n', event: 'hover', fadeIn: 200, fadeOut: 200, aHide: !0, maxW: '250px', offset: 5, stemOff: 0, doHide: !1 }, t); 0 == admin.find('#miniTip').length && admin.append('<div id="miniTip" class="sb-tooltip"><div></div></div>'); var n = admin.find('#miniTip'), a = n.find('div'); return e.doHide ? (n.stop(!0, !0).fadeOut(e.fadeOut), !1) : this.each(function () { var t = $(this), o = e.content ? e.content : t.attr('title'); if ('' != o && void 0 !== o) { window.delay = !1; var i = !1, r = !0; e.content || t.removeAttr('title'), 'hover' == e.event ? (t.hover(function () { n.removeAttr('click'), r = !0, s.call(this) }, function () { r = !1, d() }), e.aHide || n.hover(function () { i = !0 }, function () { i = !1, setTimeout(function () { !r && !n.attr('click') && d() }, 20) })) : 'click' == e.event && (e.aHide = !0, t.click(function () { return n.attr('click', 't'), n.data('last_target') !== t ? s.call(this) : 'none' == n.css("display") ? s.call(this) : d(), n.data('last_target', t), $('html').unbind('click').click(function (t) { 'block' == n.css('display') && !$(t.target).closest('#miniTip').length && ($('html').unbind('click'), d()) }), !1 })); var s = function () { e.show && e.show.call(this, e), e.content && '' != e.content && (o = e.content), a.html(o), e.render && e.render(n), n.hide().width('').width(n.width()).css('max-width', e.maxW); var i = t.is('area'); if (i) { var r, s = [], d = [], c = t.attr('coords').split(','); function h(t, e) { return t - e } for (r = 0; r < c.length; r++)s.push(c[r++]), d.push(c[r]); var f = t.parent().attr('name'), l = $('img[usemap=\\#' + f + ']').offset(), p = parseInt(l.left, 10) + parseInt((parseInt(s.sort(h)[0], 10) + parseInt(s.sort(h)[s.length - 1], 10)) / 2, 10), u = parseInt(l.top, 10) + parseInt((parseInt(d.sort(h)[0], 10) + parseInt(d.sort(h)[d.length - 1], 10)) / 2, 10) } else u = parseInt(t.offset().top, 10), p = parseInt(t.offset().left, 10); var m = i ? 0 : parseInt(t.outerWidth(), 10), I = i ? 0 : parseInt(t.outerHeight(), 10), v = n.outerWidth(), w = n.outerHeight(), g = Math.round(p + Math.round((m - v) / 2)), T = Math.round(u + I + e.offset + 8), b = Math.round(v - 16) / 2 - parseInt(n.css('borderLeftWidth'), 10), y = 0, H = p + m + v + e.offset + 8 > parseInt($(window).width(), 10), W = v + e.offset + 8 > p, k = w + e.offset + 8 > u - $(window).scrollTop(), M = u + I + w + e.offset + 8 > parseInt($(window).height() + $(window).scrollTop(), 10), x = e.anchor; W || 'e' == e.anchor && !H ? 'w' != e.anchor && 'e' != e.anchor || (x = 'e', y = Math.round(w / 2 - 8 - parseInt(n.css('borderRightWidth'), 10)), b = -8 - parseInt(n.css('borderRightWidth'), 10), g = p + m + e.offset + 8, T = Math.round(u + I / 2 - w / 2)) : (H || 'w' == e.anchor && !W) && ('w' != e.anchor && 'e' != e.anchor || (x = 'w', y = Math.round(w / 2 - 8 - parseInt(n.css('borderLeftWidth'), 10)), b = v - parseInt(n.css('borderLeftWidth'), 10), g = p - v - e.offset - 8, T = Math.round(u + I / 2 - w / 2))), M || 'n' == e.anchor && !k ? 'n' != e.anchor && 's' != e.anchor || (x = 'n', y = w - parseInt(n.css('borderTopWidth'), 10), T = u - (w + e.offset + 8)) : (k || 's' == e.anchor && !M) && ('n' != e.anchor && 's' != e.anchor || (x = 's', y = -8 - parseInt(n.css('borderBottomWidth'), 10), T = u + I + e.offset + 8)), 'n' == e.anchor || 's' == e.anchor ? v / 2 > p ? (g = g < 0 ? b + g : b, b = 0) : p + v / 2 > parseInt($(window).width(), 10) && (g -= b, b *= 2) : k ? (T += y, y = 0) : M && (T -= y, y *= 2), delay && clearTimeout(delay), delay = setTimeout(function () { n.css({ 'margin-left': g + 'px', 'margin-top': T + 'px' }).stop(!0, !0).fadeIn(e.fadeIn) }, e.delay), n.attr('class', 'sb-tooltip ' + x) }, d = function () { (!e.aHide && !i || e.aHide) && (delay && clearTimeout(delay), delay = setTimeout(function () { c() }, e.delay)) }, c = function () { !e.aHide && !i || e.aHide ? (n.stop(!0, !0).fadeOut(e.fadeOut), e.hide && e.hide.call(this)) : setTimeout(function () { d() }, 200) } } }) };

    /*
    * ----------------------------------------------------------
    * # Functions
    * ----------------------------------------------------------
    */

    // Language switcher
    $.fn.sbLanguageSwitcher = function (langs = [], source = '', active_language = false) {
        let code = `<div class="sb-language-switcher" data-source="${source}">`;
        let added = [];
        let element = $(this).hasClass('sb-language-switcher-cnt') ? $(this) : $(this).find('.sb-language-switcher-cnt');
        for (var i = 0; i < langs.length; i++) {
            if (added.includes(langs[i])) continue;
            code += `<span ${active_language == langs[i] ? 'class="sb-active" ' : ''}data-language="${langs[i]}"><i class="sb-icon-close"></i><img src="${SB_URL}/media/flags/languages/${langs[i].toLowerCase()}.png" /></span>`;
            added.push(langs[i]);
        }
        element.find('.sb-language-switcher').remove();
        element.append(code + `<i data-sb-tooltip="${sb_('Add translation')}" class="sb-icon-plus"></i></div>`);
        element.sbInitTooltips();
        return this;
    }

    // Lightbox
    $.fn.sbShowLightbox = function (popup = false, action = '') {
        admin.find('.sb-lightbox').sbActivate(false);
        overlay.sbActivate();
        $(this).sbActivate();
        if (popup) {
            $(this).addClass('sb-popup-lightbox').attr('data-action', action);
        } else {
            $(this).css({ 'margin-top': ($(this).outerHeight() / -2) + 'px', 'margin-left': ($(this).outerWidth() / -2) + 'px' })
        }
        $('body').addClass('sb-lightbox-active');
        setTimeout(() => { SBAdmin.open_popup = this }, 500);
        this.preventDefault;
        return this;
    }

    $.fn.sbHideLightbox = function () {
        $(this).find('.sb-lightbox,.sb-popup-lightbox').sbActivate(false).removeClass('sb-popup-lightbox').removeAttr('data-action');
        overlay.sbActivate(false);
        $('body').removeClass('sb-lightbox-active');
        SBAdmin.open_popup = false;
        return this;
    }

    //Tooltip init
    $.fn.sbInitTooltips = function () {
        $(this).find('[data-sb-tooltip]').each(function () {
            $(this).miniTip({
                content: $(this).attr('data-sb-tooltip'),
                anchor: 's',
                delay: 500
            });
        });
        return this;
    }

    // Update user and agent activity status
    function updateUsersActivity() {
        SBF.updateUsersActivity(agent_online ? SB_ACTIVE_AGENT['id'] : -1, activeUser() ? activeUser().id : -1, function (response) {
            if (response == 'online') {
                if (!SBChat.user_online) {
                    conversations_area.find('.sb-conversation .sb-top > .sb-labels').prepend(`<span class="sb-status-online">${sb_('Online')}</span>`);
                    SBChat.user_online = true;
                }
            } else {
                conversations_area.find('.sb-conversation .sb-top .sb-status-online').remove();
                SBChat.user_online = false;
            }
        });
    }

    // Display the bottom card information box
    function showResponse(text, type) {
        var card = admin.find('.sb-info-card');
        if (SBF.null(type)) {
            card.removeClass('sb-info-card-error sb-info-card-warning sb-info-card-info');
            clearTimeout(timeout);
            timeout = setTimeout(() => { card.sbActivate(false) }, 5000);
        } else if (!SBF.null(type) && type == 'error') {
            card.addClass('sb-info-card-error');
        } else {
            card.addClass('sb-info-card-info');
        }
        card.html(`<h3>${sb_(text)}</h3>`).sbActivate();
    }

    // Access the global user variable
    function activeUser(value) {
        if (typeof value == ND) {
            return window.sb_current_user;
        } else {
            window.sb_current_user = value;
        }
    }

    // Show alert and information lightbox
    function dialog(text, type, onConfirm = false, id = '', title = '') {
        let box = admin.find('.sb-dialog-box').attr('data-type', type);
        box.attr('id', id);
        box.find('.sb-title').html(title);
        box.find('p').html((type == 'alert' ? sb_('Are you sure?') + ' ' : '') + sb_(text));
        box.sbActivate().css({ 'margin-top': (box.outerHeight() / -2) + 'px', 'margin-left': (box.outerWidth() / -2) + 'px' });
        overlay.sbActivate();
        alertOnConfirmation = onConfirm;
        setTimeout(() => { SBAdmin.open_popup = box }, 500);
    }

    // Loading box
    function loadingGlobal(show = true, is_overlay = true) {
        admin.find('.sb-loading-global').sbActivate(show);
        if (is_overlay) {
            overlay.sbActivate(show);
            $('body').setClass('sb-lightbox-active', show);
        }
    }

    // Check if an elemen is loading and set it status to loading
    function loading(element) {
        if ($(element).sbLoading()) return true;
        else $(element).sbLoading(true);
        return false;
    }

    // Support Board js translations
    function sb_(text) {
        if (SB_TRANSLATIONS != false && !SBF.null(SB_TRANSLATIONS[text])) {
            return SB_TRANSLATIONS[text];
        } else {
            return text;
        }
    }

    // PWA functions
    function isPWA() {
        return (window.matchMedia('(display-mode: standalone)').matches) || (window.navigator.standalone) || document.referrer.includes('android-app://');
    }

    function clearCache() {
        if (typeof caches !== ND) caches.delete('sb-pwa-cache');
    }

    // Collapse
    function collapse(target, max_height) {
        target = $(target);
        let content = target.find(' > div, > ul');
        content.css({ 'height': '', 'max-height': '' });
        if (target.hasClass('sb-collapse') && $(content).prop('scrollHeight') > max_height) {
            target.sbActivate().attr('data-height', max_height);
            target.find('.sb-collapse-btn').remove();
            target.append(`<a class="sb-btn-text sb-collapse-btn">${sb_('View more')}</a>`);
            content.css({ 'height': max_height + 'px', 'max-height': max_height + 'px' });
        };
    }

    function searchInput(input, searchFunction) {
        let icon = $(input).parent().find('i');
        let search = $(input).val();
        if (!icon.sbLoading()) {
            SBF.search(search, () => {
                icon.sbLoading(true);
                searchFunction(search, icon);
            });
        };
    }

    function scrollPagination(area, check = false) {
        if (check) return $(area).scrollTop() + $(area).innerHeight() >= $(area)[0].scrollHeight;
        $(area).scrollTop($(area)[0].scrollHeight);
    }

    // Save a browser history state
    function pushState(url_parameters) {
        if (wp_admin) return;
        window.history.pushState('', '', url_parameters);
    }

    /*
    * ----------------------------------------------------------
    * # Apps
    * ----------------------------------------------------------
    */

    var SBApps = {

        dialogflow: {
            intents: false,
            token: SBF.storage('dialogflow-token'),
            smart_reply_data: false,

            smartReply: function (message) {
                SBF.ajax({
                    function: 'dialogflow-smart-reply',
                    message: message,
                    smart_reply_data: this.smart_reply_data ? this.smart_reply_data : { 'conversation_id': SBChat.conversation.id },
                    token: this.token,
                    dialogflow_language: [activeUser().language],
                    language_detection: SB_ADMIN_SETTINGS['smart-reply-language-detection'] && SBChat.conversation && !SBChat.conversation.getLastUserMessage(false, true) && (!SB_ADMIN_SETTINGS['smart-reply-language-detection-bot'] || !SBChat.conversation.getLastUserMessage(false, 'bot'))
                }, (response) => {
                    let suggestions = response['suggestions'];
                    let code = '';
                    let area = conversations_area.find('.sb-conversation .sb-list');
                    let is_bottom = area[0].scrollTop === (area[0].scrollHeight - area[0].offsetHeight);
                    if (response['token'] && this.token != response['token']) {
                        this.token = response['token'];
                        SBF.storage('dialogflow-token', response['token']);
                    }
                    for (var i = 0; i < suggestions.length; i++) {
                        code += `<span>${suggestions[i]}</span>`;
                    }
                    this.smart_reply_data = response['smart_reply'];
                    suggestions_area.html(code);
                    if (is_bottom) SBChat.scrollBottom();
                });
            },

            smartReplyUpdate: function (message, user_type = 'agent') {
                SBF.ajax({
                    function: 'dialogflow-smart-reply-update',
                    message: message,
                    smart_reply_data: this.smart_reply_data ? this.smart_reply_data : { 'conversation_id': SBChat.conversation.id },
                    token: this.token,
                    dialogflow_language: [activeUser().language],
                    user_type: user_type
                });
            },

            showCreateIntentBox: function (message_id) {
                let expression = '';
                let message = SBChat.conversation.getMessage(message_id);
                let response = message.message;
                if (SBF.isAgent(message.get('user_type'))) {
                    expression = SBChat.conversation.getLastUserMessage(message.get('index'));
                    if (expression && expression.payload('sb-human-takeover')) expression = SBChat.conversation.getLastUserMessage(expression.get('index'));
                    if (expression == false) {
                        expression = '';
                    } else {
                        expression = expression.message;
                    }
                } else {
                    expression = response;
                    response = '';
                }
                if (this.intents === false) {
                    SBF.ajax({ function: 'dialogflow-get-intents' }, (response) => {
                        let code = '';
                        if (Array.isArray(response)) {
                            for (var i = 0; i < response.length; i++) {
                                code += `<option value="${response[i].name}">${response[i].displayName}</option>`;
                            }
                            dialogflow_intent_box.find('#sb-intents-select').append(code);
                            this.intents = response;
                        } else this.intents = [];
                    });
                }
                dialogflow_intent_box.attr('data-message-id', message.id);
                dialogflow_intent_box.find('.sb-type-text:not(.sb-first)').remove();
                dialogflow_intent_box.find('.sb-type-text input').val(expression);
                dialogflow_intent_box.find('textarea').val(response);
                dialogflow_intent_box.sbShowLightbox();
            },

            submitIntent: function (button) {
                if (loading(button)) return;
                let expressions = [];
                let response = dialogflow_intent_box.find('textarea').val();
                let intent_name = dialogflow_intent_box.find('#sb-intents-select').val();
                dialogflow_intent_box.find('.sb-type-text input').each(function () {
                    if ($(this).val() != '') {
                        expressions.push($(this).val());
                    }
                });
                if ((response == '' && !intent_name) || expressions.length == 0) {
                    SBForm.showErrorMessage(dialogflow_intent_box, 'Please insert the bot response and at least one user expression.');
                    $(button).sbLoading(false);
                } else {
                   
                    SBF.ajax({
                        function: intent_name == '' ? 'dialogflow-create-intent' : 'dialogflow-update-intent',
                        expressions: expressions,
                        response: response,
                        agent_language: dialogflow_intent_box.find('.sb-dialogflow-languages select').val(),
                        conversation_id: SBChat.conversation.id,
                        intent_name: intent_name
                    }, (response) => {
                        $(button).sbLoading(false);
                        if (response === true) {
                            admin.sbHideLightbox();
                            showResponse(intent_name == '' ? 'Intent created' : 'Intent updated');
                        } else {
                            let message = 'Error';
                            if ('error' in response && 'message' in response['error']) {
                                message = response['error']['message'];
                            }
                            SBForm.showErrorMessage(dialogflow_intent_box, message);
                        }
                    });
                }
            },

            searchIntents: function (search) {
                search = search.toLowerCase();
                SBF.search(search, () => {
                    let code = `<option value="">${sb_('New Intent')}</option>`;
                    let all = search.length > 1 ? false : true;
                    let intents = this.intents;
                    for (var i = 0; i < intents.length; i++) {
                        let found = all || intents[i].displayName.toLowerCase().includes(search);
                        if (!found && 'trainingPhrases' in intents[i]) {
                            let training_phrases = intents[i].trainingPhrases;
                            for (var j = 0; j < training_phrases.length; j++) {
                                for (var y = 0; y < training_phrases[j].parts.length; y++) {
                                    if (training_phrases[j].parts[y].text.toLowerCase().includes(search)) {
                                        found = true;
                                        break;
                                    }
                                }
                                if (found) break;
                            }
                        }
                        if (found) {
                            code += `<option value="${intents[i].name}">${intents[i].displayName}</option>`;
                        }
                    }
                    dialogflow_intent_box.find('#sb-intents-select').html(code);
                });
            },

            previewIntent: function (name) {
                let code = '';
                for (var i = 0; i < this.intents.length; i++) {
                    if (this.intents[i].name == name) {
                        let training_phrases = 'trainingPhrases' in this.intents[i] ? this.intents[i].trainingPhrases : [];
                        let count = training_phrases.length;
                        if (count > 1) {
                            for (var j = 0; j < count; j++) {
                                for (var y = 0; y < training_phrases[j].parts.length; y++) {
                                    code += `<span>${training_phrases[j].parts[y].text}</span>`;
                                    if (y == 15) break;
                                }
                            }
                            dialog(code, 'info', false, 'intent-preview-box');
                        }
                        break;
                    }
                }
            },

            translate: function (strings, language_code, onSuccess) {
                if (strings.length) {
                    SBF.ajax({
                        function: 'google-translate',
                        strings: strings,
                        language_code: language_code,
                        token: this.token
                    }, (response) => {
                        this.token = response[1]
                        if ('data' in response[0] && 'translations' in response[0].data) {
                            onSuccess(response[0].data.translations);
                        } else {
                            SBF.error(JSON.stringify(response[0]), 'SBApps.dialogflow.translate');
                            return false;
                        }
                    });
                }
            }
        },

        messenger: {

            check: function (conversation) {
                return conversation.get('source') == 'fb';
            },

            send: function (PSID, facebook_page_id, message, attachments = [], onSuccess = false) {
                SBF.ajax({
                    function: 'messenger-send-message',
                    psid: PSID,
                    facebook_page_id: facebook_page_id,
                    message: message,
                    attachments: attachments
                }, (response) => {
                    if (onSuccess) onSuccess(response);
                });
            }
        },

        whatsapp: {

            check: function (conversation) {
                return conversation.get('source') == 'wa';
            },

            send: function (to, message, attachments = [], onSuccess = false) {
                SBF.ajax({
                    function: 'whatsapp-send-message',
                    to: to,
                    message: message,
                    attachments: attachments
                }, (response) => {
                    if (onSuccess) onSuccess(response);
                });
            },

            activeUserPhone: function () {
                return activeUser().getExtra('phone') ? activeUser().getExtra('phone').value.replace('+', '') : false
            }
        },

        aecommerce: {

            panel: false,

            conversationPanel: function () {
                let code = '';
                let aecommerce_id = activeUser().getExtra('aecommerce-id');
                if (!this.panel) this.panel = conversations_area.find('.sb-panel-aecommerce');
                if (!loading(this.panel) && aecommerce_id != '') {
                    SBF.ajax({
                        function: 'aecommerce-get-conversation-details',
                        aecommerce_id: aecommerce_id['value']
                    }, (response) => {
                        code = `<h3>${SB_ADMIN_SETTINGS['aecommerce-panel-title']}</h3><div><div class="sb-split"><div><div class="sb-title">${sb_('Number of orders')}</div><span>${response['orders_count']} ${sb_('orders')}</span></div><div><div class="sb-title">${sb_('Total spend')}</div><span>${response['currency_symbol']}${response['total']}</span></div></div><div class="sb-title">${sb_('Cart')}</div><div class="sb-list-items sb-list-links sb-aecommerce-cart">`;
                        for (var i = 0; i < response['cart'].length; i++) {
                            let product = response['cart'][i];
                            code += `<a href="${product['url']}" target="_blank" data-id="${product['id']}"><span>#${product['id']}</span> <span>${product['name']}</span> <span>x ${product['quantity']}</span></a>`;
                        }
                        code += (response['cart'].length ? '' : '<p>' + sb_('The cart is currently empty.') + '</p>') + '</div>';
                        if (response['orders'].length) {
                            code += `<div class="sb-title">${sb_('Orders')}</div><div class="sb-list-items sb-list-links sb-aecommerce-orders">`;
                            for (var i = 0; i < response['orders'].length; i++) {
                                let order = response['orders'][i];
                                let id = order['id'];
                                code += `<a data-id="${id}" href="${order['url']}" target="_blank"><span>#${order['id']}</span> <span>${SBF.beautifyTime(order['time'], true)}</span> <span>${response['currency_symbol']}${order['price']}</span></a>`;
                            }
                            code += '</div>';
                        }
                        $(this.panel).html(code).sbLoading(false);
                        collapse(this.panel, 160);
                    });
                }
                $(this.panel).html(code);
            }
        },

        whmcs: {

            panel: false,

            conversationPanel: function () {
                let code = '';
                let whmcs_id = activeUser().getExtra('whmcs-id');
                if (!this.panel) this.panel = conversations_area.find('.sb-panel-whmcs');
                if (!loading(this.panel) && whmcs_id != '') {
                    SBF.ajax({
                        function: 'whmcs-get-conversation-details',
                        whmcs_id: whmcs_id['value']
                    }, (response) => {
                        let services = ['products', 'addons', 'domains'];
                        code = `<h3>WHMCS</h3><div><div class="sb-split"><div><div class="sb-title">${sb_('Number of services')}</div><span>${response['services_count']} ${sb_('services')}</span></div><div><div class="sb-title">${sb_('Total spend')}</div><span>${response['currency_symbol']}${response['total']}</span></div></div>`;
                        code += `</div>`;
                        for (var i = 0; i < services.length; i++) {
                            let items = response[services[i]];
                            if (items.length) {
                                code += `<div class="sb-title">${sb_(SBF.slugToString(services[i]))}</div><div class="sb-list-items">`;
                                for (var j = 0; j < items.length; j++) {
                                    code += `<div>${items[j]['name']}</div>`;
                                }
                                code += '</div>';
                            }
                        }
                        code += `<a href="${SB_ADMIN_SETTINGS['whmcs-url']}/clientssummary.php?userid=${response['client-id']}" target="_blank" class="sb-btn sb-whmcs-link">${sb_('View on WHMCS')}</a>`;
                        $(this.panel).html(code).sbLoading(false);
                        collapse(this.panel, 160);
                    });
                }
                $(this.panel).html(code);
            }
        },

        perfex: {

            conversationPanel: function () {
                let perfex_id = activeUser().getExtra('perfex-id');
                conversations_area.find('.sb-panel-perfex').html(perfex_id == '' ? '' : `<a href="${SB_ADMIN_SETTINGS['perfex-url']}/admin/clients/client/${perfex_id['value']}" target="_blank" class="sb-btn sb-perfex-link">${sb_('View on Perfex')}</a>`);
            }
        },

        ump: {

            panel: false,

            conversationPanel: function () {
                if (loading(this.panel)) return;
                if (!this.panel) this.panel = conversations_area.find('.sb-panel-ump');
                let code = '';
                let subscriptions;
                SBF.ajax({
                    function: 'ump-get-conversation-details'
                }, (response) => {
                    subscriptions = response['subscriptions'];
                    if (subscriptions.length) {
                        code = '<i class="sb-icon-refresh"></i><h3>Membership</h3><div class="sb-list-names">';
                        for (var i = 0; i < subscriptions.length; i++) {
                            let expired = subscriptions[i]['expired'];
                            code += `<div${expired ? ' class="sb-expired"' : ''}><span>${subscriptions[i]['label']}</span><span>${sb_(expired ? 'Expired on' : 'Expires on')} ${SBF.beautifyTime(subscriptions[i]['expire_time'], false, !expired)}</span></div>`;
                        }
                        code += `</div><span class="sb-title">${sb_('Total spend')} ${response['currency_symbol']}${response['total']}</span>`;
                    }
                    $(this.panel).html(code).sbLoading(false);
                    collapse(this.panel, 160);
                });
            }
        },

        armember: {

            panel: false,

            conversationPanel: function () {
                let wp_user_id = activeUser().getExtra('wp-id');
                if (!this.panel) this.panel = conversations_area.find('.sb-panel-armember');
                if (!loading(this.panel) && !SBF.null(wp_user_id)) {
                    let code = '';
                    let subscriptions;
                    wp_user_id = wp_user_id.value;
                    SBF.ajax({
                        function: 'armember-get-conversation-details',
                        wp_user_id: wp_user_id
                    }, (response) => {
                        subscriptions = response['subscriptions'];
                        if (subscriptions.length) {
                            code = '<i class="sb-icon-refresh"></i><h3>Plans</h3><div class="sb-list-names">';
                            for (var i = 0; i < subscriptions.length; i++) {
                                let expired = subscriptions[i]['expired'];
                                code += `<div${expired ? ' class="sb-expired"' : ''}><span>${subscriptions[i].arm_current_plan_detail.arm_subscription_plan_name}</span><span>${subscriptions[i]['expire_time'] == 'never' ? '' : (sb_(expired ? 'Expired on' : 'Expires on') + ' ' + SBF.beautifyTime(subscriptions[i]['expire_time'], false, !expired))}</span></div>`;
                            }
                            code += `</div><span class="sb-title">${sb_('Total spend')} ${response['currency_symbol']}${response['total']}<a href="${window.location.href.substr(0, window.location.href.lastIndexOf('/')) + '?page=arm_manage_members&member_id=' + activeUser().getExtra('wp-id').value}" target="_blank" class="sb-btn-text"><i class="sb-icon-user"></i> ${sb_('View member')}</a></span>`;
                        }
                        $(this.panel).html(code).sbLoading(false);
                        collapse(this.panel, 160);
                    });
                } else $(this.panel).html('');
            }
        },

        woocommerce: {

            popupPaginationNumber: 1,
            popupLanguage: '',
            popupCache: [],
            panel: false,
            timeout: false,

            // Products popup
            popupCode: function (items, label = true) {
                let code = '';
                for (var i = 0; i < items.length; i++) {
                    code += `<li data-id="${items[i]['id']}"><div class="sb-image" style="background-image:url('${items[i]['image']}')"></div><div><span>${items[i]['name']}</span><span>${SB_ADMIN_SETTINGS['currency']}${items[i]['price']}</span></div></li>`;
                }
                return label ? (code == '' ? `<p>${sb_('No products found')}</p>` : code) : code;
            },

            popupSearch: function (input) {
                searchInput(input, (search, icon) => {
                    if (search == '') {
                        this.popupPopulate(function () {
                            $(icon).sbLoading(false);
                        });
                    } else {
                        this.popupPaginationNumber = 1;
                        SBF.ajax({
                            function: 'woocommerce-search-products',
                            search: search
                        }, (response) => {
                            woocommerce_products_box_ul.html(this.popupCode(response));
                            $(icon).sbLoading(false);
                        });
                    }
                });
            },

            popupFilter: function (item) {
                if (loading(woocommerce_products_box_ul)) return;
                woocommerce_products_box_ul.html('');
                this.popupPaginationNumber = 1;
                SBF.ajax({
                    function: 'woocommerce-get-products',
                    user_language: this.popupLanguage,
                    filters: { taxonomy: $(item).data('value') }
                }, (response) => {
                    woocommerce_products_box_ul.html(this.popupCode(response)).sbLoading(false);
                });
            },

            popupPopulate: function (onSuccess = false) {
                this.popupLanguage = activeUser() != false && SB_ADMIN_SETTINGS['languages'].includes(activeUser().language) ? activeUser().language : '';
                this.popupPaginationNumber = 1;
                woocommerce_products_box_ul.html('').sbLoading(true);
                SBF.ajax({
                    function: 'woocommerce-products-popup',
                    user_language: this.popupLanguage
                }, (response) => {
                    let code = '';
                    let select = woocommerce_products_box.find('.sb-select');
                    for (var i = 0; i < response[1].length; i++) {
                        code += `<li data-value="${response[1][i]['id']}">${response[1][i]['name']}</li>`;
                    }
                    select.find('> p').html(sb_('All'));
                    select.find('ul').html(`<li data-value="" class="sb-active">${sb_('All')}</li>` + code);
                    woocommerce_products_box_ul.html(this.popupCode(response[0])).sbLoading(false);
                    if (onSuccess !== false) onSuccess();
                });
            },

            popupPagination: function (area) {
                woocommerce_products_box_ul.sbLoading(area);
                SBF.ajax({
                    function: 'woocommerce-get-products',
                    filters: { taxonomy: $(area).parent().find('.sb-select p').attr('data-value') },
                    pagination: this.popupPaginationNumber,
                    user_language: this.popupLanguage
                }, (response) => {
                    woocommerce_products_box_ul.append(this.popupCode(response, false)).sbLoading(false);
                    this.popupPaginationNumber++;
                    scrollPagination(area);
                });
            },

            // Conversation panel
            conversationPanel: function () {
                if (loading(this.panel)) return;
                if (!this.panel) this.panel = conversations_area.find('.sb-panel-woocommerce');
                let code = '';
                SBF.ajax({
                    function: 'woocommerce-get-conversation-details'
                }, (response) => {
                    code = `<i class="sb-icon-refresh"></i><h3>WooCommerce</h3><div><div class="sb-split"><div><div class="sb-title">${sb_('Number of orders')}</div><span>${response['orders_count']} ${sb_('orders')}</span></div><div><div class="sb-title">${sb_('Total spend')}</div><span>${response['currency_symbol']}${response['total']}</span></div></div><div class="sb-title">${sb_('Cart')}<i class="sb-add-cart-btn sb-icon-plus"></i></div><div class="sb-list-items sb-list-links sb-woocommerce-cart">`;
                    for (var i = 0; i < response['cart'].length; i++) {
                        let product = response['cart'][i];
                        code += `<a href="${product['url']}" target="_blank" data-id="${product['id']}"><span>#${product['id']}</span> <span>${product['name']}</span> <span>x ${product['quantity']}</span><i class="sb-icon-close"></i></a>`;
                    }
                    code += (response['cart'].length ? '' : '<p>' + sb_('The cart is currently empty.') + '</p>') + '</div>';
                    if (response['orders'].length) {
                        code += `<div class="sb-title">${sb_('Orders')}</div><div class="sb-list-items sb-woocommerce-orders sb-accordion">`;
                        for (var i = 0; i < response['orders'].length; i++) {
                            let order = response['orders'][i];
                            let id = order['id'];
                            code += `<div data-id="${id}"><span><span>#${id}</span> <span>${SBF.beautifyTime(order['date'], true)}</span><a href="${SITE_URL}/wp-admin/post.php?post=${id}&action=edit" target="_blank" class="sb-icon-next"></a></span><div></div></div>`;
                        }
                        code += '</div>';
                    }
                    $(this.panel).html(code).sbLoading(false);
                    collapse(this.panel, 160);
                });
            },


            // Conversation panel order details
            conversationPanelOrder: function (order_id) {
                let accordion = this.panel.find(`[data-id="${order_id}"] > div`);
                accordion.html('');
                SBF.ajax({
                    function: 'woocommerce-get-order',
                    order_id: order_id
                }, (response) => {
                    let code = '';
                    let collapse = this.panel.find('.sb-collapse-btn:not(.sb-active)');
                    if (response) {
                        let products = response['products'];
                        code += `<div class="sb-title">${sb_('Order total')}: <span>${response['currency_symbol']}${response['total']}<span></div><div class="sb-title">${sb_('Order status')}: <span>${SBF.slugToString(response['status'].replace('wc-', ''))}<span></div><div class="sb-title">${sb_('Date')}: <span>${SBF.beautifyTime(response['date'], true)}<span></div><div class="sb-title">${sb_('Products')}</div>`;
                        for (var i = 0; i < products.length; i++) {
                            code += `<a href="${SITE_URL}?p=${products[i]['id']}" target="_blank"><span>#${products[i]['id']}</span> <span>${products[i]['quantity']} x</span> <span>${products[i]['name']}</span></a>`;
                        }
                        for (var i = 0; i < 2; i++) {
                            let key = i == 0 ? 'shipping' : 'billing';
                            if (response[key + '_address'] != '') {
                                code += `<div class="sb-title">${sb_((i == 0 ? 'Shipping' : 'Billing') + ' address')}</div><div class="sb-multiline">${response[key + '_address'].replace(/\\n/g, '<br>')}</div>`;
                            }
                        }
                    }
                    if (collapse.length) {
                        collapse.click();
                    }
                    accordion.html(code);
                });
            },

            conversationPanelUpdate: function (product_id, action = 'added') {
                let busy = false;
                let count = 0;
                this.timeout = setInterval(() => {
                    if (!busy) {
                        SBF.ajax({
                            function: 'woocommerce-get-conversation-details'
                        }, (response) => {
                            let removed = true;
                            for (var i = 0; i < response['cart'].length; i++) {
                                if (response['cart'][i]['id'] == product_id) {
                                    if (action == 'added') count = 61; else removed = false;
                                }
                            }
                            if (count > 60 || removed) {
                                this.conversationPanel();
                                conversations_area.find('.sb-add-cart-btn,.sb-woocommerce-cart > a i').sbLoading(false);
                                clearInterval(this.timeout);
                            }
                            count++;
                            busy = false;
                        });
                        busy = true;
                    }
                }, 1000);
            }
        },

        wordpress: {
            ajax: function (action, data, onSuccess) {
                $.ajax({
                    method: 'POST',
                    url: SB_WP_AJAX_URL,
                    data: $.extend({ action: 'sb_wp_ajax', type: action }, data)
                }).done((response) => {
                    if (onSuccess !== false) {
                        onSuccess(response);
                    }
                });
            }
        },

        is: function (name) {
            if (typeof SB_VERSIONS == ND) return false;
            switch (name) {
                case 'armember':
                case 'aecommerce':
                case 'whmcs':
                case 'perfex':
                case 'ump':
                case 'messenger':
                case 'woocommerce':
                case 'dialogflow':
                case 'slack':
                case 'tickets': return typeof SB_VERSIONS[name] != ND && SB_VERSIONS[name] != -1;
                case 'wordpress': return typeof SB_WP != ND;
                case 'sb': return true;
            }
            return false;
        },
    }

    /*
    * ----------------------------------------------------------
    * # Settings
    * ----------------------------------------------------------
    */

    var SBSettings = {
        init: false,

        save: function (btn) {
            if (loading(btn)) return;
            let external_settings = {};
            let settings = {};
            let tab = settings_area.find(' > .sb-tab > .sb-nav .sb-active').attr('id');
            switch (tab) {
                case 'tab-articles':
                    this.articles.save((response) => {
                        showResponse(response === true ? 'Articles and categories saved' : response);
                        $(btn).sbLoading(false);
                    });
                    break;
                case 'tab-automations':
                    let active = automations_area_nav.find('.sb-active').attr('data-id');
                    SBSettings.automations.save((response) => {
                        showResponse(response === true ? 'Automations saved' : response);
                        SBSettings.automations.populate();
                        automations_area_nav.find(`[data-id="${active}"]`).click();
                        $(btn).sbLoading(false);
                    });
                    break;
                case 'tab-translations':
                    this.translations.updateActive();
                    SBF.ajax({
                        function: 'save-translations',
                        translations: JSON.stringify(this.translations.to_update)
                    }, () => {
                        showResponse('Translations saved');
                        $(btn).sbLoading(false);
                    });
                    break;
                default:
                    settings_area.find('.sb-setting').each((i, element) => {
                        let setting = this.get(element);
                        let setting_id = $(element).data('setting');
                        if (setting[0] != '') {
                            if (typeof setting_id != ND) {
                                let originals = false;
                                if ($(element).find('[data-language]').length) {
                                    let language = $(element).find('[data-language].sb-active');
                                    originals = setting[0] in this.translations.originals ? this.translations.originals[setting[0]] : false;
                                    this.translations.save(element, language.length ? language.attr('data-language') : false);
                                    if (originals) {
                                        if (typeof originals != 'string') {
                                            for (var key in originals) {
                                                originals[key] = [originals[key], setting[1][key][1]]
                                            }
                                        }
                                    }
                                }
                                if (!(setting_id in external_settings)) external_settings[setting_id] = {};
                                external_settings[setting_id][setting[0]] = [originals ? originals : setting[1], setting[2]];
                            } else {
                                settings[setting[0]] = [setting[1], setting[2]];
                            }
                        }
                    });
                    SBF.ajax({
                        function: 'save-settings',
                        settings: settings,
                        external_settings: external_settings,
                        external_settings_translations: this.translations.translations
                    }, () => {
                        showResponse('Settings saved');
                        $(btn).sbLoading(false);
                    });
                    break;
            }
        },

        initPlugins: function () {
            settings_area.find('textarea').each(function () {
                $(this).autoExpandTextarea();
                $(this).manualExpandTextarea();
            });
            settings_area.find('[data-setting] .sb-language-switcher-cnt').each(function () {
                $(this).sbLanguageSwitcher(SBSettings.translations.getLanguageCodes($(this).closest('[data-setting]').attr('id')), 'settings');
            });
        },

        initColorPicker: function (area = false) {
            $(area ? area : settings_area).find('.sb-type-color input').colorPicker({
                renderCallback: function (t, toggled) {
                    $(t.context).closest('.input').find('input').css('background-color', t.text);
                }
            });
        },

        initHTML: function (response) {
            if ('slack-agents' in response) {
                let code = '';
                for (var key in response['slack-agents'][0]) {
                    code += `<div data-id="${key}"><select><option value="${response['slack-agents'][0][key]}"></option></select></div>`;
                }
                settings_area.find('#slack-agents .input').html(code);
            }
        },

        get: function (item) {
            item = $(item);
            let id = item.attr('id');
            let type = item.data('type');
            switch (type) {
                case 'upload':
                case 'range':
                case 'number':
                case 'text':
                case 'password':
                case 'color':
                    return [id, item.find('input').val(), type];
                    break;
                case 'textarea':
                    return [id, item.find('textarea').val(), type];
                    break;
                case 'select':
                    return [id, item.find('select').val(), type];
                    break;
                case 'checkbox':
                    return [id, item.find('input').is(':checked'), type];
                    break;
                case 'radio':
                    let value = item.find('input:checked').val();
                    if (SBF.null(value)) value = '';
                    return [id, value, type];
                    break;
                case 'upload-image':
                    let url = item.find('.image').attr('data-value');
                    if (SBF.null(url)) url = '';
                    return [id, url, type];
                    break;
                case 'multi-input':
                    let multi_inputs = {};
                    item.find('.input > div').each((i, element) => {
                        let setting = this.get(element);
                        if (setting[0] != '') {
                            multi_inputs[setting[0]] = [setting[1], setting[2]];
                        }
                    });
                    return [id, multi_inputs, type];
                    break;
                case 'select-images':
                    return [id, item.find('.input > .sb-active').data('value'), type];
                    break;
                case 'repeater':
                    return [id, this.repeater('get', item.find('.repeater-item'), ''), type];
                    break;
                case 'double-select':
                    let selects = {};
                    item.find('.input > div').each(function () {
                        let value = $(this).find('select').val();
                        if (value != -1) {
                            selects[$(this).attr('data-id')] = [value];
                        }
                    });
                    return [id, selects, type];
                    break;
                case 'timetable':
                    let times = {};
                    item.find('.sb-timetable > [data-day]').each(function () {
                        let day = $(this).attr('data-day');
                        let hours = [];
                        $(this).find('> div > div').each(function () {
                            let name = $(this).html()
                            let value = $(this).attr('data-value');
                            if (SBF.null(value)) {
                                hours.push(['', '']);
                            } else if (value == 'closed') {
                                hours.push(['closed', 'Closed']);
                            } else {
                                hours.push([value, name]);
                            }
                        });
                        times[day] = hours;
                    });
                    return [id, times, type];
                    break;
                case 'color-palette':
                    return [id, item.attr('data-value'), type];
                    break;

            }
            return ['', '', ''];
        },

        set: function (id, setting) {
            let type = $(setting)[1];
            let value = $(setting)[0];
            id = `#${id}`;
            switch (type) {
                case 'color':
                case 'upload':
                case 'number':
                case 'text':
                case 'password':
                    settings_area.find(`${id} input`).val(SBF.restoreJson(value));
                    break;
                case 'textarea':
                    settings_area.find(`${id} textarea`).val(SBF.restoreJson(value));
                    break;
                case 'select':
                    settings_area.find(`${id} select`).val(SBF.restoreJson(value));
                    break;
                case 'checkbox':
                    settings_area.find(`${id} input`).prop('checked', (value == 'false' ? false : value));
                    break;
                case 'radio':
                    settings_area.find(`${id} input[value="${SBF.restoreJson(value)}"]`).prop('checked', true);
                    break;
                case 'upload-image':
                    if (value != '') {
                        settings_area.find(id + ' .image').attr('data-value', SBF.restoreJson(value)).css('background-image', `url("${SBF.restoreJson(value)}")`);
                    }
                    break;
                case 'multi-input':
                    for (var key in value) {
                        this.set(key, value[key]);
                    }
                    break;
                case 'range':
                    let range_value = SBF.restoreJson(value);
                    settings_area.find(id + ' input').val(range_value);
                    settings_area.find(id + ' .range-value').html(range_value);
                    break;
                case 'select-images':
                    settings_area.find(id + ' .input > div').sbActivate(false);
                    settings_area.find(id + ` .input > [data-value="${SBF.restoreJson(value)}"]`).sbActivate();
                    break;
                case 'repeater':
                    let content = this.repeater('set', value, settings_area.find(id + ' .repeater-item:last-child'));
                    if (content != '') {
                        settings_area.find(id + ' .sb-repeater').html(content);
                    }
                    break;
                case 'double-select':
                    for (var key in value) {
                        settings_area.find(`${id} .input > [data-id="${key}"] select`).val(value[key]);
                    }
                    break;
                case 'timetable':
                    for (var key in value) {
                        let hours = settings_area.find(`${id} [data-day="${key}"] > div > div`);
                        for (var i = 0; i < hours.length; i++) {
                            $(hours[i]).attr('data-value', value[key][i][0]).html(value[key][i][1]);
                        }
                    }
                    break;
                case 'color-palette':
                    if (value != '') {
                        settings_area.find(id).attr('data-value', value);
                    }
                    break;
            }
        },

        repeater: function (action, items, content) {
            $(content).find('.sb-icon-close').remove();
            content = $(content).html();
            if (action == 'set') {
                var html = '';
                if (items.length > 0) {
                    for (var i = 0; i < items.length; i++) {
                        let item = $($.parseHTML(`<div>${content}</div>`));
                        for (var key in items[i]) {
                            this.setInput(item.find(`[data-id="${key}"]`), items[i][key]);
                        }
                        html += `<div class="repeater-item">${item.html()}<i class="sb-icon-close"></i></div>`;
                    }
                }
                return html;
            }
            if (action == 'get') {
                let items_array = [];
                let me = this;
                $(items).each(function () {
                    let item = {};
                    let empty = true;
                    $(this).find('[data-id]').each(function () {
                        let value = me.getInput(this);
                        if (empty && value != '' && $(this).attr('type') != 'hidden' && $(this).attr('data-type') != 'auto-id') {
                            empty = false;
                        }
                        item[$(this).attr('data-id')] = value;
                    });
                    if (!empty) {
                        items_array.push(item);
                    }
                });
                return items_array;
            }
        },

        repeaterAdd: function (item) {
            let parent = $(item).parent();
            item = $($.parseHTML(`<div>${parent.find('.repeater-item:last-child').html()}</div>`));
            item.find('[data-id]').each(function () {
                SBSettings.resetInput(this);
                if ($(this).data('type') == 'auto-id') {
                    let larger = 1;
                    parent.find('[data-type="auto-id"]').each(function () {
                        let index = parseInt($(this).val());
                        if (index > larger) {
                            larger = index;
                        }
                    });
                    $(this).attr('value', larger + 1);
                }
            });
            parent.find('.sb-repeater').append(`<div class="repeater-item">${item.html()}</div>`);
        },

        repeaterDelete: function (item) {
            let parent = $(item).parent();
            if (parent.parent().find('.repeater-item').length > 1) {
                parent.remove();
            } else {
                parent.find('[data-id]').each((e, element) => {
                    this.resetInput(element);
                });
            }
        },

        setInput: function (input, value) {
            value = $.trim(value);
            input = $(input);
            if (input.is('select')) {
                input.find(`option[value="${value}"]`).attr('selected', '');
            } else {
                if (input.is(':checkbox') && value && value != 'false') {
                    input.attr('checked', '');
                } else {
                    if (input.is('textarea')) {
                        input.html(value);
                    } else {
                        let div = input.is('div');
                        if (div || input.is('i') || input.is('li')) {
                            input.attr('data-value', value);
                            if (div && input.hasClass('image')) {
                                input.css('background-image', `url("${value}")`);
                            }
                        } else {
                            input.attr('value', value);
                        }
                    }
                }
            }
        },

        getInput: function (input) {
            input = $(input);
            if (input.is(':checkbox')) {
                return input.is(':checked');
            } else {
                if (input.is("div") || input.is("i") || input.is("li")) {
                    let value = input.attr('data-value');
                    return SBF.null(value) ? '' : value;
                } else {
                    return input.val();
                }
            }
            return '';
        },

        resetInput: function (input) {
            input = $(input);
            if (input.is("select")) {
                input.val('').find('[selected]').removeAttr('selected');
            } else {
                if (input.is("checkbox") && value) {
                    input.removeAttr('checked').prop('checked', false);
                } else {
                    if (input.is("textarea")) {
                        input.html('');
                    } else {
                        input.removeAttr('value').removeAttr('style').removeAttr('data-value').val('');
                    }
                }
            }
        },

        getSettingObject: function (setting) {
            return $(setting)[0].hasAttribute('data-setting') ? $(setting) : $(setting).closest('[data-setting]');
        },

        articles: {
            categories: [],
            articles: [],
            translations: {},

            get: function (onSuccess, categories = false, full = true) {
                SBF.ajax({
                    function: 'get-articles',
                    categories: categories,
                    articles_language: 'all',
                    full: full
                }, (response) => {
                    onSuccess(response);
                });
            },

            save: function (onSuccess = false) {
                this.updateActiveArticle();
                articles_area.find('.sb-new-category-cnt input').each((i, element) => {
                    let value = $.trim($(element).val());
                    if (value != '') {
                        this.categories.push({ id: SBF.random(), title: value });
                    }
                    $(element).parents().eq(1).remove();
                });
                SBF.ajax({
                    function: 'save-articles',
                    articles: this.articles,
                    translations: this.translations,
                    categories: this.categories.length ? this.categories : 'delete_all'
                }, (response) => {
                    articles_area.find('.sb-menu-wide .sb-active').click();
                    articles_area.find(`.sb-nav [data-id="${this.activeID()}"]`).click();
                    this.updateSelect();
                    if (onSuccess) onSuccess(response);
                });
            },

            show: function (article_id = false, element = false, language = false) {
                let contents = [-1, '', '', '', ''];
                this.updateActiveArticle();
                this.hide(false);
                let articles = language ? (language in this.translations ? this.translations[language] : []) : this.articles;
                if (article_id === false) article_id = this.activeID();
                for (var i = 0; i < articles.length; i++) {
                    if (articles[i]['id'] == article_id) {
                        contents = [article_id, articles[i]['title'], articles[i]['content'], articles[i]['link'], SBF.null(articles[i]['categories']) ? [''] : articles[i]['categories']];
                        if (element) {
                            $(element).siblings().sbActivate(false);
                            $(element).sbActivate();
                        }
                        break;
                    }
                }
                articles_area.sbLanguageSwitcher(this.getTranslations(article_id), 'articles', language);
                articles_area.find('.sb-content').attr('data-id', contents[0]);
                articles_area.find('.sb-article-title input').val(contents[1]);
                articles_area.find('.sb-article-content textarea').val(contents[2]);
                articles_area.find('.sb-article-link input').val(contents[3]);
                articles_area.find('.sb-article-category select').val(contents[4][0]);
                articles_area.find('#sb-article-id').html(`ID <span>${contents[0]}</span>`);
            },

            add: function () {
                let nav = articles_area.find('.sb-nav > ul');
                let id = SBF.random();
                this.updateActiveArticle();
                this.articles.push({ id: id, title: '', content: '', link: '', categories: [] });
                this.hide(false);
                nav.find('.sb-active').sbActivate(false);
                nav.find('.sb-no-results').remove();
                nav.append(`<li class="sb-active" data-id="${id}">${sb_('Article')} ${nav.find('li').length + 1}<i class="sb-icon-delete"></i></li>`);
                articles_area.find('input, textarea, select').val('');
                articles_area.find('.sb-content').attr('data-id', id);
                articles_area.sbLanguageSwitcher([], 'articles');
            },

            addCategory: function () {
                articles_area.find('[data-type="categories"] .sb-no-results').remove();
                articles_area.find('.sb-new-category-cnt').append('<div class="sb-input-setting sb-type-text"><div><input type="text"></div></div>');
            },

            delete: function (element, category = false) {
                let nav = $(element).closest('.sb-nav').find(' > ul');
                this[category ? 'categories' : 'articles'].splice($(element).parent().index(), 1);
                this.hide();
                if (nav.find('li').length > 1) {
                    $(element).parent().remove();
                } else {
                    nav.html(`<li class="sb-no-results">${sb_('No results found.')}</li>`);
                }
            },

            populate: function (items = false, category = false) {
                if (items === false) items = category ? this.categories : this.articles;
                if (Array.isArray(items)) {
                    let code = '';
                    if (category) {
                        this.categories = items;
                    } else {
                        this.articles = items;
                    }
                    if (items.length) {
                        for (var i = 0; i < items.length; i++) {
                            code += `<li data-id="${items[i]['id']}">${category ? '<span>' + items[i]['id'] + '</span>' : ''}${items[i]['title']}<i class="sb-icon-delete${category ? ' sb-category' : ''}"></i></li>`;
                        }
                    } else {
                        code = `<li class="sb-no-results">${sb_('No results found.')}</li>`;
                    }
                    articles_area.find('.sb-add-category,.sb-new-category-cnt').sbActivate(category);
                    articles_area.find('.sb-add-article').sbActivate(!category);
                    articles_area.find('.sb-nav').attr('data-type', category ? 'categories' : 'articles');
                    articles_area.find('.sb-nav > ul').html(code);
                    this.hide();
                } else {
                    SBF.error(items, 'SBSettings.articles.populate');
                }
            },

            updateActiveArticle: function () {
                let id = this.activeID();
                if (id != -1) {
                    let language = articles_area.find(`.sb-language-switcher [data-language].sb-active`).attr('data-language');
                    let articles = language ? (language in this.translations ? this.translations[language] : []) : this.articles;
                    for (var i = 0; i < articles.length; i++) {
                        if (articles[i]['id'] == id) {
                            articles[i] = { id: id, title: articles_area.find('.sb-article-title input').val(), content: articles_area.find('.sb-article-content textarea').val(), link: articles_area.find('.sb-article-link input').val(), categories: [articles_area.find('.sb-article-category select').val()] };
                            if (SBF.null(articles[i].title)) {
                                this.delete(articles_area.find(`.sb-nav [data-id="${id}"] i`));
                            }
                            break;
                        }
                    }
                }
            },

            updateSelect: function () {
                let select = articles_area.find('.sb-article-category select');
                let select_value = select.val();
                let code = '<option value=""></option>';
                for (var i = 0; i < this.categories.length; i++) {
                    code += `<option value="${this.categories[i]['id']}">${this.categories[i]['title']}</option>`;
                }
                select.html(code);
                select.val(select_value);
            },

            addTranslation: function (article_id = false, language) {
                if (article_id === false) article_id = this.activeID();
                if (this.getTranslations(article_id).includes(article_id)) {
                    return console.warn('Article translation already in array.');
                }
                if (!(language in this.translations)) this.translations[language] = [];
                this.translations[language].push({ id: article_id, title: '', content: '', link: '', categories: [] });
            },

            getTranslations: function (article_id = false) {
                let translations = [];
                if (article_id === false) article_id = this.activeID();
                for (var key in this.translations) {
                    let articles = this.translations[key];
                    for (var i = 0; i < articles.length; i++) {
                        if (articles[i]['id'] == article_id) {
                            translations.push(key);
                            break;
                        }
                    }
                }
                return translations;
            },

            deleteTranslation: function (article_id = false, language) {
                if (article_id === false) article_id = this.activeID();
                if (language in this.translations) {
                    let articles = this.translations[language];
                    for (var i = 0; i < articles.length; i++) {
                        if (articles[i]['id'] == article_id) {
                            this.translations[language].splice(i, 1);
                            return true;
                        }
                    }
                }
                return false;
            },

            activeID: function () {
                return articles_area.find('.sb-content').attr('data-id');
            },

            hide: function (hide = true) {
                articles_area.find('.sb-content').setClass('sb-hide', hide);
            }
        },

        automations: {
            items: { messages: [], emails: [], sms: [], popups: [], design: [] },
            translations: {},
            conditions: {
                datetime: ['Date time', ['Is between', 'Is exactly'], 'dd/mm/yyy hh:mm - dd/mm/yyy hh:mm'],
                repeat: ['Repeat', ['Every day', 'Every week', 'Every month', 'Every year']],
                browsing_time: ['Browsing time', [], 'seconds'],
                scroll_position: ['Scroll position', [], 'px'],
                include_urls: ['Include URLs', ['Contains', 'Does not contain', 'Is exactly', 'Is not'], 'URLs parts separated by commas'],
                exclude_urls: ['Exclude URLs', ['Contains', 'Does not contain', 'Is exactly', 'Is not'], 'URLs parts separated by commas'],
                referring: ['Referring URLs', ['Contains', 'Does not contain', 'Is exactly', 'Is not'], 'URLs parts separated by commas'],
                user_type: ['User type', ['Is visitor', 'Is lead', 'Is user', 'Is not visitor', 'Is not lead', 'Is not user']],
                returning_visitor: ['Returning visitor', ['First time visitor', 'Returning visitor']],
                countries: ['Countries', [], 'Country codes separated by commas'],
                languages: ['Languages', [], 'Language codes separated by commas'],
                cities: ['Cities', [], 'Cities separated by commas'],
                custom_variable: ['Custom variable', [], 'variable=value']
            },

            get: function (onSuccess) {
                SBF.ajax({
                    function: 'automations-get'
                }, (response) => {
                    this.items = response[0];
                    this.translations = Array.isArray(response[1]) && !response[1].length ? {} : response[1];
                    onSuccess(response);
                });
            },

            save: function (onSuccess = false) {
                this.updateActiveItem();
                SBF.ajax({
                    function: 'automations-save',
                    automations: this.items,
                    translations: this.translations
                }, (response) => {
                    if (onSuccess) onSuccess(response);
                });
            },

            show: function (id = false, language = false) {
                this.updateActiveItem();
                let items = language ? (language in this.translations ? this.translations[language] : []) : this.items;
                let area = automations_area.find(' > .sb-tab > .sb-content');
                if (id === false) id = this.activeID();
                this.hide(false);
                for (var key in items) {
                    for (var i = 0; i < items[key].length; i++) {
                        let item = items[key][i];
                        let conditions = item['conditions'];
                        if (item['id'] == id) {
                            conditions_area.html('');
                            for (var key in item) {
                                let element = area.find(`[data-id="${key}"]`);
                                if (element.hasClass('image')) {
                                    element.css('background-image', `url(${item[key]})`).attr('data-value', item[key]);
                                    if (item[key] == '') element.removeAttr('data-value');
                                } else if (element.attr('type') == 'checkbox') {
                                    element.prop('checked', item[key]);
                                } else element.val(item[key]);
                            }
                            if (conditions) {
                                for (var key in conditions) {
                                    this.addCondition();
                                    let condition = conditions_area.find(' > div:last-child');
                                    condition.find('select').val(conditions[key][0]);
                                    this.updateCondition(condition.find('select'));
                                    condition.find(' > div').eq(1).find('select,input').val(conditions[key][1]);
                                    if (conditions[key].length > 2) {
                                        condition.find(' > div').eq(2).find('input').val(conditions[key][2]);
                                    }
                                }
                            }
                            conditions_area.parent().setClass('sb-hide', language);
                            area.sbLanguageSwitcher(this.getTranslations(id), 'automations', language);
                            return true;
                        }
                    }
                }
                return false;
            },

            add: function () {
                let id = SBF.random();
                let name = `${sb_('Item')} ${automations_area_nav.find('li:not(.sb-no-results)').length + 1}`;
                this.updateActiveItem();
                this.items[this.activeType()].push(this.itemArray(this.activeType(), id, name));
                this.hide(false);
                automations_area_nav.find('.sb-active').sbActivate(false);
                automations_area_nav.find('.sb-no-results').remove();
                automations_area_nav.append(`<li class="sb-active" data-id="${id}">${name}<i class="sb-icon-delete"></i></li>`);
                automations_area.find('.sb-automation-values').find('input, textarea').val('');
                automations_area.sbLanguageSwitcher([], 'automations');
                conditions_area.html('');
            },

            delete: function (element) {
                this.items[this.activeType()].splice($(element).parent().index(), 1);
                $(element).parent().remove();
                this.hide();
                if (this.items[this.activeType()].length == 0) automations_area_nav.html(`<li class="sb-no-results">${sb_('No results found.')}</li>`);
            },

            populate: function (type = false) {
                if (type === false) type = this.activeType();
                let code = '';
                let items = this.items[type];
                this.updateActiveItem();
                if (items.length) {
                    for (var i = 0; i < items.length; i++) {
                        code += `<li data-id="${items[i]['id']}">${items[i]['name']}<i class="sb-icon-delete"></i></li>`;
                    }
                } else {
                    code = `<li class="sb-no-results">${sb_('No results found.')}</li>`;
                }
                automations_area_nav.html(code);
                code = '';
                switch (type) {
                    case 'emails':
                        code = `<h2>${sb_('Subject')}</h2><div class="sb-input-setting sb-type-text"><div><input data-id="subject" type="text"></div></div>`;
                        break;
                    case 'popups':
                        code = `<h2>${sb_('Title')}</h2><div class="sb-input-setting sb-type-text"><div><input data-id="title" type="text"></div></div><h2>${sb_('Profile image')}</h2><div data-type="upload-image" class="sb-input-setting sb-type-upload-image"><div class="input"><div data-id="profile_image" class="image"><i class="sb-icon-close"></i></div></div></div><h2>${sb_('Message fallback')}</h2><div class="sb-input-setting sb-type-checkbox"><div><input data-id="fallback" type="checkbox"></div></div>`;
                        break;
                    case 'design':
                        code = `<h2>${sb_('Header title')}</h2><div class="sb-input-setting sb-type-text"><div><input data-id="title" type="text"></div></div>`;
                        for (var i = 1; i < 4; i++) {
                            code += `<h2>${sb_((i == 1 ? 'Primary' : (i == 2 ? 'Secondary' : 'Tertiary')) + ' color')}</h2><div data-type="color" class="sb-input-setting sb-type-color"><div class="input"><input data-id="color_${i}" type="text"><i class="sb-close sb-icon-close"></i></div></div>`;
                        }
                        for (var i = 1; i < 4; i++) {
                            code += `<h2>${sb_(i == 1 ? 'Header background image' : (i == 2 ? 'Header brand image' : 'Chat button icon'))}</h2><div data-type="upload-image" class="sb-input-setting sb-type-upload-image"><div class="input"><div data-id="${sb_(i == 1 ? 'background' : (i == 2 ? 'brand' : 'icon'))}" class="image"><i class="sb-icon-close"></i></div></div></div>`;
                        }
                        break;
                }
                automations_area.find('.sb-automation-extra').html(code);
                SBSettings.initColorPicker(automations_area);
                this.hide();
            },

            updateActiveItem: function () {
                let id = this.activeID();
                if (id) {
                    let language = automations_area.find(`.sb-language-switcher [data-language].sb-active`).attr('data-language');
                    let type = this.activeType();
                    let items = language ? (language in this.translations ? this.translations[language][type] : []) : this.items[type];
                    let conditions = conditions_area.find(' > div');
                    for (var i = 0; i < items.length; i++) {
                        if (items[i]['id'] == id) {
                            items[i] = { id: id, conditions: [] };
                            automations_area.find('.sb-automation-values').find('input,textarea,[data-type="upload-image"] .image').each(function () {
                                items[i][$(this).attr('data-id')] = $(this).hasClass('image') && $(this)[0].hasAttribute('data-value') ? $(this).attr('data-value') : ($(this).attr('type') == 'checkbox' ? $(this).is(':checked') : $(this).val());
                            });
                            conditions.each(function () {
                                let condition = [];
                                $(this).find('input,select').each(function () {
                                    condition.push($(this).val());
                                });
                                if (condition[0] != '' && condition[1] != '' && (condition.length == 2 || condition[2] != '')) {
                                    items[i]['conditions'].push(condition);
                                }
                            });
                            if (SBF.null(items[i].name)) {
                                this.delete(automations_area_nav.find(`[data-id="${id}"] i`));
                            }
                            break;
                        }
                    }
                }
            },

            addCondition: function () {
                conditions_area.append(`<div><div class="sb-input-setting sb-type-select sb-condition-1"><select>${this.getAvailableConditions()}</select></div></div>`);
            },

            updateCondition: function (element) {
                $(element).parent().siblings().remove();
                let parent = $(element).parents().eq(1);
                if ($(element).val()) {
                    let condition = this.conditions[$(element).val()];
                    let code = '';
                    if (condition[1].length) {
                        code = '<div class="sb-input-setting sb-type-select sb-condition-2"><select>';
                        for (var i = 0; i < condition[1].length; i++) {
                            code += `<option value="${SBF.stringToSlug(condition[1][i])}">${sb_(condition[1][i])}</option>`;
                        }
                        code += '</select></div>';
                    }
                    parent.append(code + (condition.length > 2 ? `<div class="sb-input-setting sb-type-text"><input placeholder="${sb_(condition[2])}" type="text"></div>` : ''));
                    parent.siblings().find('.sb-condition-1 select').each(function () {
                        let value = $(this).val();
                        $(this).html(SBSettings.automations.getAvailableConditions(value));
                        $(this).val(value);
                    });
                } else {
                    parent.remove();
                }
            },

            getAvailableConditions: function (include = '') {
                let code = '<option value=""></option>';
                let existing_conditions = [];
                conditions_area.find('.sb-condition-1 select').each(function () {
                    existing_conditions.push($(this).val());
                });
                for (var key in this.conditions) {
                    if (!existing_conditions.includes(key) || key == include) {
                        code += `<option value="${key}">${sb_(this.conditions[key][0])}</option>`;
                    }
                }
                return code;
            },

            addTranslation: function (id = false, type = false, language) {
                if (id === false) id = this.activeID();
                if (type === false) type = this.activeType();
                if (this.getTranslations(id).includes(id)) {
                    return console.warn('Automation translation already in array.');
                }
                if (!(language in this.translations)) this.translations[language] = { messages: [], emails: [], sms: [], popups: [], design: [] };
                if (!(type in this.translations[language])) this.translations[language][type] = [];
                this.translations[language][type].push(this.itemArray(type, id));
            },

            getTranslations: function (id = false) {
                let translations = [];
                if (id === false) id = this.activeID();
                for (var key in this.translations) {
                    let types = this.translations[key];
                    for (var key2 in types) {
                        let items = types[key2];
                        for (var i = 0; i < items.length; i++) {
                            if (items[i]['id'] == id) {
                                translations.push(key);
                                break;
                            }
                        }
                    }
                }
                return translations;
            },

            deleteTranslation: function (id = false, language) {
                if (id === false) id = this.activeID();
                if (language in this.translations) {
                    let types = this.translations[language];
                    for (var key in types) {
                        let items = types[key];
                        for (var i = 0; i < items.length; i++) {
                            if (items[i]['id'] == id) {
                                this.translations[language][key].splice(i, 1);
                                return true;
                            }
                        }
                    }
                }
                return false;
            },

            activeID: function () {
                let item = automations_area_nav.find('.sb-active');
                return item.length ? item.attr('data-id') : false;
            },

            activeType: function () {
                return automations_area_select.find('li.sb-active').data('value');
            },

            itemArray: function (type, id, name = '', message = '') {
                return $.extend({ id: id, name: name, message: message }, type == 'emails' ? { subject: '' } : (type == 'popups' ? { title: '', profile_image: '' } : (type == 'design' ? { title: '', color_1: '', color_2: '', color_3: '', background: '', brand: '', icon: '' } : {})));
            },

            hide: function (hide = true) {
                automations_area.find(' > .sb-tab > .sb-content').setClass('sb-hide', hide);
            }
        },

        translations: {
            translations: {},
            originals: {},
            to_update: {},

            add: function (language) {
                let setting = SBSettings.getSettingObject(language_switcher_target);
                let setting_id = setting.attr('id');
                let active_language = language_switcher_target.find('[data-language].sb-active');
                this.save(setting, active_language.length ? active_language.attr('data-language') : false);
                setting.find('textarea,input[type="text"]').val('');
                this.save(setting, language);
                language_switcher_target.remove();
                setting.sbLanguageSwitcher(this.getLanguageCodes(setting_id), 'settings', language);
            },

            delete: function (setting, language) {
                setting = SBSettings.getSettingObject(setting);
                let setting_id = setting.attr('id');
                delete this.translations[language][setting_id];
                setting.find(`.sb-language-switcher [data-language="${language}"]`).remove();
                this.activate(setting);
            },

            activate: function (setting, language = false) {
                setting = SBSettings.getSettingObject(setting);
                let setting_id = setting.attr('id');
                let values = language ? this.translations[language][setting_id] : this.originals[setting_id];
                if (typeof values == 'string') {
                    setting.find('input, textarea').val(values);
                } else {
                    for (var key in values) {
                        setting.find('#' + key).find('input, textarea').val(typeof values[key] == 'string' ? values[key] : values[key][0]);
                    }
                }
            },

            save: function (setting, language = false) {
                setting = SBSettings.getSettingObject(setting);
                let values = {};
                let setting_id = $(setting).attr('id');
                if (setting.data('type') == 'multi-input') {
                    setting.find('.multi-input-textarea,.multi-input-text').each(function () {
                        values[$(this).attr('id')] = $(this).find('input, textarea').val();
                    });
                } else {
                    values = setting.find('input, textarea').val();
                }
                if (language) {
                    if (!(language in this.translations)) {
                        this.translations[language] = {};
                    }
                    this.translations[language][setting_id] = values;
                } else {
                    this.originals[setting_id] = values;
                }
            },

            getLanguageCodes: function (setting_id) {
                let languages = [];
                for (var key in this.translations) {
                    if (setting_id in this.translations[key]) {
                        languages.push(key);
                    }
                }
                return languages;
            },

            load: function (language_code) {
                let area = settings_area.find('.sb-translations > .sb-content');
                area.find(' > .sb-hide').removeClass('sb-hide');
                this.updateActive();
                SBF.ajax({
                    function: 'get-translation',
                    language_code: language_code
                }, (translations) => {
                    if (language_code in this.to_update) translations = this.to_update[language_code]
                    let code = '';
                    let front = translations['front'];
                    let admin = translations['admin'];
                    code += `<div class="sb-active" data-area="front">`;
                    for (var key_front in front) {
                        code += `<div class="sb-input-setting sb-type-text"><label>${key_front}</label><div><input type="text" value="${front[key_front]}"></div></div>`;
                    }
                    code += `</div><div data-area="admin">`;
                    for (var key_admin in admin) {
                        code += `<div class="sb-input-setting sb-type-text"><label>${key_admin}</label><div><input type="text" value="${admin[key_admin]}"></div></div>`;
                    }
                    code += '</div>';
                    area.find('.sb-translations-list').attr('data-value', language_code).html(code);
                    area.find('.sb-menu-wide li').sbActivate(false).eq(0).sbActivate(true);
                    area.sbLoading(false);
                });
                area.sbLoading(true);
            },

            updateActive: function () {
                let area = settings_area.find('.sb-translations-list')
                let translations = { 'front': {}, 'admin': {} };
                let language_code = area.attr('data-value');
                if (SBF.null(language_code)) return;
                for (var key in translations) {
                    area.find(' > [data-area="' + key + '"] .sb-input-setting:not(.sb-new-translation)').each(function () {
                        translations[key][$(this).find('label').html()] = $(this).find('input').val();
                    });
                    area.find('> [data-area="' + key + '"] .sb-new-translation').each(function () {
                        let original = $(this).find('input:first-child').val();
                        let value = $(this).find('input:last-child').val();
                        if (original != '' && value != '') {
                            translations[key][original] = value;
                        }
                    });
                }
                this.to_update[language_code] = translations;
            }
        }
    }

    /*
    * ----------------------------------------------------------
    * # Reports
    * ----------------------------------------------------------
    */

    var SBReports = {
        chart: false,
        active_report: false,

        initChart: function (data, type = 'line', label_type = 1) {
            let values = [];
            let labels = [];
            let blues = ['#049CFF', '#74C4F7', '#B9E5FF', '#0562A0', '#003B62', '#1F74C4', '#436786'];
            for (var key in data) {
                values.push(data[key][0]);
                labels.push(key);
            }
            if (type != 'line' && values.length > 6) {
                for (var i = 0; i < values.length; i++) {
                    blues.push('hsl(' + 210 + ', ' + Math.floor(Math.random() * 100) + '%, ' + Math.floor(Math.random() * 100) + '%)');
                }
            }
            if (this.chart) this.chart.destroy();
            this.chart = new Chart(reports_area.find('canvas'), {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: type == 'line' ? '#028be530' : blues,
                        borderColor: type == 'line' ? '#049CFF' : '#FFFFFF',
                        borderWidth: 0
                    }],
                },
                options: {
                    legend: {
                        display: false
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                callback: function (tickValue, index, ticks) {
                                    return label_type == 1 ? tickValue : (label_type == 2 ? new Date(tickValue * 1000).toISOString().substr(11, 8) : tickValue);
                                },
                                beginAtZero: true
                            }
                        }],
                        xAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }],
                    },
                    tooltips: {
                        callbacks: {
                            label: function (tooltipItem, chartData) {
                                let index = tooltipItem['index'];
                                let value = chartData['datasets'][0]['data'][index];
                                switch (label_type) {
                                    case 1: return value;
                                    case 2: return new Date(values[index] * 1000).toISOString().substr(11, 8);
                                    case 3: return value + '%';
                                    case 4: let tds = reports_area.find('.sb-table tbody tr').eq(index).find('td'); return tds.eq(0).text() + ' ' + tds.eq(1).text();
                                }
                            },
                        },
                        displayColors: false
                    }
                }
            });
        },

        initTable: function (header, data, inverse = false) {
            let code = '<thead><tr>';
            let index = data[Object.keys(data)[0]].length - 1;
            let list = [];
            for (var i = 0; i < header.length; i++) {
                code += `<th>${sb_(header[i])}</th>`;
            }
            code += '</tr></thead><tbody>';
            for (var key in data) {
                if (data[key][index] != 0) {
                    list.push([key, data[key][index]]);
                }
            }
            if (inverse) {
                list.reverse();
            }
            for (var i = 0; i < list.length; i++) {
                code += `<tr><td><div>${list[i][0]}</div></td><td>${list[i][1]}</td></tr>`;
            }
            code += '</tbody>';
            reports_area.find('table').html(code);
        },

        initReport: function (name = false, date_range = false) {
            let area = reports_area.find('.sb-tab > .sb-content');
            date_range = SBF.null(date_range) ? [false, false] : date_range.split(' - ');
            area.sbLoading(true);
            if (name) this.active_report = name;
            if (!this.active_report) return;
            this.getData(this.active_report, date_range[0], date_range[1], (response) => {
                if (response == false) {
                    area.addClass('sb-no-results-active');
                } else {
                    area.removeClass('sb-no-results-active');
                    this.initChart(response['data'], response['chart_type'], response['label_type']);
                    this.initTable(response['table'], response['data'], response['table-inverse']);
                    reports_area.find('.sb-reports-title').html(response['title']);
                    reports_area.find('.sb-reports-text').html(response['description']);
                    reports_area.find('.sb-collapse-btn').remove();
                    if (!responsive) collapse(reports_area.find('.sb-collapse'), reports_area.find('canvas').outerHeight() - 135);
                }
                area.sbLoading(false);
            });
        },

        getData: function (name, date_start = false, date_end = false, onSuccess) {
            SBF.ajax({
                function: 'reports',
                name: name,
                date_start: date_start,
                date_end: date_end
            }, (response) => {
                onSuccess(response);
            });
        },

        initDatePicker: function () {
            let settings = {
                ranges: {},
                locale: {
                    'format': 'DD/MM/YYYY',
                    'separator': ' - ',
                    'applyLabel': sb_('Apply'),
                    'cancelLabel': sb_('Cancel'),
                    'fromLabel': sb_('From'),
                    'toLabel': sb_('To'),
                    'customRangeLabel': sb_('Custom'),
                    'weekLabel': sb_('W'),
                    'daysOfWeek': [
                        sb_('Su'),
                        sb_('Mo'),
                        sb_('Tu'),
                        sb_('We'),
                        sb_('Th'),
                        sb_('Fr'),
                        sb_('Sa')
                    ],
                    'monthNames': [
                        sb_('January'),
                        sb_('February'),
                        sb_('March'),
                        sb_('April'),
                        sb_('May'),
                        sb_('June'),
                        sb_('July'),
                        sb_('August'),
                        sb_('September'),
                        sb_('October'),
                        sb_('November'),
                        sb_('December')
                    ],
                    'firstDay': 1
                },
                showCustomRangeLabel: true,
                alwaysShowCalendars: true,
                autoApply: true
            };
            settings['ranges'][sb_('Today')] = [moment(), moment()];
            settings['ranges'][sb_('Yesterday')] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
            settings['ranges'][sb_('Last 7 Days')] = [moment().subtract(6, 'days'), moment()];
            settings['ranges'][sb_('Last 30 Days')] = [moment().subtract(29, 'days'), moment()];
            settings['ranges'][sb_('This Month')] = [moment().startOf('month'), moment().endOf('month')];
            settings['ranges'][sb_('Last Month')] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];
            reports_area.find('#sb-date-picker').daterangepicker(settings).val('');
        }
    }

    /*
    * ----------------------------------------------------------
    * USERS METHODS
    * ----------------------------------------------------------
    */

    var SBUsers = {
        real_time: null,
        datetime_last_user: '2000-01-01 00:00:00',
        sorting: ['creation_time', 'DESC'],
        user_types: ['visitor', 'lead', 'user'],
        search_query: '',
        init: false,
        busy: false,

        // Table menu filter
        filter: function (type) {
            if (type == 'all') {
                type = ['visitor', 'lead', 'user'];
            } else if (type == 'agent') {
                type = ['agent', 'admin'];
            } else {
                type = [type];
            }
            this.user_types = type;
            this.loading();
            users_pagination = 1;
            let settings = type[0] == 'online' ? ['get-online-users', SB_ACTIVE_AGENT['id'], this.sorting[0]] : ['get-users', -1, this.sorting];
            SBF.ajax({
                function: settings[0],
                exclude_id: settings[1],
                sorting: settings[2],
                user_types: type,
                search: this.search_query
            }, (response) => {
                this.populate(response);
                this.loading(false);
            });
        },

        // Table menu filter
        sort: function (field, direction = 'DESC') {
            this.sorting = [field, direction];
            this.loading();
            users_pagination = 1;
            SBF.ajax({
                function: 'get-users',
                sorting: this.sorting,
                user_types: this.user_types,
                search: this.search_query
            }, (response) => {
                this.populate(response);
                this.loading(false);
            });
        },

        // Search users
        search: function (input) {
            searchInput(input, (search, icon) => {
                users_pagination = 1;
                SBF.ajax({
                    function: search.length > 1 ? 'search-users' : 'get-users',
                    search: search,
                    user_types: this.user_types,
                    sorting: this.sorting,
                }, (response) => {
                    this.user_types = ['visitor', 'lead', 'user'];
                    this.populate(response);
                    this.search_query = search;
                    $(icon).sbLoading(false);
                    users_table_menu.find('li').sbActivate(false).eq(0).sbActivate();
                });
            });
        },

        // Populate the table
        populate: function (response) {
            let code = '';
            let count = response.length;
            if (count) {
                for (var i = 0; i < count; i++) {
                    code += this.getRow(new SBUser(response[i]));
                }
            } else {
                code = `<p class="sb-no-results">${sb_('No users found.')}</p>`;
            }
            users_table.find('tbody').html(code);
        },

        // Update users and table with new users
        update: function () {
            if (!this.busy) {
                let checks = ['user', 'visitor', 'lead', 'agent'];
                let populate = checks.includes(this.user_types[0]) && this.search_query == '';
                let filter = users_table_menu.find('.sb-active').data('type');
                if (filter == 'online') {
                    this.filter(filter);
                } else {
                    this.busy = true;
                    SBF.ajax({
                        function: 'get-new-users',
                        datetime: this.datetime_last_user
                    }, (response) => {
                        let count = response.length;
                        this.busy = false;
                        if (count > 0) {
                            let code = '';
                            for (var i = 0; i < count; i++) {
                                let user = new SBUser(response[i]);
                                users[user.id] = user;
                                this.updateMenu('add', user.type);
                                if (populate) {
                                    code += this.getRow(user);
                                }
                            }
                            if (populate) {
                                users_table.find('tbody').prepend(code);
                                if (checks.includes(filter)) {
                                    let selector = '';
                                    for (var i = 0; i < checks.length; i++) {
                                        selector += checks[i] == filter ? '' : `[data-user-type="${checks[i]}"],`;
                                    }
                                    users_table.find(selector.slice(0, -1)).remove();
                                }
                            }
                            this.datetime_last_user = response[0]['creation_time'];
                        }
                    });
                }
            }
        },

        // Get a user row code
        getRow: function (user) {
            if (user instanceof SBUser) {
                return `<tr data-user-id="${user.id}" data-user-type="${user.type}"><td><input type="checkbox" /></td><td class="sb-td-profile"><a class="sb-profile"><img src="${user.image}" /><span>${user.name}</span></a></td><td class="sb-td-email">${user.get('email')}</td><td class="sb-td-ut">${user.type}</td><td>${SBF.beautifyTime(user.get('last_activity'), true)}</td><td>${user.get('creation_time')}</td></tr>`;
            } else {
                SBF.error('User not of type SBUser', 'SBUsers.getRow');
                return false;
            }
        },

        // Update a user row
        updateRow: function (user) {
            let row = users_table.find(`[data-user-id="${user.id}"]`);
            if (row.length) {
                let menu_active = users_table_menu.find('.sb-active').data('type');
                if ((user.type != menu_active) && !(user.type == 'admin' && menu_active == 'agent') && menu_active != 'all') {
                    let counter = admin.find(`[data-type="${user.type == 'admin' ? 'agent' : user.type}"] span`);
                    let count = parseInt(counter.attr('data-count'));
                    counter.html(count + 1).attr('data-count', count + 1);
                    row.remove();
                } else {
                    row.replaceWith(this.getRow(user));
                }
            } else {
                users_table.find('tbody').append(this.getRow(user));
            }
        },

        // Update users table menu
        updateMenu: function (action = 'all', type = false) {
            let user_types = ['all', 'user', 'lead', 'visitor'];
            if (action == 'all') {
                SBF.ajax({
                    function: 'count-users'
                }, (response) => {
                    for (var i = 0; i < user_types.length; i++) {
                        this.updateMenuItem('set', user_types[i], response[user_types[i]]);
                    }
                });
            } else {
                this.updateMenuItem(action, type);
            }
        },

        updateMenuItem: function (action = 'set', type = false, count = 1) {
            let item = users_table_menu.find(`[data-type="${type}"] span`);
            let user_types = ['user', 'lead', 'visitor'];
            if (action != 'set') {
                count = parseInt(item.attr('data-count')) + (1 * (action == 'add' ? 1 : -1));
            }
            item.html(`(${count})`).attr('data-count', count);
            count = 0;
            for (var i = 0; i < user_types.length; i++) {
                count += parseInt(users_table_menu.find(`[data-type="${user_types[i]}"] span`).attr('data-count'));
            }
            users_table_menu.find(`[data-type="all"] span`).html(`(${count})`).attr('data-count', count);
        },

        // Delete a user
        delete: function (user_ids) {
            this.loading();
            if (Array.isArray(user_ids)) {
                if (SB_ADMIN_SETTINGS.cloud) {
                    user_ids = SBCloud.removeAdminID(user_ids);
                    if (!user_ids.length) return;
                }
                SBF.ajax({
                    function: 'delete-users',
                    user_ids: user_ids
                }, () => {
                    for (var i = 0; i < user_ids.length; i++) {
                        delete users[user_ids[i]];
                        users_table.find(`[data-user-id="${user_ids[i]}"]`).remove();
                        conversations_admin_list_ul.find(`[data-user-id="${user_ids[i]}"]`).remove();
                    }
                    if (users_table.find('[data-user-id]').length == 0) {
                        this.filter(users_table_menu.find('.sb-active').data('type'));
                    }
                    showResponse('Users deleted');
                    this.updateMenu();
                    this.loading(false);
                });
            } else {
                users[user_ids].delete(() => {
                    let conversation = conversations_admin_list_ul.find(`[data-user-id="${user_ids}"]`);
                    if (activeUser().id == user_ids) {
                        activeUser(false);
                    }
                    if (conversation.sbActive()) SBConversations.clickFirst();
                    delete users[user_ids];
                    users_table.find(`[data-user-id="${user_ids}"]`).remove();
                    conversation.remove();
                    admin.sbHideLightbox();
                    showResponse('User deleted');
                    this.updateMenu();
                    this.loading(false);
                });
            }
        },

        // Start or stop the real time update of the users and table
        startRealTime: function () {
            if (SBPusher.active) return;
            this.stopRealTime();
            this.real_time = setInterval(() => {
                this.update();
            }, 1000);
        },

        stopRealTime: function () {
            clearInterval(this.real_time);
        },

        // Table loading
        loading: function (show = true) {
            let loading = users_area.find('.sb-loading-table');
            if (show) loading.sbActivate();
            else loading.sbActivate(false);
        },

        // CSV generation and download
        csv: function () {
            SBF.ajax({ function: 'csv-users' }, (response) => window.open(response));
        }
    }

    /*
    * ----------------------------------------------------------
    * # Conversations 
    * ----------------------------------------------------------
    */

    var SBConversations = {
        real_time: null,
        datetime_last_conversation: '2000-01-01 00:00:00',
        user_typing: false,
        desktop_notifications: false,
        flash_notifications: false,
        busy: false,
        is_search: false,
        menu_count_ajax: false,

        // Open the conversations tab
        open: function (conversation_id = -1, user_id) {
            if (conversation_id != -1) {
                this.openConversation(conversation_id, user_id);
            }
            admin.sbHideLightbox();
            header.find('.sb-admin-nav a').sbActivate(false).parent().find('#sb-conversations').sbActivate();
            admin.find(' > main > div').sbActivate(false);
            conversations_area.sbActivate().find('.sb-board').removeClass('sb-no-conversation');
            this.startRealTime();
        },

        // Open a single conversation
        openConversation: function (conversation_id, user_id = false, scroll = true) {
            if (user_id === false && conversation_id) {
                SBF.ajax({
                    function: 'get-user-from-conversation',
                    conversation_id: conversation_id
                }, (response) => {
                    if (!SBF.null(response['id'])) {
                        this.openConversation(conversation_id, response['id'], scroll);
                    } else SBF.error('User not found', 'SBAdmin.openConversation');
                });
            } else {
                let area = conversations_area.find('.sb-conversation .sb-list');
                let new_user = SBF.null(users[user_id]) || !('email' in users[user_id].details);
                let conversation = conversations_area.find(`[data-conversation-id="${conversation_id}"]`);
                area.html('');
                area.sbLoading(true);

                // Init the user
                if (new_user) {
                    activeUser(new SBUser({ 'id': user_id }));
                    activeUser().update(() => {
                        users[user_id] = activeUser();
                        this.updateUserDetails();
                    });
                } else {
                    activeUser(users[user_id]);
                }
                if (SBPusher.active) {
                    SBPusher.event('client-typing', (response) => {
                        if (response.user_id == activeUser().id) {
                            SBConversations.typing(true);
                            clearTimeout(pusher_timeout);
                            pusher_timeout = setTimeout(() => { SBConversations.typing(false) }, 1000);
                        }
                    });
                    SBPusher.event('new-message', () => {
                        SBChat.update();
                    });
                    SBPusher.event('init', () => {
                        SBConversations.updateCurrentURL();
                    });
                }
                if (SB_ADMIN_SETTINGS['smart-reply']) suggestions_area.html('');

                // Open the conversation
                conversations_admin_list_ul.find('li').sbActivate(false);
                conversation.sbActivate();
                if (conversation_id != -1) {
                    activeUser().getFullConversation(conversation_id, (response) => {

                        let conversation_status_code = response.get('conversation_status_code');
                        let select = conversations_admin_list.find('.sb-select');
                        let select_status_code = select.find('.sb-active').attr('data-value');

                        SBChat.setConversation(response);
                        SBChat.populate();
                        conversations_area.find('.sb-top > a').html(response.get('title'));
                        conversations_area.find('.sb-top [data-value="read"]').sbActivate(conversation_status_code == 2);
                        conversations_area.find('.sb-conversation-busy').remove();
                        this.updateUserDetails();

                        // Automatic translation
                        SBAdmin.must_translate = SB_ADMIN_SETTINGS['translation'] && activeUser().language && SB_ADMIN_SETTINGS['active-agent-language'] != activeUser().language;
                        if (SBAdmin.must_translate) {
                            let strings = [];
                            let message_ids = [];
                            let message_user_types = [];
                            for (var i = 0; i < response.messages.length; i++) {
                                let message = response.messages[i];
                                if (message.get('user_type') != 'bot') {
                                    strings.push(message.message);
                                    message_ids.push(message.id);
                                    message_user_types.push(message.get('user_type'));
                                }
                            }
                            SBApps.dialogflow.translate(strings, SB_ADMIN_SETTINGS['active-agent-language'], (response) => {
                                if (response) {
                                    for (var i = 0; i < response.length; i++) {
                                        let message = SBChat.conversation.getMessage(message_ids[i]);
                                        area.find(`[data-id="${message_ids[i]}"] .sb-message`).html(response[i].translatedText);
                                        area.find(`[data-id="${message_ids[i]}"] .sb-menu`).prepend(`<li data-value="original">${sb_(SBF.isAgent(message_user_types[i]) ? 'View translation' : 'View original message')}</li>`);
                                        if (message) message.set('translation', response[i].translatedText);
                                    }
                                }
                            });
                        }

                        // Departments
                        let select_departments = conversations_area.find('#conversation-department');
                        if (select_departments.length) {
                            let item = select_departments.find(`[data-id="${response.get('department')}"]`);
                            select_departments.find(' > p').attr('data-value', item.data('value')).html(item.html());
                        }

                        // Agent assignment
                        let select_agents = conversations_area.find('#conversation-agent');
                        if (select_agents.length) {
                            let item = select_agents.find(`[data-id="${response.get('agent_id')}"]`);
                            select_agents.find(' > p').attr('data-value', item.data('id')).html(item.html());
                        }

                        // Activate conversation
                        if ([1, 2].includes(conversation_status_code)) {
                            conversation_status_code = 0;
                        }
                        if (select_status_code != conversation_status_code && !$(conversations_admin_list).find('.sb-search-btn').sbActive()) {
                            select.find(`[data-value="${conversation_status_code}"]`).click();
                            select.find('ul').sbActivate(false);
                        }
                        if (responsive) {
                            this.mobileOpenConversation();
                        }
                        if (!conversation.length && select_status_code == conversation_status_code) {
                            conversations_admin_list_ul.prepend(SBConversations.getListCode(response).replace('<li', '<li class="sb-active"'));
                        }
                        conversation.sbActivate();
                        if (scroll) {
                            this.scrollTo();
                        }

                        // Check if another agent has the conversation open
                        if (response.get('busy')) {
                            conversations_area.find('.sb-editor > .sb-labels').prepend(`<span class="sb-status-warning sb-conversation-busy">${sb_('Another agent is replying to this conversation')}</span>`);
                        }

                        // Woocommerce
                        if (SBApps.is('woocommerce')) {
                            SBApps.woocommerce.conversationPanel();
                        }

                        // UMP
                        if (SBApps.is('ump')) {
                            SBApps.ump.conversationPanel();
                        }

                        // Perfex
                        if (SBApps.is('perfex')) {
                            SBApps.perfex.conversationPanel();
                        }

                        // WHMCS
                        if (SBApps.is('whmcs')) {
                            SBApps.whmcs.conversationPanel();
                        }

                        // Active eCommerce
                        if (SBApps.is('aecommerce')) {
                            SBApps.aecommerce.conversationPanel();
                        }

                        // ARMember
                        if (SBApps.is('armember')) {
                            SBApps.armember.conversationPanel();
                        }

                        // Notes
                        this.notes.update(response['details']['notes']);

                        // Attachments
                        this.attachments();

                        // Suggestions
                        if (SB_ADMIN_SETTINGS['smart-reply']) {
                            let message = response.getLastUserMessage();
                            suggestions_area.html('');
                            if (message && message.payload('sb-human-takeover')) {
                                message = response.getLastUserMessage(message.get('index'));
                            }
                            if (message) {
                                SBApps.dialogflow.smart_reply_data = false;
                                SBApps.dialogflow.smartReply(message.message);
                            }
                        }

                        // Rating
                        for (var i = response.messages.length - 1; i > 0; i--) {
                            let payload = response.messages[i].get('payload');
                            let break_loop = false;
                            if (payload && 'rich-messages' in payload) {
                                for (var rich_message_id in payload['rich-messages']) {
                                    let rich_message = payload['rich-messages'][rich_message_id];
                                    if (rich_message.type == 'rating') {
                                        conversations_area.find('.sb-profile-list > ul').append(`<li data-id="rating"><i class="sb-icon sb-icon-${rich_message.result.rating == 1 ? 'like' : 'dislike'}"></i><span>${sb_('User rating')}</span><label>${sb_(rich_message.result.rating == 1 ? 'Helpful' : 'Not helpful')}</label></li>`);
                                        break_loop = true;
                                        break;
                                    }
                                }
                            }
                            if (break_loop) break;
                        }

                        area.sbLoading(false);
                    });
                } else {
                    SBChat.clear();
                    conversations_admin_list_ul.find('li').sbActivate(false);
                    area.sbLoading(false);
                }

                // User details
                if (!new_user) {
                    this.updateUserDetails();
                }

                // Populate user conversations on the bottom right area
                activeUser().getConversations(function (response) {
                    conversations_area.find('.sb-user-conversations').html(activeUser().getConversationsCode(response));
                });

                // More settings
                conversations_area.find('.sb-board').removeClass('sb-no-conversation');
                updateUsersActivity();
                this.startRealTime();
                if (SBF.getURL('conversation') != conversation_id) pushState('?conversation=' + conversation_id);
            }
        },

        // [Deprecated] this method is obsolete and it will be removed soon
        populate: function (conversation_id, user_id, scroll) {
            this.openConversation(conversation_id, user_id, scroll);
        },

        // Populate conversations
        populateList: function (response) {
            let code = '';
            conversations = [];
            for (var i = 0; i < response.length; i++) {
                code += this.getListCode(response[i]);
                conversations.push(response[i]);
            }
            if (code == '') {
                code = `<p class="sb-no-results">${sb_('No conversations found.')}</p>`;
            }
            conversations_admin_list_ul.html(code);
            this.updateMenu();
            SBF.event('SBAdminConversationsLoaded', { conversations: response });
        },

        // Update the left conversations list with new conversations or messages 
        update: function () {
            if (!this.busy) {
                this.busy = true;
                SBF.ajax({
                    function: 'get-new-conversations',
                    datetime: this.datetime_last_conversation,
                    routing: SB_ADMIN_SETTINGS['routing'],
                    routing_unassigned: SB_ADMIN_SETTINGS['assign-conversation-to-agent']
                }, (response) => {
                    this.busy = false;
                    if (response.length) {
                        let code_pending = '';
                        let code_not_pending = '';
                        let active_conversation_id = SBChat.conversation ? SBChat.conversation.id : -1;
                        let item_not_pending;
                        let scroll_to_conversation = false;
                        let id_check = [];
                        this.datetime_last_conversation = response[0]['creation_time'];

                        for (var i = 0; i < response.length; i++) {
                            if (!id_check.includes(response[i]['conversation_id'])) {
                                let item = response[i];
                                let status = item['conversation_status_code'];
                                let user_id = item['user_id'];
                                let conversation_id = item['conversation_id'];
                                let active_conversation = active_conversation_id == conversation_id;
                                let conversation_code = this.getListCode(item, null);
                                let conversation_li = conversations_admin_list_ul.find(`[data-conversation-id="${conversation_id}"]`);
                                let conversation_index = conversation_li.index();
                                let conversation = conversation_li.length;
                                let payload = SBF.null(response[i]['payload']) ? {} : JSON.parse(response[i]['payload']);

                                // Active conversation
                                if (active_conversation) {
                                    conversation_code = conversation_code.replace('<li', '<li class="sb-active"');
                                    if (conversation) {
                                        if (item['message'] != '') {
                                            conversation_li.replaceWith(conversation_code);
                                        }
                                        conversations[conversation_index]['conversation_status_code'] = status;
                                        conversations_area.find('.sb-top [data-value="read"]').sbActivate(status == 2);
                                    } else {
                                        scroll_to_conversation = true;
                                    }
                                } else if (conversation) {

                                    // Conversation already in list but not active
                                    conversations[conversation_index] = item;
                                    conversations_admin_list_ul.find(`[data-conversation-id="${conversation_id}"]`).remove();
                                }

                                // Add the user to the global users array if it doesn't exists
                                if (!(user_id in users)) {
                                    users[user_id] = new SBUser({ 'id': user_id, 'first_name': item['first_name'], 'last_name': item['last_name'], 'profile_image': item['profile_image'], 'user_type': item['user_type'] });
                                }

                                // New conversation 
                                if (!active_conversation || !conversation) {
                                    if (status == 2) {
                                        code_pending += conversation_code;
                                        conversations.unshift(item);
                                    } else if (status == 0 || status == 1) {
                                        item_not_pending = conversations_admin_list_ul.find('[data-conversation-status="2"]').last();
                                        if (item_not_pending.length == 0) {
                                            code_pending += conversation_code;
                                        } else {
                                            conversations.splice(item_not_pending.index() + 1, 0, item);
                                            code_not_pending += conversation_code;
                                        }
                                    }
                                    if (user_id == activeUser().id) {
                                        activeUser().getConversations(function (response) {
                                            conversations_area.find('.sb-user-conversations').html(activeUser().getConversationsCode(response));
                                        });
                                    }
                                }

                                // Update user
                                if (activeUser() && (('event' in payload && payload['event'] == 'update-user') || (users[user_id].type != item['user_type']))) {
                                    activeUser().update(() => {
                                        this.updateUserDetails();
                                        users[activeUser().id] = activeUser();
                                    });
                                }

                                // Desktop, flash, sounds notifications
                                if (!SBChat.tab_active && item['conversation_status_code'] == 2 && (!SBF.isAgent(item['message_user_type']) || 'bot-unknow-notify' in payload) && !(SBF.null(item.message) && SBF.null(item.attachments))) {
                                    if (this.desktop_notifications) {
                                        let user_details = [item['first_name'] + ' ' + item['last_name'], (item['profile_image'].indexOf('user.svg') > 0 ? SB_ADMIN_SETTINGS['notifications-icon'] : item['profile_image'])];
                                        if ('bot-unknow-notify' in payload) {
                                            users[user_id].getFullConversation(conversation_id, (response) => {
                                                let last_message = response.getLastUserMessage();
                                                if ('id' in last_message.get('payload') && last_message.get('payload').id == 'sb-human-takeover') {
                                                    last_message = response.getLastUserMessage(response.messages.length - 3);
                                                }
                                                SBChat.desktopNotification(user_details[0], last_message.message, user_details[1], conversation_id, user_id);
                                            });
                                        } else {
                                            SBChat.desktopNotification(user_details[0], item['message'], user_details[1], conversation_id, user_id);
                                        }
                                    }
                                    if (this.flash_notifications) {
                                        document.title = sb_('New message...');
                                    }
                                    if (SBChat.audio && ['a', 'c', 'ic'].includes(SB_ADMIN_SETTINGS['sounds'])) {
                                        SBChat.audio.play();
                                    }
                                }
                                id_check.push(conversation_id);
                            }
                        }
                        if (code_pending != '') {
                            conversations_admin_list_ul.prepend(code_pending);
                        }
                        if (code_not_pending != '') {
                            $(code_not_pending).insertAfter(item_not_pending);
                        }
                        if (scroll_to_conversation) {
                            this.scrollTo();
                        }
                        this.updateMenu();
                    }
                });
                if (SB_ADMIN_SETTINGS['assign-conversation-to-agent'] || SB_ACTIVE_AGENT['department']) {
                    let ids = conversations_admin_list_ul.find(' > li').map(function () { return $(this).attr('data-conversation-id') }).get();
                    if (ids.length) {
                        SBF.ajax({
                            function: 'check-conversations-assignment',
                            conversations_ids: ids,
                            agent_id: SB_ADMIN_SETTINGS['assign-conversation-to-agent'] ? SB_ACTIVE_AGENT['id'] : false,
                            department: SB_ACTIVE_AGENT['department']
                        }, (response) => {
                            if (response) {
                                for (var i = 0; i < response.length; i++) {
                                    conversations_admin_list_ul.find(`[data-conversation-id="${response[i]}"]`).remove();
                                }
                            }
                        });
                    }
                }
            }
        },

        // Update the top left filter
        updateMenu: function () {
            let count = conversations_admin_list_ul.find('[data-conversation-status="2"]').length;
            let item = conversations_admin_list.find('.sb-select');
            let span = item.find(' > p span');
            if (count == 100 || this.menu_count_ajax) {
                let status = item.find('li.sb-active').data('value');
                this.menu_count_ajax = true;
                SBF.ajax({
                    function: 'count-conversations',
                    status_code: status == 0 ? 2 : status
                }, (response) => {
                    span.html(`(${response})`);
                });
            } else {
                span.html(`(${count})`);
            }
        },

        // Return the code of the message menu
        messageMenu: function (agent) {
            return `<i class="sb-menu-btn sb-icon-menu"></i><ul class="sb-menu">${SBApps.is('dialogflow') ? `<li data-value="bot">${sb_('Send to Dialogflow')}</li>` : ''}${agent ? `<li data-value="delete">${sb_('Delete')}</li>` : ''}</ul>`;
        },

        // Update the users details of the conversations area
        updateUserDetails() {
            if (!activeUser()) return;
            conversations_area.find(`[data-user-id="${activeUser().id}"] .sb-name,.sb-top > a`).html(activeUser().name);
            conversations_area.find('.sb-user-details .sb-profile').setProfile();
            SBProfile.populate(activeUser(), conversations_area.find('.sb-profile-list'));
        },

        // Return the conversation code of the left conversations list
        getListCode: function (conversation, status) {
            if (conversation instanceof SBConversation) {
                conversation = {
                    message: conversation.getLastMessage().message,
                    attachments: conversation.getLastMessage().attachments,
                    user_id: conversation.get('user_id'),
                    conversation_id: conversation.id,
                    conversation_status_code: conversation.get('conversation_status_code'),
                    conversation_source: conversation.get('source'),
                    profile_image: conversation.get('profile_image'),
                    first_name: conversation.get('first_name'),
                    last_name: conversation.get('last_name'),
                    creation_time: conversation.get('creation_time'),
                };
            }
            let message = conversation['message'];
            if (SBF.null(status)) status = conversation['conversation_status_code'];
            if (message.length > 110) {
                message = message.substr(0, 110) + ' ...';
            }
            if (message == '' && !SBF.null(conversation['attachments'])) {
                let files = JSON.parse(conversation['attachments']);
                if (Array.isArray(files)) {
                    for (var i = 0; i < files.length; i++) {
                        message += files[i][0] + ' ';
                    }
                    if (message.length > 114) {
                        message = message.substr(0, 114) + ' ...';
                    }
                }
            }
            return `<li data-user-id="${conversation['user_id']}" data-conversation-id="${conversation['conversation_id']}" data-conversation-status="${status}"${!SBF.null(conversation['conversation_source']) ? ` data-conversation-source="${conversation['conversation_source']}"` : ''}><div class="sb-profile"><img src="${conversation['profile_image']}"><span class="sb-name">${conversation['first_name']} ${conversation['last_name']}</span><span class="sb-time">${SBF.beautifyTime(conversation['creation_time'])}</span></div><p>${message}</p></li>`;
        },

        // Start or stop the real time update of left conversations list and chat 
        startRealTime: function () {
            if (SBPusher.active) return;
            this.stopRealTime();
            this.real_time = setInterval(() => {
                this.update();
                this.updateCurrentURL();
            }, 10000);
            SBChat.startRealTime();
        },
        stopRealTime: function () {
            clearInterval(this.real_time);
            SBChat.stopRealTime();
        },

        // Transcript generation and download
        transcript: function (conversation_id) {
            SBF.ajax({
                function: 'transcript',
                conversation_id: conversation_id
            }, (response) => window.open(response));
        },

        // Set the typing status
        typing: function (typing) {
            if (typing) {
                if (SBChat.user_online && !this.user_typing) {
                    conversations_area.find('.sb-conversation .sb-top > .sb-labels').append('<span class="sb-status-typing">' + sb_('Typing') + '</span>');
                    this.user_typing = true;
                }
            } else if (this.user_typing) {
                conversations_area.find('.sb-conversation .sb-top .sb-status-typing').remove();
                this.user_typing = false;
            }
        },

        // Scroll the left conversations list to the active conversation
        scrollTo: function () {
            let active = conversations_admin_list_ul.find('.sb-active');
            let offset = active.length ? active[0].offsetTop : 0;
            conversations_admin_list_ul.parent().scrollTop(offset - (responsive ? 120 : 70));
        },

        // Search conversations
        search: function (input) {
            searchInput(input, (search, icon) => {
                if (search.length > 1) {
                    SBF.ajax({
                        function: 'search-conversations',
                        search: search,
                        routing: SB_ADMIN_SETTINGS['routing']
                    }, (response) => {
                        SBConversations.populateList(response);
                        $(icon).sbLoading(false);
                        this.scrollTo();
                        this.is_search = true;
                    });
                } else {
                    pagination = 1;
                    SBF.ajax({
                        function: 'get-conversations',
                        status_code: conversations_admin_list.find('.sb-select li.sb-active').data('value'),
                        routing: SB_ADMIN_SETTINGS['routing']
                    }, (response) => {
                        SBConversations.populateList(response);
                        $(icon).sbLoading(false);
                        this.is_search = false;
                    });
                }
            });
        },

        // Get the page url of the user
        updateCurrentURL: function () {
            if (SBChat.user_online && activeUser() != false) {
                let url = conversations_area.find('.sb-profile-list [data-id="current_url"] label');
                if (url.length) {
                    SBF.ajax({
                        function: 'current-url'
                    }, (response) => {
                        if (response != false) {
                            url.attr('data-value', response).html(response.replace('https://', '').replace('http://', '').replace(/\/$/, ''));
                        }
                    });
                }
            }
        },

        // [Deprecated] this method is obsolete and it will be removed soon
        showDialogflowIntent: function (message_id) {
            SBApps.dialogflow.showCreateIntentBox(message_id);
        },

        // [Deprecated] this method is obsolete and it will be removed soon
        sendDialogflowIntent: function (button) {
            SBApps.dialogflow.submitIntent(button);
        },

        // Update the department of a conversation
        assignDepartment: function (conversation_id, department, onSuccess) {
            SBF.ajax({
                function: 'update-conversation-department',
                conversation_id: conversation_id,
                department: department,
                message: SBChat.conversation.getLastMessage().message
            }, (response) => {
                onSuccess(response);
            });
        },

        // Update the agent assignged to a conversation
        assignAgent: function (conversation_id, agent_id, onSuccess = false) {
            SBF.ajax({
                function: 'update-conversation-agent',
                conversation_id: conversation_id,
                agent_id: agent_id,
                message: SBChat.conversation.getLastMessage().message
            }, (response) => {
                if (onSuccess) onSuccess(response);
            });
        },

        // Mobile conversations menu
        mobileOpenConversation: function () {
            conversations_area.find('.sb-admin-list').sbActivate(false);
            conversations_area.find('.sb-conversation').sbActivate();
            header.addClass('sb-hide');
        },

        mobileCloseConversation: function () {
            conversations_admin_list_ul.find('li.sb-active').sbActivate(false);
            conversations_area.find('.sb-admin-list').sbActivate();
            conversations_area.find('.sb-conversation').sbActivate(false);
            header.removeClass('sb-hide');
        },

        // Trigger the click event of the first conversation
        clickFirst: function () {
            conversations_admin_list_ul.find('li:first-child').click();
            SBConversations.scrollTo();
        },

        // Saved replies
        savedReplies: function (textarea, value) {
            let last_char = value.charAt(textarea.selectionStart - 1);
            if (last_char == '#') {
                SBChat.editor_listening = true;
            }
            if (SBChat.editor_listening && last_char == ' ') {
                let keyword = value.substr(value.lastIndexOf('#') + 1).replace(' ', '');
                SBChat.editor_listening = false;
                for (var i = 0; i < saved_replies_list.length; i++) {
                    if (saved_replies_list[i]['reply-name'] == keyword) {
                        $(textarea).val(value.substr(0, value.lastIndexOf('#')) + saved_replies_list[i]['reply-text']);
                        return;
                    }
                }
            }
        },

        // Conversation attachments panel
        attachments: function () {
            if (attachments_panel.length) {
                let attachments = SBChat.conversation.getAttachments();
                let code = '';
                for (var i = 0; i < attachments.length; i++) {
                    code += `<a href="${attachments[i][1]}" target="_blank"><i class="sb-icon sb-icon-download"></i>${attachments[i][0]}</a>`;
                }
                $(attachments_panel).html(code == '' ? '' : `<h3>${sb_('Attachments')}</h3><div class="sb-list-items sb-list-links">${code}</div>`);
                collapse(attachments_panel, 160);
            }
        },

        // Notes
        notes: {
            busy: false,

            add: function (conversation_id, user_id, name, message, onSuccess = false) {
                SBF.ajax({
                    function: 'add-note',
                    conversation_id: conversation_id,
                    user_id: user_id,
                    name: name,
                    message: message
                }, (response) => {
                    if (onSuccess) onSuccess(response);
                });
            },

            update: function (notes, add = false) {
                if (notes_panel.length) {
                    let code = '';
                    let div = notes_panel.find(' > div');
                    for (var i = 0; i < notes.length; i++) {
                        let note = notes[i];
                        code += `<div data-id="${note['id']}"><span>${note['name']}${SB_ACTIVE_AGENT['id'] == note['user_id'] ? '<i class="sb-delete-note sb-icon-close"></i>' : ''}</span><span>${note['message']}</span></div>`;
                    }
                    if (add) {
                        div.append(code);
                    } else {
                        div.html(code);
                    }
                    div.attr('style', '');
                    notes_panel.find('.sb-collapse-btn').remove();
                    collapse(notes_panel, 155);
                    this.busy = false;
                }
            },

            delete: function (conversation_id, note_id, onSuccess = false) {
                if (this.busy) return;
                this.busy = true;
                SBF.ajax({
                    function: 'delete-note',
                    conversation_id: conversation_id,
                    note_id: note_id
                }, (response) => {
                    this.busy = false;
                    if (onSuccess) onSuccess(response);
                });
            }
        },

        // Direct message
        showDirectMessageBox: function (type, user_ids = []) {
            let email = type == 'email';
            SBForm.clear(direct_message_box);
            direct_message_box.find('.sb-direct-message-users').val(user_ids.length ? user_ids.join(',') : 'all');
            direct_message_box.find('.sb-bottom > div').html('');
            direct_message_box.find('.sb-top-bar > div:first-child').html(sb_(`Send ${type == 'sms' ? 'text message' : type}`));
            direct_message_box.find('.sb-loading').sbLoading(false);
            direct_message_box.find('.sb-direct-message-subject').sbActivate(email).find('input').attr('required', email);
            direct_message_box.attr('data-type', type);
            direct_message_box.sbShowLightbox();
        }
    }

    /* 
    * ----------------------------------------------------------
    * # Profile
    * ----------------------------------------------------------
    */

    var SBProfile = {

        // Get all profile settings
        getAll: function (profile_area) {
            return SBForm.getAll(profile_area);
        },

        // Get a single setting
        get: function (input) {
            return SBForm.get(input);
        },

        // Set a single setting
        set: function (item, value) {
            return SBForm.set(item, value);
        },

        // Display the user box
        show: function (user_id) {
            loadingGlobal();
            activeUser(new SBUser({ 'id': user_id }));
            activeUser().update(() => {
                this.populate(activeUser(), profile_box.find('.sb-profile-list'));
                profile_box.find('.sb-profile').setProfile();
                activeUser().getConversations((response) => {
                    let user_type = activeUser().type;
                    if (SBF.isAgent(user_type)) {
                        this.agentData();
                    }
                    profile_box.find('.sb-user-conversations').html(activeUser().getConversationsCode(response));
                    profile_box.find('.sb-top-bar [data-value]').sbActivate(false);
                    if (!SBF.null(activeUser().get('email'))) {
                        profile_box.find('.sb-top-bar [data-value=email]').sbActivate();
                    }
                    if (activeUser().getExtra('phone') && SB_ADMIN_SETTINGS['sms']) {
                        profile_box.find('.sb-top-bar [data-value=sms]').sbActivate();
                    }
                    this.boxClasses(profile_box, user_type);
                    profile_box.attr('data-user-id', activeUser().id).sbShowLightbox();
                    loadingGlobal(false, false);
                });
                users[user_id] = activeUser();
                if (SBF.getURL('user') != user_id) pushState('?user=' + user_id);
            });
        },

        showEdit: function (user) {
            if (user instanceof SBUser) {
                let password = profile_edit_box.find('#password input');
                let current_user_type = user.type;
                let select = profile_edit_box.find('#user_type select');
                let email = profile_edit_box.find('#email input');

                profile_edit_box.removeClass('sb-user-new').attr('data-user-id', user.id);
                profile_edit_box.find('.sb-top-bar .sb-save').html(`<i class="sb-icon-check"></i>${sb_('Save changes')}`);
                profile_edit_box.find('.sb-profile').setProfile();
                profile_edit_box.find('.sb-custom-detail').remove();

                // Custom details
                let code = '';
                let default_ids = profile_edit_box.find('.sb-additional-details [id]').map(function () { return this.id; }).get().concat(['wp-id', 'perfex-id', 'whmcs-id', 'aecommerce-id', 'facebook-id', 'ip', 'os', 'current_url', 'country_code', 'browser_language', 'browser']);
                for (var id in user.extra) {
                    if (!default_ids.includes(id)) {
                        code += `<div id="${id}" data-type="text" class="sb-input sb-custom-detail"><span>${sb_(user.extra[id].name)}</span><input type="text"></div>`;
                    }
                }
                profile_edit_box.find('.sb-additional-details .sb-edit-box').append(code);

                // Set values
                this.populateEdit(user, profile_edit_box);
                if (SBF.isAgent(current_user_type)) {
                    email.prop('required', true);
                    password.prop('required', true);
                } else {
                    email.removeAttr('required');
                    password.removeAttr('required');
                }

                // User type select
                if (SB_ACTIVE_AGENT['user_type'] == 'admin' && SBF.isAgent(current_user_type)) {
                    select.html('<option value="agent">Agent</option><option value="admin"' + (current_user_type == 'admin' ? ' selected' : '') + '>Admin</option>');
                }

                // Password
                if (password.val() != '') {
                    password.val('********');
                }

                // Cloud
                if (SB_ADMIN_SETTINGS.cloud) {
                    profile_edit_box.setClass('sb-cloud-admin', SB_ADMIN_SETTINGS.cloud.id == user.id);
                }

                // Show the edit box
                this.boxClasses(profile_edit_box, current_user_type);
                profile_edit_box.sbShowLightbox();
            } else {
                SBF.error('User not of type SBUser', 'SBUsers.showEdit');
                return false;
            }
        },

        // Populate profile
        populate: function (user, profile_area) {
            let exclude = ['first_name', 'last_name', 'password', 'profile_image'];
            let code = '';
            if (profile_area.hasClass('sb-profile-list-conversation') && SBChat.conversation) {
                let source = SBChat.conversation.get('source');
                code = this.profileRow('conversation-id', SBChat.conversation.id, sb_('Conversation ID'));
                if (!SBF.null(source)) {
                    code += this.profileRow('conversation-source', source == 'fb' ? 'Facebook' : (source == 'wa' ? 'WhatsApp' : source), sb_('Source'));
                }
            }
            if (SB_ACTIVE_AGENT['user_type'] != 'admin') {
                exclude.push('token');
            }
            for (var key in user.details) {
                if (!exclude.includes(key)) {
                    code += this.profileRow(key, user.get(key));
                }
            }
            if (user.isExtraEmpty()) {
                SBF.ajax({
                    function: 'get-user-extra',
                    user_id: user.id
                }, (response) => {
                    for (var i = 0; i < response.length; i++) {
                        let slug = response[i]['slug'];
                        user.setExtra(slug, response[i]);
                        code += this.profileRow(slug, response[i]['value'], response[i]['name']);
                    }
                    profile_area.html(`<ul>${code}</ul>`);
                    collapse(profile_area, 145);
                });
            } else {
                for (var key in user.extra) {
                    let info = user.getExtra(key);
                    code += this.profileRow(key, info['value'], info['name']);
                }
                profile_area.html(`<ul>${code}</ul>`);
                collapse(profile_area, 145);
            }
        },

        profileRow: function (key, value, name = key) {
            if (value == '') return '';
            let icons = { 'id': 'padlock', 'conversation-id': 'padlock', 'full_name': 'user', 'email': 'envelope', 'phone': 'phone', 'user_type': 'user', 'last_activity': 'calendar', 'creation_time': 'calendar', 'token': 'shuffle', 'currency': 'currency', 'location': 'marker', 'country': 'marker', 'address': 'marker', 'city': 'marker', 'postal_code': 'marker', 'os': 'desktop', 'current_url': 'next', 'timezone': 'clock' };
            let icon = `<i class="sb-icon sb-icon-${key in icons ? icons[key] : 'plane'}"></i>`;
            let lowercase;
            let image = false;
            switch (key) {
                case 'last_activity':
                case 'creation_time':
                    value = SBF.beautifyTime(value);
                    break;
                case 'user_type':
                    value = SBF.slugToString(value);
                    break;
                case 'country_code':
                case 'language':
                case 'browser_language':
                    icon = `<img src="${SB_URL}/media/flags/${key == 'country_code' ? 'countries' : 'languages'}/${value.toLowerCase()}.png" />`;
                    break;
                case 'browser':
                    lowercase = value.toLowerCase();
                    if (lowercase.includes('chrome')) {
                        image = 'chrome';
                    } else if (lowercase.includes('edge')) {
                        image = 'edge';
                    } else if (lowercase.includes('firefox')) {
                        image = 'firefox';
                    } else if (lowercase.includes('opera')) {
                        image = 'opera';
                    } else if (lowercase.includes('safari')) {
                        image = 'safari';
                    }
                    break;
                case 'os':
                    lowercase = value.toLowerCase();
                    if (lowercase.includes('windows')) {
                        image = 'windows';
                    } else if (lowercase.includes('mac') || lowercase.includes('apple') || lowercase.includes('ipad') || lowercase.includes('iphone')) {
                        image = 'apple';
                    } else if (lowercase.includes('android')) {
                        image = 'android';
                    } else if (lowercase.includes('linux')) {
                        image = 'linux';
                    } else if (lowercase.includes('ubuntu')) {
                        image = 'ubuntu';
                    }
                    break;
                case 'conversation-source':
                    image = value.toLowerCase();
                case 'browser':
                case 'os':
                case 'conversation-source':
                    if (image) {
                        icon = `<img src="${SB_URL}/media/devices/${image}.svg" />`;
                    }
                    break;

            }
            return `<li data-id="${key}">${icon}<span>${sb_(SBF.slugToString(name))}</span><label>${value}</label></li>`;
        },

        // Populate profile edit box
        populateEdit: function (user, profile_edit_area) {
            profile_edit_area.find('.sb-details .sb-input').each((i, element) => {
                this.set(element, user.details[$(element).attr('id')]);
            });
            profile_edit_area.find('.sb-additional-details .sb-input').each((i, element) => {
                let key = $(element).attr('id');
                if (key in user.extra) {
                    this.set(element, user.extra[key]['value']);
                } else {
                    this.set(element, '');
                }
            });
        },

        // Clear the profile edit area
        clear: function (profile_edit_area) {
            SBForm.clear(profile_edit_area);
        },

        // Check for errors on user input
        errors: function (profile_edit_area) {
            return SBForm.errors(profile_edit_area.find('.sb-details'));
        },

        // Display a error message
        showErrorMessage: function (profile_edit_area, message) {
            SBForm.showErrorMessage(profile_edit_area, message);
        },

        // Agents data area
        agentData: function () {
            let code = `<div class="sb-title">${sb_('Feedback rating')}</div><div class="sb-rating-area sb-loading"></div>`;
            let area = profile_box.find('.sb-agent-area');
            area.html(code);
            SBF.ajax({
                function: 'get-rating'
            }, (response) => {
                if (response[0] == 0 && response[1] == 0) {
                    code = `<p class="sb-no-results">${sb_('No ratings yet.')}</p>`;
                } else {
                    let total = response[0] + response[1];
                    let positive = response[0] * 100 / total;
                    let negative = response[1] * 100 / total;
                    code = `<div><div>${sb_('Positive')}</div><span data-count="${response[0]}" style="width: ${Math.round(positive * 2)}px"></span><div>${positive.toFixed(2)} %</div></div><div><div>${sb_('Negative')}</div><span data-count="${response[1]}" style="width: ${Math.round(negative * 2)}px"></span><div>${negative.toFixed(2)} %</div></div><p class="sb-rating-count">${total} ${sb_('Ratings')}</p>`;
                }
                area.find('.sb-rating-area').html(code).sbLoading(false);
            });
        },

        boxClasses: function (box, user_type = false) {
            $(box).removeClass('sb-type-admin sb-type-agent sb-type-lead sb-type-user sb-type-visitor').addClass(`${user_type != false ? `sb-type-${user_type}` : ''} sb-agent-${SB_ACTIVE_AGENT['user_type']}`);
        }
    }

    /*
    * ----------------------------------------------------------
    * # Init
    * ----------------------------------------------------------
    */

    var SBAdmin = {
        dialog: dialog,
        open_popup: false,
        must_translate: false,
        conversations: SBConversations,
        users: SBUsers,
        settings: SBSettings,
        profile: SBProfile,
        apps: SBApps
    }
    window.SBAdmin = SBAdmin;

    $(document).ready(function () {

        admin = $('.sb-admin');
        header = admin.find('> .sb-header');
        conversations_area = admin.find('.sb-area-conversations');
        conversations_admin_list = conversations_area.find('.sb-admin-list');
        conversations_admin_list_ul = conversations_admin_list.find('.sb-scroll-area ul');
        users_area = admin.find('.sb-area-users');
        users_table = users_area.find('.sb-table-users');
        users_table_menu = users_area.find('.sb-menu-users');
        profile_box = admin.find('.sb-profile-box');
        profile_edit_box = admin.find('.sb-profile-edit-box');
        settings_area = admin.find('.sb-area-settings');
        automations_area = settings_area.find('.sb-automations-area');
        conditions_area = automations_area.find('.sb-conditions');
        automations_area_select = automations_area.find(' > .sb-select');
        automations_area_nav = automations_area.find(' > .sb-tab > .sb-nav > ul');
        reports_area = admin.find('.sb-area-reports');
        articles_area = settings_area.find('.sb-articles-area');
        saved_replies = conversations_area.find('.sb-replies');
        overlay = admin.find('.sb-lightbox-overlay');
        SITE_URL = typeof SB_URL != ND ? SB_URL.substr(0, SB_URL.indexOf('-content') - 3) : '';
        woocommerce_products_box = conversations_area.find('.sb-woocommerce-products');
        woocommerce_products_box_ul = woocommerce_products_box.find(' > div > ul');
        notes_panel = conversations_area.find('.sb-panel-notes');
        attachments_panel = conversations_area.find('.sb-panel-attachments');
        direct_message_box = admin.find('.sb-direct-message-box');
        wp_admin = SBApps.is('wordpress') && $('.wp-admin').length;
        dialogflow_intent_box = admin.find('.sb-dialogflow-intent-box');
        suggestions_area = conversations_area.find('.sb-editor > .sb-suggestions');

        // Browser history
        window.onpopstate = function () {
            if (SBF.getURL('user')) {
                if (!users_area.sbActive()) header.find('.sb-admin-nav #sb-users').click();
                SBProfile.show(SBF.getURL('user'));
            } else if (SBF.getURL('area')) {
                header.find('.sb-admin-nav #sb-' + SBF.getURL('area')).click();
            } else if (SBF.getURL('conversation')) {
                if (!conversations_area.sbActive()) header.find('.sb-admin-nav #sb-conversations').click();
                SBConversations.openConversation(SBF.getURL('conversation'));
            } else if (SBF.getURL('setting')) {
                if (!settings_area.sbActive()) header.find('.sb-admin-nav #sb-settings').click();
                settings_area.find('#tab-' + SBF.getURL('setting')).click();
            } else if (SBF.getURL('report')) {
                if (!reports_area.sbActive()) header.find('.sb-admin-nav #sb-reports').click();
                reports_area.find('#' + SBF.getURL('report')).click();
            }
        };

        if (SBF.getURL('area')) {
            setTimeout(() => { header.find('.sb-admin-nav #sb-' + SBF.getURL('area')).click() }, 300);
        }

        // Installation
        if (typeof SB_ADMIN_SETTINGS == ND) {
            let area = admin.find('.sb-intall');
            $(admin).on('click', '.sb-submit-installation', function () {
                if (loading(this)) return;
                let message = false;
                let account = area.find('#first-name').length;
                if (SBForm.errors(area)) {
                    message = account ? 'All fields are required. Minimum password length is 8 characters. Be sure you\'ve entered a valid email.' : 'All fields are required.';
                } else {
                    if (account && area.find('#password input').val() != area.find('#password-check input').val()) {
                        message = 'The passwords do not match.';
                    } else {
                        let url = window.location.href.replace('/admin', '').replace('.php', '').replace(/#$|\/$/, '').replace(/#$|\/$/, '');
                        $.ajax({
                            method: 'POST',
                            url: url + '/include/ajax.php',
                            data: {
                                function: 'installation',
                                details: $.extend(SBForm.getAll(area), { url: url })
                            }
                        }).done((response) => {
                            response = JSON.parse(response);
                            if (response != false) {
                                response = response[1];
                                if (response === true) {
                                    setTimeout(() => {
                                        window.location.href = window.location.href + '?refresh=true';
                                    }, 1000);
                                    return;
                                } else {
                                    switch (response) {
                                        case 'connection-error':
                                            message = 'Support Board cannot connect to the database. Please check the database information and try again.';
                                            break;
                                        case 'missing-details':
                                            message = 'Missing database details! Please check the database information and try again.';
                                            break;
                                        case 'missing-url':
                                            message = 'Support Board cannot get the plugin URL.';
                                            break;
                                        default:
                                            message = response;
                                    }
                                }
                            } else {
                                message = response;
                            }
                            if (message !== false) {
                                SBForm.showErrorMessage(area, message);
                                $('html, body').animate({ scrollTop: 0 }, 500);
                            }
                            $(this).sbLoading(false);
                        });
                    }
                }
                if (message !== false) {
                    SBForm.showErrorMessage(area, message);
                    $('html, body').animate({ scrollTop: 0 }, 500);
                    $(this).sbLoading(false);
                }
            });
            return;
        }

        // Initialization
        if (!admin.length) return;
        loadingGlobal();
        admin.removeAttr('style');
        if (isPWA()) {
            admin.addClass('sb-pwa');
        }
        if (SBApps.is('woocommerce')) {
            woocommerce_products_box = conversations_area.find('.sb-woocommerce-products');
            woocommerce_products_box_ul = woocommerce_products_box.find(' > div > ul');
        }
        if (localhost) {
            clearCache();
        }
        if (admin.find(' > .sb-rich-login').length) {
            return;
        }
        if (SB_ADMIN_SETTINGS['pusher']) {
            SBPusher.active = true;
            SBPusher.init(() => {
                SBPusher.presence(1, () => {
                    updateUsersActivity();
                });
                SBPusher.event('update-conversations', () => {
                    SBConversations.update();
                }, 'global');
                initialization();
            });

        } else {
            initialization();
            setInterval(function () {
                updateUsersActivity();
            }, 10000);
        }

        // Keyboard shortcuts
        $(window).keydown(function (e) {
            let code = e.which;
            let valid = false;
            if ([13, 27, 37, 38, 39, 40, 46].includes(code)) {
                if (admin.find('.sb-dialog-box').sbActive()) {
                    let target = admin.find('.sb-dialog-box');
                    switch (code) {
                        case 46:
                        case 27:
                            target.find('.sb-cancel').click();
                            break;
                        case 13:
                            target.find(target.attr('data-type') != 'info' ? '.sb-confirm' : '.sb-close').click();
                            break;
                    }
                    valid = true;
                } else if ([38, 40, 46].includes(code) && conversations_area.sbActive()) {
                    if (code == 46) {
                        if (conversations_area.find('.sb-editor textarea').is(':focus')) return;
                        let target = conversations_area.find(' > div > .sb-conversation');
                        target.find('.sb-top [data-value="' + (target.attr('data-conversation-status') == 3 ? 'delete' : 'archive') + '"]').click();
                    } else if (e.ctrlKey) {
                        let target = conversations_admin_list_ul.find('.sb-active');
                        if (code == 40) {
                            target.next().click();
                        } else {
                            target.prev().click();
                        }
                        SBConversations.scrollTo();
                    }
                    valid = true;
                } else if ([37, 39].includes(code) && users_area.sbActive() && admin.find('.sb-lightbox').sbActive()) {
                    let target = users_table.find(`[data-user-id="${activeUser().id}"]`);
                    target = code == 39 ? target.next() : target.prev();
                    if (target.length) {
                        admin.sbHideLightbox();
                        SBProfile.show(target.attr('data-user-id'));
                    }
                    valid = true;
                } else if ([46, 27].includes(code)) {
                    if (admin.find('.sb-lightbox').sbActive()) {
                        admin.sbHideLightbox();
                        valid = true;
                    } else {
                        let target = admin.find('.sb-search-btn.sb-active');
                        if (target.length) {
                            target.find('i').click();
                            valid = true;
                        }
                    }
                }
                if (valid) {
                    e.preventDefault();
                }
            }
        });

        // Check if the admin is active
        $(document).on('click keydown mousemove', function () {
            SBF.debounce(function () {
                if (!SBChat.tab_active) {
                    SBF.visibilityChange();
                }
                SBChat.tab_active = true;
                clearTimeout(active_interval);
                active_interval = setTimeout(() => { SBChat.tab_active = false }, 10000);
            }, '#3', 8000);
        });

        // Updates 
        $(header).on('click', '.sb-version', function () {
            let box = admin.find('.sb-updates-box');
            SBF.ajax({
                function: 'get-versions'
            }, (response) => {
                let code = '';
                let names = { 'sb': 'Support Board', 'slack': 'Slack', 'dialogflow': 'Dialogflow', 'tickets': 'Tickets', 'woocommerce': 'Woocommerce', 'ump': 'Ultimate Membership Pro', 'perfex': 'Perfex', 'whmcs': 'WHMCS', 'aecommerce': 'Active eCommerce', 'messenger': 'Messenger', 'whatsapp': 'WhatsApp', 'armember': 'ARMember' };
                let updates = false;
                for (var key in response) {
                    if (SBApps.is(key)) {
                        let updated = SB_VERSIONS[key] == response[key];
                        if (!updated) {
                            updates = true;
                        }
                        code += `<div class="sb-input"><span>${names[key]}</span><div${updated ? ' class="sb-green"' : ''}>${updated ? sb_('You are running the latest version.') : sb_('Update available! Please update now.')} ${sb_('Your version is')} V ${SB_VERSIONS[key]}.</div></div>`;
                    }
                }
                if (updates) {
                    box.find('.sb-update').removeClass('sb-hide');
                } else {
                    box.find('.sb-update').addClass('sb-hide');
                }
                loadingGlobal(false);
                box.find('.sb-main').prepend(code);
                box.sbShowLightbox();
            });
            loadingGlobal(true);
            box.sbActivate(false);
            box.find('.sb-input').remove();
        });

        $(admin).on('click', '.sb-updates-box .sb-update', function () {
            if (loading(this)) return;
            let box = admin.find('.sb-updates-box');
            SBF.ajax({
                function: 'update'
            }, (response) => {
                let error = '';
                if (SBF.errorValidation(response, 'envato-purchase-code-not-found')) {
                    error = 'Please go to Settings > Miscellaneous and insert the Envato Purchase Code of Support Board.'
                } else if (Array.isArray(response) && !response.length) {
                    error = 'Invalid Envato Purchase Code.'
                } else {
                    let success = true;
                    for (var key in response) {
                        if (response[key] != 'success') {
                            success = false;
                            break;
                        }
                    }
                    if (!success) {
                        error = JSON.stringify(response);
                    }
                }
                if (error == '') {
                    clearCache();
                    showResponse('Update completed.');
                    location.reload();
                } else {
                    SBForm.showErrorMessage(box, error);
                }
                $(this).sbLoading(false);
            });
        });

        setTimeout(function () {
            let last = SBF.storage('last-update-check');
            let today_arr = [today.getMonth(), today.getDate()];
            if (SB_ADMIN_SETTINGS.cloud) return;
            if (last == false || today_arr[0] != last[0] || (today_arr[1] > (last[1] + 10))) {
                SBF.storage('last-update-check', today_arr);
                if (SB_ADMIN_SETTINGS['auto-updates']) {
                    SBF.ajax({
                        function: 'update'
                    }, (response) => {
                        if (typeof response !== 'string' && !Array.isArray(response)) {
                            showResponse('Automatic update completed. Reload the admin area to apply the update.');
                            clearCache();
                        }
                    });
                } else if (SB_ACTIVE_AGENT['user_type'] == 'admin') {
                    SBF.ajax({
                        function: 'updates-available'
                    }, (response) => {
                        if (response === true) {
                            showResponse(`${sb_('Update available.')} <span onclick="$(\'.sb-version\').click()">${sb_('Click here to update now')}</span>`, 'info');
                        }
                    });
                }
            }
        }, 1000);

        // Apps
        $(admin).on('click', '.sb-apps > div', function () {
            let box = admin.find('.sb-app-box');
            let app_name = $(this).data('app');
            let ga = '?utm_source=plugin&utm_medium=admin_area&utm_campaign=plugin';
            SBF.ajax({
                function: 'app-get-key',
                app_name: app_name
            }, (response) => {
                box.find('input').val(response);
            });
            box.find('input').val('');
            box.find('.sb-top-bar > div:first-child').html($(this).find('h2').html());
            box.find('p').html($(this).find('p').html());
            box.attr('data-app', app_name);
            box.find('.sb-btn-app-setting').sbActivate(SBApps.is(app_name));
            box.find('.sb-btn-app-puchase').attr('href', 'https://board.support/shop/' + app_name + ga);
            box.find('.sb-btn-app-details').attr('href', 'https://board.support/' + app_name + ga);
            box.sbShowLightbox();
        });

        $(admin).on('click', '.sb-app-box .sb-activate', function () {
            let box = admin.find('.sb-app-box');
            let key = box.find('input').val();
            let app_name = box.attr('data-app');
            if (key != '') {
                if (loading(this)) return;
                SBF.ajax({
                    function: 'app-activation',
                    app_name: app_name,
                    key: key
                }, (response) => {
                    if (SBF.errorValidation(response)) {
                        let error = '';
                        response = response[1];
                        if (response == 'invalid-key') {
                            error = 'It looks like your license key is invalid. If you believe this is an error, please contact support.';
                        } else if (response == 'expired') {
                            error = `Your license key is expired. Please purchase a new license <a href="https://board.support/shop/${app_name}" target="_blank"> here</a>.`;
                        } else {
                            error = 'Error: ' + response;
                        }
                        SBForm.showErrorMessage(box, error);
                        $(this).sbLoading(false);
                    } else {
                        showResponse('Activation complete! Page reload in progress...');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                });
            } else {
                SBForm.showErrorMessage(box, 'Please insert the license key.');
            }
        });

        $(admin).on('click', '.sb-app-box .sb-btn-app-setting', function () {
            settings_area.find('#tab-' + $(this).closest('[data-app]').attr('data-app')).click()
        });

        // Desktop and flash notifications
        if (typeof Notification !== ND && (SB_ADMIN_SETTINGS['desktop-notifications'] == 'all' || SB_ADMIN_SETTINGS['desktop-notifications'] == 'agents') && !SB_ADMIN_SETTINGS['push-notifications']) {
            SBConversations.desktop_notifications = true;
        }

        if (SB_ADMIN_SETTINGS['flash-notifications'] == 'all' || SB_ADMIN_SETTINGS['flash-notifications'] == 'agents') {
            SBConversations.flash_notifications = true;
        }

        // Cron jobs
        if (today.getDate() != SBF.storage('admin-clean')) {
            setTimeout(function () {
                SBF.ajax({ function: 'cron-jobs' });
                SBF.storage('admin-clean', today.getDate());
            }, 10000);
        }

        // Collapse button
        $(admin).on('click', '.sb-collapse-btn', function () {
            let active = $(this).sbActive();
            let height = active ? $(this).parent().data('height') + 'px' : '';
            $(this).html(sb_(active ? 'View more' : 'Close'));
            $(this).parent().find(' > div, > ul').css({ 'height': height, 'max-height': height });
            $(this).sbActivate(!active);
        });

        // Close lightbox popup
        $(admin).on('click', '.sb-popup-close', function () {
            admin.sbHideLightbox(true);
        });

        /*
        * ----------------------------------------------------------
        * # Responsive and mobile
        * ----------------------------------------------------------
        */

        if (responsive) {
            $(admin).on('click', '.sb-menu-mobile > i', function () {
                $(this).toggleClass('sb-active');
                SBAdmin.open_popup = $(this).parent();
            });

            $(admin).on('click', '.sb-menu-mobile a', function () {
                $(this).closest('.sb-menu-mobile').find(' > i').sbActivate(false);
            });

            $(admin).on('click', '.sb-menu-wide,.sb-nav', function () {
                $(this).toggleClass('sb-active');
            });

            $(admin).on('click', '.sb-menu-wide > ul > li, .sb-nav > ul > li', function (e) {
                let menu = $(this).parent().parent();
                menu.find('li').sbActivate(false);
                menu.find('> div:not(.sb-menu-wide):not(.sb-btn)').html($(this).html());
                menu.sbActivate(false);
                if (menu.find('> .sb-menu-wide').length) {
                    menu.closest('.sb-scroll-area').scrollTop(menu.next()[0].offsetTop - (admin.hasClass('sb-header-hidden') ? 70 : 130));
                }
                e.preventDefault();
                return false;
            });

            $(admin).find('.sb-admin-list .sb-scroll-area, main > div > .sb-scroll-area,.sb-area-settings > .sb-tab > .sb-scroll-area,.sb-area-reports > .sb-tab > .sb-scroll-area').on('scroll', function () {
                let scroll = $(this).scrollTop();
                if (scrolls['last'] < (scroll - 10) && scrolls['header']) {
                    admin.addClass('sb-header-hidden');
                    scrolls['header'] = false;
                } else if (scrolls['last'] > (scroll + 10) && !scrolls['header'] && !scrolls['always_hidden']) {
                    admin.removeClass('sb-header-hidden');
                    scrolls['header'] = true;
                }
                scrolls['last'] = scroll;
            });

            $(admin).on('click', '.sb-search-btn i', function () {
                if ($(this).parent().sbActive()) {
                    admin.addClass('sb-header-hidden');
                    scrolls['always_hidden'] = true;
                } else {
                    scrolls['always_hidden'] = false;
                    if (conversations_admin_list_ul.parent().scrollTop() < 10) {
                        admin.removeClass('sb-header-hidden');
                    }
                }
            });

            $(admin).on('click', '.sb-top .sb-btn-back', function () {
                SBConversations.mobileCloseConversation();
            });

            $(users_table).find('th:first-child').html(sb_('Order by'));

            $(users_table).on('click', 'th:first-child', function () {
                $(this).parent().toggleClass('sb-active');
            });
        }

        /*
        * ----------------------------------------------------------
        * # Left nav
        * ----------------------------------------------------------
        */

        $(header).on('click', ' .sb-admin-nav a', function () {
            let id = $(this).attr('id');
            header.find('.sb-admin-nav a').sbActivate(false);
            admin.find(' > main > div').sbActivate(false).eq($(this).index()).sbActivate();
            $(this).sbActivate();
            SBF.deactivateAll();

            switch (id) {
                case 'sb-conversations':
                    if (!responsive && !SBF.getURL('conversation')) {
                        SBConversations.clickFirst();
                    }
                    SBConversations.update();
                    SBConversations.startRealTime();
                    SBUsers.stopRealTime();
                    break;
                case 'sb-users':
                    SBUsers.startRealTime();
                    SBConversations.stopRealTime();
                    if (!SBUsers.init) {
                        loadingGlobal();
                        users_pagination = 1;
                        SBF.ajax({
                            function: 'get-users',
                            user_types: ['user', 'visitor', 'lead']
                        }, (response) => {
                            SBUsers.populate(response);
                            SBUsers.updateMenu();
                            SBUsers.init = true;
                            SBUsers.datetime_last_user = SBF.dateDB('now');
                            loadingGlobal(false);
                        });
                    }
                    break;
                case 'sb-settings':
                    if (!SBSettings.init) {
                        loadingGlobal();
                        SBF.ajax({
                            function: 'get-all-settings'
                        }, (response) => {
                            let translations = response['external-settings-translations'];
                            SBSettings.initHTML(response);
                            SBSettings.translations.translations = Array.isArray(translations) && !translations.length ? {} : translations;
                            delete response['external-settings-translations'];
                            for (var key in response) {
                                SBSettings.set(key, response[key]);
                            }
                            SBSettings.initPlugins();
                            SBSettings.init = true;
                            loadingGlobal(false);
                        });
                    }
                    SBUsers.stopRealTime();
                    SBConversations.stopRealTime();
                    break;
                case 'sb-reports':
                    if (reports_area.sbLoading()) {
                        $.getScript(SB_URL + '/vendor/moment.min.js', () => {
                            $.getScript(SB_URL + '/vendor/daterangepicker.min.js', () => {
                                $.getScript(SB_URL + '/vendor/chart.min.js', () => {
                                    SBReports.initDatePicker();
                                    SBReports.initReport('conversations');
                                    reports_area.sbLoading(false);
                                });
                            });
                        });
                    }
                    SBUsers.stopRealTime();
                    SBConversations.stopRealTime();
                    break;
            }
            let slug = id.substr(3);
            let url_area = SBF.getURL('area');
            if (url_area != slug && ((slug == 'conversations' && !SBF.getURL('conversation')) || (slug == 'users' && !SBF.getURL('user')) || (slug == 'settings' && !SBF.getURL('setting')) || (slug == 'reports' && !SBF.getURL('report')))) pushState('?area=' + slug);
        });

        $(header).on('click', '.sb-profile', function () {
            $(this).next().toggleClass('sb-active');
        });

        $(header).on('click', '[data-value="logout"],.logout', function () {
            SBUsers.stopRealTime();
            SBConversations.stopRealTime();
            SBF.logout();
        });

        $(header).on('click', '[data-value="edit-profile"],.edit-profile', function () {
            loadingGlobal();
            let user = new SBUser({ 'id': SB_ACTIVE_AGENT['id'] });
            user.update(() => {
                activeUser(user);
                conversations_area.find('.sb-board').addClass('sb-no-conversation');
                conversations_admin_list_ul.find('.sb-active').sbActivate(false);
                SBProfile.showEdit(user);
            });
        });

        $(header).on('click', '[data-value="status"]', function () {
            agent_online = !$(this).hasClass('sb-online');
            $(this).html(sb_(agent_online ? 'Online' : 'Offline')).attr('class', agent_online ? 'sb-online' : 'sb-offline');
            if (SBPusher.active) {
                if (agent_online) {
                    SBPusher.presence();
                } else {
                    SBPusher.presenceUnsubscribe();
                }
            }
        });

        $(header).find('.sb-account').setProfile(SB_ACTIVE_AGENT['full_name'], SB_ACTIVE_AGENT['profile_image']);

        /*
        * ----------------------------------------------------------
        * # Conversations area
        * ----------------------------------------------------------
        */

        // Open the conversation clicked on the left menu
        $(conversations_admin_list_ul).on('click', 'li', function () {
            SBConversations.openConversation($(this).attr('data-conversation-id'), $(this).attr('data-user-id'), false);
            SBF.deactivateAll();
        });

        // Open the user conversation clicked on the bottom right area or user profile box
        $(admin).on('click', '.sb-user-conversations li', function () {
            SBConversations.openConversation($(this).attr('data-conversation-id'), activeUser().id, $(this).attr('data-conversation-status'));
            SBF.deactivateAll();
        });

        // Archive, delete or restore conversations
        $(conversations_area).on('click', '.sb-top ul a', function () {
            let status_code = -1;
            let message = 'The conversation will be ';
            let value = $(this).attr('data-value');
            let conversation_id = SBChat.conversation.id;
            switch (value) {
                case 'inbox':
                    status_code = 0;
                    message += 'restored.';
                    break;
                case 'archive':
                    message += 'archived.';
                    status_code = 3;
                    break;
                case 'delete':
                    message += 'deleted.';
                    status_code = 4;
                    break;
                case 'empty-trash':
                    status_code = 5;
                    message = 'All conversations in the trash (including their messages) will be deleted permanently.'
                    break;
                case 'transcript':
                    SBConversations.transcript(conversation_id);
                    break;
                case 'read':
                    status_code = 0;
                    message += 'marked as read.';
                    break;
            }
            if (status_code != -1) {
                dialog(message, 'alert', function () {
                    SBF.ajax({
                        function: 'update-conversation-status',
                        conversation_id: conversation_id,
                        status_code: status_code
                    }, () => {
                        if ([0, 3, 4].includes(status_code)) {
                            for (var i = 0; i < conversations.length; i++) {
                                if (conversations[i]['conversation_id'] == conversation_id) {
                                    conversations[i]['conversation_status_code'] = status_code;
                                    break;
                                }
                            }
                        }
                        if (value == 'read') {
                            conversations_admin_list_ul.find('.sb-active').attr('data-conversation-status', 0);
                            conversations_area.find('.sb-top [data-value="read"]').sbActivate(false);
                        } else {
                            conversations_admin_list.find('.sb-select li.sb-active').click();
                            conversations_admin_list.find('.sb-select ul').sbActivate(false);
                        }
                        if (SB_ADMIN_SETTINGS['close-message'] && status_code == 3) {
                            SBF.ajax({ function: 'close-message', bot_id: SB_ADMIN_SETTINGS['bot-id'], conversation_id: conversation_id });
                        }
                    });
                    if (SBChat.conversation && SBChat.conversation.id == conversation_id) SBChat.conversation.set('conversation_status_code', status_code);
                });
            }
        });

        // Saved replies
        SBF.ajax({
            function: 'saved-replies'
        }, (response) => {
            let code = `<p class="sb-no-results">${sb_('No saved replies found. Add new saved replies via Settings > Miscellaneous.')}</p>`;
            if (Array.isArray(response)) {
                if (response.length > 0 && response[0]['reply-name'] != '') {
                    code = '';
                    saved_replies_list = response;
                    for (var i = 0; i < response.length; i++) {
                        code += `<li><div>${response[i]['reply-name']}</div><div>${response[i]['reply-text']}</div></li>`;
                    }
                }
            }
            saved_replies.find('.sb-replies-list > ul').html(code).sbLoading(false);
        });

        $(conversations_area).on('click', '.sb-btn-saved-replies', function () {
            saved_replies.sbTogglePopup(this);
        });

        $(saved_replies).on('click', '.sb-replies-list li', function () {
            SBChat.insertText($(this).find('div:last-child').html());
            SBF.deactivateAll();
            admin.removeClass('sb-popup-active');
        });

        $(saved_replies).on('input', '.sb-search-btn input', function () {
            let search = $(this).val().toLowerCase();
            SBF.search(search, () => {
                let code = '';
                let all = search.length > 1 ? false : true;
                for (var i = 0; i < saved_replies_list.length; i++) {
                    if (all || saved_replies_list[i]['reply-name'].toLowerCase().includes(search) || saved_replies_list[i]['reply-text'].toLowerCase().includes(search)) {
                        code += `<li><div>${saved_replies_list[i]['reply-name']}</div><div>${saved_replies_list[i]['reply-text']}</div></li>`;
                    }
                }
                saved_replies.find('.sb-replies-list > ul').html(code);
            });
        });

        // Pagination for conversations
        $(conversations_admin_list).find('.sb-scroll-area').on('scroll', function () {
            if (!is_busy && !SBConversations.is_search && scrollPagination(this, true) && !pagination_end) {
                let parent = conversations_area.find('.sb-admin-list');
                is_busy = true;
                parent.append('<div class="sb-loading-global sb-loading"></div>');
                SBF.ajax({
                    function: 'get-conversations',
                    pagination: pagination,
                    status_code: parent.find(' > .sb-top > .sb-select p').attr('data-value'),
                    routing: SB_ADMIN_SETTINGS['routing'],
                    routing_unassigned: SB_ADMIN_SETTINGS['assign-conversation-to-agent']
                }, (response) => {
                    pagination_end = response.length;
                    if (pagination_end) {
                        let code = '';
                        is_busy = false;
                        for (var i = 0; i < pagination_end; i++) {
                            code += SBConversations.getListCode(response[i]);
                            conversations.push(response[i]);
                        }
                        pagination++;
                        conversations_admin_list_ul.append(code); 
                        scrollPagination(this);
                    }
                    parent.find(' > .sb-loading').remove();
                });
            }
        });

        // Event: message deleted
        $(document).on('SBMessageDeleted', function () {
            let last_message = SBChat.conversation.getLastMessage();
            if (last_message != false) {
                conversations_admin_list_ul.find('li.sb-active p').html(last_message.message);
            } else {
                conversations_admin_list_ul.find('li.sb-active').remove();
                SBConversations.clickFirst();
                SBConversations.scrollTo();
            }
        });

        // Event: message sent
        $(document).on('SBMessageSent', function (e, response) {
            let conversation_id = response['conversation_id'];
            let item = conversations_admin_list_ul.find(`[data-conversation-id="${conversation_id}"]`);
            if (response['message'] != '') {
                item.find('p').html(response['message']);
            }
            if (response['conversation_status_code'] != -1) {
                item.attr('data-conversation-status', response['conversation_status_code']);
                SBConversations.updateMenu();
            }
            if (SBApps.messenger.check(SBChat.conversation)) {
                SBApps.messenger.send(activeUser().getExtra('facebook-id').value, SBChat.conversation.get('extra'), response['message'], response['attachments'], (response) => {
                    if ('error' in response) {
                        dialog('Error. Message not sent to Messenger. Error message: ' + response.error.message, 'info');
                    }
                });
            }
            if (SBApps.whatsapp.check(SBChat.conversation)) {
                SBApps.whatsapp.send(SBApps.whatsapp.activeUserPhone(), response['message'], response['attachments'], (response) => {
                    if ('ErrorCode' in response) {
                        dialog('Error. Message not sent to WhatsApp. Error message: ' + response.errorMessage, 'info');
                    }
                });
            }
            if (SB_ADMIN_SETTINGS['smart-reply']) {
                suggestions_area.html('');
            }
            if (SB_ADMIN_SETTINGS['assign-conversation-to-agent'] && SBChat.conversation && SBChat.conversation.id == conversation_id && SBF.null(SBChat.conversation.get('agent_id'))) {
                SBConversations.assignAgent(conversation_id, SB_ACTIVE_AGENT['id'], () => {
                    SBChat.conversation.set('agent_id', SB_ACTIVE_AGENT['id']);
                    $(conversations_area).find('#conversation-agent > p').html(SB_ACTIVE_AGENT['full_name']);
                });
            }
        });

        // Event: new message of active chat conversation received
        $(document).on('SBNewMessagesReceived', function (e, response) {
            let payload = response.get('payload');
            let area = conversations_area.find('.sb-conversation');
            setTimeout(function () {
                area.find('.sb-top .sb-status-typing').remove();
            }, 300);
            if (SB_ADMIN_SETTINGS['smart-reply']) {
                if (SBF.isAgent(response.get('user_type'))) {
                    if (SB_ADMIN_SETTINGS['smart-reply-agent-assistant']) SBApps.dialogflow.smartReplyUpdate(response.message);
                } else {
                    SBApps.dialogflow.smartReply(response.message);
                }
            }
            if (SBAdmin.must_translate) {
                let message = area.find(`[data-id="${response.id}"] .sb-message`);
                let message_menu = area.find(`[data-id="${response.id}"] .sb-menu`);
                if ('original-message' in payload) {
                    message.html(payload['original-message']);
                    message_menu.prepend(`<li data-value="original">${sb_('View translation')}</li>`);
                    SBChat.conversation.getMessage(response.id).set('translation', payload['original-message']);
                } else {
                    SBApps.dialogflow.translate([response.message], SB_ADMIN_SETTINGS['active-agent-language'], (response_2) => {
                        if (response_2) {
                            message.html(response_2[0].translatedText);
                            message_menu.prepend(`<li data-value="original">${sb_('View original message')}</li>`);
                            SBChat.conversation.getMessage(response.id).set('translation', response_2[0].translatedText);
                        }
                    });
                }
            }
            if ('ErrorCode' in payload) {
                dialog('Error. Message not sent to WhatsApp. Error message: ' + payload['ErrorMessage'], 'info');
            }
            if ('whatsapp-fallback' in payload) {
                showResponse(`Message sent as text message.${'whatsapp-template-fallback' in payload ? ' The user has been notified via WhatsApp Template notification.' : ''}`);
            }
            if ('whatsapp-template-fallback' in payload && !('whatsapp-fallback' in payload)) {
                showResponse('The user has been notified via WhatsApp Template notification.');
            }
            SBConversations.update();
        });

        // Event: new conversation created 
        $(document).on('SBNewConversationCreated', function () {
            SBConversations.update();
        });

        // Event: email notification sent
        $(document).on('SBEmailSent', function (e, response) {
            showResponse(`The user has been notified by email${SB_ADMIN_SETTINGS['sms-active-users'] && users[response['recipient_id']].getExtra('phone') ? ' and text message' : ''}.`);
        });

        // Event: SMS notification sent
        $(document).on('SBSMSSent', function (e, response) {
            if (!SB_ADMIN_SETTINGS['notify-user-email'] || SBF.null(users[response['recipient_id']].get('email'))) {
                showResponse('The user has been notified by text message.');
            }
        });

        // Event: user typing status change
        $(document).on('SBTyping', function (e, response) {
            SBConversations.typing(response);
        });

        // Conversations search
        $(conversations_admin_list).on('input', '.sb-search-btn input', function () {
            SBConversations.search(this);
        });

        $(conversations_area).on('click', '.sb-admin-list .sb-search-btn i', function () {
            SBF.searchClear(this, () => { SBConversations.search($(this).next()) });
        });

        // Conversations filter
        $(conversations_area).on('click', '.sb-admin-list .sb-select li', function () {
            let parent = conversations_admin_list_ul.parent();
            let status_code = $(this).data('value');
            if (loading(parent)) return;
            pagination = 1;
            SBF.ajax({
                function: 'get-conversations',
                status_code: status_code,
                routing: SB_ADMIN_SETTINGS['routing'],
                routing_unassigned: SB_ADMIN_SETTINGS['assign-conversation-to-agent']
            }, (response) => {
                SBConversations.populateList(response);
                conversations_area.find('.sb-conversation').attr('data-conversation-status', status_code);
                if (response.length) {
                    if (!responsive) {
                        if (SBChat.conversation != false) {
                            let conversation = conversations_admin_list_ul.find(`[data-conversation-id="${SBChat.conversation.id}"]`);
                            if (conversation.length) conversation.sbActivate();
                            else if (status_code == SBChat.conversation.get('conversation_status_code')) conversations_admin_list_ul.prepend(SBConversations.getListCode(SBChat.conversation).replace('<li', '<li class="sb-active"'));
                            else SBConversations.clickFirst();
                        } else SBConversations.clickFirst();
                        SBConversations.scrollTo();
                    }
                } else {
                    conversations_area.find('.sb-board').addClass('sb-no-conversation');
                    SBChat.conversation = false;
                }
                parent.sbLoading(false);
            });
        });

        // Display the user details box
        $(conversations_area).on('click', '.sb-user-details .sb-scroll-area > .sb-profile,.sb-top > a', function () {
            let user_id = conversations_admin_list_ul.find('.sb-active').attr('data-user-id');
            if (activeUser().id != user_id) {
                activeUser(users[user_id]);
            }
            SBProfile.show(activeUser().id);
        });

        // Right profile list methods
        $(admin).on('click', '.sb-profile-list [data-id="location"]', function () {
            let location = $(this).find('label').html().replace(', ', '+');
            dialog('<iframe src="https://maps.google.com/maps?q=' + location + '&output=embed"></iframe>', 'map');
        });

        $(admin).on('click', '.sb-profile-list [data-id="timezone"]', function () {
            SBF.getLocationTimeString(activeUser().extra, (response) => {
                loadingGlobal(false);
                dialog(response, 'info');
            });
        });

        $(admin).on('click', '.sb-profile-list [data-id="current_url"]', function () {
            let label = $(this).find('label');
            window.open(SBF.null(label.attr('data-value')) ? label.html() : label.attr('data-value'));
        });

        $(admin).on('click', '.sb-profile-list [data-id="conversation-source"]', function () {
            if ($(this).find('img').attr('src').indexOf('whatsapp') && activeUser().getExtra('phone')) {
                window.open('https://wa.me/' + SBApps.whatsapp.activeUserPhone());
            } else {
                window.open('https://www.facebook.com/messages/t/' + SBChat.conversation.get('extra'));
            }
        });

        $(admin).on('click', '.sb-profile-list [data-id="wp-id"]', function () {
            window.open(window.location.href.substr(0, window.location.href.lastIndexOf('/')) + '/user-edit.php?user_id=' + activeUser().getExtra('wp-id').value);
        });

        // Dialogflow
        $(conversations_area).on('click', '.sb-menu [data-value="bot"]', function () {
            SBApps.dialogflow.showCreateIntentBox($(this).closest('[data-id]').attr('data-id'));
        });

        $(dialogflow_intent_box).on('click', '.sb-intent-add [data-value="add"]', function () {
            dialogflow_intent_box.find('> div > .sb-type-text').last().after('<div class="sb-input-setting sb-type-text"><input type="text"></div>');
        });

        $(dialogflow_intent_box).on('click', '.sb-intent-add [data-value="previous"],.sb-intent-add [data-value="next"]', function () {
            let message_id = dialogflow_intent_box.attr('data-message-id');
            let next = $(this).attr('data-value') == 'next';
            let messages = SBChat.conversation.getUserMessages();
            for (var i = 0; i < messages.length; i++) {
                if (messages[i].id == message_id) {
                    if ((i == 0 && !next) || (i == (messages.length - 1) && next)) return;
                    i = i + (next ? 1 : -1);
                    dialogflow_intent_box.find('.sb-first input').val(messages[i].message);
                    dialogflow_intent_box.attr('data-message-id', messages[i].id);
                    break;
                }
            }
        });

        $(dialogflow_intent_box).on('click', '.sb-send', function () {
            SBApps.dialogflow.submitIntent(this);
        });

        $(dialogflow_intent_box).on('input', '.sb-search-btn input', function () {
            SBApps.dialogflow.searchIntents($(this).val());
        });

        $(dialogflow_intent_box).on('click', '.sb-search-btn i', function () {
            SBF.searchClear(this, () => { SBApps.dialogflow.searchIntents($(this).val()) });
        });

        $(dialogflow_intent_box).on('click', '#sb-intent-preview', function () {
            SBApps.dialogflow.previewIntent(dialogflow_intent_box.find('#sb-intents-select').val());
        });

        // Departments
        $(conversations_area).on('click', '#conversation-department li', function (e) {
            let select = $(this).parent().parent();
            if ($(this).data('value') == select.find(' > p').attr('data-value')) {
                return true;
            }
            if (SBChat.conversation == false) {
                $(this).parent().sbActivate(false);
                e.preventDefault();
                return false;
            }
            if (!select.sbLoading()) {
                dialog(`${sb_('All agents assigned to the new department will be notified. The new department will be')} ${$(this).html()}.`, 'alert', () => {
                    select.sbLoading(true);
                    SBConversations.assignDepartment(SBChat.conversation.id, $(this).data('id'), () => {
                        showResponse('Department updated. The agents have been notified.');
                        select.find(' > p').attr('data-value', $(this).data('value')).html($(this).html()).next().sbActivate(false);
                        if (SB_ACTIVE_AGENT['user_type'] == 'agent' && SB_ACTIVE_AGENT['department'] && SB_ACTIVE_AGENT['department'] != $(this).data('id')) {
                            conversations_admin_list_ul.find(`[data-conversation-id="${SBChat.conversation.id}"]`).remove();
                            SBConversations.clickFirst();
                        }
                        select.sbLoading(false);
                    });
                });
            }
            e.preventDefault();
            return false;
        });

        // Agent assignment
        $(conversations_area).on('click', '#conversation-agent li', function (e) {
            let select = $(this).parent().parent();
            let agent_id = $(this).data('id');
            if (agent_id == select.find(' > p').attr('data-value') || agent_id == SB_ACTIVE_AGENT['id']) {
                return true;
            }
            if (SBChat.conversation == false) {
                $(this).parent().sbActivate(false);
                e.preventDefault();
                return false;
            }
            if (!select.sbLoading()) {
                dialog(`${sb_('The new agent will be')} ${$(this).html()}.`, 'alert', () => {
                    select.sbLoading(true);
                    SBConversations.assignAgent(SBChat.conversation.id, agent_id, () => {
                        if (agent_id) showResponse('Agent assigned. The agent has been notified.');
                        select.find(' > p').attr('data-value', $(this).data('value')).html($(this).html()).next().sbActivate(false);
                        if (SB_ACTIVE_AGENT['user_type'] == 'agent' && (!SB_ADMIN_SETTINGS['assign-conversation-to-agent'] || agent_id != '')) {
                            conversations_admin_list_ul.find(`[data-conversation-id="${SBChat.conversation.id}"]`).remove();
                            SBConversations.clickFirst();
                        }
                        select.sbLoading(false);
                    });
                });
            }
            e.preventDefault();
            return false;
        });

        // Notes 
        notes_panel.on('click', '> i', function (e) {
            admin.find('.sb-notes-box').sbShowLightbox();
            e.preventDefault();
            return false;
        });

        notes_panel.on('click', '.sb-delete-note', function () {
            let item = $(this).parents().eq(1);
            SBConversations.notes.delete(SBChat.conversation.id, item.attr('data-id'), (response) => {
                if (response === true) item.remove(); else SBF.error(response);
            });
        });

        $(admin).on('click', '.sb-add-note', function () {
            let textarea = $(this).parent().parents().eq(1).find('textarea');
            let message = textarea.val();
            if (message.length == 0) {
                SBForm.showErrorMessage(admin.find('.sb-notes-box'), 'Please write something...');
            } else {
                if (loading(this)) return;
                SBConversations.notes.add(SBChat.conversation.id, SB_ACTIVE_AGENT['id'], SB_ACTIVE_AGENT['full_name'], message, (response) => {
                    if (Number.isInteger(response)) {
                        $(this).sbLoading(false);
                        admin.sbHideLightbox();
                        SBConversations.notes.update([{ 'id': response, 'conversation_id': SBChat.conversation.id, 'user_id': SB_ACTIVE_AGENT['id'], 'name': SB_ACTIVE_AGENT['full_name'], 'message': message }], true);
                        textarea.val('');
                        showResponse('New note successfully added.');
                    } else {
                        SBForm.showErrorMessage(response);
                    }
                });
            }
        });

        // Suggestions
        $(suggestions_area).on('click', 'span', function () {
            conversations_area.find('.sb-editor textarea').val($(this).html()).focus();
            conversations_area.find('.sb-editor .sb-bar').sbActivate();
            suggestions_area.html('');
        });

        // Message menu
        $(conversations_area).on('click', '.sb-list .sb-menu > li', function () {
            let message = $(this).closest('[data-id]');
            let message_id = message.attr('data-id');
            let message_user_type = SBChat.conversation.getMessage(message_id).get('user_type');
            switch ($(this).attr('data-value')) {
                case 'delete':
                    if (SBChat.user_online) {
                        SBF.ajax({
                            function: 'update-message',
                            message_id: message_id,
                            message: '',
                            attachments: [],
                            payload: { 'event': 'delete-message' }
                        }, () => {
                            SBChat.conversation.deleteMessage(message_id);
                            message.remove();
                        });
                    } else {
                        SBChat.deleteMessage(message_id);
                    }
                    break;
                case 'original':
                    message.find('.sb-message').html(SBChat.conversation.getMessage(message_id).message);
                    $(this).attr('data-value', 'translation').html(sb_(SBF.isAgent(message_user_type) ? 'View original message' : 'View translation'));
                    break;
                case 'translation':
                    message.find('.sb-message').html(SBChat.conversation.getMessage(message_id).get('translation'));
                    $(this).attr('data-value', 'original').html(sb_(SBF.isAgent(message_user_type) ? 'View translation' : 'View original message'));
                    break;
            }
        });

        /*
        * ----------------------------------------------------------
        * # Users area
        * ----------------------------------------------------------
        */

        // Open user box by URL
        if (SBF.getURL('user')) {
            header.find('.sb-admin-nav #sb-users').click();
            setTimeout(() => { SBProfile.show(SBF.getURL('user')) }, 500);
        }

        // Checkbox selector
        $(users_table).on('click', 'th :checkbox', function () {
            users_table.find('td :checkbox').prop('checked', $(this).prop('checked'));
        });

        $(users_table).on('click', ':checkbox', function () {
            let button = users_area.find('[data-value="delete"]');
            if (users_table.find('td input:checked').length) {
                button.removeAttr('style');
            } else {
                button.hide();
            }
        });

        // Table menu filter
        $(users_table_menu).on('click', 'li', function () {
            SBUsers.filter($(this).data('type'));
        });

        // Search users
        $(users_area).on('input', '.sb-search-btn input', function () {
            SBUsers.search(this);
        });

        $(users_area).on('click', '.sb-search-btn i', function () {
            SBF.searchClear(this, () => { SBUsers.search($(this).next()) });
        });

        // Sorting
        $(users_table).on('click', 'th:not(:first-child)', function () {
            let direction = $(this).hasClass('sb-order-asc') ? 'DESC' : 'ASC';
            $(this).toggleClass('sb-order-asc');
            $(this).siblings().sbActivate(false);
            $(this).sbActivate();
            SBUsers.sort($(this).data('field'), direction);
        });

        // Pagination for users
        $(users_table).parent().on('scroll', function () {
            if (!is_busy && SBUsers.search_query == '' && scrollPagination(this, true)) {
                is_busy = true;
                users_area.append('<div class="sb-loading-global sb-loading sb-loading-pagination"></div>');
                SBF.ajax({
                    function: 'get-users',
                    pagination: users_pagination,
                    sorting: SBUsers.sorting,
                    user_types: SBUsers.user_types,
                    search: SBUsers.search_query
                }, (response) => {
                    let code = '';
                    is_busy = false;
                    users_pagination++;
                    for (var i = 0; i < response.length; i++) {
                        let user = new SBUser(response[i]);
                        code += SBUsers.getRow(user);
                        users[user.id] = user;
                    }
                    users_table.find('tbody').append(code);
                    users_area.find(' > .sb-loading-pagination').remove();
                    scrollPagination(this);
                });
            }
        });

        // Delete user button
        $(profile_edit_box).on('click', '.sb-delete', function () {
            dialog('This user will be deleted permanently including all linked data, conversations, and messages.', 'alert', function () {
                SBUsers.delete(activeUser().id);
            });
        });

        // Display user box
        $(users_table).on('click', 'td:not(:first-child)', function () {
            SBProfile.show($(this).parent().attr('data-user-id'));
        });

        // Display edit box
        $(profile_box).on('click', '.sb-top-bar .sb-edit', function () {
            SBProfile.showEdit(activeUser());
        });

        // Display new user box
        $(users_area).on('click', '.sb-new-user', function () {
            profile_edit_box.addClass('sb-user-new');
            profile_edit_box.find('.sb-top-bar .sb-profile span').html(sb_('Add new user'));
            profile_edit_box.find('.sb-top-bar .sb-save').html(`<i class="sb-icon-check"></i>${sb_('Add user')}`);
            profile_edit_box.find('#email input').prop('required', true);
            if (SB_ACTIVE_AGENT['user_type'] == 'admin') {
                profile_edit_box.find('#user_type').find('select').html('<option value="user">User</option><option value="agent">Agent</option><option value="admin">Admin</option>');
            }
            SBProfile.clear(profile_edit_box);
            SBProfile.boxClasses(profile_edit_box);
            profile_edit_box.sbShowLightbox();
        });

        // Add or update user
        $(profile_edit_box).on('click', '.sb-save', function () {
            if (loading(this)) return;
            let new_user = (profile_edit_box.hasClass('sb-user-new') ? true : false);
            let user_id = profile_edit_box.attr('data-user-id');

            // Get settings
            let settings = SBProfile.getAll(profile_edit_box.find('.sb-details'));
            let settings_extra = SBProfile.getAll(profile_edit_box.find('.sb-additional-details'));

            // Errors check
            if (SBProfile.errors(profile_edit_box)) {
                SBProfile.showErrorMessage(profile_edit_box, ['agent', 'admin'].includes(profile_edit_box.find('#user_type :selected').val()) ? 'First name, last name, and a valid email are required.' : 'First name and last name are required.');
                $(this).sbLoading(false);
                return;
            }

            // Save the settings
            SBF.ajax({
                function: (new_user ? 'add-user' : 'update-user'),
                user_id: user_id,
                settings: settings,
                settings_extra: settings_extra
            }, (response) => {
                if (SBF.errorValidation(response, 'duplicate-email') || SBF.errorValidation(response, 'duplicate-phone')) {
                    SBProfile.showErrorMessage(profile_edit_box, `This ${SBF.errorValidation(response, 'duplicate-email') ? 'email' : 'phone number'} is already in use.`);
                    $(this).sbLoading(false);
                    return;
                }
                if (new_user) {
                    user_id = response;
                    activeUser(new SBUser({ 'id': user_id }));
                }
                activeUser().update(() => {
                    users[user_id] = activeUser();
                    if (new_user) {
                        SBProfile.clear(profile_edit_box);
                        SBUsers.update();
                    } else {
                        SBUsers.updateRow(activeUser());
                        if (conversations_area.sbActive()) {
                            SBConversations.updateUserDetails();
                        }
                        if (user_id == SB_ACTIVE_AGENT['id']) {
                            SBF.loginCookie(response[1]);
                            SB_ACTIVE_AGENT['full_name'] = activeUser().name;
                            SB_ACTIVE_AGENT['profile_image'] = activeUser().image;
                            header.find('.sb-account').setProfile();
                        }
                    }
                    $(this).sbLoading(false);
                    profile_edit_box.find('.sb-profile').setProfile();
                    showResponse(new_user ? 'New user added' : 'User updated');
                });
                SBF.event('SBUserUpdated', { new_user: new_user, user_id: user_id });
            });
        });

        // Set and unset required visitor fields
        $(profile_edit_box).on('change', '#user_type', function () {
            SBProfile.boxClasses(profile_edit_box, $(this).find("option:selected").val());
        });

        // Open a user conversation
        $(profile_box).on('click', '.sb-user-conversations li', function () {
            SBConversations.open($(this).attr('data-conversation-id'), activeUser().id);
        });

        // Start a new user conversation
        $(profile_box).on('click', '.sb-start-conversation', function () {
            SBConversations.open(-1, activeUser().id);
            SBConversations.openConversation(-1, activeUser().id);
            if (responsive) SBConversations.mobileOpenConversation();
        });

        // Show direct message box from user profile
        $(profile_box).on('click', '.sb-top-bar [data-value]', function () {
            SBConversations.showDirectMessageBox($(this).attr('data-value'), [activeUser().id]);
        });

        // Top icons menu
        $(users_area).on('click', '.sb-top-bar [data-value]', function () {
            let value = $(this).data('value');
            let user_ids = [];
            users_table.find('tr').each(function () {
                if ($(this).find('td input[type="checkbox"]').is(':checked')) {
                    user_ids.push($(this).attr('data-user-id'));
                }
            });
            switch (value) {
                case 'message':
                case 'email':
                case 'sms':
                    SBConversations.showDirectMessageBox(value, user_ids);
                    break;
                case 'csv':
                    SBUsers.csv();
                    break;
                case 'delete':
                    dialog('All selected users will be deleted permanently including all linked data, conversations, and messages.', 'alert', () => {
                        SBUsers.delete(user_ids);
                        $(this).hide();
                        users_table.find('th:first-child input').prop('checked', false);
                    });
                    break;
            }
        });

        // Direct message
        $(admin).on('click', '.sb-send-direct-message', function () {
            let type = direct_message_box.attr('data-type');
            let subject = direct_message_box.find('.sb-direct-message-subject input').val();
            let message = direct_message_box.find('textarea').val();
            let user_ids = direct_message_box.find('.sb-direct-message-users').val().replace(/ /g, '');
            if (SBForm.errors(direct_message_box)) {
                SBForm.showErrorMessage(direct_message_box, 'All fields are required.');
            } else {
                if (loading(this)) return;
                if (type == 'message') {
                    SBF.ajax({
                        function: 'direct-message',
                        user_ids: user_ids,
                        message: message
                    }, (response) => {
                        $(this).sbLoading(false);
                        let send_email = SB_ADMIN_SETTINGS['notify-user-email'];
                        let send_sms = SB_ADMIN_SETTINGS['sms-active-users'];
                        if (SBF.errorValidation(response)) {
                            return SBForm.showErrorMessage(direct_message_box, 'An error has occurred. Please make sure all user ids are correct.');
                        }
                        if (send_email || send_sms) {
                            SBF.ajax({
                                function: 'get-users-with-details',
                                user_ids: user_ids,
                                details: send_email && send_sms ? ['email', 'phone'] : [send_email ? 'email' : 'phone']
                            }, (response) => {
                                if (send_email && response['email'].length) {
                                    recursiveSending(response['email'], message, 0, send_sms ? response['phone'] : [], 'email', subject);
                                } else if (send_sms && response['phone'].length) {
                                    recursiveSending(response['phone'], message, 0, [], 'sms');
                                } else {
                                    admin.sbHideLightbox();
                                }
                            });
                        }
                        showResponse(`${SBF.slugToString(type)} sent to all users.`);
                    });
                } else {
                    let slug = type == 'email' ? 'email' : 'phone';
                    SBF.ajax({
                        function: 'get-users-with-details',
                        user_ids: user_ids,
                        details: [slug]
                    }, (response) => {
                        if (response[slug].length) {
                            recursiveSending(response[slug], message, 0, [], type == 'email' ? 'custom-email' : 'sms', subject);
                        } else {
                            return SBForm.showErrorMessage(direct_message_box, 'No users found.');
                        }
                    });
                }
            }
        });

        function recursiveSending(user_ids, message, i = 0, user_ids_sms = [], type, subject = false) {
            let settings = type == 'custom-email' ? ['send-custom-email', 'emails', ' users with an email', 'direct-emails'] : (type == 'sms' ? ['send-sms', 'text messages', ' with a phone number', 'direct-sms'] : ['create-email', 'emails', ' with an email', false]);
            SBF.ajax({
                function: settings[0],
                to: user_ids[i]['value'],
                recipient_id: user_ids[i]['id'],
                sender_name: SB_ACTIVE_AGENT['full_name'],
                sender_profile_image: SB_ACTIVE_AGENT['profile_image'],
                subject: subject,
                message: message,
                template: false
            }, (response) => {
                let user_ids_length = user_ids.length;
                direct_message_box.find('.sb-bottom > div').html(sb_(`Sending ${settings[1]}... ${i + 1} / ${user_ids_length}`));
                if (!response) console.warn(response);
                if (response !== true && 'status' in response && response.status == 400) {
                    dialog(`${response.message}. Details at ${response.more_info}`, 'info');
                    console.warn(response);
                    return;
                }
                if (i < user_ids_length - 1) {
                    return recursiveSending(user_ids, message, i + 1, user_ids_sms, type, subject);
                } else {
                    if (user_ids_sms.length) {
                        recursiveSending(user_ids_sms, message, 0, [], 'sms', false);
                    } else {
                        admin.sbHideLightbox();
                        if (settings[3]) SBF.ajax({ function: 'reports-update', name: settings[3], value: message.substr(0, 18) + ' | ' + user_ids_length });
                    }
                    showResponse(user_ids_length == 1 ? 'Message sent' : `${SBF.slugToString(settings[1])} sent to all users${settings[2]}.`);
                }
            });
        }

        /*
        * ----------------------------------------------------------
        * # Settings area
        * ----------------------------------------------------------
        */

        // Open settings area by URL
        if (SBF.getURL('setting')) {
            header.find('.sb-admin-nav #sb-settings').click();
            setTimeout(() => { settings_area.find('#tab-' + SBF.getURL('setting')).click() }, 300);
        }

        // Settings history
        $(settings_area).on('click', ' > .sb-tab > .sb-nav [id]', function () {
            let id = $(this).attr('id').substr(4);
            if (SBF.getURL('setting') != id) pushState('?setting=' + id);
        });

        // Upload image
        $(settings_area).on('click', '[data-type="upload-image"] .image', function () {
            upload_target = this;
            admin.find('.sb-upload-form-admin .sb-upload-files').click();
        });

        $(settings_area).on('click', '[data-type="upload-image"] .image > i', function (e) {
            $(this).parent().removeAttr('data-value').css('background-image', '');
            e.preventDefault();
            return false;
        });

        // Repeater
        $(settings_area).on('click', '.sb-repeater-add', function () {
            SBSettings.repeaterAdd(this);
        });

        $(settings_area).on('click', '.repeater-item > i', function () {
            SBSettings.repeaterDelete(this);
        });

        // Color picker
        SBSettings.initColorPicker();

        $(settings_area).find('[data-type="color"]').focusout(function () {
            let t = $(this).closest('.input-color');
            let color = t.find('input').val();
            setTimeout(function () { t.find('input').val(''); t.find('.color-preview').css('background-color', color); }, 300);
            SBSettings.set($(this).attr('id'), color);
        });

        $(settings_area).on('click', '.sb-type-color .input i', function (e) {
            $(this).parent().find('input').removeAttr('style').val('');
        });

        // Color palette
        $(settings_area).on('click', '.sb-color-palette span', function () {
            let active = $(this).hasClass('sb-active');
            $(this).closest('.sb-repeater').find('.sb-active').sbActivate(false);
            $(this).sbActivate(!active);
        });

        $(settings_area).on('click', '.sb-color-palette ul li', function () {
            $(this).parent().parent().attr('data-value', $(this).data('value')).find('span').sbActivate(false);
        });

        // Select images
        $(settings_area).on('click', '[data-type="select-images"] .input > div', function () {
            $(this).siblings().sbActivate(false);
            $(this).sbActivate();
        });

        // Save
        $(settings_area).on('click', '.sb-save-changes', function () {
            SBSettings.save(this);
        });

        // Miscellaneous
        $(settings_area).on('change', '#user-additional-fields [data-id="extra-field-slug"], #saved-replies [data-id="reply-name"], [data-id="rich-message-name"]', function () {
            $(this).val(SBF.stringToSlug($(this).val()));
        });

        $(settings_area).on('click', '#timetable-utc input', function () {
            if ($(this).val() == '') {
                $(this).val(Math.round(today.getTimezoneOffset() / 60));
            }
        });

        $(settings_area).on('click', '#dialogflow-button .sb-btn', function (e) {
            let client_id = settings_area.find('#google-client-id input').val();
            if (client_id) {
                window.open(`https://accounts.google.com/o/oauth2/auth?scope=https%3A%2F%2Fwww.googleapis.com/auth/dialogflow%20https%3A%2F%2Fwww.googleapis.com%2Fauth%2Fcloud-translation&response_type=code&access_type=offline&redirect_uri=${SB_URL}/include/google.php&client_id=${client_id}&prompt=consent`);
            } else window.open('https://board.support/synch/?service=dialogflow&plugin_url=' + SB_URL);
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#google-domain-information .sb-btn', function (e) {
            let uri = SB_URL.substr(0, SB_URL.replace('//', '').indexOf('/') + 2);
            dialog(`Authorised JavaScript origins URI: ${uri}<br><br>Authorised redirect URI: ${SB_URL}/include/google.php`, 'info');
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#test-email-user .sb-btn, #test-email-agent .sb-btn', function () {
            let email = $(this).parent().find('input').val();
            if (email != '' && email.indexOf('@') > 0 && loading(this)) {
                SBF.ajax({
                    function: 'send-test-email',
                    to: email,
                    email_type: $(this).parent().parent().attr('id') == 'test-email-user' ? 'user' : 'agent'
                }, () => {
                    dialog('Email successfully sent. Check your emails!', 'info');
                    $(this).sbLoading(false);
                });
            }
        });

        $(settings_area).on('click', '.sb-timetable > div > div > div', function () {
            let timetable = $(this).closest('.sb-timetable');
            let active = $(this).sbActive();
            $(timetable).find('.sb-active').sbActivate(false);
            if (active) {
                $(this).sbActivate(false).find('.sb-custom-select').remove();
            } else {
                let select = $(timetable).find('> .sb-custom-select').html();
                $(timetable).find(' > div .sb-custom-select').remove();
                $(this).append(`<div class="sb-custom-select">${select}</div>`).sbActivate();
            }
        });

        $(settings_area).on('click', '.sb-timetable .sb-custom-select span', function () {
            let value = [$(this).html(), $(this).attr('data-value')];
            $(this).closest('.sb-timetable').find('> div > div > .sb-active').html(value[0]).attr('data-value', value[1]);
            $(this).parent().sbActivate(false);
        });

        $(settings_area).on('click', '#system-requirements a', function (e) {
            let box = admin.find('.sb-requirements-box');
            let code = '';
            SBF.ajax({
                function: 'system-requirements'
            }, (response) => {
                for (var key in response) {
                    code += `<div class="sb-input"><span>${sb_(SBF.slugToString(key))}</span><div${response[key] ? ' class="sb-green"' : ''}>${response[key] ? sb_('Success') : sb_('Error')}</div></div>`;
                }
                loadingGlobal(false);
                box.find('.sb-main').html(code);
                box.sbShowLightbox();
            });
            box.sbActivate(false);
            loadingGlobal(true);
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#sb-path a', function (e) {
            SBF.ajax({
                function: 'path'
            }, (response) => {
                dialog(response, 'info');
            });
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#delete-leads a', function (e) {
            if (!$(this).sbLoading()) {
                dialog('All leads, including all the linked conversations and messages, will be deleted permanently.', 'alert', () => {
                    $(this).sbLoading(true);
                    SBF.ajax({
                        function: 'delete-leads'
                    }, () => {
                        dialog('Leads and conversations successfully deleted.', 'info');
                        $(this).sbLoading(false);
                    });
                });
            }
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#dialogflow-smart-reply-generate a', function (e) {
            if (loading(this)) return;
            SBF.ajax({
                function: 'dialogflow-smart-reply-generate-conversations-data'
            }, (response) => {
                dialog('Conversations data successfully generated. Folder: ' + response, 'info');
                $(this).sbLoading(false);
            });
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#sb-export-settings a', function (e) {
            if (loading(this)) return;
            SBF.ajax({
                function: 'export-settings'
            }, (response) => {
                window.open(response);
                dialog(`${sb_('For security reasons, delete the settings file after downloading it. Close this window to automatically delete it. File location:')}<pre>${response}</pre>`, 'info', false, 'sb-export-settings-close', 'Settings exported');
                $(this).sbLoading(false);
            });
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#sb-import-settings a', function (e) {
            if (loading(this)) return;
            upload_target = this;
            upload_on_success = (response) => {
                SBF.ajax({
                    function: 'import-settings',
                    file_url: response
                }, (response) => {
                    if (response) showResponse('Settings successfully imported. Reload the admin area to apply the new settings.');
                    else dialog(response, 'info');
                    $(this).sbLoading(false);
                });
                upload_on_success = false;
            }
            admin.find('.sb-upload-form-admin .sb-upload-files').click();
            e.preventDefault();
            return false;
        });

        $(admin).on('click', '#sb-export-settings-close', function () {
            SBF.ajax({ function: 'delete-file', path: admin.find('.sb-dialog-box p pre').html() });
        });


        // Slack  
        $(settings_area).find('#slack-button .sb-btn').attr('href', 'https://board.support/synch/?service=slack&plugin_url=' + SB_URL);

        $(settings_area).on('click', '#slack-test .sb-btn', function (e) {
            if (loading(this)) return;
            SBF.ajax({
                function: 'send-slack-message',
                user_id: -1,
                full_name: SB_ACTIVE_AGENT['full_name'],
                profile_image: SB_ACTIVE_AGENT['profile_image'],
                message: 'Lorem ipsum dolor sit amete consectetur adipiscing elite incidido labore et dolore magna aliqua.',
                attachments: [['Example link', SB_URL + '/media/user.svg'], ['Example link two', SB_URL + '/media/user.svg']]
            }, (response) => {
                if (SBF.errorValidation(response)) {
                    if (response[1] == 'slack-not-active') {
                        dialog('Please first activate Slack, then save the settings and reload the admin area.', 'info');
                    } else {
                        dialog('Error. Response: ' + JSON.stringify(response), 'info');
                    }
                } else {
                    dialog(response[0] == 'success' ? 'Slack message successfully sent. Check your Slack app!' : JSON.stringify(response), 'info');
                }
                $(this).sbLoading(false);
            });
            e.preventDefault();
            return false;
        });

        $(settings_area).on('click', '#tab-slack', function () {
            let input = settings_area.find('#slack-agents .input');
            input.html('<div class="sb-loading"></div>');
            SBF.ajax({
                function: 'slack-users'
            }, (response) => {
                let code = '';
                if (SBF.errorValidation(response, 'slack-token-not-found')) {
                    code = `<p>${sb_('Synchronize Slack and save changes before linking agents.')}</p>`;
                } else {
                    let select = '<option value="-1"></option>';
                    for (var i = 0; i < response['agents'].length; i++) {
                        select += `<option value="${response['agents'][i]['id']}">${response['agents'][i]['name']}</option>`;
                    }
                    for (var i = 0; i < response['slack_users'].length; i++) {
                        code += `<div data-id="${response['slack_users'][i]['id']}"><label>${response['slack_users'][i]['name']}</label><select>${select}</select></div>`;
                    }
                }
                input.html(code);
                SBSettings.set('slack-agents', [response['saved'], 'double-select']);
            });
        });

        $(settings_area).on('click', '#slack-archive-channels .sb-btn', function (e) {
            if (loading(this)) return;
            SBF.ajax({
                function: 'archive-slack-channels'
            }, (response) => {
                if (response === true) {
                    dialog('Slack channels archived successfully!', 'info');
                }
                $(this).sbLoading(false);
            });
            e.preventDefault();
        });

        // Messenger
        $(settings_area).find('#messenger-button .sb-btn').attr('href', 'https://board.support/synch/?service=messenger&plugin_url=' + SB_URL);

        // WhatsApp
        $(settings_area).on('click', '#whatsapp-twilio-btn .sb-btn', function (e) {
            dialog(SB_URL + '/apps/whatsapp/post.php', 'info');
            return false;
            e.preventDefault();
        });

        // WordPress
        $(settings_area).on('click', '#wp-sync .sb-btn', function (e) {
            if (loading(this)) return;
            SBApps.wordpress.ajax('wp-sync', {}, (response) => {
                if (response === true || response === '1') {
                    SBUsers.update();
                    dialog('WordPress users successfully imported.', 'info');
                } else {
                    dialog('Error. Response: ' + JSON.stringify(response), 'info');
                }
                $(this).sbLoading(false);
            });
            return false;
            e.preventDefault();
        });

        $('body').on('click', '#wp-admin-bar-logout', function () {
            SBF.logout(false);
        });

        // Translations
        $(settings_area).on('click', '#tab-translations', function () {
            let nav = settings_area.find('.sb-translations > .sb-nav > ul');
            if (nav.html() == '') {
                let code = '';
                for (var key in SB_LANGUAGE_CODES) {
                    if (key == 'en') continue;
                    code += `<li data-code="${key}"><img src="${SB_URL}/media/flags/languages/${key}.png" />${SB_LANGUAGE_CODES[key]}</li>`;
                }
                nav.html(code);
            }
        });

        $(settings_area).on('click', '.sb-translations .sb-nav li', function () {
            SBSettings.translations.load($(this).data('code'));
        });

        $(settings_area).on('click', '.sb-translations .sb-menu-wide li', function () {
            settings_area.find(`.sb-translations [data-area="${$(this).data('value')}"]`).sbActivate().siblings().sbActivate(false);
        });

        $(settings_area).on('click', '.sb-add-translation', function () {
            settings_area.find('.sb-translations-list > .sb-active').prepend(`<div class="sb-input-setting sb-type-text sb-new-translation"><input type="text" placeholder="${sb_('Enter original text...')}"><input type="text" placeholder="${sb_('Enter translation...')}"></div></div>`);
        });

        $(settings_area).on('input', '.sb-search-translation input', function () {
            let search = $(this).val().toLowerCase();
            SBF.search(search, () => {
                if (search.length > 1) {
                    settings_area.find('.sb-translations .sb-content > .sb-active label').each(function () {
                        let value = $(this).html().toLowerCase();
                        if (value.includes(search) && value != temp) {
                            let scroll_area = settings_area.find('.sb-scroll-area');
                            scroll_area[0].scrollTop = 0;
                            scroll_area[0].scrollTop = $(this).position().top - 80;
                            temp = value;
                            return false;
                        }
                    });
                }
            });
        });

        // Articles
        $(settings_area).on('click', '#tab-articles', function () {
            articles_area.find('.sb-menu-wide li').eq(0).sbActivate().next().sbActivate(false);
            SBSettings.articles.get((response) => {
                SBSettings.articles.populate(response[0]);
                SBSettings.articles.categories = response[1];
                SBSettings.articles.translations = Array.isArray(response[2]) && !response[2].length ? {} : response[2];
                SBSettings.articles.updateSelect();
                loadingGlobal(false);
            }, true);
            loadingGlobal();
        });

        $(articles_area).on('click', '.sb-nav:not([data-type="categories"]) li', function () {
            SBSettings.articles.show($(this).attr('data-id'), this);
        });

        $(articles_area).on('click', '.sb-add-article', function () {
            SBSettings.articles.add();
        });

        $(articles_area).on('click', '.sb-add-category', function () {
            SBSettings.articles.addCategory(false, $(this).data('type') == 'categories');
        });

        $(articles_area).on('click', '.sb-nav i', function () {
            let category = $(this).hasClass('sb-category');
            dialog(`The ${category ? 'category' : 'article'} will be deleted permanently.`, 'alert', () => {
                SBSettings.articles.delete(this, category);
            });
        });

        $(articles_area).on('click', '.sb-menu-wide li', function () {
            SBSettings.articles.populate(false, $(this).data('type') == 'categories');
        });

        // Email piping manual sync
        $(settings_area).on('click', '#email-piping-sync a', function (e) {
            if (loading(this)) return;
            SBF.ajax({
                function: 'email-piping',
                force: true
            }, (response) => {
                dialog(response === true ? 'Email piping syncronization completed.' : response, 'info');
                $(this).sbLoading(false);
            });
            e.preventDefault();
        });

        // Automations
        $(settings_area).on('click', '#tab-automations', function () {
            SBSettings.automations.get(() => {
                SBSettings.automations.populate();
                loadingGlobal(false);
            }, true);
            loadingGlobal();
        });

        $(automations_area).on('click', '.sb-add-condition', function () {
            SBSettings.automations.addCondition();
        });

        $(automations_area).on('change', '.sb-condition-1 select', function () {
            SBSettings.automations.updateCondition(this);
        });

        $(automations_area_select).on('click', 'li', function () {
            SBSettings.automations.populate($(this).data('value'));
        });

        $(automations_area_nav).on('click', 'li', function () {
            SBSettings.automations.show($(this).attr('data-id'));
        });

        $(automations_area).on('click', '.sb-add-automation', function () {
            SBSettings.automations.add();
        });

        $(automations_area_nav).on('click', 'li i', function () {
            dialog(`The automation will be deleted permanently.`, 'alert', () => {
                SBSettings.automations.delete(this);
            });
        });

        /*
        * ----------------------------------------------------------
        * # Reports area
        * ----------------------------------------------------------
        */

        $(reports_area).on('click', '.sb-nav [id]', function () {
            let id = $(this).attr('id');
            SBReports.active_report = false;
            reports_area.find('#sb-date-picker').val('');
            reports_area.attr('class', 'sb-area-reports sb-active sb-report-' + id);
            SBReports.initReport($(this).attr('id'));
            if (SBF.getURL('report') != id) pushState('?report=' + id);
        });

        $(reports_area).on('change', '#sb-date-picker', function () {
            SBReports.initReport(false, $(this).val());
        });

        if (SBF.getURL('report')) {
            if (!reports_area.sbActive()) header.find('.sb-admin-nav #sb-reports').click();
            setTimeout(() => { reports_area.find('#' + SBF.getURL('report')).click() }, 500);
        }

        /*
        * ----------------------------------------------------------
        * # Woocommerce
        * ----------------------------------------------------------
        */

        // Panel reload button
        $(conversations_area).on('click', '.sb-panel-woocommerce > i', function () {
            SBApps.woocommerce.conversationPanel();
        });

        // Get order details
        $(conversations_area).on('click', '.sb-woocommerce-orders > div > span', function (e) {
            let parent = $(this).parent();
            if (!$(e.target).is('span')) return;
            if (!parent.sbActive()) {
                SBApps.woocommerce.conversationPanelOrder(parent.attr('data-id'));
            }
        });

        // Products popup 
        $(conversations_area).on('click', '.sb-btn-woocommerce', function () {
            if (woocommerce_products_box_ul.sbLoading() || (activeUser() != false && activeUser().language != SBApps.woocommerce.popupLanguage)) {
                SBApps.woocommerce.popupPopulate();
            }
            woocommerce_products_box.sbTogglePopup(this);
        });

        // Products popup pagination
        $(woocommerce_products_box).find('.sb-woocommerce-products-list').on('scroll', function () {
            if (scrollPagination(this, true)) {
                SBApps.woocommerce.popupPagination(this);
            }
        });

        // Products popup filter
        $(woocommerce_products_box).on('click', '.sb-select li', function () {
            SBApps.woocommerce.popupFilter(this);
        });

        // Products popup search
        $(woocommerce_products_box).on('input', '.sb-search-btn input', function () {
            SBApps.woocommerce.popupSearch(this);
        });

        $(woocommerce_products_box).on('click', '.sb-search-btn i', function () {
            SBF.searchClear(this, () => { SBApps.woocommerce.popupSearch($(this).next()) });
        });

        // Cart popup insert product
        $(woocommerce_products_box).on('click', '.sb-woocommerce-products-list li', function () {
            let action = woocommerce_products_box.attr('data-action');
            let id = $(this).data('id');
            if (SBF.null(action)) {
                SBChat.insertText(`{product_card id="${id}"}`);
            } else {
                woocommerce_products_box_ul.sbLoading(true);
                conversations_area.find('.sb-add-cart-btn').sbLoading(true);
                SBChat.sendMessage(-1, '', [], (response) => {
                    if (response) {
                        SBApps.woocommerce.conversationPanelUpdate(id);
                        admin.sbHideLightbox();
                    }
                }, { 'event': 'woocommerce-update-cart', 'action': 'cart-add', 'id': id });
            }
            SBF.deactivateAll();
            admin.removeClass('sb-popup-active');
        });

        // Cart add product
        $(conversations_area).on('click', '.sb-panel-woocommerce .sb-add-cart-btn', function () {
            if ($(this).sbLoading()) return;
            if (SBChat.user_online) {
                SBApps.woocommerce.popupPopulate();
                woocommerce_products_box.sbShowLightbox(true, 'cart-add');
            } else {
                dialog('The user is offline. Only the carts of online users can be updated.', 'info');
            }
        });

        // Cart remove product
        $(conversations_area).on('click', '.sb-panel-woocommerce .sb-list-items > a > i', function (e) {
            let id = $(this).parent().attr('data-id');
            SBChat.sendMessage(-1, '', [], () => {
                SBApps.woocommerce.conversationPanelUpdate(id, 'removed');
            }, { 'event': 'woocommerce-update-cart', 'action': 'cart-remove', 'id': id });
            $(this).sbLoading(true);
            e.preventDefault();
            return false;
        });

        // Settings
        $(settings_area).on('click', '#wc-dialogflow-synch a, #wc-dialogflow-create-intents a', function (e) {
            if (SBApps.is('dialogflow')) {
                if (loading(this)) return;
                let id = $(this).parent().attr('id');
                SBF.ajax({
                    function: 'woocommerce-dialogflow-' + (id == 'wc-dialogflow-synch' ? 'entities' : 'intents')
                }, (response) => {
                    $(this).sbLoading(false);
                    dialog(response ? 'Dialogflow synchronization completed.' : 'Error. Something went wrong.', 'info');
                });
            } else {
                dialog('This feature requires the Dialogflow App. Get it from the apps area.', 'info');
            }
            e.preventDefault();
            return false;
        });

        /*
        * ----------------------------------------------------------
        * # Apps area
        * ----------------------------------------------------------
        */

        // Ump
        $(conversations_area).on('click', '.sb-panel-ump > i', function () {
            SBApps.ump.conversationPanel();
        });

        // ARMember
        $(conversations_area).on('click', '.sb-panel-armember > i', function () {
            SBApps.armember.conversationPanel();
        });

        // Perfex, whmcs, aecommerce
        $(settings_area).on('click', '#perfex-sync .sb-btn, #whmcs-sync .sb-btn,#perfex-articles-sync .sb-btn, #whmcs-articles-sync .sb-btn, #aecommerce-sync .sb-btn, #aecommerce-sync-admins .sb-btn, #aecommerce-sync-sellers .sb-btn', function (e) {
            if (loading(this)) return;
            let function_name = $(this).closest('[id]').attr('id');
            let articles = function_name.indexOf('article') > 0;
            SBF.ajax({
                function: function_name
            }, (response) => {
                if (response === true) {
                    if (!articles) {
                        SBUsers.update();
                    }
                    dialog(articles ? 'Knowledge base articles successfully imported.' : 'Users successfully imported.', 'info');
                } else {
                    dialog('Error. Response: ' + JSON.stringify(response), 'info');
                }
                $(this).sbLoading(false);
            });
            e.preventDefault();
        });

        /*
        * ----------------------------------------------------------
        * # Components
        * ----------------------------------------------------------
        */

        // Language switcher
        $(admin).on('click', '.sb-language-switcher > i', function () {
            let switcher = $(this).parent();
            let active_languages = switcher.find('[data-language]').map(function () { return $(this).attr('data-language') }).get();
            let box = admin.find('.sb-languages-box');
            let code = '';
            active_languages.push('en');
            for (var key in SB_LANGUAGE_CODES) {
                if (!active_languages.includes(key)) {
                    code += `<div data-language="${key}"><img src="${SB_URL}/media/flags/languages/${key}.png" />${sb_(SB_LANGUAGE_CODES[key])}</div>`;
                }
            }
            language_switcher_target = switcher;
            box.attr('data-source', switcher.attr('data-source'));
            box.find('.sb-main').html(code);
            box.sbShowLightbox();
        });

        $(admin).on('click', '.sb-language-switcher img', function () {
            let item = $(this).parent();
            let active = item.sbActive();
            let language = active ? false : item.attr('data-language');
            switch (item.parent().attr('data-source')) {
                case 'articles':
                    SBSettings.articles.show(false, false, language);
                    break;
                case 'automations':
                    SBSettings.automations.show(false, language);
                    break;
                case 'settings':
                    let active_language = item.parent().find('[data-language].sb-active');
                    SBSettings.translations.save(item, active ? item.attr('data-language') : (active_language.length ? active_language.attr('data-language') : false));
                    SBSettings.translations.activate(item, language);
                    break;
            }
            item.siblings().sbActivate(false);
            item.sbActivate(!active);
        });

        $(admin).on('click', '.sb-language-switcher span > i', function () {
            let item = $(this).parent();
            let language = item.attr('data-language');
            dialog(`The ${sb_(SB_LANGUAGE_CODES[language])} translation will be deleted.`, 'alert', () => {
                switch (item.parent().attr('data-source')) {
                    case 'articles':
                        SBSettings.articles.deleteTranslation(false, language);
                        SBSettings.articles.show();
                        break;
                    case 'automations':
                        SBSettings.automations.deleteTranslation(false, language);
                        SBSettings.automations.show();
                        break;
                    case 'settings':
                        SBSettings.translations.delete(item, language);
                        break;
                }
                item.remove();
            });
        });

        $(admin).on('click', '.sb-languages-box [data-language]', function () {
            let box = $(this).parents().eq(1);
            let language = $(this).attr('data-language');
            switch (box.attr('data-source')) {
                case 'articles':
                    SBSettings.articles.addTranslation(false, language);
                    SBSettings.articles.show(false, false, language);
                    break;
                case 'automations':
                    SBSettings.automations.addTranslation(false, false, language);
                    SBSettings.automations.show(false, language);
                    break;
                case 'settings':
                    SBSettings.translations.add(language);
                    break;
            }
            admin.sbHideLightbox();
        });

        // Lightbox
        $(admin).on('click', '.sb-lightbox .sb-top-bar .sb-close', function () {
            admin.sbHideLightbox();
        });

        $(admin).on('click', '.sb-lightbox .sb-info', function () {
            $(this).sbActivate(false);
        });

        // Alert and information box
        $(admin).on('click', '.sb-dialog-box a', function () {
            let lightbox = $(this).closest('.sb-lightbox');
            if ($(this).hasClass('sb-confirm')) {
                alertOnConfirmation();
            }
            if (admin.find('.sb-lightbox.sb-active').length == 1) {
                overlay.sbActivate(false);
            }
            SBAdmin.open_popup = false;
            lightbox.sbActivate(false);
        });

        /*
       * ----------------------------------------------------------
       * # Miscellaneous
       * ----------------------------------------------------------
       */

        $(admin).on('click', '.sb-menu-wide li, .sb-nav li', function () {
            $(this).siblings().sbActivate(false);
            $(this).sbActivate();
        });

        $(admin).on('click', '.sb-nav:not(.sb-nav-only) li:not(.sb-tab-nav-title)', function () {
            let area = $(this).closest('.sb-tab');
            let tab = $(area).find(' > .sb-content > div').sbActivate(false).eq($(this).parent().find('li:not(.sb-tab-nav-title)').index(this));
            tab.sbActivate();
            tab.find('textarea').each(function () {
                $(this).autoExpandTextarea();
                $(this).manualExpandTextarea();
            });
            area.find('.sb-scroll-area').scrollTop(0);
        });

        $(admin).sbInitTooltips();

        $(admin).on('click', '[data-button="toggle"]', function () {
            let show = admin.find('.' + $(this).data('show'));
            let hide = admin.find('.' + $(this).data('hide'));
            show.addClass('sb-show-animation').show();
            hide.hide();
            SBAdmin.open_popup = show.hasClass('sb-lightbox') || show.hasClass('sb-popup') ? show : false;
        });

        $(admin).on('click', '.sb-info-card', function () {
            $(this).sbActivate(false);
        });

        $(admin).on('change', '.sb-upload-form-admin .sb-upload-files', function (data) {
            $(this).sbUploadFiles(function (response) {
                response = JSON.parse(response);
                if (response[0] == 'success') {
                    let type = $(upload_target).closest('[data-type]').data('type');
                    if (type == 'upload-image') {
                        $(upload_target).attr('data-value', response[1]).css('background-image', `url("${response[1]}")`);
                    }
                    if (upload_on_success) upload_on_success(response[1]);
                } else {
                    console.log(response[1]);
                }
            });
        });

        $(admin).on('click', '.sb-accordion > div > span', function (e) {
            let parent = $(this).parent();
            let active = $(parent).sbActive();
            if (!$(e.target).is('span')) return;
            parent.siblings().sbActivate(false);
            parent.sbActivate(!active);
        });

        $(admin).on('mousedown', function (e) {
            if ($(SBAdmin.open_popup).length) {
                let popup = $(SBAdmin.open_popup);
                if (!popup.is(e.target) && popup.has(e.target).length === 0 && !['sb-btn-saved-replies', 'sb-btn-emoji'].includes($(e.target).attr('class'))) {
                    if (popup.hasClass('sb-popup')) popup.sbTogglePopup();
                    else if (popup.hasClass('sb-select')) popup.find('ul').sbActivate(false);
                    else if (popup.hasClass('sb-menu-mobile')) popup.find('i').sbActivate(false);
                    else if (popup.hasClass('sb-menu')) popup.sbActivate(false);
                    else admin.sbHideLightbox();
                    SBAdmin.open_popup = false;
                }
            }
        });
    });

    function initialization() {
        SBF.ajax({
            function: 'get-conversations',
            routing: SB_ADMIN_SETTINGS['routing'],
            routing_unassigned: SB_ADMIN_SETTINGS['assign-conversation-to-agent']
        }, (response) => {
            if (response.length == 0) {
                conversations_area.find('.sb-board').addClass('sb-no-conversation');
            }
            SBConversations.populateList(response);
            if (responsive) {
                conversations_area.find('.sb-admin-list').sbActivate();
            }
            if (SBF.getURL('conversation')) {
                if (!conversations_area.sbActive()) header.find('.sb-admin-nav #sb-conversations').click();
                SBConversations.openConversation(SBF.getURL('conversation'));
            } else if (!responsive && !SBF.getURL('user') && !SBF.getURL('setting') && !SBF.getURL('report') && (!SBF.getURL('area') || SBF.getURL('area') == 'conversations')) {
                SBConversations.clickFirst();
            }
            SBConversations.startRealTime();
            SBConversations.datetime_last_conversation = SB_ADMIN_SETTINGS['now-db'];
            loadingGlobal(false);
        });
        SBPusher.initServiceWorker();
        if (SB_ADMIN_SETTINGS['push-notifications']) {
            SBPusher.initPushNotifications();
        }
    }

}(jQuery));

// tinyColorPicker v1.1.1 2016-08-30 

!function (a, b) { "object" == typeof exports ? module.exports = b(a) : "function" == typeof define && define.amd ? define("colors", [], function () { return b(a) }) : a.Colors = b(a) }(this, function (a, b) { "use strict"; function c(a, c, d, f, g) { if ("string" == typeof c) { var c = v.txt2color(c); d = c.type, p[d] = c[d], g = g !== b ? g : c.alpha } else if (c) for (var h in c) a[d][h] = k(c[h] / l[d][h][1], 0, 1); return g !== b && (a.alpha = k(+g, 0, 1)), e(d, f ? a : b) } function d(a, b, c) { var d = o.options.grey, e = {}; return e.RGB = { r: a.r, g: a.g, b: a.b }, e.rgb = { r: b.r, g: b.g, b: b.b }, e.alpha = c, e.equivalentGrey = n(d.r * a.r + d.g * a.g + d.b * a.b), e.rgbaMixBlack = i(b, { r: 0, g: 0, b: 0 }, c, 1), e.rgbaMixWhite = i(b, { r: 1, g: 1, b: 1 }, c, 1), e.rgbaMixBlack.luminance = h(e.rgbaMixBlack, !0), e.rgbaMixWhite.luminance = h(e.rgbaMixWhite, !0), o.options.customBG && (e.rgbaMixCustom = i(b, o.options.customBG, c, 1), e.rgbaMixCustom.luminance = h(e.rgbaMixCustom, !0), o.options.customBG.luminance = h(o.options.customBG, !0)), e } function e(a, b) { var c, e, k, q = b || p, r = v, s = o.options, t = l, u = q.RND, w = "", x = "", y = { hsl: "hsv", rgb: a }, z = u.rgb; if ("alpha" !== a) { for (var A in t) if (!t[A][A]) { a !== A && (x = y[A] || "rgb", q[A] = r[x + "2" + A](q[x])), u[A] || (u[A] = {}), c = q[A]; for (w in c) u[A][w] = n(c[w] * t[A][w][1]) } z = u.rgb, q.HEX = r.RGB2HEX(z), q.equivalentGrey = s.grey.r * q.rgb.r + s.grey.g * q.rgb.g + s.grey.b * q.rgb.b, q.webSave = e = f(z, 51), q.webSmart = k = f(z, 17), q.saveColor = z.r === e.r && z.g === e.g && z.b === e.b ? "web save" : z.r === k.r && z.g === k.g && z.b === k.b ? "web smart" : "", q.hueRGB = v.hue2RGB(q.hsv.h), b && (q.background = d(z, q.rgb, q.alpha)) } var B, C, D, E = q.rgb, F = q.alpha, G = "luminance", H = q.background; return B = i(E, { r: 0, g: 0, b: 0 }, F, 1), B[G] = h(B, !0), q.rgbaMixBlack = B, C = i(E, { r: 1, g: 1, b: 1 }, F, 1), C[G] = h(C, !0), q.rgbaMixWhite = C, s.customBG && (D = i(E, H.rgbaMixCustom, F, 1), D[G] = h(D, !0), D.WCAG2Ratio = j(D[G], H.rgbaMixCustom[G]), q.rgbaMixBGMixCustom = D, D.luminanceDelta = m.abs(D[G] - H.rgbaMixCustom[G]), D.hueDelta = g(H.rgbaMixCustom, D, !0)), q.RGBLuminance = h(z), q.HUELuminance = h(q.hueRGB), s.convertCallback && s.convertCallback(q, a), q } function f(a, b) { var c = {}, d = 0, e = b / 2; for (var f in a) d = a[f] % b, c[f] = a[f] + (d > e ? b - d : -d); return c } function g(a, b, c) { return (m.max(a.r - b.r, b.r - a.r) + m.max(a.g - b.g, b.g - a.g) + m.max(a.b - b.b, b.b - a.b)) * (c ? 255 : 1) / 765 } function h(a, b) { for (var c = b ? 1 : 255, d = [a.r / c, a.g / c, a.b / c], e = o.options.luminance, f = d.length; f--;)d[f] = d[f] <= .03928 ? d[f] / 12.92 : m.pow((d[f] + .055) / 1.055, 2.4); return e.r * d[0] + e.g * d[1] + e.b * d[2] } function i(a, c, d, e) { var f = {}, g = d !== b ? d : 1, h = e !== b ? e : 1, i = g + h * (1 - g); for (var j in a) f[j] = (a[j] * g + c[j] * h * (1 - g)) / i; return f.a = i, f } function j(a, b) { var c = 1; return c = a >= b ? (a + .05) / (b + .05) : (b + .05) / (a + .05), n(100 * c) / 100 } function k(a, b, c) { return a > c ? c : b > a ? b : a } var l = { rgb: { r: [0, 255], g: [0, 255], b: [0, 255] }, hsv: { h: [0, 360], s: [0, 100], v: [0, 100] }, hsl: { h: [0, 360], s: [0, 100], l: [0, 100] }, alpha: { alpha: [0, 1] }, HEX: { HEX: [0, 16777215] } }, m = a.Math, n = m.round, o = {}, p = {}, q = { r: .298954, g: .586434, b: .114612 }, r = { r: .2126, g: .7152, b: .0722 }, s = function (a) { this.colors = { RND: {} }, this.options = { color: "rgba(0,0,0,0)", grey: q, luminance: r, valueRanges: l }, t(this, a || {}) }, t = function (a, d) { var e, f = a.options; u(a); for (var g in d) d[g] !== b && (f[g] = d[g]); e = f.customBG, f.customBG = "string" == typeof e ? v.txt2color(e).rgb : e, p = c(a.colors, f.color, b, !0) }, u = function (a) { o !== a && (o = a, p = a.colors) }; s.prototype.setColor = function (a, d, f) { return u(this), a ? c(this.colors, a, d, b, f) : (f !== b && (this.colors.alpha = k(f, 0, 1)), e(d)) }, s.prototype.setCustomBackground = function (a) { return u(this), this.options.customBG = "string" == typeof a ? v.txt2color(a).rgb : a, c(this.colors, b, "rgb") }, s.prototype.saveAsBackground = function () { return u(this), c(this.colors, b, "rgb", !0) }, s.prototype.toString = function (a, b) { return v.color2text((a || "rgb").toLowerCase(), this.colors, b) }; var v = { txt2color: function (a) { var b = {}, c = a.replace(/(?:#|\)|%)/g, "").split("("), d = (c[1] || "").split(/,\s*/), e = c[1] ? c[0].substr(0, 3) : "rgb", f = ""; if (b.type = e, b[e] = {}, c[1]) for (var g = 3; g--;)f = e[g] || e.charAt(g), b[e][f] = +d[g] / l[e][f][1]; else b.rgb = v.HEX2rgb(c[0]); return b.alpha = d[3] ? +d[3] : 1, b }, color2text: function (a, b, c) { var d = c !== !1 && n(100 * b.alpha) / 100, e = "number" == typeof d && c !== !1 && (c || 1 !== d), f = b.RND.rgb, g = b.RND.hsl, h = "hex" === a && e, i = "hex" === a && !h, j = "rgb" === a || h, k = j ? f.r + ", " + f.g + ", " + f.b : i ? "#" + b.HEX : g.h + ", " + g.s + "%, " + g.l + "%"; return i ? k : (h ? "rgb" : a) + (e ? "a" : "") + "(" + k + (e ? ", " + d : "") + ")" }, RGB2HEX: function (a) { return ((a.r < 16 ? "0" : "") + a.r.toString(16) + (a.g < 16 ? "0" : "") + a.g.toString(16) + (a.b < 16 ? "0" : "") + a.b.toString(16)).toUpperCase() }, HEX2rgb: function (a) { return a = a.split(""), { r: +("0x" + a[0] + a[a[3] ? 1 : 0]) / 255, g: +("0x" + a[a[3] ? 2 : 1] + (a[3] || a[1])) / 255, b: +("0x" + (a[4] || a[2]) + (a[5] || a[2])) / 255 } }, hue2RGB: function (a) { var b = 6 * a, c = ~~b % 6, d = 6 === b ? 0 : b - c; return { r: n(255 * [1, 1 - d, 0, 0, d, 1][c]), g: n(255 * [d, 1, 1, 1 - d, 0, 0][c]), b: n(255 * [0, 0, d, 1, 1, 1 - d][c]) } }, rgb2hsv: function (a) { var b, c, d, e = a.r, f = a.g, g = a.b, h = 0; return g > f && (f = g + (g = f, 0), h = -1), c = g, f > e && (e = f + (f = e, 0), h = -2 / 6 - h, c = m.min(f, g)), b = e - c, d = e ? b / e : 0, { h: 1e-15 > d ? p && p.hsl && p.hsl.h || 0 : b ? m.abs(h + (f - g) / (6 * b)) : 0, s: e ? b / e : p && p.hsv && p.hsv.s || 0, v: e } }, hsv2rgb: function (a) { var b = 6 * a.h, c = a.s, d = a.v, e = ~~b, f = b - e, g = d * (1 - c), h = d * (1 - f * c), i = d * (1 - (1 - f) * c), j = e % 6; return { r: [d, h, g, g, i, d][j], g: [i, d, d, h, g, g][j], b: [g, g, i, d, d, h][j] } }, hsv2hsl: function (a) { var b = (2 - a.s) * a.v, c = a.s * a.v; return c = a.s ? 1 > b ? b ? c / b : 0 : c / (2 - b) : 0, { h: a.h, s: a.v || c ? c : p && p.hsl && p.hsl.s || 0, l: b / 2 } }, rgb2hsl: function (a, b) { var c = v.rgb2hsv(a); return v.hsv2hsl(b ? c : p.hsv = c) }, hsl2rgb: function (a) { var b = 6 * a.h, c = a.s, d = a.l, e = .5 > d ? d * (1 + c) : d + c - c * d, f = d + d - e, g = e ? (e - f) / e : 0, h = ~~b, i = b - h, j = e * g * i, k = f + j, l = e - j, m = h % 6; return { r: [e, l, f, f, k, e][m], g: [k, e, e, l, f, f][m], b: [f, f, k, e, e, l][m] } } }; return s }), function (a, b) { "object" == typeof exports ? module.exports = b(a, require("jquery"), require("colors")) : "function" == typeof define && define.amd ? define(["jquery", "colors"], function (c, d) { return b(a, c, d) }) : b(a, a.jQuery, a.Colors) }(this, function (a, b, c, d) { "use strict"; function e(a) { return a.value || a.getAttribute("value") || b(a).css("background-color") || "#FFF" } function f(a) { return a = a.originalEvent && a.originalEvent.touches ? a.originalEvent.touches[0] : a, a.originalEvent ? a.originalEvent : a } function g(a) { return b(a.find(r.doRender)[0] || a[0]) } function h(c) { var d = b(this), f = d.offset(), h = b(a), k = r.gap; c ? (s = g(d), s._colorMode = s.data("colorMode"), p.$trigger = d, (t || i()).css(r.positionCallback.call(p, d) || { left: (t._left = f.left) - ((t._left += t._width - (h.scrollLeft() + h.width())) + k > 0 ? t._left + k : 0), top: (t._top = f.top + d.outerHeight()) - ((t._top += t._height - (h.scrollTop() + h.height())) + k > 0 ? t._top + k : 0) }).show(r.animationSpeed, function () { c !== !0 && (y.toggle(!!r.opacity)._width = y.width(), v._width = v.width(), v._height = v.height(), u._height = u.height(), q.setColor(e(s[0])), n(!0)) }).off(".tcp").on(D, ".cp-xy-slider,.cp-z-slider,.cp-alpha", j)) : p.$trigger && b(t).hide(r.animationSpeed, function () { n(!1), p.$trigger = null }).off(".tcp") } function i() { return b("head")[r.cssPrepend ? "prepend" : "append"]('<style type="text/css" id="tinyColorPickerStyles">' + (r.css || I) + (r.cssAddon || "") + "</style>"), b(H).css({ margin: r.margin }).appendTo("body").show(0, function () { p.$UI = t = b(this), F = r.GPU && t.css("perspective") !== d, u = b(".cp-z-slider", this), v = b(".cp-xy-slider", this), w = b(".cp-xy-cursor", this), x = b(".cp-z-cursor", this), y = b(".cp-alpha", this), z = b(".cp-alpha-cursor", this), r.buildCallback.call(p, t), t.prepend("<div>").children().eq(0).css("width", t.children().eq(0).width()), t._width = this.offsetWidth, t._height = this.offsetHeight }).hide() } function j(a) { var c = this.className.replace(/cp-(.*?)(?:\s*|$)/, "$1").replace("-", "_"); (a.button || a.which) > 1 || (a.preventDefault && a.preventDefault(), a.returnValue = !1, s._offset = b(this).offset(), (c = "xy_slider" === c ? k : "z_slider" === c ? l : m)(a), n(), A.on(E, function () { A.off(".tcp") }).on(C, function (a) { c(a), n() })) } function k(a) { var b = f(a), c = b.pageX - s._offset.left, d = b.pageY - s._offset.top; q.setColor({ s: c / v._width * 100, v: 100 - d / v._height * 100 }, "hsv") } function l(a) { var b = f(a).pageY - s._offset.top; q.setColor({ h: 360 - b / u._height * 360 }, "hsv") } function m(a) { var b = f(a).pageX - s._offset.left, c = b / y._width; q.setColor({}, "rgb", c) } function n(a) { var b = q.colors, c = b.hueRGB, e = (b.RND.rgb, b.RND.hsl, r.dark), f = r.light, g = q.toString(s._colorMode, r.forceAlpha), h = b.HUELuminance > .22 ? e : f, i = b.rgbaMixBlack.luminance > .22 ? e : f, j = (1 - b.hsv.h) * u._height, k = b.hsv.s * v._width, l = (1 - b.hsv.v) * v._height, m = b.alpha * y._width, n = F ? "translate3d" : "", p = s[0].value, t = s[0].hasAttribute("value") && "" === p && a !== d; v._css = { backgroundColor: "rgb(" + c.r + "," + c.g + "," + c.b + ")" }, w._css = { transform: n + "(" + k + "px, " + l + "px, 0)", left: F ? "" : k, top: F ? "" : l, borderColor: b.RGBLuminance > .22 ? e : f }, x._css = { transform: n + "(0, " + j + "px, 0)", top: F ? "" : j, borderColor: "transparent " + h }, y._css = { backgroundColor: "#" + b.HEX }, z._css = { transform: n + "(" + m + "px, 0, 0)", left: F ? "" : m, borderColor: i + " transparent" }, s._css = { backgroundColor: t ? "" : g, color: t ? "" : b.rgbaMixBGMixCustom.luminance > .22 ? e : f }, s.text = t ? "" : p !== g ? g : "", a !== d ? o(a) : G(o) } function o(a) { v.css(v._css), w.css(w._css), x.css(x._css), y.css(y._css), z.css(z._css), r.doRender && s.css(s._css), s.text && s.val(s.text), r.renderCallback.call(p, s, "boolean" == typeof a ? a : d) } var p, q, r, s, t, u, v, w, x, y, z, A = b(document), B = b(), C = "touchmove.tcp mousemove.tcp pointermove.tcp", D = "touchstart.tcp mousedown.tcp pointerdown.tcp", E = "touchend.tcp mouseup.tcp pointerup.tcp", F = !1, G = a.requestAnimationFrame || a.webkitRequestAnimationFrame || function (a) { a() }, H = '<div class="cp-color-picker"><div class="cp-z-slider"><div class="cp-z-cursor"></div></div><div class="cp-xy-slider"><div class="cp-white"></div><div class="cp-xy-cursor"></div></div><div class="cp-alpha"><div class="cp-alpha-cursor"></div></div></div>', I = ".cp-color-picker{position:absolute;overflow:hidden;padding:6px 6px 0;background-color:#444;color:#bbb;font-family:Arial,Helvetica,sans-serif;font-size:12px;font-weight:400;cursor:default;border-radius:5px}.cp-color-picker>div{position:relative;overflow:hidden}.cp-xy-slider{float:left;height:128px;width:128px;margin-bottom:6px;background:linear-gradient(to right,#FFF,rgba(255,255,255,0))}.cp-white{height:100%;width:100%;background:linear-gradient(rgba(0,0,0,0),#000)}.cp-xy-cursor{position:absolute;top:0;width:10px;height:10px;margin:-5px;border:1px solid #fff;border-radius:100%;box-sizing:border-box}.cp-z-slider{float:right;margin-left:6px;height:128px;width:20px;background:linear-gradient(red 0,#f0f 17%,#00f 33%,#0ff 50%,#0f0 67%,#ff0 83%,red 100%)}.cp-z-cursor{position:absolute;margin-top:-4px;width:100%;border:4px solid #fff;border-color:transparent #fff;box-sizing:border-box}.cp-alpha{clear:both;width:100%;height:16px;margin:6px 0;background:linear-gradient(to right,#444,rgba(0,0,0,0))}.cp-alpha-cursor{position:absolute;margin-left:-4px;height:100%;border:4px solid #fff;border-color:#fff transparent;box-sizing:border-box}", J = function (a) { q = this.color = new c(a), r = q.options, p = this }; J.prototype = { render: n, toggle: h }, b.fn.colorPicker = function (c) { var d = this, f = function () { }; return c = b.extend({ animationSpeed: 150, GPU: !0, doRender: !0, customBG: "#FFF", opacity: !0, renderCallback: f, buildCallback: f, positionCallback: f, body: document.body, scrollResize: !0, gap: 4, dark: "#222", light: "#DDD" }, c), !p && c.scrollResize && b(a).on("resize.tcp scroll.tcp", function () { p.$trigger && p.toggle.call(p.$trigger[0], !0) }), B = B.add(this), this.colorPicker = p || new J(c), this.options = c, b(c.body).off(".tcp").on(D, function (a) { -1 === B.add(t).add(b(t).find(a.target)).index(a.target) && h() }), this.on("focusin.tcp click.tcp", function (a) { p.color.options = b.extend(p.color.options, r = d.options), h.call(this, a) }).on("change.tcp", function () { q.setColor(this.value || "#FFF"), d.colorPicker.render(!0) }).each(function () { var a = e(this), d = a.split("("), f = g(b(this)); f.data("colorMode", d[1] ? d[0].substr(0, 3) : "HEX").attr("readonly", r.preventFocus), c.doRender && f.css({ "background-color": a, color: function () { return q.setColor(a).rgbaMixBGMixCustom.luminance > .22 ? c.dark : c.light } }) }) }, b.fn.colorPicker.destroy = function () { b("*").off(".tcp"), p.toggle(!1), B = b() } });