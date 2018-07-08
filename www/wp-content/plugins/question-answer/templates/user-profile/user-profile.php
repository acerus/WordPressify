<?php
/*
* @Author 		PickPlugins
* Copyright: 	2015 PickPlugins.com
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 

$author_id = isset($_GET['id']) ? sanitize_text_field($_GET['id']): '';

//var_dump($author_id);

do_action('qa_user_profile_before', $author_id);

?>
<div id="qa-user-profile" class="qa-user-profile">
<?php do_action('qa_user_profile', $author_id); ?>
</div>
<?php


do_action('qa_user_profile_after', $author_id);
