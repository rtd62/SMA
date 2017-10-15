<?php
require_once 'config.php';

use Facebook\FacebookSession;
use Facebook\FacebookRequest;

// see if we have a session
if (isset($session)) {
  // save the session
  $_SESSION['fb_token'] = $session->getToken();
  // create a session using saved token or the new one we generated at login
  $session = new FacebookSession($session->getToken());
  $request = new FacebookRequest($session, 'GET', '/' . $_POST['public_page_id']);
  $response = $request->execute();
  $graphObject = $response->getGraphObject()->asArray();
  echo '<p>Category - ' . $graphObject['category'] . '</p><p>Description - ' . $graphObject['about'] . '</p><p>Number of likes - ' . $graphObject['likes'] . '</p><p>Link - <a href="' . $graphObject['link'] . '">' . $graphObject['link'] . '</a></p>';
}

?>
