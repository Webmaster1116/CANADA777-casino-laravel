<?php

/*
 * ==========================================================
 * MESSENGER.PHP
 * ==========================================================
 *
 * Facebook Messenger response listener. This file receive the messages sent by the Facebook users to your Facebook pages forwarded by board.support. This file requires the Messenger App.
 * © 2021 board.support. All rights reserved.
 *
 */

if (file_exists('../apps/messenger/functions.php')) {
    require_once('functions.php');
    require_once('../apps/messenger/functions.php');
    $response = json_decode(file_get_contents('php://input'), true);
    sb_messenger_listener($response);
}

?>