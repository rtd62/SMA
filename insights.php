<?php
if ($_POST['pageID'] == 'not_selected') {
	exit();
}
require_once 'vendor/autoload.php';
// include required files from Facebook SDK
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;

include_once 'class.sma.database.php';

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
  $request = new FacebookRequest($session, 'GET', '/' . $_POST['pageID'] . '/insights');
  $response = $request->execute();
  $insights = $response ->getGraphObject()->asArray();
  //print_r($insights);
  $html = '<div id="insightsContainer">';

  $date = substr($insights['data']['0']->values['0']->end_time, 0, 10);

  $html .= '<p>' . $date . '</p>';
  $db_data = $date;

  $page_fans = $insights['data']['165']->values['0']->value;

  $html .= '<p>Page fans - ' . $page_fans . '</p><p>Fans sorted by location:<ul>';
  $db_data .= ',' . $page_fans . ',';

  $page_fans_city = $insights['data']['167']->values['0']->value;
  $i = 0;
  foreach ($page_fans_city as $key => $value) {
  	$html .= '<li>' . $key . ' - ' . $value . '</li>';
  	$db_data .= str_replace(',', '', $key) . ' - ' . $value . ',';
  	$i++;
  	if ($i == 5) {
  		break;
  	}
  }

  $page_fan_adds_unique = $insights['data']['0']->values['0']->value;

  $html .= '</ul></p><p>New fans - ' . $page_fan_adds_unique . '</p>';
  $db_data .= $page_fan_adds_unique . ',';

  $page_fan_removes_unique = $insights['data']['3']->values['0']->value;

  $html .= '<p>Unlikes - ' . $page_fan_removes_unique . '</p><p>Fans sorted by gender & age:<ul>';
  $db_data .= $page_fan_removes_unique . ',';

  $page_fans_gender_age = $insights['data']['171']->values['0']->value;
  $i = 0;
  foreach ($page_fans_gender_age as $key => $value) {
  	$html .= '<li>' . $key . ' - ' . $value . '</li>';
  	$db_data .= $key . ' - ' . $value . ',';
  	$i++;
  	if ($i == 5) {
  		break;
  	}
  }

  $page_views_unique = $insights['data']['201']->values['0']->value;

  $html .= '</ul></p><p>Page views - ' . $page_views_unique . '</p>';
  $db_data .= $page_views_unique . ',';

  $page_posts_impressions_viral_unique = $insights['data']['119']->values['0']->value;

  $html .= '<p>Trending posts impressions - ' . $page_posts_impressions_viral_unique . '</p><p>Consumption types:<ul>';
  $db_data .= $page_posts_impressions_viral_unique . ',';

  $page_consumptions_by_consumption_type_unique = $insights['data']['137']->values['0']->value;
  foreach ($page_consumptions_by_consumption_type_unique as $key => $value) {
  	$html .= '<li>' . $key . ' - ' . $value . '</li>';
  	$db_data .= $key . ' - ' . $value . ',';
  }

  $page_engaged_users = $insights['data']['189']->values['0']->value;

  $html .= '</ul></p><p>Engaged Users - ' . $page_engaged_users . '</p><p>Negative feedback types:<ul>';
  $db_data .= $page_engaged_users . ',';

  $page_negative_feedback_by_type_unique = $insights['data']['153']->values['0']->value;
  foreach ($page_negative_feedback_by_type_unique as $key => $value) {
  	$html .= '<li>' . $key . ' - ' . $value . '</li>';
  	$db_data .= $key . ' - ' . $value . ',';
  }

  $html .= '</ul></p><p>Positive feedback types:<ul>';

  $page_positive_feedback_by_type_unique = $insights['data']['159']->values['0']->value;
  foreach ($page_positive_feedback_by_type_unique as $key => $value) {
  	$html .= '<li>' . $key . ' - ' . $value . '</li>';
  	$db_data .= $key . ' - ' . $value . ',';
  }

  $db_data = substr($db_data, 0, -1);
  $sql = "SELECT id, date FROM page_insights WHERE page_id = '%s'";
  $db = new SMA_Database();
  $db->prepare($sql,array($_POST['pageID']));
  $compareDates = $db->execute();
  $compareSelect = '<div style="text-align:center;"><select id="compareDates" class="appSelect">';
  for ($i=0; $i < count($compareDates); $i++) { 
  	$compareSelect .= '<option value="' . $compareDates[$i]->id . '">' . $compareDates[$i]->date . '</option>';
  }
  $compareSelect .= '</select></div>';
  $html .= '</ul></p></div><input id="dbData" type="hidden" name="db_data" value="' . $db_data . '"><button id="saveInsightsDb" class="appBtn" type="button">Save to database</button><div id="dbDataResponse"></div>' . $compareSelect . '<br><button id="compareInsights" class="appBtn" type="button">Compare Insights</button><div id="compareInsightsContainer"></div>';
  echo $html;
}
?>
