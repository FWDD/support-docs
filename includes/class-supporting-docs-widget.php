<?php

/**
 * Supporting_Docs_Widget widget class
 * taken from wp-includes/default-widgets.php
 *
 * @since 1.0.0
 */
class Supporting_Docs_Widget extends WP_Widget {

	public function __construct() {

		$id_base = 'supporting-docs';
		$name    = __( 'Supporting Documents Widget', 'fwdd-support-docs' );

		$widget_ops      = array(
			'classname'   => 'supporting-docs',
			'description' => __( "Show supporting documents on single page or post.", 'fwdd-support-docs' )
		);
		$control_options = array(
			'width'  => 400,
			'height' => 350
		);
		parent::__construct( $id_base, $name, $widget_ops, $control_options );
	}

	/**
	 * Displays the current widget instance
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		//Only show on single post or page
		if ( ! is_single() && ! is_page() ){
			return;
		}
		//Only show if the current post or page has supporting documents
		if ( ! has_supporting_docs() ){
			return;
		}
		$title     = ! empty( $instance['title'] ) ? $instance['title'] : '';

		/** This filter is documented in wp-includes/default-widgets.php */
		$title     = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		echo $args['before_widget'];

		if ( ! empty( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		echo do_shortcode( '[supporting_docs]' );

		echo $args['after_widget'];
	}

	/**
	 * Update the current widget instance
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	function update( $new_instance, $old_instance ) {
		$instance          = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Display widget form
	 * @param array $instance
	 * @return html
	 */
	public function form( $instance ) {

		$title     = isset( $instance['title'] ) ? sanitize_text_field( $instance['title'] ) : '';
		?>
		<p><label
				for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'fwdd-support-docs' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>"
			       name="<?php echo $this->get_field_name( 'title' ); ?>" type="text"
			       value="<?php echo esc_attr( $title ); ?>"/>
		</p>
		<p>
			<?php _e('This widget will only appear on posts or pages that have supporting documents', 'fwdd-support-docs'); ?>
		</p>

		<?php
	}
}

//Register and load the widget
function FWDD_supporting_docs_widget() {
	register_widget( 'Supporting_Docs_Widget' );
}

add_action( 'widgets_init', 'FWDD_supporting_docs_widget' );