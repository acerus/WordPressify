<?php
/*
* @Author 		pickplugins
* Copyright: 	2015 pickplugins
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


function qa_registration_function() {
	
    if (isset($_POST['submit'])) {
        qa_registration_validation($_POST['username'],$_POST['password'],$_POST['email']);
		
        global $username, $password, $email;
        $username	= 	sanitize_user($_POST['username']);
        $password 	= 	esc_attr($_POST['password']);
        $email 		= 	sanitize_email($_POST['email']);

        qa_complete_registration(
        $username,
        $password,
        $email

		);
    }
else{
	global $username, $password, $email;
	}

    qa_registration_form(
    	$username,
        $password,
        $email
	);
}

function qa_registration_form( $username, $password, $email) {
	
	
	?>
	
 
    <form action="<?php echo $_SERVER['REQUEST_URI']; ?> " method="post">
	<p>
	<label for="username"><?php echo __('Username', 'question-answer'); ?><strong>*</strong><br>
	<input type="text" name="username" value="<?php echo (isset($_POST['username']) ? sanitize_user($username) : '' );  ?>">
	</label>
	</p>
	
	<p>
	<label for="password"><?php echo __('Password', 'question-answer'); ?><strong>*</strong><br>
	<input type="password" name="password" value="<?php //echo (isset($_POST['password']) ? $password : '' ); ?>">
	</label>
	</p>
	
	<p>
	<label for="email"><?php echo __('Email', 'question-answer') ?><strong>*</strong><br>
	<input type="text" name="email" value="<?php echo (isset($_POST['email']) ? sanitize_email($email) : '' ); ?>">
	</label>
	</p>	
	
	<p>
	<?php wp_nonce_field( 'qa_register' ); ?>
	<input type="submit" name="submit" value="<?php echo __('Register', 'question-answer'); ?>"/>
	</p>
	
	</form>
	
    
    <?php
}

function qa_registration_validation( $username, $password, $email )  {
	
    global $reg_errors;
    $reg_errors = new WP_Error;

	$nonce = $_POST['_wpnonce'];

	if(!wp_verify_nonce( $nonce, 'qa_register' )){
		
		$reg_errors->add('nonce', __('Nonce error', 'question-answer'));
		}


    if ( empty( $username ) || empty( $password ) || empty( $email ) ) {
        $reg_errors->add('field', __('Required form field is missing', 'question-answer'));
    }

    if ( strlen( $username ) < 4 ) {
        $reg_errors->add('username_length', __('Username too short. At least 4 characters is required', 'question-answer'));
    }

    if ( username_exists( $username ) )
        $reg_errors->add('user_name',__( 'Sorry, that username already exists!', 'question-answer'));

    if ( !validate_username( $username ) ) {
        $reg_errors->add('username_invalid', __('Sorry, the username you entered is not valid', 'question-answer'));
    }

    if ( strlen( $password ) < 5 ) {
        $reg_errors->add('password', __('Password length must be greater than 5', 'question-answer'));
    }

    if ( !is_email( $email ) ) {
        $reg_errors->add('email_invalid', __('Email is not valid', 'question-answer'));
    }

    if ( email_exists( $email ) ) {
        $reg_errors->add('email', __('Email Already in use', 'question-answer'));
    }
    


    if ( is_wp_error( $reg_errors ) ) {

        foreach ( $reg_errors->get_error_messages() as $error ) {
            echo '<div>';
            echo '<strong>'.__('ERROR', 'question-answer').'</strong>: '.$error;
            echo '</div>';
        }
    }
}

function qa_complete_registration() {
    global $reg_errors, $username, $password, $email;
    if ( count($reg_errors->get_error_messages()) < 1 ) {
        $userdata = array(
        'user_login'	=> 	$username,
        'user_email' 	=> 	$email,
        'user_pass' 	=> 	$password,

		);
        $user = wp_insert_user( $userdata );
        echo __('Registration complete.', 'question-answer');
	}
}
