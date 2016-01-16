<?php 
/**
* These are  functions I created to use on one of my projects  
* 
* @package    imekrato3
* @subpackage imerkato3/includs 
* @author     Tekalegn Fisseha
* @version    imekato 3.0
* ...
*/
?>
<?php
//document root for server files 
function include_layout_template($template=""){
	include(SITE_ROOT.DS.'public'.DS.'layouts'.DS.$template);
}
//for server 
function convert_date_time($str) {
    list($date,$time) = explode(" ",$str);
    list($year,$month,$day) = explode('-', $date);
    list($hour,$minute,$second) = explode('.', $time);
    $timestamp = mktime($hour,$minute,$second,$month,$day,$year);
    return $timestamp;
}
//document root for local files 
function include_layout_template($template=""){
	include("C:\\xampp\\htdocs\\iweb\\Imerkato3\\public\\layouts\\".$template);
}
// for localhost 
function convert_date_time($str) {
	return true;
}
//confvert time to easily readable format 
function time_passed($timestamp){
    $timestamp      = (int) $timestamp;
    $current_time   = time();
    $diff           = $current_time - $timestamp;
    //intervals in seconds
    $intervals      = array (
        'year' => 31556926, 'month' => 2629744, 'week' => 604800, 'day' => 86400, 'hour' => 3600, 'minute'=> 60
    );
    //find the difference 
    if ($diff == 0){
        return 'Just now';
    }
    if ($diff < 60){
        return $diff == 1 ? $diff . ' second ago' : $diff . ' seconds ago';
    }
    if ($diff >= 60 && $diff < $intervals['hour']){
        $diff = floor($diff/$intervals['minute']);
        return $diff == 1 ? $diff . ' minute ago' : $diff . ' minutes ago';
    }
    if ($diff >= $intervals['hour'] && $diff < $intervals['day']){
        $diff = floor($diff/$intervals['hour']);
        return $diff == 1 ? $diff . ' hour ago' : $diff . ' hours ago';
    }
    if ($diff >= $intervals['day'] && $diff < $intervals['week']){
        $diff = floor($diff/$intervals['day']);
        return $diff == 1 ? $diff . ' day ago' : $diff . ' days ago';
    }
    if ($diff >= $intervals['week'] && $diff < $intervals['month']){
        $diff = floor($diff/$intervals['week']);
        return $diff == 1 ? $diff . ' week ago' : $diff . ' weeks ago';
    }
    if ($diff >= $intervals['month'] && $diff < $intervals['year']){
        $diff = floor($diff/$intervals['month']);
        return $diff == 1 ? $diff . ' month ago' : $diff . ' months ago';
    }
    if ($diff >= $intervals['year']){
        $diff = floor($diff/$intervals['year']);
        return $diff == 1 ? $diff . ' year ago' : $diff . ' years ago';
    }
}
//validate form inputs  
function validate_input($in){
	$pattern = '/^[A-Za-z0-9._-\s]{1,40}$/';
	if(preg_match($pattern, $in)){
		return true;
	}else{
		return false;
	}
}
//validate email inputs 
function validate_email($e){
	$pattern = '/^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,7}$/';
	if(preg_match($pattern, $e)){
		return true;
	}else{
		return false;
	}
}
//use on the page set_error_handler('tekalegn_error_handler', E_ALL);
function tekalegn_error_handler($number,$text,$the_file,$the_line){
	if(ob_get_length()) ob_clean();
	$error_message = 'Error: ' . $number . chr(10) .'<br />'.
					 'Message: ' . $text . chr(10) .'<br />'.
					 'File: ' . $the_file .chr(10) .'<br />'.
					 'Line: ' . $the_line;
	echo $error_message;
	exit;
}
//confirm query 
function confirm_query($result_set,$query_num=null){
	if(!$result_set){
		//echo "failed at $query_num ".$_SERVER['PHP_SELF'];
		redirect_to("prompt.php?prompt_message_id=24&query_error_at=$query_num");
	}
}
//generate random string for password rest 
function generateRandomString($length = 7) {
$characters = '0123456789abcdefghijklmnopqrstuvwxyzAB';
$randomString = '';
for ($i = 0; $i < $length; $i++) {
$randomString .= $characters[rand(0, strlen($characters) - 1)];
}
return $randomString;
}
//convert time and save to database
function convert_php_time_to_sql(){
	$time_stamp = time();
	$sql_format_time = date('Y-m-d H:i:s',$time_stamp);
	return $sql_format_time;
}
function strip_zeros_from_date($marked_string = ""){
	//first remove marked zeros
	$no_zeros = str_replace('*0', '', $marked_string);
	//then remove any remaining marks 
	$cleaned_string = str_replace('*', '', $no_zeros);
	return $cleaned_string;
}
//redirect to another page 
function redirect_to($location = NULL){
	if($location != NULL){
		header("Location: {$location}");
		exit();
	}
}
//Shows  info. on logged in users' files header only 
function admin_header_displays(){
	if(isset($_SESSION['user_id']) OR isset($_SESSION['user_email'])){
    	$user_id = $_SESSION['user_id'];
    	$memebr_email = $_SESSION['user_email'];
	    	//echo $memebr_email;
		    $query = "SELECT * FROM trader_message_table  WHERE user_email = '{$memebr_email}' AND seen = 1";
		    $trader_unread_message_Object = new TraderMessage(); 
		    $trader_unread_messages = $trader_unread_message_Object->find_by_sql($query);
		    $trader_message_count = sizeof($seller_unread_messages);
		    $query = "SELECT * FROM purchaser_message_table  WHERE user_email = '{$memebr_email}' AND seen = 1";
		    $purchaser_unread_messageObject = new PurchaserMessage(); 
		    $purchaser_unread_messages = $purchaser_unread_messageObject->find_by_sql($query);
		    $purchaser_message_count = sizeof($purchaser_unread_messages);
		    $total_new_messages = $trader_message_count + $purchaser_message_count;
		if($total_new_messages > 0){
			echo '<a href="../admin/message_list.php">
			<div id="circle">
			<div id="num">'.$total_new_messages.'</div>
			</div>
			</a>';
		}
        echo '<a href="../admin/message_list.php"><span style="color:white">Messages |</span></a>';
        echo '<a href="../admin/index.php"><span style="color:white">my account  |</span></a> ';
        echo '<a href="../admin/logout.php"><span style="color:white">Logout</span></a> ';
       
    }
    
}
//query db to fined business types 
function find_business_types_for_index_page(){
	echo '<br />';
	$query = "SELECT * FROM type_table WHERE category_id = 4 ORDER BY name ASC";
	$business_types = Type::find_by_sql($query);
	foreach ($business_types as $key => $value) {
    	echo '<li>'.'<a href="index_countries.php?cn='.urlencode('Business').'&tpn='.urldecode($value->name).'">'.$value->name.'</a>'.'</li>';         
   	}   
}
//query db to find sale types 
function find_sale_types_for_index_page(){
	echo '<br />';
	$query = "SELECT * FROM type_table WHERE category_id = 3 ORDER BY name ASC";
	$sale_types = Type::find_by_sql($query);
	foreach ($sale_types as $key => $value) {
		echo '<li>'.'<a href="index_countries.php?cn='.urlencode('Sale').'&tpn='.urldecode($value->name).'">'.$value->name.'</a>'.'</li>';         
	}
}
//query db to fined housing 
function find_housing_types_for_index_page(){
	echo '<br />';
	$query = "SELECT * FROM type_table WHERE category_id = 1 ORDER BY name ASC";
	$housing_types = Type::find_by_sql($query);
	foreach ($housing_types as $key => $value) {
		echo '<li>'.'<a href="index_countries.php?cn='.urlencode('Housing').'&tpn='.urldecode($value->name).'">'.$value->name.'</a>'.'</li>';         
	} 
}
//query db to find jobs 
function find_job_types_for_index_page(){
	echo '<br />';
	$query = "SELECT * FROM type_table WHERE category_id = 2 ORDER BY name ASC";
	$job_types = Type::find_by_sql($query);
	foreach ($job_types as $key => $value) {
		echo '<li>'.'<a href="index_countries.php?cn='.urlencode('Job').'&tpn='.urldecode($value->name).'">'.$value->name.'</a>'.'</li>';         
	} 
}
//$get_param = allowed_get_params(['username','password']);
//echo error message to users 
function output_error_message($message=""){
	if(!empty($message)){
		return "<p class = \"message\">{$message}</p>";
	}else{
		return "";
	}
}
//query db to find categories  
function search_category_options($connect){
   global $dbc;
	 $query = "SELECT * FROM category_table";
	// $result = mysqli_query($connect,$query);
	 $result=$dbc->query($query);
	// print_r($result);die();
     while ($row = mysqli_fetch_assoc($result)) {
     	echo '<option value="'.$row['category_id'].'">'.$row['category_name'].'</option>';
     }
}
//search db to find countries 
function search_country_options($connect){
global $dbc;
	 $query = "SELECT * FROM country_table";
	// $result = mysqli_query($connect, $query);
	 $result=$dbc->query($query);
	 
	 //print_r($result);die();
     while ($row = mysqli_fetch_assoc($result)) {
     echo $result->country_name;
     // echo '<option value="'.$row['country_id'].'">'.$row['country_name'].'</option>';
     }
}
function categories_list(){
	global $categoryObject;
	$query = "SELECT * FROM category_table";
	$result = $categoryObject->find_by_sql($query);
	foreach ($result as $value) {
	echo'<option value="'.$value->name.'">'.$value->name.'</option>';
	}
}
function countries_list(){
	global $countryObject;
	$query = "SELECT * FROM country_table";
	$result = $countryObject->find_by_sql($query);
	foreach ($result as $value) {
	echo'<option value="'.$value->name.'">'.$value->name.'</option>';
	}
}
//login form 
function login_form(){
	$login_form = "";
	$login_form .= '<form method="post" action='.SITE_ROOT.'/public/admin/login.php>';
	$login_form .= '<label>Username or E-mail:</label>'; 
	$login_form .= '<input type="text" name="user_email">';
	$login_form .= '<label>Password:</label>';
	$login_form .= '<input type="password" name="password">';
	$login_form .= '<input type="submit" name="submit" maxlength="15" style="margin-left:3px" value="Login"/>';  
	$login_form .= ' <a href="'.SITE_ROOT.'/public/admin/registration.php"><span style="color:#fff">Register</span></a>';
	$login_form .= '</form>';
	echo $login_form;
}
//search form 
function search_form(){
	if(isset($_SESSION['user_email']) || isset($_SESSION['user_id'])){
		$send_search_form_to = SITE_ROOT."/public/admin/search_list_login.php";
	}else{
		$send_search_form_to = SITE_ROOT."/public/search_list_logout.php";
	}
	(isset($_GET['search_term'])) ? $default_text = htmlentities($_GET['search_term']) : $default_text = "";
 			echo'<form name="search_form" action="'.$send_search_form_to.'" method="get">
					<input type="text" name="search_term" id="search_term"  size="45" value="'.$default_text.'"> &nbsp;
					<select name="cn" style="display:none" id="search_category" style="width:110px;" width="15" class="searchBox" onchange="list_select_options(this.id,\'search_type\')" >
					<option value="">Categories</option>';
					categories_list();
			echo	'</select>
					<select name="tpn" style="display:none" id="search_type" style="width:85px;" class="searchBox">
					<option value="">Types</option>
					</select>
					<select name="cyn" style="display:none" id="search_country" style="width:100px;"  class="searchBox" onchange="list_select_options(this.id,\'search_state\')">
					<option value="">Countries</option>';
					countries_list();
			echo	'</select>
					<select name="sn" style="display:none" id="search_state" style="width:100px;" class="searchBox" onchange="list_select_options(this.id,\'search_city\')">
					<option value="">States</option>
					</select>
					<select name="cin" style="display:none" id="search_city" style="width:90px;" class="searchBox">
					<option value="">Cities</option>
					</select>
					<input type="submit" name="search_submit_button" maxlength="15" style="margin-left:3px" value="Search"/>
				</form>';
}
//promt message to display 
function create_prompt_message($x,$code=null,$query_error_at=null){
	if(is_numeric($x)){
	switch($x) {
		case 0:
			$message = '<p>One more step left.'.'<br />'.'Now go to your email account and click the link in your email to finalize the registration process</p>';
			break;
		case 1:
			$message = '<p><font color="green">Success!</font>'.'<br />'.'Thank you for activating your account it is ready to use!</p>';
			break;
		case 2:
			$message = '<p>Your account is already activated. Please login to use it.</p>';
			break;
		case 3:
			$message = '<p><font color="green">Your account has been updated successfully</font>.</p>';
			break;	
		case 4:
			$message = '<p><font color="red">There is a problem updating your account please try later</font>.</p>';
			break;
		case 5:
			$message = '<p><font color="red">Sorry, your password already set </font>.</p>';
			break;
		case 6:
			$message = '<p><font color="green">You have changed your password successfully </font>.</p>';
			break;
		case 7: 
			$message = '<p><font color="green">Email sent!</font>'.'<br />'.'We have sent you an email, please click the link in the email to get your temporary password</p>';
			break;
		case 8:
			$message = '<p>Your password has been changed. Here is your temporary password. Please change the temporary password as soon as you logged in to your account.</p>'.'<b>'.$code.'</b>';
			break;
		case 10:
			$message = '<p>Thank you for contacting Imerkato! Imerkato team will respond as fast as possible if it is needed.</p>';
			break;
		case 11:
			$message = '<p>Your product has been saved</p>';
			break;
		case 12:
			$message = '<p>You sent the message</p>';
		break;
		case 13:
			$message = '<p>Your complaint has been submitted.</p>';
		break;
		case 14:
			$message = '<p><font color="green">Message sent</font>'.'<br />'.'Your message has been sent, please wait until the poster respond.</p>';
		break;
		case 15:
			$message = '<p>Oops your account could not be activated, please contact the admin.</p>';
		break;
		case 16:
			$message = '<p>Error, please contact the system admin!</p>';
		break;
		case 17:
			$message = '<p>Oops your password could not be changed, please contact the admin.</p>';
		break;
		case 18:
			$message = '<p><font color="green">Posted successfully</font>'.'<br />'.'Your post has been posted successfully. You can edit,view, or delete the post from your user page.</p>';
		break;
	    case 20:
			$message = '<p>Your post has been deleted successfully.</p>';
		break;
		case 21:
			$message = '<p>Your post has been updated successfully.</p>';
		break;
		case 22:
			$message = '<p>Your search term is too short.</p>';
		break;
		case 23:
			$message = '<p>You did not select your search category and type.</p>';
		break;
		case 24:
			$message = '<p>DBQF at '.$query_error_at.'</p>';
		break;
		case 25:
			$message = '<p>You are not authorized to see this message or page.</p>';
		break;
		case 26:
			$message = '<p>Thank you for posting your feedback.</p>';
		break;
	}
	echo $message;
	}
}
function no_post_yet(){
	echo '<tr>
		   <td><em>You haven\'t posted yet...</em></td>
		   <td></td>
		   <td></td>
		   <td></td>
		   <td></td>                
        </tr>';
}
//acquire website visitors ip address 
function get_client_ip() {
     $ipaddress = '';
     if (getenv('HTTP_CLIENT_IP'))
         $ip_address = getenv('HTTP_CLIENT_IP');
     else if(getenv('HTTP_X_FORWARDED_FOR'))
         $ip_address = getenv('HTTP_X_FORWARDED_FOR');
     else if(getenv('HTTP_X_FORWARDED'))
         $ip_address = getenv('HTTP_X_FORWARDED');
     else if(getenv('HTTP_FORWARDED_FOR'))
         $ip_address = getenv('HTTP_FORWARDED_FOR');
     else if(getenv('HTTP_FORWARDED'))
        $ip_address = getenv('HTTP_FORWARDED');
     else if(getenv('REMOTE_ADDR'))
         $ip_address = getenv('REMOTE_ADDR');
     else
         $ip_address = 'UNKNOWN';
     return $ip_address; 
}
?>
Status API Training Shop Blog About Pricing
© 2016 GitHub, Inc. Terms Privacy Security Contact Help