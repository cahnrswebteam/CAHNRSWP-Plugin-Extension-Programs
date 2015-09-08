<?php
/*
Plugin Name: CAHNRSWP Extension Programs
Plugin URI:	http://cahnrs.wsu.edu/communications/
Description: Registers a custom content type for creating a dynamic Programs list.
Author:	CAHNRS, philcable
Version: 0.0.1
*/

class CAHNRSWP_Plugin_Extension_Programs {

	/**
	 * @var string Post type slug.
	 */
	var $post_type = 'extension_program';

	/**
	 * Start the plugin and apply associated hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'init' ), 11 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
		add_action( 'save_post_extension_program', array( $this, 'save_post' ) );
	}

	/**
	 * Register the post type.
	 */
	public function init() {
		register_post_type( $this->post_type,
    	array(
				'label'         => 'Extension Programs',
				'description'   => '',
				'labels'        => array(
					'name'               => 'Programs',
					'singular_name'      => 'Program',
					'all_items'          => 'All Programs',
					'view_item'          => 'View Program',
					'add_new_item'       => 'Add New Program',
					'edit_item'          => 'Edit Program',
					'update_item'        => 'Update Program',
					'search_items'       => 'Search Programs',
					'not_found'          => 'No programs found',
					'not_found_in_trash' => 'No programs found in Trash',
				),
				'public'        => true,
				'menu_position' => 5,
				'menu_icon'     => 'dashicons-clipboard',
				'supports'      => array(
					'title',
					'editor',
					'author',
					'thumbnail',
					'revisions',
				),
				/*'taxonomies'    => array(
					'topics',
				),
				'has_archive'   => true,
				'rewrite'       => array(
					'slug'       => 'programs',
					'with_front' => false
				),*/
			)
		);
	}

	/**
	 * Enqueue scripts and styles for the admin.
	 *
	 * @param int $hook Hook suffix for the current admin page.
	 */
	public function admin_enqueue_scripts( $hook ) {
		$screen = get_current_screen();
		if ( ( 'post-new.php' === $hook || 'post.php' === $hook ) && $this->post_type === $screen->post_type ) {
			wp_enqueue_style( 'programs-admin', plugins_url( 'css/admin-programs.css', __FILE__ ), array() );
		}
	}

	/**
	 * Add a field for Program Web Address after the title.
	 *
	 * @param WP_Post $post
	 */
	public function edit_form_after_title( $post ) {
		if ( $this->post_type !== $post->post_type ) {
			return;
		}
		wp_nonce_field( 'extension_program_meta', 'extension_program_meta_nonce' );
		$value = get_post_meta( $post->ID, '_program_url', true );
		?>
		<label for="program_url"><h3>Web Address</h3>
		<input type="text" id="program_url" name="program_url" value="<?php echo esc_attr( $value ); ?>" class="widefat" /></label>
		<?php
	}

	/**
	 * Save data associated with an Impact Report.
	 *
	 * @param int $post_id
	 *
	 * @return mixed
	 */
	public function save_post( $post_id ) {

		if ( ! isset( $_POST['extension_program_meta_nonce'] ) ) {
			return $post_id;
		}

		$nonce = $_POST['extension_program_meta_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'extension_program_meta' ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Sanitize and save data.
		if ( isset( $_POST['program_url'] ) && $_POST['program_url'] != '' ) {
			update_post_meta( $post_id, '_program_url', sanitize_text_field( $_POST['program_url'] ) );
		} else {
			delete_post_meta( $post_id, '_program_url' );
		}

	}


}

new CAHNRSWP_Plugin_Extension_Programs();