<?php

/*
 * ==========================================================
 * COMPONENTS.PHP
 * ==========================================================
 *
 * Library of static html components for the admin area. This file must not be executed directly. © 2021 board.support. All rights reserved.
 *
 */

/*
 * ----------------------------------------------------------
 * PROFILE BOX
 * ----------------------------------------------------------
 *
 * Profile information area used in admin side
 *
 */

function sb_profile_box() { ?>
    <div class="sb-profile-box sb-lightbox">
        <div class="sb-top-bar">
            <div class="sb-profile">
                <img src="<?php echo SB_URL ?>/media/user.svg" />
                <span class="sb-name"></span>
            </div>
            <div>
                <a data-value="email" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Send email') ?>">
                    <i class="sb-icon-envelope"></i>
                </a>      
                <a data-value="sms" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Send text message') ?>">
                    <i class="sb-icon-sms"></i>
                </a>
                <a class="sb-edit sb-btn sb-icon" data-button="toggle" data-hide="sb-profile-area" data-show="sb-edit-area">
                    <i class="sb-icon-user"></i><?php sb_e('Edit user') ?>
                </a>
                <a class="sb-start-conversation sb-btn sb-icon">
                    <i class="sb-icon-message"></i><?php sb_e('Start a conversation') ?>
                </a>
                <a class="sb-close sb-btn-icon" data-button="toggle" data-hide="sb-profile-area" data-show="sb-table-area">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div>
                <div class="sb-title">
                    <?php sb_e('Details') ?>
                </div>
                <div class="sb-profile-list"></div>
                <div class="sb-agent-area"></div>
            </div>
            <div>
                <div class="sb-title">
                    <?php sb_e('User conversations') ?>
                </div>
                <ul class="sb-user-conversations"></ul>
            </div>
        </div>
    </div>
<?php } ?>
<?php

/*
 * ----------------------------------------------------------
 * PROFILE EDIT BOX
 * ----------------------------------------------------------
 *
 * Profile editing area used in admin side
 *
 */

function sb_profile_edit_box() { ?>
    <div class="sb-profile-edit-box sb-lightbox">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div class="sb-profile">
                <img src="<?php echo SB_URL ?>/media/user.svg" />
                <span class="sb-name"></span>
            </div>
            <div>
                <a class="sb-save sb-btn sb-icon">
                    <i class="sb-icon-check"></i><?php sb_e('Save changes') ?>
                </a>
                <a class="sb-close sb-btn-icon" data-button="toggle" data-hide="sb-profile-area" data-show="sb-table-area">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div class="sb-details">
                <div class="sb-title">
                    <?php sb_e('Edit details') ?>
                </div>
                <div class="sb-edit-box">
                    <div id="profile_image" data-type="image" class="sb-input sb-input-image sb-profile-image">
                        <span><?php sb_e('Profile image') ?></span>
                        <div class="image">
                            <div class="sb-icon-close"></div>
                        </div>
                    </div>
                    <div id="user_type" data-type="select" class="sb-input sb-input-select">
                        <span><?php sb_e('Type') ?></span>
                        <select>
                            <option value="agent"><?php sb_e('Agent') ?></option>
                            <option value="admin"><?php sb_e('Admin') ?></option>
                        </select>
                    </div>
                    <?php sb_departments('select') ?>
                    <div id="first_name" data-type="text" class="sb-input">
                        <span><?php sb_e('First name') ?></span>
                        <input type="text" required />
                    </div>
                    <div id="last_name" data-type="text" class="sb-input">
                        <span><?php sb_e('Last name') ?></span>
                        <input type="text" required />
                    </div>
                    <div id="password" data-type="text" class="sb-input">
                        <span><?php sb_e('Password') ?></span>
                        <input type="password" />
                    </div>
                    <div id="email" data-type="email" class="sb-input">
                        <span><?php sb_e('Email') ?></span>
                        <input type="email" />
                    </div>
                </div>
                <a class="sb-delete sb-btn-text sb-btn-red">
                    <i class="sb-icon-delete"></i><?php sb_e('Delete user') ?>
                </a>
            </div>
            <div class="sb-additional-details">
                <div class="sb-title">
                    <?php sb_e('Edit additional details') ?>
                </div>
                <div class="sb-edit-box">
                    <div id="address" data-type="text" class="sb-input">
                        <span><?php sb_e('Address') ?></span>
                        <input type="text" />
                    </div>
                    <div id="city" data-type="text" class="sb-input">
                        <span><?php sb_e('City') ?></span>
                        <input type="text" />
                    </div>
                    <div id="country" data-type="select" class="sb-input">
                        <span><?php sb_e('Country') ?></span>
                        <?php echo sb_select_countries() ?>
                    </div>
                    <div id="postal_code" data-type="text" class="sb-input">
                        <span><?php sb_e('Postal code') ?></span>
                        <input type="text" />
                    </div>
                    <div id="phone" data-type="text" class="sb-input">
                        <span><?php sb_e('Phone') ?></span>
                        <input type="text" />
                    </div>
                    <div id="language" data-type="select" class="sb-input">
                        <span><?php sb_e('Language') ?></span>
                        <?php echo sb_select_languages() ?>
                    </div>
                    <div id="birthdate" data-type="date" class="sb-input">
                        <span><?php sb_e('Birthdate') ?></span>
                        <input type="date" />
                    </div>
                    <div id="company" data-type="text" class="sb-input">
                        <span><?php sb_e('Company') ?></span>
                        <input type="text" />
                    </div>
                    <div id="facebook" data-type="text" class="sb-input">
                        <span><?php sb_e('Facebook') ?></span>
                        <input type="text" />
                    </div>
                    <div id="twitter" data-type="text" class="sb-input">
                        <span><?php sb_e('Twitter') ?></span>
                        <input type="text" />
                    </div>
                    <div id="linkedin" data-type="text" class="sb-input">
                        <span><?php sb_e('LinkedIn') ?></span>
                        <input type="text" />
                    </div>
                    <div id="website" data-type="text" class="sb-input">
                        <span><?php sb_e('Website') ?></span>
                        <input type="text" />
                    </div>
                    <div id="timezone" data-type="text" class="sb-input">
                        <span><?php sb_e('Timezone') ?></span>
                        <input type="text" />
                    </div>
                    <?php

    $additional_fields = sb_get_setting('user-additional-fields');
    if ($additional_fields != false && is_array($additional_fields)) {
        $code = '';
        for ($i = 0; $i < count($additional_fields); $i++) {
            $value = $additional_fields[$i];
            if ($value['extra-field-name'] != '') {
                $code .= '<div id="' . $value['extra-field-slug'] . '" data-type="text" class="sb-input"><span>' . $value['extra-field-name'] . '</span><input type="text"></div>';
            }
        }
        echo $code;
    }

                    ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php

/*
 * ----------------------------------------------------------
 * LOGIN BOX
 * ----------------------------------------------------------
 *
 * Administration area login box
 *
 */

function sb_login_box() { ?>
    <form class="sb sb-rich-login sb-admin-box">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <img src="<?php echo sb_get_setting('login-icon') != false ? sb_get_setting('login-icon') : SB_URL . '/media/logo.svg' ?>" />
            <div class="sb-title"><?php sb_e(sb_get_setting('admin-title', 'Sign into')) ?></div>
            <div class="sb-text"><?php sb_e(sb_get_setting('login-message', defined('SB_WP') ? 'Please insert email and password of your WordPress account' : 'To continue to Support Board')) ?></div>
        </div>
        <div class="sb-main">
            <div id="email" class="sb-input">
                <span><?php sb_e('Email') ?></span>
                <input type="text" />
            </div>
            <div id="password" class="sb-input">
                <span><?php sb_e('Password') ?></span>
                <input type="password" />
            </div>
            <div class="sb-bottom">
                <div class="sb-btn sb-submit-login"><?php sb_e('Login') ?></div>
            </div>
        </div>
    </form>
    <img id="sb-error-check" style="display:none" src="<?php echo SB_URL . '/media/logo.svg' ?>" />
    <script>
        (function ($) { 
            $(document).ready(function () {
                $('.sb-admin-start').removeAttr('style');
                $('.sb-submit-login').on('click', function () {
                    SBF.loginForm(this, false, function () {
                        location.reload();
                    });
                });
                $('#sb-error-check').one('error', function () {
                    $('.sb-info').html('It looks like the chat URL has changed. Edit the config.php file(it\'s in the Support Board folder) and update the SB_URL constant with the new URL.').addClass('sb-active');
                });
            });
            $(window).keydown(function (e) {
                if (e.which == 13) {
                    $('.sb-submit-login').click();
                }
            });
        }(jQuery)); 
    </script>
<?php } ?>
<?php

/*
 * ----------------------------------------------------------
 * CONFIRMATION ALERT BOX
 * ----------------------------------------------------------
 *
 * Ask a yes / no question to confirm an operation
 *
 */

function sb_dialog() { ?>
    <div class="sb-dialog-box sb-lightbox">
        <div class="sb-title"></div>
        <p></p>
        <div>
            <a class="sb-confirm sb-btn"><?php sb_e('Confirm') ?></a>
            <a class="sb-cancel sb-btn sb-btn-red"><?php sb_e('Cancel') ?></a>
            <a class="sb-close sb-btn"><?php sb_e('Close') ?></a>
        </div>
    </div>
<?php } ?>

<?php

/*
 * ----------------------------------------------------------
 * UPDATES BOX
 * ----------------------------------------------------------
 *
 * Display the updates box
 *
 */

function sb_updates_box() { ?>
    <div class="sb-lightbox sb-updates-box">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div><?php sb_e('Update center') ?></div>
            <div>
                <a class="sb-close sb-btn-icon">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div class="sb-bottom">
                <a class="sb-update sb-btn sb-icon">
                    <i class="sb-icon-reload"></i><?php sb_e('Update now') ?>
                </a>
                <a href="http://board.support/changes" target="_blank" class="sb-btn-text">
                    <i class="sb-icon-clock"></i><?php sb_e('Change Log') ?>
                </a>
            </div>
        </div>
    </div>
<?php } ?>
<?php

/*
 * ----------------------------------------------------------
 * SYSTEM REQUIREMENTS BOX
 * ----------------------------------------------------------
 *
 * Display the system requirements box
 *
 */

function sb_requirements_box() { ?>
    <div class="sb-lightbox sb-requirements-box">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div><?php sb_e('System requirements') ?></div>
            <div>
                <a class="sb-close sb-btn-icon">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main"></div>
    </div>
<?php } ?>
<?php

/*
 * ----------------------------------------------------------
 * APP BOX
 * ----------------------------------------------------------
 *
 * Display the app box
 *
 */

function sb_app_box() { ?>
    <div class="sb-lightbox sb-app-box" data-app="">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div></div>
            <div>
                <a class="sb-close sb-btn-icon">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main">
            <p></p>
            <div class="sb-title"><?php sb_e('License key') ?></div>
            <div class="sb-input-setting sb-type-text">
                <input type="text" required>
            </div>
            <div class="sb-bottom">
                <a class="sb-btn sb-icon sb-btn-app-setting">
                    <i class="sb-icon-settings"></i><?php sb_e('Settings') ?>
                </a>
                <a class="sb-activate sb-btn sb-icon">
                    <i class="sb-icon-check"></i><?php sb_e('Activate') ?>
                </a>
                <a class="sb-btn sb-icon sb-btn-app-puchase" target="_blank" href="#">
                    <i class="sb-icon-plane"></i><?php sb_e('Purchase license') ?>
                </a>
                <a class="sb-btn-text sb-btn-app-details" target="_blank" href="#">
                    <i class="sb-icon-help"></i><?php sb_e('Read more') ?>
                </a>
            </div>
        </div>
    </div>
<?php } ?>
<?php

/*
 * ----------------------------------------------------------
 * NOTES BOX
 * ----------------------------------------------------------
 *
 * Display the notes box
 *
 */

function sb_notes_box() { ?>
    <div class="sb-lightbox sb-notes-box">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div><?php sb_e('Add new note') ?></div>
            <div>
                <a class="sb-close sb-btn-icon">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main">
            <div class="sb-input-setting sb-type-textarea"><textarea placeholder="<?php sb_e('Write here your note...') ?>"></textarea></div>
            <div class="sb-bottom">
                <a class="sb-add-note sb-btn sb-icon">
                    <i class="sb-icon-plus"></i><?php sb_e('Add note') ?>
                </a>
            </div>
        </div>
    </div>
<?php } ?>
<?php

/*
 * ----------------------------------------------------------
 * DIRECT MESSAGE BOX
 * ----------------------------------------------------------
 *
 * Display the direct message box
 *
 */

function sb_direct_message_box() { ?>
    <div class="sb-lightbox sb-direct-message-box">
        <div class="sb-info"></div>
        <div class="sb-top-bar">
            <div></div>
            <div>
                <a class="sb-close sb-btn-icon">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area">
            <div class="sb-title"><?php sb_e('User IDs') ?></div>
            <div class="sb-input-setting sb-type-text sb-first">
                <input class="sb-direct-message-users" type="text" placeholder="<?php sb_e('User IDs separated by commas') ?>" required>
            </div>
            <div class="sb-title sb-direct-message-subject"><?php sb_e('Subject') ?></div>
            <div class="sb-input-setting sb-type-text sb-direct-message-subject">
                <input type="text" placeholder="<?php sb_e('Email subject') ?>">
            </div>
            <div class="sb-title sb-direct-message-title-subject"><?php sb_e('Message') ?></div>
            <div class="sb-input-setting sb-type-textarea"><textarea placeholder="<?php sb_e('Write here your message...') ?>" required></textarea></div>
            <div class="sb-bottom">
                <a class="sb-send-direct-message sb-btn sb-icon">
                    <i class="sb-icon-plane"></i><?php sb_e('Send message now') ?>
                </a>
                <div></div>
                <a class="sb-btn-text" target="_blank" href="https://board.support/docs#direct-message">
                    <i class="sb-icon-help"></i>
                </a>
            </div>
        </div>
    </div>
<?php } ?>

<?php

/*
 * ----------------------------------------------------------
 * LANGUAGES BOX
 * ----------------------------------------------------------
 *
 * Display the languages selector lightbox
 *
 */

function sb_languages_box() { ?>
    <div class="sb-lightbox sb-languages-box" data-source="">
        <div class="sb-top-bar">
            <div><?php sb_e('Choose a language') ?></div>
            <div>
                <a class="sb-close sb-btn-icon">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
        </div>
        <div class="sb-main sb-scroll-area"></div>
    </div>
<?php } ?>

<?php

/*
 * ----------------------------------------------------------
 * ROUTING AGENTS LIST
 * ----------------------------------------------------------
 *
 * Display the agents list for the routing
 *
 */

function sb_routing_select($exclude_id = false) { 
    $agents = sb_db_get('SELECT id, first_name, last_name FROM sb_users WHERE (user_type = "agent" OR user_type = "admin")' . ($exclude_id ? (' AND id <> ' . sb_db_escape($exclude_id)) : ''), false);
    $code = '<div class="sb-inline sb-inline-agents"><h3>' . sb_('Agent') . '</h3><div id="conversation-agent" class="sb-select"><p>' . sb_('No agents assigned') . '</p><ul><li data-id="" data-value="">' . sb_('No agents assigned') . '</li>';
    for ($i = 0; $i < count($agents); $i++) {
        $code .= '<li data-id="' . $agents[$i]['id'] . '">' . $agents[$i]['first_name'] . ' ' . $agents[$i]['last_name'] . '</li>';
    }
    echo $code . '</ul></div></div>';
}

?>

<?php

/*
 * ----------------------------------------------------------
 * INSTALLATION BOX
 * ----------------------------------------------------------
 *
 * Display the form to install Support Board
 *
 */

function sb_installation_box($error = false) {
    global $SB_LANGUAGE;
    $SB_LANGUAGE = isset($_GET['lang']) ? $_GET['lang'] : strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));

?>
    <div class="sb-main sb-admin sb-admin-start">
        <form class="sb-intall sb-admin-box">
            <?php echo $error === false || $error == 'installation' ? '<div class="sb-info"></div>' : '<div class="sb-info sb-active">' . sb_('We\'re having trouble connecting to your database. Please re-enter your database connection details below. Error: ') . $error . '.</div>'; ?>
            <div class="sb-top-bar">
                <img src="<?php echo (SB_URL == '' || SB_URL == '[url]' ? '' : SB_URL . '/') ?>media/logo.svg" />
                <div class="sb-title"><?php sb_e('Installation') ?></div>
                <div class="sb-text">
                    <?php sb_e('Please complete the installation process by entering your database connection details below. If you are not sure about this, contact your hosting provider for support.') ?>
                </div>
            </div>
            <div class="sb-main">
                <div id="db-name" class="sb-input">
                    <span><?php sb_e('Database Name') ?></span>
                    <input type="text" required />
                </div>
                <div id="db-user" class="sb-input">
                    <span><?php sb_e('Username') ?></span>
                    <input type="text" required />
                </div>
                <div id="db-password" class="sb-input">
                    <span><?php sb_e('Password') ?></span>
                    <input type="text" />
                </div>
                <div id="db-host" class="sb-input">
                    <span><?php sb_e('Host') ?></span>
                    <input type="text" required />
                </div>
                <div id="db-port" class="sb-input">
                    <span><?php sb_e('Port') ?></span>
                    <input type="text" placeholder="Default" />
                </div>
                <?php if ($error === false || $error == 'installation') { ?>
                    <div class="sb-text">
                        <?php sb_e('Enter the user details of the main account you will use to login into the administration area. You can update these details later.') ?>
                    </div>
                    <div id="first-name" class="sb-input">
                        <span><?php sb_e('First name') ?></span>
                        <input type="text" required />
                    </div>
                    <div id="last-name" class="sb-input">
                        <span><?php sb_e('Last name') ?></span>
                        <input type="text" required />
                    </div>
                    <div id="email" class="sb-input">
                        <span><?php sb_e('Email') ?></span>
                        <input type="email" required />
                    </div>
                    <div id="password" class="sb-input">
                        <span><?php sb_e('Password') ?></span>
                        <input type="password" required />
                    </div>
                    <div id="password-check" class="sb-input">
                        <span><?php sb_e('Repeat password') ?></span>
                        <input type="password" required />
                    </div>
                <?php } ?>
                <div class="sb-bottom">
                    <div class="sb-btn sb-submit-installation"><?php sb_e('Complete installation') ?></div>
                </div>
            </div>
        </form>
    </div>
<?php } ?>
<?php

/*
 * ----------------------------------------------------------
 * ADMIN AREA
 * ----------------------------------------------------------
 *
 * Display the administration area
 *
 */

function sb_component_admin() {
    $is_cloud = defined('SB_CLOUD');
    $sb_settings = json_decode(file_get_contents(SB_PATH . '/resources/json/settings.json'), true);
    $active_user = sb_get_active_user(false, true);
    $collapse = sb_get_setting('collapse') ? ' sb-collapse' : '';
    $apps = [
        ['SB_WP', 'wordpress', 'WordPress'], 
        ['SB_DIALOGFLOW', 'dialogflow', 'Dialogflow', 'Connect smart chatbots and automate conversations by using one of the most advanced forms of artificial intelligence in the world.'],
        ['SB_TICKETS', 'tickets', 'Tickets', 'Provide help desk support to your customers by including a ticket area, with all chat features included, on any web page in seconds.'],
        ['SB_MESSENGER', 'messenger', 'Messenger', 'Read, manage and reply to all messages sent to your Facebook pages directly from Support Board, and keep your communication in one place.'],
        ['SB_WHATSAPP', 'whatsapp', 'WhatsApp', 'Lets your users reach you via WhatsApp. Read and reply to all messages sent to your WhatsApp Business account directly from Support Board.'],
        ['SB_WOOCOMMERCE', 'woocommerce', 'WooCommerce', 'Increase sales, provide better support, and faster solutions, by integrating WooCommerce with Support Board.'],
        ['SB_SLACK', 'slack', 'Slack', 'Communicate with your users right from Slack. Send and receive messages and attachments, use emojis, and much more.'], 
        ['SB_UMP', 'ump', 'Ultimate Membership Pro', 'Enable ticket and chat support for subscribers only, view member profile details and subscription details in the admin area.'],
        ['SB_PERFEX', 'perfex', 'Perfex', 'Synchronize your Perfex customers in real-time and let them contact you via chat! View profile details, proactively engage them, and more.'],
        ['SB_WHMCS', 'whmcs', 'Whmcs', 'Synchronize your customers in real-time, chat with them and boost their engagement, or provide a better and faster support.'],
        ['SB_AECOMMERCE', 'aecommerce', 'Active eCommerce', 'Increase sales and connect you and sellers with customers in real-time by integrating Active eCommerce with Support Board.'],
        ['SB_ARMEMBER', 'armember', 'ARMember', 'Synchronize customers, enable ticket and chat support for subscribers only, view subscription plans in the admin area.']
    ];
    $logged = $active_user && sb_is_agent($active_user['user_type']);
    $is_admin = $active_user && $active_user['user_type'] == 'admin';
    $sms = sb_get_multi_setting('sms', 'sms-user');
    $css = ($logged ? 'sb-admin' : 'sb-admin-start') . (sb_get_setting('rtl-admin') ? ' sb-rtl' : '') . ($is_cloud ? ' sb-cloud' : '');

?>
    <div class="sb-main <?php echo $css ?>" style="opacity: 0">
        <?php if ($logged) { ?>
        <div class="sb-header">
            <div class="sb-admin-nav">
                <img src="<?php echo SB_URL ?>/media/icon.svg" />
                <div>
                    <a id="sb-conversations" class="sb-active">
                        <span>
                            <?php sb_e('Conversations') ?>
                        </span>
                    </a>
                    <?php 
                  if ($is_admin || sb_get_setting('admin-agents-users-area')) echo '<a id="sb-users"><span>' . sb_('Users') . '</span></a>';
                  if ($is_admin) echo '<a id="sb-settings"><span>' . sb_('Settings') . '</span></a><a id="sb-reports"><span>' . sb_('Reports') . '</span></a>';
                    ?>
                </div>
            </div>
            <div class="sb-admin-nav-right sb-menu-mobile">
                <i class="sb-icon-menu"></i>
                <div class="sb-desktop">
                    <div class="sb-account">
                        <img src="<?php echo SB_URL ?>/media/user.svg" />
                        <div>
                            <a class="sb-profile">
                                <img src="<?php echo SB_URL ?>/media/user.svg" />
                                <span class="sb-name"></span>
                            </a>
                            <ul class="sb-menu">
                                <li data-value="status" class="sb-online">
                                    <?php sb_e('Online') ?>
                                </li>
                                <?php if ($is_admin) echo '<li data-value="edit-profile">' . sb_('Edit profile') . '</li>' . ($is_cloud ? ('<li data-value="account">' . sb_('Account') . '</li>') : '') ?>
                                <li data-value="logout">
                                    <?php sb_e('Logout') ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <?php if ($is_admin) echo '<a href="http://board.support/docs/' . ($is_cloud ? '?cloud' : '') . '" target="_blank" class="sb-docs"><i class="sb-icon-help"></i></a><a href="#" class="sb-version">' . SB_VERSION . '</a>' ?> 
                </div>
                <div class="sb-mobile">
                    <?php if ($is_admin) echo '<a href="#" class="edit-profile">' . sb_('Edit profile') . '</a><a href="#" class="sb-docs">' . sb_('Docs') . '</a><a href="#" class="sb-version">' . sb_('Updates') . '</a>' ?>
                    <a href="#" class="logout">
                        <?php sb_e('Logout') ?>
                    </a>
                </div>
            </div>
        </div>
        <main>
            <div class="sb-active sb-area-conversations">
                <div class="sb-board">
                    <div class="sb-admin-list">
                        <div class="sb-top">
                            <div class="sb-select">
                                <p>
                                    <?php sb_e('Inbox') ?>
                                    <span></span>
                                </p>
                                <ul>
                                    <li data-value="0" class="sb-active">
                                        <?php sb_e('Inbox') ?>
                                        <span></span>
                                    </li>
                                    <li data-value="3">
                                        <?php sb_e('Archive') ?>
                                    </li>
                                    <li data-value="4">
                                        <?php sb_e('Trash') ?>
                                    </li>
                                </ul>
                            </div>
                            <div class="sb-search-btn">
                                <i class="sb-icon sb-icon-search"></i>
                                <input type="text" autocomplete="false" placeholder="<?php sb_e('Search for keywords or users...') ?>" />
                            </div>
                        </div>
                        <div class="sb-scroll-area">
                            <ul></ul>
                        </div>
                    </div>
                    <div class="sb-conversation">
                        <div class="sb-top">
                            <i class="sb-btn-back sb-icon-arrow-left"></i>
                            <a></a>
                            <div class="sb-labels"></div>
                            <div class="sb-menu-mobile">
                                <i class="sb-icon-menu"></i>
                                <ul>
                                    <li>
                                        <a data-value="archive" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Archive conversation') ?>">
                                            <i class="sb-icon-check"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a data-value="read" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Mark as read') ?>">
                                            <i class="sb-icon-check-circle"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a data-value="transcript" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Download transcript') ?>">
                                            <i class="sb-icon-download"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a data-value="inbox" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Send to inbox') ?>">
                                            <i class="sb-icon-back"></i>
                                        </a>
                                    </li>
                                    <?php if ($is_admin) echo '<li><a data-value="delete" class="sb-btn-icon sb-btn-red" data-sb-tooltip="' . sb_('Delete conversation') . '"><i class="sb-icon-delete"></i></a></li><li><a data-value="empty-trash" class="sb-btn-icon sb-btn-red" data-sb-tooltip="' . sb_('Empty trash') . '"><i class="sb-icon-delete"></i></a></li>' ?>
                                </ul>
                            </div>
                        </div>
                        <div class="sb-list"></div>
                        <?php sb_component_editor(true); ?>
                        <div class="sb-no-conversation-message">
                            <div>
                                <label>
                                    <?php sb_e('Select a conversation or start a new one') ?>
                                </label>
                                <p>
                                    <?php sb_e('Select a conversation from the left menu or start a new conversation from the users area.') ?>
                                </p>
                            </div>
                        </div>
                        <?php if (sb_get_setting('chat-sound-admin') != 'n') echo '<audio id="sb-audio" preload="auto"><source src="' . SB_URL . '/media/sound.mp3" type="audio/mpeg"></audio><audio id="sb-audio-out" preload="auto"><source src="' . SB_URL . '/media/sound-out.mp3" type="audio/mpeg"></audio>' ?>
                    </div>
                    <div class="sb-user-details">
                        <div class="sb-top">
                            <?php sb_e('Details') ?>
                        </div>
                        <div class="sb-scroll-area">
                            <div class="sb-profile">
                                <img src="<?php echo SB_URL ?>/media/user.svg" />
                                <span class="sb-name"></span>
                            </div>
                            <div class="sb-profile-list sb-profile-list-conversation<?php echo $collapse ?>"></div>
                            <?php sb_apps_panel() ?>
                            <?php sb_departments('custom-select') ?>
                            <?php if (sb_get_setting('routing') || (sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-active') && sb_get_multi_setting('agent-hide-conversations', 'agent-hide-conversations-menu'))) sb_routing_select() ?>
                            <?php if (!sb_get_setting('disable-notes')) echo '<div class="sb-panel-details sb-panel-notes' . $collapse . '"><i class="sb-icon-plus"></i><h3>' . sb_('Notes') . '</h3><div></div></div>' ?>
                            <?php if (!sb_get_setting('disable-attachments')) echo '<div class="sb-panel-details sb-panel-attachments' . $collapse . '"></div>' ?>
                            <h3>
                                <?php sb_e('User conversations') ?>
                            </h3>
                            <ul class="sb-user-conversations"></ul>
                        </div>
                        <div class="sb-no-conversation-message"></div>
                    </div>
                </div>
            </div>
            <div class="sb-area-users">
                <div class="sb-top-bar">
                    <div>
                        <h2>
                            <?php sb_e('Users list') ?>
                        </h2>
                        <div class="sb-menu-wide sb-menu-users">
                            <div>
                                <?php sb_e('All') ?>
                                <span data-count="0"></span>
                            </div>
                            <ul>
                                <li data-type="all" class="sb-active">
                                    <?php sb_e('All') ?>
                                    <span data-count="0">(0)</span>
                                </li>
                                <li data-type="user">
                                    <?php sb_e('Users') ?>
                                    <span data-count="0">(0)</span>
                                </li>
                                <li data-type="lead">
                                    <?php sb_e('Leads') ?>
                                    <span data-count="0">(0)</span>
                                </li>
                                <li data-type="visitor">
                                    <?php sb_e('Visitors') ?>
                                    <span data-count="0">(0)</span>
                                </li>
                                <li data-type="online">
                                    <?php sb_e('Online') ?>
                                </li>
                                <?php if ($is_admin) { echo '<li data-type="agent">' . sb_('Agents &amp; Admins') . '</li>'; } ?>
                            </ul>
                        </div>
                        <div class="sb-menu-mobile">
                            <i class="sb-icon-menu"></i>
                            <ul>
                                <li>
                                    <a data-value="csv" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Download CSV') ?>">
                                        <i class="sb-icon-download"></i>
                                    </a>
                                </li>
                                <li>
                                    <a data-value="message" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Send message') ?>">
                                        <i class="sb-icon-chat"></i>
                                    </a>
                                </li>
                                <li>
                                    <a data-value="email" class="sb-btn-icon" data-sb-tooltip="<?php sb_e('Send email') ?>">
                                        <i class="sb-icon-envelope"></i>
                                    </a>      
                                </li>
                                <?php if ($sms) echo '<li><a data-value="sms" class="sb-btn-icon" data-sb-tooltip="' . sb_('Send text message') . '"><i class="sb-icon-sms"></i></a><li>' ?>
                                <li>
                                    <a data-value="delete" class="sb-btn-icon sb-btn-red" data-sb-tooltip="<?php sb_e('Delete users') ?>" style="display: none;">
                                        <i class="sb-icon-delete"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div>
                        <div class="sb-search-btn">
                            <i class="sb-icon sb-icon-search"></i>
                            <input type="text" autocomplete="false" placeholder="<?php sb_e('Search users ...') ?>" />
                        </div>
                        <a class="sb-btn sb-icon sb-new-user">
                            <i class="sb-icon-user"></i><?php sb_e('Add new user') ?>
                        </a>
                    </div>
                </div>
                <div class="sb-scroll-area">
                    <table class="sb-table sb-table-users">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" />
                                </th>
                                <th data-field="first_name">
                                    <?php sb_e('Full name') ?>
                                </th>
                                <th data-field="email">
                                    <?php sb_e('Email') ?>
                                </th>
                                <th data-field="user_type">
                                    <?php sb_e('Type') ?>
                                </th>
                                <th data-field="last_activity">
                                    <?php sb_e('Last activity') ?>
                                </th>
                                <th data-field="creation_time" class="sb-active">
                                    <?php sb_e('Registration date') ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <div class="sb-loading sb-loading-table"></div>
            </div>
            <?php if ($is_admin) { ?>
                <div class="sb-area-settings">
                    <div class="sb-top-bar">
                        <div>
                            <h2>
                                <?php sb_e('Settings') ?>
                            </h2> 
                        </div>
                        <div>
                            <a class="sb-btn sb-save-changes sb-icon">
                                <i class="sb-icon-check"></i><?php sb_e('Save changes') ?>
                            </a>
                        </div>
                    </div>
                    <div class="sb-tab">
                        <div class="sb-nav">
                            <div><?php sb_e('Settings') ?></div>
                            <ul>
                                <li id="tab-chat" class="sb-active">
                                    <?php sb_e('Chat') ?>
                                </li>
                                <li id="tab-messages">
                                    <?php sb_e('Messages') ?>
                                </li>
                                <li id="tab-admin">
                                    <?php sb_e('Admin') ?>
                                </li>
                                <li id="tab-notifications">
                                    <?php sb_e('Notifications') ?>
                                </li>
                                <li id="tab-users">
                                    <?php sb_e('Users') ?>
                                </li>
                                <li id="tab-design">
                                    <?php sb_e('Design') ?>
                                </li>
                                <li id="tab-various">
                                    <?php sb_e('Miscellaneous') ?>
                                </li>
                                <?php for ($i = 0; $i < count($apps); $i++) { if (defined($apps[$i][0])) echo '<li id="tab-' . $apps[$i][1] . '">' . $apps[$i][2] . '</li>'; } ?>
                                <li id="tab-apps">
                                    <?php sb_e('Apps') ?>
                                </li>
                                <li id="tab-articles">
                                    <?php sb_e('Articles') ?>
                                </li>
                                <li id="tab-automations">
                                    <?php sb_e('Automations') ?>
                                </li>
                                <li id="tab-translations">
                                    <?php sb_e('Translations') ?>
                                </li>
                            </ul>
                        </div>
                        <div class="sb-content sb-scroll-area">
                            <div class="sb-active">
                                <?php sb_populate_settings('chat', $sb_settings) ?>
                            </div>
                            <div>
                                <?php sb_populate_settings('messages', $sb_settings) ?>
                            </div>
                            <div>
                                <?php sb_populate_settings('admin', $sb_settings) ?>
                            </div>
                            <div>
                                <?php sb_populate_settings('notifications', $sb_settings) ?>
                            </div>
                            <div>
                                <?php sb_populate_settings('users', $sb_settings) ?>
                            </div>
                            <div>
                                <?php sb_populate_settings('design', $sb_settings) ?>
                            </div>
                            <div>
                                <?php sb_populate_settings('miscellaneous', $sb_settings) ?>
                            </div>
                            <?php sb_apps_area($apps) ?>
                            <div>
                                <div class="sb-articles-area sb-inner-tab sb-tab">
                                    <div class="sb-nav sb-nav-only">
                                        <div class="sb-menu-wide">
                                            <div>
                                                <?php sb_e('Articles') ?>
                                            </div>
                                            <ul>
                                                <li data-type="articles" class="sb-active">
                                                  <?php sb_e('Articles') ?>
                                                </li>
                                                <li data-type="categories">
                                                   <?php sb_e('Categories') ?>
                                                </li>
                                            </ul>
                                        </div>
                                        <ul></ul>
                                        <span class="sb-new-category-cnt"></span>
                                        <div class="sb-add-category sb-btn sb-icon"><i class="sb-icon-plus"></i><?php sb_e('Add new category') ?></div>
                                        <div class="sb-add-article sb-btn sb-icon"><i class="sb-icon-plus"></i><?php sb_e('Add new article') ?></div>
                                    </div>
                                    <div class="sb-content">
                                        <h2 class="sb-language-switcher-cnt">
                                            <?php sb_e('Article title') ?>
                                        </h2>
                                        <div class="sb-input-setting sb-type-text sb-article-title">
                                            <div>
                                                <input type="text" />
                                            </div>
                                        </div>
                                        <h2>
                                            <?php sb_e('Content') ?>
                                        </h2>
                                        <div class="sb-input-setting sb-type-textarea sb-article-content">
                                            <div>
                                                <textarea></textarea>
                                            </div>
                                        </div>
                                        <h2>
                                            <?php sb_e('External link') ?>
                                        </h2>
                                        <div class="sb-input-setting sb-type-text sb-article-link">
                                            <div>
                                                <input type="text" />
                                            </div>
                                        </div>
                                        <h2>
                                            <?php sb_e('Category') ?>
                                        </h2>
                                        <div class="sb-input-setting sb-type-select sb-article-category">
                                            <div>
                                                <select></select>
                                            </div>
                                        </div>
                                        <h2 id="sb-article-id"></h2>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="sb-automations-area">
                                    <div class="sb-select">
                                        <p data-value="messages">
                                            <?php sb_e('Messages') ?> 
                                        </p>
                                        <ul>
                                            <li data-value="messages" class="sb-active">
                                                <?php sb_e('Messages') ?>
                                            </li>
                                            <li data-value="emails">
                                                <?php sb_e('Emails') ?>
                                            </li>
                                            <?php if ($sms) echo '<li data-value="sms">' . sb_('Text messages') . '</li>' ?>
                                            <li data-value="popups">
                                                <?php sb_e('Pop-ups') ?>
                                            </li>
                                            <li data-value="design">
                                                <?php sb_e('Design') ?>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="sb-inner-tab sb-tab">
                                        <div class="sb-nav sb-nav-only">
                                            <ul></ul>
                                            <div class="sb-add-automation sb-btn sb-icon"><i class="sb-icon-plus"></i><?php sb_e('Add new automation') ?></div>
                                        </div>
                                        <div class="sb-content sb-hide">
                                            <div class="sb-automation-values">
                                                <h2 class="sb-language-switcher-cnt"><?php sb_e('Name') ?></h2>
                                                <div class="sb-input-setting sb-type-text">
                                                    <div>
                                                        <input data-id="name" type="text">
                                                    </div>
                                                </div>
                                                <h2><?php sb_e('Message') ?></h2>
                                                <div class="sb-input-setting sb-type-textarea">
                                                    <div>
                                                        <textarea data-id="message"></textarea>
                                                    </div>
                                                </div>
                                                <div class="sb-automation-extra"></div>
                                            </div>
                                            <div class="sb-automation-conditions">
                                                <hr />
                                                <h2><?php sb_e('Conditions') ?></h2>
                                                <div class="sb-conditions"></div>
                                                <div class="sb-add-condition sb-btn sb-icon"><i class="sb-icon-plus"></i><?php sb_e('Add condition') ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="sb-translations sb-tab">
                                    <div class="sb-nav sb-nav-only"><div class="sb-active"></div><ul></ul></div>
                                    <div class="sb-content">
                                        <div class="sb-hide">
                                            <div class="sb-menu-wide">
                                                <div><?php sb_e('Front End') ?></div>
                                                <ul>
                                                    <li data-value="front" class="sb-active"><?php sb_e('Front End') ?></li>
                                                    <li data-value="admin"><?php sb_e('Admin') ?></li>
                                                </ul>
                                            </div>
                                            <a class="sb-btn sb-icon sb-add-translation"><i class="sb-icon-plus"></i><?php sb_e('New translation') ?></a>
                                        </div>
                                        <div class="sb-translations-list">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sb-area-reports sb-loading">
                    <div class="sb-top-bar">
                        <div>
                            <h2><?php sb_e('Reports') ?></h2> 
                        </div>
                        <div>
                            <div class="sb-setting sb-type-text"><input id="sb-date-picker" placeholder="00/00/0000 - 00/00/0000" type="text" /></div>
                        </div>
                    </div>
                    <div class="sb-tab">
                        <div class="sb-nav sb-nav-only">
                            <div><?php sb_e('Reports') ?></div>
                            <ul>
                                <li class="sb-tab-nav-title">
                                    <?php sb_e('Conversations') ?>
                                </li>
                                <li id="conversations" class="sb-active">
                                    <?php sb_e('Conversations') ?>
                                </li>
                                <li id="missed-conversations">
                                    <?php sb_e('Missed conversations') ?>
                                </li>
                                <li id="conversations-time">
                                    <?php sb_e('Conversations time') ?>
                                </li>
                                 <li class="sb-tab-nav-title">
                                    <?php sb_e('Direct messages') ?>
                                </li>
                                <li id="direct-messages">
                                    <?php sb_e('Chat messages') ?>
                                </li>
                                <li id="direct-emails">
                                    <?php sb_e('Emails') ?>
                                </li>
                                <li id="direct-sms">
                                    <?php sb_e('Text messages') ?>
                                </li>
                                <li class="sb-tab-nav-title">
                                    <?php sb_e('Users and agents') ?>
                                </li>
                                <li id="visitors">
                                    <?php sb_e('Visitors') ?>
                                </li>
                                <li id="leads">
                                    <?php sb_e('Leads') ?>
                                </li>
                                <li id="users">
                                    <?php sb_e('Users') ?>
                                </li>
                                <li id="registrations">
                                    <?php sb_e('Registrations') ?>
                                </li>
                                <li id="agents-response-time">
                                    <?php sb_e('Agents response time') ?>
                                </li>
                                <li id="agents-conversations">
                                    <?php sb_e('Agents conversations') ?>
                                </li>
                                <li id="agents-conversations-time">
                                    <?php sb_e('Agents conversations time') ?>
                                </li>
                                <li id="agents-ratings">
                                    <?php sb_e('Agents ratings') ?>
                                </li>
                                <li id="countries">
                                    <?php sb_e('Countries') ?>
                                </li>
                                <li id="languages">
                                    <?php sb_e('Languages') ?>
                                </li>
                                <li id="browsers">
                                    <?php sb_e('Browsers') ?>
                                </li>
                                <li id="os">
                                    <?php sb_e('Operating systems') ?>
                                </li>
                                <li class="sb-tab-nav-title">
                                    <?php sb_e('Automation') ?>
                                </li>
                                <li id="subscribe">
                                    <?php sb_e('Subscribe') ?>
                                </li>
                                <li id="follow-up">
                                    <?php sb_e('Follow up') ?>
                                </li>
                                <li id="message-automations">
                                    <?php sb_e('Message automations') ?>
                                </li>
                                <li id="email-automations">
                                    <?php sb_e('Email automations') ?>
                                </li>
                                <?php if ($sms) echo '<li id="sms-automations">' . sb_('Text message automations') . '</li>' ?>
                                <li class="sb-tab-nav-title">
                                    <?php sb_e('Articles') ?>
                                </li>
                                <li id="articles-searches">
                                    <?php sb_e('Searches') ?>
                                </li>
                                <li id="articles-views">
                                    <?php sb_e('Articles views') ?>
                                </li>
                                <li id="articles-views-single">
                                    <?php sb_e('Articles views by article') ?>
                                </li>
                                 <li id="articles-ratings">
                                    <?php sb_e('Articles ratings') ?>
                                </li>
                            </ul>
                        </div>
                        <div class="sb-content sb-scroll-area">
                            <div class="sb-reports-chart">
                                <div class="chart-cnt"><canvas></canvas></div>
                            </div>
                            <div class="sb-reports-sidebar">
                                <div class="sb-title sb-reports-title"></div>
                                <p class="sb-reports-text"></p>
                                <div class="sb-collapse">
                                    <div><table class="sb-table"></table></div>
                                </div>
                            </div>
                            <p class="sb-no-results"><?php echo sb_('No data found.') ?></p>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </main>
        <?php

                  sb_profile_box();
                  sb_profile_edit_box();
                  sb_dialog();
                  sb_direct_message_box();
                  if (defined('SB_DIALOGFLOW')) sb_dialogflow_intent_box();
                  if (!sb_get_setting('disable-notes')) sb_notes_box();
                  if ($is_admin) {
                      sb_updates_box();
                      sb_requirements_box();
                      sb_app_box();
                      sb_languages_box();
                  }

        ?>
        <form class="sb-upload-form-admin sb-upload-form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
            <input type="file" name="files[]" class="sb-upload-files" multiple />
        </form>
        <div class="sb-info-card"></div>
        <?php } else { sb_login_box(); } ?>
        <div class="sb-lightbox sb-lightbox-media">
            <div></div>
            <i class="sb-icon-close"></i>
        </div>
        <div class="sb-lightbox-overlay"></div>
        <div class="sb-loading-global sb-loading sb-lightbox"></div>
        <input type="email" name="email" style="display:none" autocomplete="email" />
        <input type="password" name="hidden" style="display:none" autocomplete="new-password" />
    </div>
    <?php 
    if (!empty(sb_get_setting('custom-js')) && !$is_cloud) echo '<script id="sb-custom-js" src="' . sb_get_setting('custom-js') . '"></script>';
    if (!empty(sb_get_setting('custom-css')) && !$is_cloud) echo '<link id="sb-custom-css" rel="stylesheet" type="text/css" href="' . sb_get_setting('custom-css') . '" media="all">';
    ?>
<?php } ?>
<?php

/*
 * ----------------------------------------------------------
 * DIALOGFLOW LANGUAGES LIST
 * ----------------------------------------------------------
 *
 * Return the Dialogflow languages list
 *
 */

function sb_dialogflow_languages_list() {
    $languages = [['', sb_('Default'), 'pt-BR', 'Brazilian Portuguese'], ['zh-HK', 'Chinese (Cantonese)'], ['zh-CN', 'Chinese (Simplified)'], ['zh-TW', 'Chinese (Traditional)'], ['da', 'Danish'], ['hi', 'Hindi'], ['id', 'Indonesian'], ['no', 'Norwegian'], ['pl', 'Polish'], ['sv', 'Swedish'], ['th', 'Thai'], ['tr', 'Turkish'], ['en', 'English'], ['nl', 'Dutch'], ['fr', 'French'], ['de', 'German'], ['it', 'Italian'], ['ja', 'Japanese'], ['ko', 'Korean'], ['pt', 'Portuguese'], ['ru', 'Russian'], ['es', 'Spanish'], ['uk', 'Ukranian']];
    $code = '<div data-type="select" class="sb-input-setting sb-type-select sb-dialogflow-languages"><div class="input"><select>';
    for ($i = 0; $i < count($languages); $i++) {
        $code .= '<option value="' . $languages[$i][0] . '">' . $languages[$i][1] . '</option>';
    }
    return $code . '</select></div></div>';
}

/*
 * ----------------------------------------------------------
 * APPS AREA
 * ----------------------------------------------------------
 *
 * 1. Echo the apps settings and apps area
 * 2. Echo the apps conversation panel container
 * 
 */

function sb_apps_area($apps) {
    $apps_wp = ['SB_WP', 'SB_WOOCOMMERCE', 'SB_UMP', 'SB_ARMEMBER']; 
    $apps_php = [];
    $wp = defined('SB_WP');
    $code = '';
    for ($i = 0; $i < count($apps); $i++) {
        if (defined($apps[$i][0])) {
            $code .= '<div>' . sb_populate_app_settings($apps[$i][1]) . '</div>';
        }
    }
    $code .= '<div><div class="sb-apps">';
    for ($i = 1; $i < count($apps); $i++) { 
        if (($wp && !in_array($apps[$i][0], $apps_php)) || (!$wp && !in_array($apps[$i][0], $apps_wp))) {
            $code .= '<div data-app="' . $apps[$i][1] . '">' . (defined($apps[$i][0]) ? '<i class="sb-icon-check"></i>' : '' ) . ' <img src="' . SB_URL . '/media/apps/' . $apps[$i][1] . '.svg" /><h2>' . $apps[$i][2] . '</h2><p>' . sb_($apps[$i][3]) . '</p></div>';
        }
    }
    echo $code . '</div></div>';
}

function sb_apps_panel() {
    $code = '';
    $collapse = sb_get_setting('collapse') ? ' sb-collapse' : '';
    $panels = [['SB_UMP', 'ump'], ['SB_WOOCOMMERCE', 'woocommerce'], ['SB_PERFEX', 'perfex'], ['SB_WHMCS', 'whmcs'], ['SB_AECOMMERCE', 'aecommerce'], ['SB_ARMEMBER', 'armember']];
    for ($i = 0; $i < count($panels); $i++) {
        if (defined($panels[$i][0])) $code .= '<div class="sb-panel-details sb-panel-' . $panels[$i][1] . $collapse . '"></div>';
    }
    echo $code;
}

?>