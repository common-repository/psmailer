<?php
/**
 * Plugin Name: PSmailer
 * Description: Formulario suscripción en la sidebar
 * Version: 3.4
 * Author: PSmailer
 * Author URI: https://www.psmailer.com
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: psmailer-form
 * Domain Path: translation
 */


if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('NO se puede acceder a esta pagina directamente'); }


// load plugin text domain
function psmailer_init() { 
	load_plugin_textdomain( 'psmailer-form', false, dirname( plugin_basename( __FILE__ ) ) . '/translation' );
}
add_action('plugins_loaded', 'psmailer_init');


// enqueues plugin scripts
function psmailer_scripts() {	
	if(!is_admin())	{
		wp_enqueue_style('psmailer_style', plugins_url('/css/psmailer_style.css',__FILE__));
	}
}
add_action('wp_enqueue_scripts', 'psmailer_scripts');


// the sidebar widget
function register_psmailer_widget() {
	register_widget( 'psmailer_widget' );
}
add_action( 'widgets_init', 'register_psmailer_widget' );


// function to get ip of user
function psmailer_get_the_ip() {
	if (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
		return $_SERVER["HTTP_X_FORWARDED_FOR"];
	}
	elseif (isset($_SERVER["HTTP_CLIENT_IP"])) {
		return $_SERVER["HTTP_CLIENT_IP"];
	}
	else {
		return $_SERVER["REMOTE_ADDR"];
	}
}


// include form and widget file
include 'psmailer_main.php';
include 'psmailer_widget.php';

?>