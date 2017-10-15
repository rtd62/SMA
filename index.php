<?php
require_once 'config.php';
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
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
    <input type="text" class="search" id="searchbox"> <button style="padding: 12px;">Search</button> <!--For handler onChange-->
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
<script type="text/javascript" src="js/script.js"></script>
</body>
</html>
