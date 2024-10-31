<?php

class psmailer_widget extends WP_Widget {

	// Constructor 
	function psmailer_widget() {
		$widget_ops = array( 'classname' => 'psmailer_sidebar', 'description' => __('Psmailer newsletter widget', 'psmailer-form') );
		parent::__construct( 'psmailer-widget', __('Psmailer formulario Newsletter', 'psmailer-form'), $widget_ops );
	}


	// Set widget and title in dashboard
	function form( $instance ) {
		$instance = wp_parse_args( $instance, array(
			'title' => '',
			'id_list' => '',
			'text' => '',
			'api_code' => ''
		));
		$title = !empty( $instance['title'] ) ? $instance['title'] : __('Psmailer formulario newsletter', 'psmailer-form'); 
		$id_list = $instance['id_list'];
		$text = $instance['text'];
		$api_code = $instance['api_code'];
		?> 
		<p> 
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Titulo del widget', 'psmailer-form'); ?>:</label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" maxlength='50' value="<?php echo esc_attr( $title ); ?>">
 		</p> 
		<p>
		<label for="<?php echo $this->get_field_id('text'); ?>"><?php _e('Texto informativo', 'psmailer-form'); ?>:</label>
		<textarea class="widefat monospace" rows="6" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo esc_textarea( $text ); ?></textarea>
		</p>
		<p> 
		<label for="<?php echo $this->get_field_id( 'id_list' ); ?>"><?php _e('Id de la lista que recibe los emails', 'psmailer-form'); ?>:</label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'id_list' ); ?>" name="<?php echo $this->get_field_name( 'id_list' ); ?>" type="text" maxlength='200' value="<?php echo esc_attr( $id_list ); ?>">
 		</p>
        <p> 
		<label for="<?php echo $this->get_field_id( 'api_code' ); ?>"><?php _e('Código API psmailer', 'psmailer-form'); ?>:</label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'api_code' ); ?>" name="<?php echo $this->get_field_name( 'api_code' ); ?>" type="text" maxlength='200' value="<?php echo esc_attr( $api_code ); ?>">
 		</p> 
		<?php 
	}


	// Update widget 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		// Strip tags from title to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['id_list'] = strip_tags( $new_instance['id_list'] );
		$instance['text'] = strip_tags( $new_instance['text'] );
		$instance['api_code'] = strip_tags( $new_instance['api_code'] );

		return $instance;
	}


	// Display widget with signup form in frontend 
	function widget( $args, $instance ) {
		
		echo $args['before_widget']; 

		if ( !empty( $instance['title'] ) ) { 
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title']; 
		} 

		if ( !empty( $instance['text'] ) ) { 
			echo wpautop( esc_textarea($instance['text']) );
		}

		echo do_shortcode( '[signup api_code="'.$instance['api_code'].'" id_list="'.$instance['id_list'].'"]' );

		echo $args['after_widget']; 
	}

}
?>