<?php
include_once 'class.sma.database.php';

if (isset($_POST['page_id']) && isset($_POST['db_data'])) {
	$sql = "INSERT INTO page_insights (page_id, date, page_fans, fans_location1, fans_location2, fans_location3, fans_location4, fans_location5, new_fans, unlikes, fans_gender_age1, fans_gender_age2, fans_gender_age3, fans_gender_age4, fans_gender_age5, page_views, post_impressions, consumption_type1, consumption_type2, consumption_type3, consumption_type4, engaged_users, negative_feedback_type1, negative_feedback_type2, negative_feedback_type3, negative_feedback_type4, negative_feedback_type5, positive_feedback_type1, positive_feedback_type2, positive_feedback_type3, positive_feedback_type4, positive_feedback_type5, positive_feedback_type6) VALUES ('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')";
	$db = new SMA_Database();
	$valuesStr = $_POST['page_id'] . ',' . $_POST['db_data'];
	$values = explode(',', $valuesStr);
	$db->prepare($sql,$values);
	$db_result = $db->execute();
	if (empty($db_result)) {
		echo '<span class="redResponse">There has been an error!</span>';
	} else {
		echo '<span class="greenResponse">Insights saved succesfully!</span>';
	}
}

if (isset($_POST['compare_entry'])) {
	$sql = "SELECT * FROM page_insights WHERE id = '%s'";
	$db = new SMA_Database();
	$db->prepare($sql,array($_POST['compare_entry']));
	$db_result = $db->execute();
	$html = '<div id="compareContainer"><p>' . $db_result[0]->date . '</p><p>Page fans - ' . $db_result[0]->page_fans . '</p><p>Fans sorted by location:<ul><li>' . $db_result[0]->fans_location1 . '</li><li>' . $db_result[0]->fans_location2 . '</li><li>' . $db_result[0]->fans_location3 . '</li><li>' . $db_result[0]->fans_location4 . '</li><li>' . $db_result[0]->fans_location5 . '</li></ul></p><p>New fans - ' . $db_result[0]->new_fans . '</p><p>Unlikes - ' . $db_result[0]->unlikes . '</p><p>Fans sorted by gender & age:<ul><li>' . $db_result[0]->fans_gender_age1 . '</li><li>' . $db_result[0]->fans_gender_age2 . '</li><li>' . $db_result[0]->fans_gender_age3 . '</li><li>' . $db_result[0]->fans_gender_age4 . '</li><li>' . $db_result[0]->fans_gender_age5 . '</li></ul></p><p>Page views - ' . $db_result[0]->page_views . '</p><p>Trending posts impressions - ' . $db_result[0]->post_impressions . '</p><p>Consumption types:<ul><li>' . $db_result[0]->consumption_type1 . '</li><li>' . $db_result[0]->consumption_type2 . '</li><li>' . $db_result[0]->consumption_type3 . '</li><li>' . $db_result[0]->consumption_type4 . '</li></ul></p><p>Engaged Users - ' . $db_result[0]->engaged_users . '</p><p>Negative feedback types:<ul><li>' . $db_result[0]->negative_feedback_type1 . '</li><li>' . $db_result[0]->negative_feedback_type2 . '</li><li>' . $db_result[0]->negative_feedback_type3 . '</li><li>' . $db_result[0]->negative_feedback_type4 . '</li><li>' . $db_result[0]->negative_feedback_type5 . '</li></ul></p><p>Positive feedback types:<ul><li>' . $db_result[0]->positive_feedback_type1 . '</li><li->' . $db_result[0]->positive_feedback_type2 . '</li><li>' . $db_result[0]->positive_feedback_type3 . '</li><li>' . $db_result[0]->positive_feedback_type4 . '</li><li>' . $db_result[0]->positive_feedback_type5 . '</li><li>' . $db_result[0]->positive_feedback_type6 . '</li></ul></p></div>';
	echo $html;	
}
?>