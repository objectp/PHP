<?php 
/**
* The website I have built has a system in which members of the website rate each other after they finished their transactions.
* This code handls the process. 
* @package    imekrato3
* @subpackage imerkato3/includs 
* @author     Tekalegn Fisseha
* @version    imekato 3.0
* ...
*/
?>

<?php require_once('../../includes/admin_initialize.php');//includes initializer files?>
<?php if(!$sessionObject->is_logged_in()){redirect_to("login.php");}//check if the member?>

<?php
//create database object
//this is a class it has to be included from separate file
require_once('database.php');
class Feedback extends DatabaseObject{
	protected static $table_name = "feedback_table";
	protected static $db_fields = array('id','poster_id','poster_username','responder_id','responder_username','post_id','post_title',
		'posetive','neutral','negative','responder_comment','posted_date','feedback_by');
	public $id;
	public $poster_id;
	public $poster_username;
	public $responder_id;
	public $responder_username;
	public $post_id;
	public $post_title;
	public $posetive;
	public $neutral;
	public $negative;
	public $responder_comment;
	public $posted_date;
	public $feedback_by;
	
}	
$feedbackObject = new Feedback();
?>

<?php
//seller table
if(isset($_GET['last_inserted_purchaser_id'])){
	$last_inserted_purchaser_id = $_SESSION['last_inserted_purchaser_id'] = urldecode($_GET['last_inserted_purchaser_id']);
}
//buyer table 
if(isset($_GET['purchaser_message_id'])){
	$purchaser_message_id = $_SESSION['purchaser_message_id'] = urldecode($_GET['purchaser_message_id']);
}
if(isset($_POST['submit'])){
	if(isset($_SESSION['last_inserted_purchaser_id']))
		$last_inserted_purchaser_id = $_SESSION['last_inserted_purchaser_id'];
	if(isset($_SESSION['purchaser_message_id']))
		$purchaser_message_id = $_SESSION['purchaser_message_id'];
	if(isset($last_inserted_purchaser_id)){
	    //------find post detail and poster information from sellers message table--------
  		$query = "SELECT * FROM trader_message_table WHERE last_inserted_purchaser_id = {$last_inserted_purchaser_id} LIMIT 1";
	    $found_message_trader = $tradermessageObject->find_by_sql($query);
	    foreach ($found_message_trader as $a_message) {
	    	$trader_message_id = $a_message->id;//use this id to insert into deleted message 
	   		$trader_id = $a_message->member_id;
			$trader_username = $a_message->member_username;
			$purchaser_id = $a_message->purchaser_id;
			$purchaser_username = $a_message->purchaser_username;
			$post_id = $a_message->post_id;
			$post_title = $a_message->post_title;
			(isset($_POST['comment_body'])) ? $comment_body = $dbc->escape_value($_POST['comment_body']):$comment_body = "";
			(isset($_POST['feedback_type'])) ? $feedback_type = $_POST['feedback_type'] : $feedback_type = null;
			if($feedback_type != null){
				switch ($feedback_type) {
					case 'Posetive':
						$posetive = 1;$neutral = 0; $negative = 0;
						break;
					case 'Neutral':
						$posetive = 0;$neutral = 1;$negative = 0;
						break;
					case 'Negative':
						$posetive = 0; $neutral = 0; $negative = 1;
						break;
				}
			} 
			$feedback_by = "seller"; 
			$posted_date = convert_php_time_to_sql();
	    }
	}
	if(isset($purchaser_message_id)){ 
	   	//------find post detail and buyer information from purchaser_table --------
		$query = "SELECT * FROM purchaser_message_table WHERE id = {$purchaser_message_id} LIMIT 1";
		$found_message_buyer = $buyermessageObject->find_by_sql($query);
	   	foreach ($found_message_buyer as $a_message) {
	   		$purchaser_message_id = $a_message->id;//use this id to insert into deleted message
	   		$purchaser_id = $a_message->member_id;
	   		$purchaser_username = $a_message->member_username;
	   		$trader_id = $a_message->trader_id;
	   		$trader_username = $a_message->trader_username;
	   		$post_id = $a_message->post_id;
	   		$post_title = $a_message->post_title;
   			(isset($_POST['comment_body'])) ? $comment_body = $dbc->escape_value($_POST['comment_body']):$comment_body = "";
   			(isset($_POST['feedback_type'])) ? $feedback_type = $_POST['feedback_type'] : $feedback_type = null;
			if($feedback_type != null){
				switch ($feedback_type) {
					case 'Posetive':
						$posetive = 1;$neutral = 0; $negative = 0;
						break;
					case 'Neutral':
						$posetive = 0;$neutral = 1;$negative = 0;
						break;
					case 'Negative':
						$posetive = 0; $neutral = 0; $negative = 1;
						break;
				}
			} 
	   		$feedback_by = "buyer"; 
	   		$posted_date = convert_php_time_to_sql();
	    }
    }
	
	$feedbackObject->poster_id = $trader_id;
	$feedbackObject->poster_username = $trader_username;
	$feedbackObject->responder_id = (isset($last_inserted_purchaser_id))? $trader_id : $purchaser_id;
	$feedbackObject->responder_username = (isset($last_inserted_purchaser_id))? $trader_username : $purchaser_username;
	$feedbackObject->post_id = $post_id;
	$feedbackObject->post_title = $post_title;
	$feedbackObject->posetive = $posetive;
	$feedbackObject->neutral = $neutral;
	$feedbackObject->negative = $negative;
	$feedbackObject->responder_comment = $comment_body;
	$feedbackObject->posted_date = $posted_date;
	$feedbackObject->feedback_by = $feedback_by;
	if($feedbackObject->save()){ //save the collected data into the database
		if(isset($last_inserted_purchaser_id)){
			$query = "INSERT INTO deleted_trader_message_table SELECT * FROM trader_message_table WHERE id={$trader_message_id}";
			$result = mysqli_query($dbc->open_connection(), $query);
  			if($result){
  				$query = "DELETE FROM trader_message_table WHERE id = {$trader_message_id}";
  				$result = mysqli_query($dbc->open_connection(), $query);
  				if($result){
  					redirect_to(SITE_ROOT."/public/prompt_message.php?prompt_message_id=26");
  				}else{
  					echo "cannot delete from seller message table";
  				}
			}else{
				echo "not inserted into deleted seller message table";
			}
		}
		if(isset($purchaser_message_id)){
			$query = "INSERT INTO deleted_trader_message_table SELECT * FROM trader_message_table WHERE id={$purchaser_message_id}";
			$result = mysqli_query($dbc->open_connection(), $query);
			if($result){
				$query = "DELETE FROM purchaser_message_table WHERE id = {$purchaser_message_id}";
				$result = mysqli_query($dbc->open_connection(), $query);
				if($result){
					redirect_to(SITE_ROOT."/public/prompt_message.php?prompt_message_id=26");
				}else{
					echo "cannot delete from buyer message table";
				}
				
			}else{
				echo "not inserted into deleted buyer message table";
			}
			
		} 
		
	}	
}
?>

<?php $page_title = 'Leave Feedbacks';?>
<?php include_once('../layouts/admin_header.php');?>
<?php include_layout_template('admin_content_left.php');?>

<h2>Leave Feedback</h2>

<form name="comment_form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']);?>" method="post">
	What kind of feedback you want ot give for this member? Please make sure to leave the correct feedback because other members can take a 
	note about this member before they start doing business with him/her in the future.<br />
	<input type="radio" name="feedback_type" value="Posetive">Posetive<br>
	<input type="radio" name="feedback_type" value="Neutral">Neutral<br>
	<input type="radio" name="feedback_type" value="Negative">Negative<br>
    Please comment why you give this feedback:<textarea type="text" name="comment_body" id="comment_body"  rows="4" cols="48"></textarea>
    <input type="submit" name ="submit" value="Post Feedback">
</form>
<?php include_layout_template('content_right.php');?>
<?php include_layout_template('admin_footer.php');?> 