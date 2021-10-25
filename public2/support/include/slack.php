<?php

/*
 * ==========================================================
 * SLACK.PHP
 * ==========================================================
 *
 * Slack response listener. This file receive the Slack messages of the agents forwarded by board.support. This file requires the Slack App. 
 * © 2021 board.support. All rights reserved.
 *
 */

if (file_exists('../apps/slack/functions.php')) {
    require_once('functions.php');
    require_once('../apps/slack/functions.php');
    $response = json_decode(file_get_contents('php://input'), true);
    sb_slack_listener($response);
}

?>