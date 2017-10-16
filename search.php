<?php
if ($_POST['searchword'] == ''){
  exit();
}
require_once 'config.php';
use Facebook\FacebookSession;
use Facebook\FacebookRequest;

// see if we have a session
if (isset($session)) {
  // save the session
  $_SESSION['fb_token'] = $session->getToken();
  // create a session using saved token or the new one we generated at login
  $session = new FacebookSession($session->getToken()); 
  $request = new FacebookRequest($session, 'GET', '/search?q=' . $_POST['searchword'] . '&type=page&limit=5');
  $response = $request->execute();
  $graphObject = $response->getGraphObject()->asArray();
  for ($i=0; $i<count($graphObject['data']); $i++){
    echo '<input type="hidden" name="public_page_id" value="' . $graphObject['data'][$i]->id . '"><button class="publicPage appBtn" type="button">' . $graphObject['data'][$i]->name . '</button><div></div>';
  }
}

?>
