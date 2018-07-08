<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

$post_statuses = get_post_statuses();
$current_post_status = get_post_status(get_the_id());

//var_dump($post_statuses);

$post_id = get_the_id();


//echo '<pre>'.var_export($post_id, true).'</pre>';

?>

<?php if( current_user_can( 'manage_options' ) ) : ?>
    
<div class="admin-actions">

	<form class="post-status">
    
		<?php foreach( $post_statuses as $status_index => $status_name ) { ?>
        <label> <input <?php if($current_post_status==$status_index) echo 'checked'; ?>  name="post_status" type="radio" value="<?php echo $status_index; ?>"> <?php echo $status_name; ?></label>
		<?php } ?>
            
        <input type="hidden" value="<?php echo get_the_id(); ?>" name="post_id">
		<?php wp_nonce_field( 'nonce_qa_update_post_status' ); ?>
		<button type="button" class="admin_actions_submit"><?php echo __('Update', 'question-answer'); ?></button>
        
	</form>

</div>
<?php endif; ?>


