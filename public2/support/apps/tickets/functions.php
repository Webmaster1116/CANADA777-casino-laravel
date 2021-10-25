<?php

/*
 * ==========================================================
 * TICKETS APP
 * ==========================================================
 *
 * Tickets App main file. © 2021 board.support. All rights reserved.
 *
 */

define('SB_TICKETS', '1.0.8');

/*
 * ----------------------------------------------------------
 * COMPONENT TICKETS
 * ----------------------------------------------------------
 *
 * The tickets main block that render the whole tickets panel code.
 *
 */

function sb_component_tickets() {
    sb_js_global();
    sb_css();
    sb_tickets_css();
    $css = '';
    $disable_fields = sb_get_setting('tickets-disable-features');
    $disable_arrows = sb_isset($disable_fields, 'tickets-arrows');
    $custom_fields = sb_get_setting('tickets-custom-fields');
    if ($disable_fields == false) {
        $disable_fields = [];
    }
    if ($disable_arrows) {
        $css .= ' sb-no-arrows';
    }
    if (sb_get_setting('rtl') || in_array(sb_get_user_language(), ['ar', 'he', 'ku', 'fa', 'ur'])) {
        $css .= ' sb-rtl';
    }
?>
<div class="sb-main sb-tickets sb-loading sb-load<?php echo $css ?>" data-height="<?php echo sb_get_setting('tickets-height') ?>" data-offset="<?php echo sb_get_setting('tickets-height-offset') ?>">
    <div class="sb-tickets-area" style="visibility: hidden; opacity: 0;">
        <?php if (!sb_isset($disable_fields, 'tickets-left-panel')) { ?>
        <div class="sb-panel-left">
            <div class="sb-top">
                <div>
                    <?php if (!sb_isset($disable_fields, 'tickets-button')) echo '<div class="sb-btn sb-icon sb-new-ticket"><i class="sb-icon-plus"></i>' . sb_('Create Ticket') .'</div>'; else echo '<div class="sb-title">' . sb_('Tickets') . '</div>'; ?>
                </div>
                <div class="sb-search-btn">
                    <i class="sb-icon sb-icon-search"></i>
                    <input type="text" autocomplete="false" placeholder="Search for keywords or users..." />
                </div>
            </div>
            <ul class="sb-user-conversations sb-scroll-area" data-profile-image="<?php echo sb_isset($disable_fields, 'tickets-profile-image') ? 'false' : 'true' ?>"></ul>
        </div>
        <?php } ?>
        <div class="sb-panel-main">
            <div class="sb-top">
                <i class="sb-btn-back sb-icon-arrow-left"></i>
                <div class="sb-title"></div>
                <div class="sb-labels"></div>
                <a class="sb-close sb-btn-icon">
                    <i class="sb-icon-close"></i>
                </a>
            </div>
            <div class="sb-conversation">
                <div class="sb-list"></div>
                <?php sb_component_editor(); ?>
                <div class="sb-no-conversation-message">
                    <div>
                        <label>
                            <?php sb_e('Select a ticket or create a new one') ?>
                        </label>
                        <p>
                            <?php sb_e('Select a ticket from the left area or create a new one.') ?>
                        </p>
                    </div>
                </div>
                <audio id="sb-audio" preload="auto">
                    <source src="<?php echo SB_URL ?>/media/sound.mp3" type="audio/mpeg">
                </audio>
                <audio id="sb-audio-out" preload="auto">
                    <source src="<?php echo SB_URL ?>/media/sound-out.mp3" type="audio/mpeg">
                </audio>
            </div>
            <div class="sb-panel sb-scroll-area"></div>
        </div>
        <?php if (!sb_isset($disable_fields, 'tickets-right-panel')) { ?>
        <div class="sb-panel-right">
            <div class="sb-top">
                <?php if (!sb_isset($disable_fields, 'tickets-registration-login')) { ?>
                <div class="sb-profile-menu">
                    <div class="sb-profile<?php if (!sb_get_setting('registration-profile-img') || sb_get_setting('tickets-registration-required')) echo ' sb-no-profile-image' ?>">
                        <img src="" />
                        <span class="sb-name"></span>
                    </div>
                    <div>
                        <ul class="sb-menu">
                            <?php
                          if (!sb_isset($disable_fields, 'tickets-edit-profile')) echo '<li data-value="edit-profile">' . sb_('Edit profile') . '</li>';
                          if (!sb_get_setting('tickets-registration-disable-password')) echo '<li data-value="logout">' . sb_('Logout') . '</li>';
                            ?>
                        </ul>
                    </div>
                </div>
                <?php } else echo '<div class="sb-title">' . sb_('Details') . '</div>' ?>
            </div>
            <div class="sb-scroll-area">
                <?php

                  $code = '';
                  if (!sb_isset($disable_fields, 'tickets-agent')) echo '<div class="sb-profile sb-profile-agent"><img src="" /><div><span class="sb-name"></span><span class="sb-status"></span></div></div>' . (sb_isset($disable_fields, 'tickets-agent-details') ? '' : '<div class="sb-agent-label"></div>');
                  $code .= '<div class="sb-ticket-details"></div>';
                  if (!sb_isset($disable_fields, 'tickets-department')) $code .= '<div class="sb-department" data-label="' . sb_(sb_isset(sb_get_setting('departments-settings'), 'departments-single-label', 'Department')) . '"></div>';
                  $code .= '<div class="sb-conversation-attachments"></div>';
                  if (sb_get_setting('tickets-articles')) $code .= sb_get_rich_message('articles');
                  echo $code;

                ?>
            </div>
            <div class="sb-no-conversation-message"></div>
        </div>
        <?php } ?>
        <?php if (!sb_isset($disable_fields, 'tickets-left-panel') && !$disable_arrows) echo '<i class="sb-btn-collapse sb-left sb-icon-arrow-left"></i>' ?>
        <?php if (!sb_isset($disable_fields, 'tickets-right-panel') && !$disable_arrows) echo '<i class="sb-btn-collapse sb-right sb-icon-arrow-right"></i>' ?>
    </div>
    <div class="sb-lightbox sb-lightbox-media">
        <div></div>
        <i class="sb-icon-close"></i>
    </div>
    <div class="sb-lightbox-overlay"></div>
    <div class="sb-ticket-fields">
        <?php

    $code = '';
    if (sb_get_multi_setting('tickets-fields', 'tickets-field-departments')) {
        $departments = sb_get_departments();
        $code .= '<div id="department" class="sb-input sb-input-select"><span>' . sb_(sb_isset(sb_get_setting('departments-settings'), 'departments-label', 'Department')) . '</span><div class="sb-select"><p data-value="" data-required="true">' . sb_('Select a value') . '</p><ul>';
        foreach ($departments as $key => $value) {
            $code .= '<li data-value="' . $key . '">' . sb_($value['name']) . '</li>';
        }
        $code .= '</ul></div></div>';
    }
    if (sb_get_multi_setting('tickets-fields', 'tickets-field-priority')) {
        $code .= '<div id="priority" class="sb-input sb-input-select"><span>' . sb_('Priority') . '</span><div class="sb-select"><p data-value="" data-required="true">' . sb_('Select a value') . '</p><ul><li data-value="' . sb_('General issue') . '">' . sb_('General issue') . '</li><li data-value="' . sb_('Medium') . '">' . sb_('Medium') . '</li><li data-value="' . sb_('Critical') . '">' . sb_('Critical') . '</li></ul></div></div>';
    }
    if ($custom_fields != false && is_array($custom_fields)) {
        for ($i = 0; $i < count($custom_fields); $i++) {
            $value = $custom_fields[$i];
            if ($value['tickets-extra-field-name'] != '') {
                $code .= '<div id="' . sb_string_slug($value['tickets-extra-field-name']) . '" class="sb-input sb-input-text"><span>' . sb_($value['tickets-extra-field-name']) . '</span><input type="text"></div>';
            }
        }
    }
    echo $code

        ?>
    </div>
</div>
<?php } ?>
<?php

/*
 * ----------------------------------------------------------
 * CSS
 * ----------------------------------------------------------
 *
 * Generate the CSS for the ticketswith values setted in the settings area
 *
 */

function sb_tickets_css() {
    $css = '';
    $color_1 = sb_get_setting('color-1');
    if ($color_1 != '') {
        $css .= '.sb-tickets .sb-panel-right .sb-input.sb-input-btn>div:hover, .sb-tickets .sb-panel-right .sb-input.sb-input-btn input:focus+div,.sb-tickets .sb-top .sb-btn:hover, .sb-tickets .sb-create-ticket:hover, .sb-tickets .sb-panel-right .sb-btn:hover { background-color: ' . $color_1 . '; border-color: ' . $color_1 . '; }';
        $css .= '.sb-tickets .sb-ticket-details>div .sb-icon,.sb-btn-collapse:hover,.sb-profile-menu:hover .sb-name,.sb-tickets .sb-conversation-attachments a i { color: ' . $color_1 . '; }';
        $css .= '.sb-user-conversations>li.sb-active{ border-left-color: ' . $color_1 . '; }';
        $css .= '.sb-search-btn>input:focus,[data-panel="new-ticket"] .sb-editor.sb-focus { border-color: ' . $color_1 . '; }';
        $css .= '.sb-btn-icon:hover { border-color: ' . $color_1 . '; color: ' . $color_1 . '; }';
    }
    if ($css != '') {
        echo '<style>' . $css . '</style>';
    }
}

?>