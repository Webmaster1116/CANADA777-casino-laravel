
/*
 * ==========================================================
 * TICKETS SCRIPT
 * ==========================================================
 *
 * Tickets App main Javascript file. © 2021 board.support. All rights reserved.
 * 
 */

'use strict';

(function ($) {

    var main;
    var main_panel;
    var conversation_area;
    var panel;
    var editor;
    var active_panel;
    var left_conversations;
    var cache_agents = {};
    var cache_account = {};
    var main_title;
    var agent_profile;
    var user_profile;
    var width;
    var mobile = $(window).width() < 426;

    /*
    * ----------------------------------------------------------
    * # FUNCTIONS
    * ----------------------------------------------------------
    */

    var SBTickets = {

        // Display the conversation area or a panel
        showPanel: function (name = '', title = false) {
            let previous = active_panel;
            active_panel = name;
            $(main).addClass('sb-panel-active sb-load').removeClass('sb-panel-form').attr('data-panel', name);
            switch (name) {
                case 'privacy':
                    SBF.ajax({
                        function: 'get-block-setting',
                        value: 'privacy'
                    }, (response) => {
                        $(main_title).html(sb_(response['title']));
                        $(panel).append(`<div class="sb-privacy sb-init-form" data-decline="${sb_(response['decline'].replace(/"/g, ''))}"><div class="sb-text">${sb_(response['message'])}</div>` + (response['link'] != '' ? `<a target="_blank" href="${response['link']}">${sb_(response['link-name'])}</a>` : '') + `<div class="sb-buttons"><a class="sb-btn sb-approve">${sb_(response['btn-approve'])}</a><a class="sb-btn sb-decline">${sb_(response['btn-decline'])}</a></div></div>`);
                    });
                    this.showSidePanels(false);
                    break;
                case 'articles':
                    $(main_title).html(sb_(title == false ? 'Articles' : title));
                    this.showSidePanels(false);
                    break;
                case 'edit-profile':
                case 'login':
                case 'registration':
                    let is_edit_profile = name == 'edit-profile';
                    this.showSidePanels(false);
                    $(main).addClass('sb-panel-form');
                    if (name in cache_account) {
                        $(panel).html(cache_account[name]);
                        $(main_title).html(sb_($(panel).find('.sb-top').html()));
                    } else {
                        SBF.ajax({
                            function: 'get-rich-message',
                            name: (is_edit_profile ? 'registration' : name) + '-tickets'
                        }, (response) => {
                            $(panel).html(response);
                            let title = $(panel).find('.sb-top').html();
                            if (is_edit_profile) {
                                $(panel).find('.sb-top').html(sb_('Edit profile'));
                            }
                            $(main_title).html(sb_(title));
                            setTimeout(function () {
                                $(main_title).html(sb_(title));
                            }, 300);
                            $(panel).find('.sb-link-area').insertAfter('.sb-buttons');
                            $(panel).find('.sb-info').insertBefore('.sb-buttons');
                            cache_account[name] = $(panel).html();
                        });
                        $(panel).html('<div class="sb-loading"></div>');
                    }
                    break;
                case 'new-ticket':
                    this.showSidePanels(false);
                    $(main_title).html(sb_('Create a new ticket'));
                    $(panel).html(`<div class="sb-info"></div><div class="sb-input sb-input-text sb-ticket-title"><span>${sb_('Title')}</span><input type="text" required></div>${$(main).find('.sb-ticket-fields').html()}<div class="sb-input sb-editor-cnt"><span>${sb_('Message')}</span></div><div class="sb-btn sb-icon sb-create-ticket"><i class="sb-icon-plus"></i>${sb_('Create Ticket')}</div>`);
                    $(main_panel).find('.sb-editor-cnt').append(editor);
                    break;
                default:
                    this.showSidePanels(true);
                    if (previous == 'new-ticket') {
                        $(editor).removeClass('sb-error');
                        $(editor).find('textarea').val('');
                        $(editor).find('.sb-bar').sbActivate(false);
                        $(conversation_area).after(editor);
                    }
                    $(panel).html('');
                    $(main).removeClass('sb-panel-active sb-load').removeAttr('data-panel');
                    setConversationName(SBChat.conversation);
                    break;
            }
            SBF.event('SBPanelActive', name);
            setTimeout(function () {
                $(main).removeClass('sb-load');
            }, 300);
        },

        // Display or hide the side panels
        showSidePanels: function (show = true) {
            let button = $(main).find('.sb-btn-collapse');
            setCollapsing();
            if (!show || width > 800) {
                $(main).find('.sb-panel-left,.sb-panel-right').setClass('sb-collapsed', !show);
            } else if (width <= 800) {
                $(button).sbActivate();
            }
            $(button).css('display', show ? '' : 'none');
        },

        // Get the agent details and display them
        setAgent: function (agent_id) {
            let label = $(main).find('.sb-agent-label').sbActivate(false);
            SBChat.agent_id = agent_id;
            if (agent_id in cache_agents) {
                let agent = cache_agents[agent_id];
                $(agent_profile).setProfile(agent.name, agent.image).sbActivate();
                if ('details' in agent) {
                    SBF.getLocationTimeString(agent.extra, (response) => {
                        $(label).html((agent.get('flag') != '' ? `<img src="${SB_URL}/media/flags/${agent.get('flag')}">` : '<i class="sb-icon sb-icon-marker"></i>') + response).sbActivate();
                    });
                }
            } else {
                SBF.ajax({
                    function: 'get-agent',
                    agent_id: agent_id
                }, (response) => {
                    if (response != false) {
                        cache_agents[agent_id] = new SBUser(response);
                        this.setAgent(agent_id);
                    }
                });
            }
        },

        // Activate a conversation
        activateConversation: function (conversation) {
            if (conversation instanceof SBConversation) {
                let last_agent = SBChat.lastAgent();
                let details = ['id', 'creation_time', 'last_update'];
                let code = '';

                // Activate the conversation
                this.selectConversation(conversation.id);
                setConversationName(conversation);
                $(main).find('.sb-panel-right .sb-scroll-area > div').sbActivate(false);

                // Set the agent details
                if (last_agent != false) {
                    $(agent_profile).setProfile(last_agent['full_name'], last_agent['profile_image']);
                    this.setAgent(last_agent['user_id']);
                    setTimeout(function () {
                        SBChat.updateUsersActivity();
                    }, 300);
                } else {
                    $(agent_profile).sbActivate(false);
                }

                // Set the ticket details
                if (conversation.get('department') != '') {
                    SBChat.getDepartmentCode(conversation.get('department'), (response) => {
                        let department = $(main).find('.sb-department');
                        $(department).html(`<span class="sb-title">${$(department).data('label')}</span>${response}`).sbActivate();
                    });
                }
                for (var i = 0; i < details.length; i++) {
                    let values;
                    switch (details[i]) {
                        case 'id':
                            values = ['padlock', sb_('Ticket ID'), conversation.id];
                            break;
                        case 'creation_time':
                            values = ['calendar', sb_('Creation time'), SBF.beautifyTime(conversation.get('creation_time'), true)];
                            break;
                        case 'last_update':
                            values = ['reload', sb_('Last update'), SBF.beautifyTime(conversation.getLastMessage() == false ? conversation.get('creation_time') : conversation.getLastMessage().get('creation_time'), true)];
                            break;
                    }
                    code += `<div data-id="${details[i]}"><i class="sb-icon sb-icon-${values[0]}"></i><span>${values[1]}</span><div>${values[2]}</div></div>`;
                }
                $(main).find('.sb-ticket-details').html(code);

                // Attachments
                let attachments = conversation.getAttachments();
                code = '';
                for (var i = 0; i < attachments.length; i++) {
                    code += `<a href="${attachments[i][1]}" target="_blank"><i class="sb-icon sb-icon-file"></i>${attachments[i][0]}</a>`;
                }
                $(main).find('.sb-conversation-attachments').html((code == '' ? '' : `<div class="sb-title">${sb_('Attachments')}</div>`) + code);

                $(conversation_area).sbLoading(false);
            } else {
                SBF.error('Value not of type SBConversation', 'activateConversation');
            }
        },

        // Apply the selected style to the active conversation
        selectConversation: function (conversation_id) {
            let conversation = $(left_conversations).find(`[data-conversation-id="${conversation_id}"]`);
            $(left_conversations).find('> li').sbActivate(false);
            if ($(conversation).attr('data-conversation-status') == 1) {
                $(conversation).attr('data-conversation-status', 0);
            }
            $(conversation).sbActivate();
        },

        // Get the ID of the active conversation
        getActiveConversation: function (type = '') {
            let conversation = $(left_conversations).find(' > .sb-active');
            return conversation.length ? (type == 'ID' ? $(conversation).attr('data-conversation-id') : conversation) : -1;
        },

        // Tickets welcome message
        welcome: function () {
            if (SBF.setting('tickets-welcome-active') && !SBF.storage('tickets-welcome')) {
                setTimeout(() => {
                    SBChat.sendMessage(SBF.setting('bot-id'), SBF.setting('tickets-welcome-message'));
                    SBF.storage('tickets-welcome', true);
                }, 1000);
            }
        },

        // Initialize the tickets area
        init: function () {

            main = $('body').find('.sb-tickets');
            main_panel = $(main).find(' > div > .sb-panel-main');
            panel = $(main_panel).find('.sb-panel');
            editor = $(main_panel).find('.sb-editor');
            main_title = $(main_panel).find(' > .sb-top .sb-title');
            left_conversations = $(main).find('.sb-user-conversations');
            conversation_area = $(main_panel).find('.sb-list');
            agent_profile = $(main).find('.sb-profile-agent');
            user_profile = $(main).find('.sb-panel-right > .sb-top .sb-profile');
            width = $(main).width();
            ticketsInit();

            if (!main.length) return;
            if (SBF.setting('tickets-registration-required') && (!activeUser() || ['visitor', 'lead'].includes(activeUser().type))) {
                let redirect = SBF.setting('tickets-registration-redirect');
                if (redirect != '') {
                    document.location = redirect;
                } else {
                    $(main).addClass('sb-no-conversations');
                    SBTickets.showPanel(SBF.setting('tickets-default-form'));
                }
            } else if (!SBF.setting('welcome') && (!activeUser() || activeUser().conversations == false)) {
                $(main).addClass('sb-no-conversations');
                if (SBF.setting('privacy') && !SBF.storage('privacy-approved')) {
                    SBTickets.showPanel('privacy');
                } else {
                    SBTickets.showPanel('new-ticket');
                }
            } else {
                if (activeUser().conversations.length && SBTickets.getActiveConversation() == -1) {
                    SBChat.openConversation(SBF.getURL('conversation') ? SBF.getURL('conversation') : activeUser().conversations[0].id);
                } else if (SBF.setting('welcome')) {
                    setConversationName();
                }
            }

            let height = parseInt(SBF.null($(main).data('height')) ? ($(window).height()) : $(main).data('height'));
            let height_offset = parseInt(SBF.null($(main).data('offset')) ? 0 : $(main).data('offset'));

            if (width <= 800) {
                $(main).addClass('sb-800');
                $(main).find('.sb-panel-left,.sb-panel-right').addClass('sb-collapsed');
                $(main).find('.sb-btn-collapse').sbActivate();
            } else if (width <= 1000) {
                $(main).addClass('sb-1000');
            } else if (width <= 1300) {
                $(main).addClass('sb-1300');
            }
            setUserProfile();
            $(main).removeClass('sb-loading').find('.sb-tickets-area').attr('style', `height: ${height - height_offset}px`);
            setTimeout(function () {
                $(main).removeClass('sb-load');
            }, 300);

            SBChat.startRealTime();
            SBF.event('SBTicketsInit');
        },

        // Triggered when a message is sent
        onMessageSent: function () {
            if (active_panel == 'new-ticket') {
                let title = $(main_panel).find('.sb-ticket-title input').val();
                SBChat.updateConversations();
                $(main).find('.sb-panel-right .sb-scroll-area > div').sbActivate(false);
                $(main).find('.sb-conversation-attachments,.sb-ticket-details').html('');
                $(main).removeClass('sb-no-conversations');
                SBTickets.showPanel();
                $(main_title).html(title);
            }
        },

        // Triggered when a new conversation is received
        onNewConversationReceived: function (conversation) {
            if (conversation.id == SBChat.conversation.id && SBTickets.getActiveConversation('ID') != conversation.id) {
                setTimeout(function () {
                    SBTickets.activateConversation(SBChat.conversation);
                }, 300);
            }
        },

        // Triggered when a new message is received
        onNewMessageReceived: function (message) {
            if (message instanceof SBMessage && message.get('conversation_id') == SBChat.conversation.id) {
                let last_agent = SBChat.lastAgent();
                $(SBTickets.getActiveConversation()).html(SBChat.conversation.getCode());
                if (last_agent != false && SBF.isAgent(message.get('user_type')) && last_agent.id != message.get('user_id')) {
                    SBTickets.setAgent(message.get('user_id'));
                }
            }
        }
    }
    window.SBTickets = SBTickets;

    // Set overflow hidden for 1s
    function setCollapsing() {
        $(main).addClass('sb-collapsing');
        setTimeout(function () {
            $(main).removeClass('sb-collapsing');
        }, 1000);
    }

    // Access the global user variable
    function activeUser(value) {
        if (typeof value == 'undefined') {
            return window.sb_current_user;
        } else {
            window.sb_current_user = value;
        }
    }

    // Support Board js translations
    function sb_(string) {
        return SBF.translate(string);
    }

    // Set the profile box of the user
    function setUserProfile() {
        if (activeUser() != false) {
            $(user_profile).setProfile(activeUser().get('last_name').charAt(0) == '#' ? sb_('Account') : activeUser().name);
        }
    }

    function setConversationName(conversation = false) {
        let name = '';
        if (conversation && 'title' in conversation.details && !SBF.null(conversation.details['title'])) {
            name = conversation.get('title');
        } else {
            name = SBF.setting('tickets-conversation-name');
        }
        $(main_title).html(name == '' || name == -1 ? activeUser().name : name);
    }

    if (SBF.null($.fn.setProfile)) {
        $.fn.sbActivate = function (show = true) {
            $(this).setClass('sb-active', show);
            return this;
        };

        $.fn.sbActive = function () {
            return $(this).hasClass('sb-active');
        };

        $.fn.sbLoading = function (value = 'check') {
            if (value == 'check') {
                return $(this).hasClass('sb-loading');
            } else {
                $(this).setClass('sb-loading', value);
            }
            return this;
        }

        $.fn.setProfile = function (name = false, profile_image = false) {
            if (SBF.null(name)) name = activeUser() != false ? activeUser().name : '';
            if (SBF.null(profile_image)) profile_image = activeUser() != false ? activeUser().image : SB_URL + '/media/user.svg';
            $(this).find('img').attr('src', profile_image);
            $(this).find('.sb-name').html(name);
            return this;
        }

        $.fn.setClass = function (class_name, add = true) {
            if (add) {
                $(this).addClass(class_name);
            } else {
                $(this).removeClass(class_name);
            }
        }
    }

    function ticketsInit() {

        /*
        * ----------------------------------------------------------
        * # MISCELLANEOUS
        * ----------------------------------------------------------
        */

        $(main).on('click', '.sb-btn-collapse', function () {
            setCollapsing();
            $(main).find('.sb-panel-' + ($(this).hasClass('sb-left') ? 'left' : 'right')).toggleClass('sb-collapsed');
            $(this).toggleClass('sb-active');
        });

        $(editor).on('focus focusout', 'textarea', function () {
            $(this).parent().parent().toggleClass('sb-focus');
        });

        if (!mobile) {
            $(editor).on('click', '.sb-btn-emoji', function () {
                let settings = active_panel == 'new-ticket' ? [panel, 'padding-top', 415] : [main, 'margin-top', 335];
                if ($(editor).find('.sb-emoji').sbActive()) {
                    let offset_emoji = $(this).offset().top + $(settings[0])[0].scrollTop - window.scrollY;
                    let offset_tickets = $(main).offset().top - window.scrollY;
                    if (offset_emoji - offset_tickets < 380) {
                        $(settings[0]).css(settings[1], (settings[2] - (offset_emoji - offset_tickets)) + 'px');
                    }
                } else {
                    $(settings[0]).css(settings[1], '');
                }
            });
            $(editor).on('click', '.sb-emoji-list > ul > li', function () {
                let settings = active_panel == 'new-ticket' ? [panel, 'padding-top', 415] : [main, 'margin-top', 335];
                $(settings[0]).css(settings[1], '');
            });
        }

        /*
        * ----------------------------------------------------------
        * # MAIN PANEL
        * ----------------------------------------------------------
        */

        $(main_panel).on('click', '> .sb-top .sb-close', function () {
            SBTickets.showPanel();
        });

        /*
        * ----------------------------------------------------------
        * # CONVERSATION AREA
        * ----------------------------------------------------------
        */

        $(main_panel).on('click', '.sb-create-ticket', function () {
            let errors = false;
            $(editor).removeClass('sb-error');
            if (SBForm.errors(panel)) {
                SBForm.showErrorMessage(panel, 'Please fill in all the required fields.');
                errors = true;
            }
            if ($(editor).find('textarea').val().trim() == '') {
                if (!errors) {
                    SBForm.showErrorMessage(panel, 'Please write a message.');
                }
                $(editor).addClass('sb-error');
                errors = true;
            }
            if (!errors && !SBChat.is_busy) {
                let message = '';
                let settings = SBForm.getAll(panel);
                let department = 'department' in settings ? settings['department'][0] : null;
                SBChat.clear();
                SBChat.activateBar(false);
                for (var key in settings) {
                    if (settings[key][1] != '' && settings[key][0] != '') {
                        message += `*${sb_(settings[key][1])}*\n${settings[key][0]}\n\n`;
                    }
                }
                message += $(editor).find('textarea').val().trim();
                if (!activeUser()) {
                    SBChat.addUserAndLogin(() => {
                        SBChat.newConversation(2, -1, message, [], department, null, function () { SBTickets.welcome() });
                    });
                } else {
                    SBChat.newConversation(2, -1, message, [], department, null, function () { SBTickets.welcome() });
                }
            }
        });

        $(main).on('click', '.sb-new-ticket', function () {
            SBTickets.showPanel('new-ticket');
        });

        $(left_conversations).on('click', 'li', function () {
            SBChat.clear();
            SBTickets.selectConversation($(this).attr('data-conversation-id'));
            $(conversation_area).sbLoading(true);
            if (mobile) {
                $(main).find('.sb-panel-left').addClass('sb-collapsed');
            }
        });

        $(main).find('.sb-panel-left .sb-search-btn input').on('input', function () {
            let search = $(this).val();
            SBF.search(search, () => {
                if (search.length > 1) {
                    SBF.ajax({
                        function: 'search-user-conversations',
                        search: search
                    }, (response) => {
                        let conversations = [];
                        for (var i = 0; i < response.length; i++) {
                            conversations.push(new SBConversation([new SBMessage(response[i])], { id: response[i]['conversation_id'], title: response[i]['title'], conversation_status_code: response[i]['conversation_status_code'] }));
                        }
                        $(left_conversations).html(activeUser().getConversationsCode(conversations));
                    });
                } else {
                    SBChat.populateConversations();
                }
            });
        });

        $(main).on('click', '.sb-panel-left .sb-search-btn i', function () {
            SBF.searchClear(this, function () { SBChat.populateConversations(); });
        });

        /*
        * ----------------------------------------------------------
        * # REGISTRATION AND LOGIN
        * ----------------------------------------------------------
        */

        $(panel).on('click', '.sb-login-area', function () {
            SBTickets.showPanel('login');
        });

        $(panel).on('click', '.sb-registration-area', function () {
            SBTickets.showPanel('registration');
        });

        $(main).on('click', '.sb-profile-menu [data-value="edit-profile"]', function () {
            SBTickets.showPanel('edit-profile');
        });

        $(main).on('click', '.sb-profile-menu [data-value="logout"]', function () {
            SBF.logout(false);
            SBTickets.showPanel('login');
        });

        $(panel).on('click', '.sb-submit', function () {
            if (!$(this).sbLoading()) {
                let settings = SBForm.getAll(panel);
                let settings_extra = SBForm.getAll(panel.find('.sb-form-extra'));
                let is_edit_profile = active_panel == 'edit-profile';
                if (SBForm.errors(panel)) {
                    SBForm.showErrorMessage(panel, SBForm.getRegistrationErrorMessage(panel));
                } else {
                    $(this).sbLoading(true);
                    SBF.ajax({
                        function: is_edit_profile || activeUser() ? 'update-user' : 'add-user-and-login',
                        settings: settings,
                        settings_extra: settings_extra
                    }, (response) => {
                        if (response != false && !SBF.errorValidation(response)) {
                            if (activeUser() == false) {
                                activeUser(new SBUser(response[0]));
                            } else {
                                activeUser().details = response[0];
                            }
                            SBF.loginCookie(response[1]);
                            setUserProfile();
                            if (is_edit_profile) {
                                SBTickets.showPanel();
                            } else {
                                SBF.event('SBRegistrationForm', { user: settings });
                                SBF.event('SBNewEmailAddress', { name: activeUser().name, email: activeUser().get('email') });
                                SBTickets.showPanel('new-ticket');
                            }
                        } else {
                            SBForm.showErrorMessage(panel, SBForm.getRegistrationErrorMessage(response, 'response'));
                        }
                        $(this).sbLoading(false);
                    });
                }
            }
        });

        $(panel).on('click', '.sb-submit-login', function () {
            SBF.loginForm(this, panel, (response) => {
                activeUser(new SBUser(response[0]));
                setUserProfile();
                SBChat.populateConversations((response) => {
                    if (response.length == 0) {
                        $(main).addClass('sb-no-conversations');
                        SBTickets.showPanel('new-ticket');
                    } else {
                        SBChat.openConversation(response[0].id);
                        SBTickets.showPanel();
                    }
                });
            });
        });
    }
}(jQuery));