<?php

/**
 * Class Sections_Meta_Box
 * @see https://generatewp.com/the-meta-box-generator/
 */
class Supporting_Docs_Meta_Box {

	public function __construct() {
		$this->supportingDocsRendered = false;
		if ( is_admin() ) {
			add_action( 'load-post.php', array( $this, 'init_metabox' ) );
			add_action( 'load-post-new.php', array( $this, 'init_metabox' ) );

		}

	}

	/**
	 * Initialize the meta box
	 */
	public function init_metabox() {

		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );

	}

	/**
	 * Add the meta box below the post editor
	 */
	public function add_metabox() {

		add_meta_box(
			'supporting_docs_meta', //ID attribute of meta box
			__( 'Supporting Documents', 'fwdd-support-docs' ), //Title of meta box, visible to user
			array( $this, 'render_metabox' ), //Callback function that prints meta box in wp-admin
			array( 'page', 'post' ), //Show box for posts, pages, custom, etc. TODO should only be pages
			'normal', //Context where the box appears (normal, side, advanced.
			'high' //Priority (low, high, default)
		);

	}

	/**
	 * Display the Supporting Docs meta box
	 * @see https://codex.wordpress.org/Javascript_Reference/wp.media
	 *
	 * @param $post
	 */
	public function render_metabox( $post ) {
		// Get WordPress' media upload URL
		$upload_link = esc_url( get_upload_iframe_src( 'image', $post->ID ) );
		?>
		<p><?php _e( 'Drag each item into the order you prefer. Use this shortcode to display supporting documents in the post or page:', 'fwdd-support-docs' ); ?>[supporting_docs]</p>
		<!-- Your image container, which can be manipulated with js -->
		<div class="supporting-docs-container ui-sortable">
			<?php
			/**
			 * Get an array of supporting documents to show in our form
			 * set 'single' flag to false because we want an array.
			 */
			$supporting_documents = get_post_meta( $post->ID, 'supporting_documents', true );

			if ( ! empty( $supporting_documents ) ) {
				foreach ( $supporting_documents as $document ) {
					$id        = esc_attr( $document['id'] );
					$extension = esc_attr( $document['extension'] );
					$url       = esc_url( $document['url'] );
					$filename  = esc_attr( $document['filename'] );
					$size      = esc_attr( $document['size'] );
					echo '<div class="supporting-document doc-icon-' . $extension . '">';
					echo '<input type="hidden" name="attachments[' . $id . '][id]" value="' . $id . '" />';
					echo '<input type="hidden" name="attachments[' . $id . '][extension]" value="' . $extension . '" />';
					echo '<input type="hidden" name="attachments[' . $id . '][url]" value="' . $url . '" />';
					echo '<input type="hidden" name="attachments[' . $id . '][filename]" value="' . $filename . '" />';
					echo '<input type="hidden" name="attachments[' . $id . '][size]" value="' . $size . '" />';
					echo '<a href="' . $url . '">' . $filename . '</a><br>';
					echo '<i>' . $size . '</i>';
					echo '<a class="remove-doc" href="#">Remove</a>';
					echo '</div>';
				}
			}
			?>
		</div>

		<!-- Your add & remove image links -->
		<p class="hide-if-no-js">
			<a class="upload-supporting-docs button" href="<?php echo $upload_link ?>">
				<?php _e( 'Add Supporting Documents' ) ?>
			</a>
		</p>
		<?php
		// Add nonce for security and authentication.
		wp_nonce_field( 'save_services', 'services_nonce' );
	}

	/**
	 * Handles saving the meta box content on post save/publish
	 * @param $post_id
	 * @param $post
	 */
	public function save_metabox( $post_id, $post ) {
		$screen = get_current_screen();
		if ( 'page' != $screen->post_type && 'post' != $screen->post_type ) {
			return;

		}

		// Add nonce for security and authentication.
		$nonce_name   = $_POST['services_nonce'];
		$nonce_action = 'save_services';

		// Check if a nonce is set and valid.
		if ( ! isset( $nonce_name ) || ! wp_verify_nonce( $nonce_name, $nonce_action ) ) {
			return;
		}

		// Check if the user has permissions to save data.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Check if it's not an autosave.
		if ( wp_is_post_autosave( $post_id ) ) {
			return;
		}

		// Check if it's not a revision.
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Sanitize user input.
		$supporting_docs = '';

		foreach ( $_POST['attachments'] as $index => $document ) {
			foreach ( $document as $key => $value ) {
				switch ( $key ) {
					case 'id':
						$value = absint( $value );
						break;
					case 'extension':
						$value = ! empty( $value ) ? sanitize_html_class( $value ) : '';
						break;
					case 'url':
						$value = 'URL' . ! empty( $value ) ? esc_url_raw( $value ) : '';
						break;
					case 'filename':
						$value = 'Filename' . ! empty( $value ) ? sanitize_text_field( $value ) : '';
						break;
					case 'size':
						$value = 'Size' . ! empty( $value ) ? sanitize_text_field( $value ) : '';
						break;
				}
				$supporting_docs[ $index ][ $key ] = $value;
			}
		}

		// Update the meta field in the database.
		update_post_meta( $post_id, 'supporting_documents', $supporting_docs );
	}

	public function load_scripts( $hook ) {
		//Only load scripts for edit post or new post
		if ( 'post.php' != $hook && 'post-new.php' != $hook ) {
			return;
		}
		//Get the current screen for the editor
		$screen = get_current_screen();
		//Only oad scripts if we are in page or post screen (not custom post types)
		if ( 'page' != $screen->post_type && 'post' != $screen->post_type ) {
			return;

		}
		wp_register_script( 'support-docs', plugins_url( 'js/support-docs.js', dirname( __FILE__ ) ), array( 'jquery' ), '1.0', true );
		//Enqueue the script
		wp_enqueue_script( 'support-docs' );
		wp_register_style( 'support-docs', plugins_url( 'css/support-docs.css', dirname( __FILE__ ) ) );
		//Enqueue the main stylesheet
		wp_enqueue_style( 'support-docs' );
	}

}

//Create an instance of the meta box class
new Supporting_Docs_Meta_Box;