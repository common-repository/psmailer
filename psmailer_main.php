<?php


// Start session for captcha validation
//if (!isset ($_SESSION)) session_start(); 
$_SESSION['psmailer-rand'] = isset($_SESSION['psmailer-rand']) ? $_SESSION['psmailer-rand'] : rand(100, 999);


// The shortcode
function psmailer_shortcode($psmailer_atts) {
	
	$psmailer_atts = shortcode_atts( array( 
		"api_code" => __('', 'api_code'),
		"id_list" => __('', 'api_code'),
		"email_admin" => get_bloginfo('admin_email'),
		"label_name" => __('Nombre', 'psmailer-form'),
		"label_email" => __('Email', 'psmailer-form'),
		"label_phone" => __('Telefono', 'psmailer-form'),
		"label_captcha" => __('Captcha: introduzca el número %s', 'psmailer-form'),
		"label_submit" => __('Enviar', 'psmailer-form'),
		"error_name" => __('Por favor, introduzca al menos 2 caracteres', 'psmailer-form'),
		"error_phone" => __('Por favor, introduzca al menos 2 caracteres', 'psmailer-form'),
		"error_captcha" => __('Por favor, introduzca un número válido', 'psmailer-form'),
		"error_email" => __('Por favor, introduzca un email válido', 'psmailer-form'),
		"message_error" => __('Por favor, rellene los campos requeridos', 'psmailer-form'),
		"message_success" => __('Gracias por registrarse en nuestra newsletter, en breve comenzará a recibirlas', 'psmailer-form')
	), $psmailer_atts);

	// Set some variables 
	$form_data = array(
		'form_name' => '',
		'email' => '',
		'form_phone' => '',
		'form_captcha' => '',
		'form_firstname' => '',
		'form_lastname' => ''
	);
	$error = false;
	$sent = false;
	$info = '';

	if (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['signup_send']) ) {
	
		// Get posted data and sanitize them
		$post_data = array(
			'form_name' => sanitize_text_field($_POST['form_name']),
			'email' => sanitize_email($_POST['email']),
			'form_phone' => sanitize_text_field($_POST['form_phone']),
			'form_captcha' => sanitize_text_field($_POST['form_captcha']),
			'form_firstname' => sanitize_text_field($_POST['form_firstname']),
			'form_lastname' => sanitize_text_field($_POST['form_lastname'])
		);

		// Validate name
		$value = $post_data['form_name'];
		if ( strlen($value)<2 ) {
			$error_class['form_name'] = true;
			$error = true;
			$result = $psmailer_atts['message_error'];
		}
		$form_data['form_name'] = $value;

		// Validate email
		$value = $post_data['email'];
		if ( empty($value) ) {
			$error_class['email'] = true;
			$error = true;
			$result = $psmailer_atts['message_error'];
		}
		$form_data['email'] = $value;

		// Validate first honeypot field
		$value = $post_data['form_firstname'];
		if ( strlen($value)>0 ) {
			$error = true;
		}
		$form_data['form_firstname'] = $value;

		// Validate second honeypot field
		$value = $post_data['form_lastname'];
		if ( strlen($value)>0 ) {
			$error = true;
		}
		$form_data['form_lastname'] = $value;

		// Sending message to admin
		if ($error == false) {
			$to = $psmailer_atts['email_admin'];
			$subject = sprintf( esc_attr__( 'New signup: %s', 'psmailer-form' ), get_bloginfo('name') );
			$message = $form_data['form_name'] . "\r\n\r\n" . $form_data['email'] . "\r\n\r\n" . $form_data['form_phone'] . "\r\n\r\n" . sprintf( esc_attr__( 'IP: %s', 'psmailer-form' ), psmailer_get_the_ip() ); 
			$headers = "Content-Type: text/plain; charset=UTF-8" . "\r\n";
			$headers .= "Content-Transfer-Encoding: 8bit" . "\r\n";
			$headers .= "From: ".$form_data['form_name']." <".$form_data['email'].">" . "\r\n";
			$headers .= "Reply-To: <".$form_data['email'].">" . "\r\n";
			wp_mail($to, $subject, $message, $headers);
			$result = $psmailer_atts['message_success'];
			$sent = true;
		}
				
	}


	// Display message above form in case of success or error 
	if (!empty($result)) {
		
		// Save to psmailer API
		$url = 'http://api.psmailer.info/public/api/list_add.php?id_user='.$psmailer_atts['api_code'].'&id_list='.$psmailer_atts['id_list'].'&email='.$form_data['email'];
		$response = wp_remote_get( $url );
		
		// Show msg
		$info = '<p class="psmailer_info">'.esc_attr($result).'</p>';
		//$info = '<p class="psmailer_info">'.esc_attr($url).'</p>';
		
	}
	

	// The contact form with error messages
	$email_form = '<form class="psmailer" id="psmailer" method="post" action="">
		
		<p><label for="psmailer_name">'.esc_attr($psmailer_atts['label_name']).': <span class="'.((isset($error_class['form_name'])) ? "error" : "hide").'" >'.esc_attr($psmailer_atts['error_name']).'</span></label></p>
		<p><input type="text" name="form_name" id="psmailer_name" class="'.((isset($error_class['form_name'])) ? "error" : "").'" maxlength="50" value="'.esc_attr($form_data['form_name']).'" /></p>
		
		<p><label for="psmailer_email">'.esc_attr($psmailer_atts['label_email']).': <span class="'.((isset($error_class['email'])) ? "error" : "hide").'" >'.esc_attr($psmailer_atts['error_email']).'</span></label></p>
		<p><input type="text" name="email" id="psmailer_email" class="'.((isset($error_class['email'])) ? "error" : "").'" maxlength="50" value="'.esc_attr($form_data['email']).'" /></p>
		
		<p><input type="text" name="form_firstname" id="psmailer_firstname" maxlength="50" value="'.esc_attr($form_data['form_firstname']).'" /></p>
		
		<p><input type="text" name="form_lastname" id="psmailer_lastname" maxlength="50" value="'.esc_attr($form_data['form_lastname']).'" /></p>
		
		<p><input type="submit" value="'.esc_attr($psmailer_atts['label_submit']).'" name="signup_send" class="psmailer_send" id="psmailer_send" style="margin-top:10px;" /></p>

	</form>';

	
	// Send message and unset captcha variabele or display form with error message
	if(isset($sent) && $sent == true) {
		unset($_SESSION['psmailer-rand']);
		return $info;
	} else {
		return $info . $email_form;
	}
	
} 

add_shortcode('signup', 'psmailer_shortcode');

?>