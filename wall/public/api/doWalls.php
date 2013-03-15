<?php
/* This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this file,
 * You can obtain one at http://mozilla.org/MPL/2.0/. */

require_once('../../lib/parapara.inc');
require_once('api.inc');
require_once('walls.inc');

header('Content-Type: text/plain; charset=UTF-8');

// Check we are logged in
session_name(WALLMAKER_SESSION_NAME);
session_start();
if (!isset($_SESSION['email'])) {
  bailWithError('logged-out');
}

// Prepare common parameters
$wallId = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : null;

// Parse input
$handle = fopen('php://input','r');
$jsonString = fgets($handle);
$json = json_decode($jsonString,true);
fclose($handle);

switch ($_SERVER['REQUEST_METHOD']) {
  case 'POST':
    $params = array('ownerEmail' => $_SESSION['email']);
    $params['design'] = @$json['design'];
    $params['title']  = @$json['title'];

    // XXX Move the following to Walls::create
    // Run the query
    $wallId = createWall($params);

    // Prepare result
    $result = array('wallId' => $wallId);
    // start session
    $currentdatetime = gmdate("Y-m-d H:i:s");
    startNewSession($wallId, null, $currentdatetime);
    break;

  case 'GET':
    if ($wallId === null) {
      // XXX Return the list of walls here
      bailWithError('bad-request');
    }
    $result = getWallDetails($wallId, $_SESSION['email']);
    break;

  default:
    bailWithError('bad-request');
}

// Return the result
print json_encode($result);
?>