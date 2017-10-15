<?php
if ($_POST['searchword'] == ''){
  exit();
}

require_once 'vendor/autoload.php';
// include required files from Facebook SDK
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;

// start session
session_start();

// init Social Media Analysis with app id and secret
FacebookSession::setDefaultApplication('1501451756809750','c6ea8a75ba70d8a2ab2ac72d0d5511fd');

// login helper with redirect_uri
$helper = new FacebookRedirectLoginHelper('http://localhost/sma/');

// see if a existing session exists
if (isset($_SESSION) && isset($_SESSION['fb_token'])) {
  // create new session from saved access_token
  $session = new FacebookSession($_SESSION['fb_token']);
  // validate the access_token to make sure it's still valid
  try {
    if (!$session->validate()) {
      $session = null;
    }
  } catch (Exception $e) {
    // catch any exceptions
    $session = null;
  }
}

if (!isset($session) || $session === null) {
  // no session exists
  try {
    $session = $helper->getSessionFromRedirect();
  } catch(FacebookRequestException $ex) {
    // when Facebook returns an error
    print_r($ex);
  } catch(Exception $ex) {
    // when validation fails or other local issues
    print_r($ex);
  }
}

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
