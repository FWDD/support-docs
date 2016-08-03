<?php

/**
 * FWDD Supporting Documents
 *
 * @since             1.0.0
 * @package           fwdd_support_docs
 *
 * @wordpress-plugin
 * Plugin Name:       FWDD Supporting Documents
 * Plugin URI:        https://github.com/FWDD/support-docs
 * Description:       Adds supporting documents to posts or pages
 * Version:           1.0.0
 * Author:            FWDD
 * Author URI:        https://freelance-web-designer-developer.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fwdd-support-docs
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( !function_exists( 'add_action' ) ) {
	echo 'Sorry, this is a plugin that won\'t do anything if called directly.';
	exit;
}

// Directory constant
define("FWDDSD_DIR", dirname(__FILE__));

/**
 * Include the plugin functions file
 */
require_once(FWDDSD_DIR . '/includes/functions.php');

/**
 * Include the metabox for posts and pages
 */
require_once(FWDDSD_DIR . '/includes/class-metabox.php');

/**
 * Include the widget
 */
require_once(FWDDSD_DIR . '/includes/class-supporting-docs-widget.php');

/**
 * Add mime types to media library
 * @param $post_mime_types
 *
 * @return mixed
 */
function modify_post_mime_types( $post_mime_types ) {

	// select the mime type, here: 'application/pdf'
	// then we define an array with the label values

	$post_mime_types['application/pdf'] = array( __( 'PDFs' ), __( 'Manage PDFs' ), _n_noop( 'PDF <span class="count">(%s)</span>', 'PDFs <span class="count">(%s)</span>' ) );
	$post_mime_types['application/vnd.openxmlformats-officedocument.wordprocessingml.document'] = array('Word Docs', 'Manage Word Docs', _n_noop('Word DOC <span class="count">(%s)</span>', 'Word Docs <span class="count">(%s)</span>'));

	// then we return the $post_mime_types variable
	return $post_mime_types;

}

// Add Filter Hook
add_filter( 'post_mime_types', 'modify_post_mime_types' );

/**
 * Add categories to media library
 */
function wptp_add_categories_to_attachments() {
	register_taxonomy_for_object_type( 'category', 'attachment' );
}
add_action( 'init' , 'wptp_add_categories_to_attachments' );