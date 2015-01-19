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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Social Media Analysis</title>
  <link rel="stylesheet" type="text/css" href="css/main.css">
</head>
<body>
<?php
// see if we have a session
if (isset($session)) {
  // save the session
  $_SESSION['fb_token'] = $session->getToken();
  // create a session using saved token or the new one we generated at login
  $session = new FacebookSession($session->getToken());
  $request = new FacebookRequest($session, 'GET', '/me');
  $response = $request->execute();
  $graphObject = $response->getGraphObject()->asArray(); 
  ?>
  <div id="loginHeader" class="header">
    <a href=""><img width="64px" src="img/logo.png"></a>
    <h1 id="headerTitle">Social Media Analysis</h1>
    <span id="userName" class="userInfos"><?php echo $graphObject['first_name'] . ' ' . $graphObject['last_name']; ?></span>
    <span id="userBirthday" class="userInfos"><?php echo $graphObject['birthday']; ?></span>
    <span id="userEmail" class="userInfos"><?php echo $graphObject['email']; ?></span>
  <?php 
  $request = new FacebookRequest($session, 'GET', '/me/picture?redirect=false&height=85&type=normal&width=85');
  $response = $request->execute();
  $graphObject = $response->getGraphObject()->asArray();
  ?>
    <img id="profilePic" src="<?php echo $graphObject['url']; ?>">
  </div>
  <?php
  $request = new FacebookRequest($session, 'GET', '/1501451756809750');
  $response = $request->execute();
  $graphObject = $response->getGraphObject()->asArray();
  ?>
  <div id="appInfos" class="section">
    <p>You are now using Social Media Analysis</p>
    <p>Its overall rank based on the subscriber counter is <?php echo $graphObject['daily_active_users_rank']; ?></p>
    <p>The daily active users are <?php echo $graphObject['daily_active_users']; ?></p>
    <p>The weekly active users are <?php echo $graphObject['weekly_active_users']; ?></p>
    <p>The monthly active users are <?php echo $graphObject['monthly_active_users']; ?></p>
  </div>
  <?php 
  $request = new FacebookRequest($session, 'GET', '/me/accounts');
  $response = $request->execute();
  $graphObject = $response->getGraphObject()->asArray();
  ?>
  <div class="section">
    <p>Search for a Facebook page:</p>
    <input type="text" class="search" id="searchbox">
    <div id="display"></div>
  </div>
  <div id="adminPagesInfo" class="section">
  <p>You are administering the following pages:</p>
  <select id="viewInsights" class="appSelect">
    <option value="not_selected">Choose your page</option>
    <?php
    for ($i=0; $i<count($graphObject['data']); $i++) {
      ?>
      <option value="<?php echo $graphObject['data'][$i]->id; ?>"><?php echo $graphObject['data'][$i]->name; ?></option>
      <?php 
    }
    ?>
  </select>
  </div>
  <div class="section">
    <div id="insightsResponse"></div>
  </div>
  <a href="<?php echo $helper->getLogoutUrl($session, 'http://localhost/sma/logout.php'); ?>"><img id="logoutBtn" width="170px" src="img/logout_button.png"></a>
<?php
} else {
?>
<div id="loginHeader">
  <a href=""><img width="64px" src="img/logo.png"></a>
  <h1 id="headerTitle">Social Media Analysis</h1>
</div>
<div id="loginBtn"><a href="<?php  echo $helper->getLoginUrl(array('email','user_friends','user_likes','user_photos')); ?>"><img width="200px" src="img/login_button.png"></a></div>
<?php
}
?>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<script type="text/javascript">
$(document).ready(function(){
  $('#viewInsights').change(function(){
    $('#insightsResponse').html('<img src="img/loading.gif">');
    $.ajax({
      type: 'POST',
      url: 'insights.php',
      data: {
        pageID : $('#viewInsights').val()
      },
      success: function(response) {
        $('#insightsResponse').html(response);
        $('#saveInsightsDb').on('click',function(){
          $('#dbDataResponse').html('<img src="img/loading.gif">');
          $.ajax({
            type: 'POST',
            url: 'db_handler.php',
            data: {
              db_data : $('#dbData').val(),
              page_id : $('#viewInsights').val()
            },
            success: function(response) {
              $('#dbDataResponse').html(response);
            }
          });
        });
        $('#compareInsights').on('click',function(){
          $('#compareInsightsContainer').html('<img src="img/loading.gif">');
          $.ajax({
            type: 'POST',
            url: 'db_handler.php',
            data: {
              compare_entry : $('#compareDates').val()
            },
            success: function(response) {
             var compareFirstTop = $('#insightsResponse').position().top;
             $('#compareInsightsContainer').css('top',compareFirstTop);
             $('#compareInsightsContainer').html(response);
            }
          })
        })
      }
    });
    return false;
  });
  $('#searchbox').keyup(function(){
    var searchbox = $(this).val();
    var dataString = 'searchword=' + searchbox;
    if(!searchbox == ''){
      $.ajax({
        type: 'POST',
        url: 'search.php',
        data: dataString,
        success: function(response){
          $('#display').html(response);
          $('.publicPage').on('click',function(){
            var thisBtn = $(this);
            thisBtn.next().html('<img src="img/loading.gif">');
            $.ajax({
              type: 'POST',
              url: 'public_pages.php',
              data: {
                public_page_id : thisBtn.prev().val()
              },
              success: function(response){
                thisBtn.next().html(response);
              }
            })
            return false;
          })
        }
      });
    }
    return false;
  });
})
</script>
</body>
</html>
