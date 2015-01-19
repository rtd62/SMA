<?php

// include required files from Facebook SDK
require_once('Facebook/HttpClients/FacebookHttpable.php');
require_once('Facebook/HttpClients/FacebookCurl.php');
require_once('Facebook/HttpClients/FacebookCurlHttpClient.php');
require_once('Facebook/HttpClients/FacebookHttpable.php');
require_once('Facebook/HttpClients/FacebookCurl.php');
require_once('Facebook/HttpClients/FacebookCurlHttpClient.php');
require_once('Facebook/Entities/AccessToken.php');
require_once('Facebook/Entities/SignedRequest.php');
require_once('Facebook/FacebookSession.php');
require_once('Facebook/FacebookRedirectLoginHelper.php');
require_once('Facebook/FacebookRequest.php');
require_once('Facebook/FacebookResponse.php');
require_once('Facebook/FacebookSDKException.php');
require_once('Facebook/FacebookRequestException.php');
require_once('Facebook/FacebookOtherException.php');
require_once('Facebook/FacebookAuthorizationException.php');
require_once('Facebook/GraphObject.php');
require_once('Facebook/GraphSessionInfo.php');
use Facebook\HttpClients\FacebookHttpable;
use Facebook\HttpClients\FacebookCurl;
use Facebook\HttpClients\FacebookCurlHttpClient;
use Facebook\Entities\AccessToken;
use Facebook\Entities\SignedRequest;
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookOtherException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphSessionInfo;

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
  $request = new FacebookRequest($session, 'GET', '/' . $_POST['public_page_id']);
  $response = $request->execute();
  $graphObject = $response->getGraphObject()->asArray();
  echo '<p>Category - ' . $graphObject['category'] . '</p><p>Description - ' . $graphObject['about'] . '</p><p>Number of likes - ' . $graphObject['likes'] . '</p><p>Link - <a href="' . $graphObject['link'] . '">' . $graphObject['link'] . '</a></p>';
}

?>
