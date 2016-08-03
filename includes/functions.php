<?php

/**
 * Get Supporting Docs
 *
 * @return  string  Formatted HTML
 */
if ( ! function_exists( 'get_supporting_docs' ) ):
	function get_supporting_docs() {
		global $post;
		$output = '<div class="supporting-docs-container">';
		/**
		 * Get an array of supporting documents to show
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
				$output .= '<div class="supporting-document doc-icon-' . $extension . '">';
				$output .= '<a href="' . $url . '">' . $filename . '</a><br>';
				$output .= '<i>' . $size . '</i>';
				$output .= '</div>';
			}
		}
		$output .= '</div><!-- /supporting-docs-container-->';

		return $output;
	}
endif;

/**
 * Checks to see if we have any supporting documents
 * @return bool
 */
if ( ! function_exists( 'has_supporting_docs' ) ):
	function has_supporting_docs() {
		global $post;
		$supporting_documents = get_post_meta( $post->ID, 'supporting_documents', true );
		if ( empty( $supporting_documents ) ) {
			return false;
		}

		return true;
	}
endif;


/**
 * Display Supporting Docs
 *
 */
if ( ! function_exists( 'supporting_docs' ) ):
	function supporting_docs() {
		echo get_supporting_docs();
	}
endif;

/**
 * Add Supporting Docs stylesheet
 */
if ( ! function_exists( 'load_supporting_doc_styles' ) ):
	function load_supporting_doc_styles() {
	//Only load the CSS
	global $fwdd_supporting_documents_css;
		if ( ! $fwdd_supporting_documents_css ){
			return;
		}
		wp_enqueue_style( 'support-docs', plugins_url( 'css/support-docs.css', dirname( __FILE__ ) ) );
	}

	add_action( 'init', 'load_supporting_doc_styles' );
endif;

/**
 * Add Supporting Docs shortcode support
 */
if ( ! function_exists( 'supporting_docs_shortcode' ) ):
	global $fwdd_supporting_documents_css;
	$fwdd_supporting_documents_css = true;
	function supporting_docs_shortcode() {
		return get_supporting_docs();
	}
endif;

add_shortcode( 'supporting_docs', 'supporting_docs_shortcode' );