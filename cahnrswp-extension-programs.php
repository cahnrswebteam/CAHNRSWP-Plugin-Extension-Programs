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
		add_action( 'init', array( $this, 'add_taxonomies' ), 12 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
		add_action( 'save_post_extension_program', array( $this, 'save_post' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
		add_filter( 'template_include', array( $this, 'template_include' ), 1 );
		add_action( 'wp_ajax_nopriv_extension_programs_request', array( $this, 'ajax_post_request' ) );
		add_action( 'wp_ajax_extension_programs_request', array( $this, 'ajax_post_request' ) );
	}

	/**
	 * Register the post type.
	 */
	public function init() {
		register_post_type( $this->post_type,
    	array(
				'label'             => 'Extension Programs',
				'description'       => '',
				'labels'            => array(
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
				'public'            => true,
				'show_in_nav_menus' => false,
				'menu_position'     => 5,
				'menu_icon'         => 'dashicons-clipboard',
				'supports'          => array(
					'title',
					'editor',
					'author',
					'thumbnail',
					'revisions',
				),
				'has_archive'       => true,
				'rewrite'           => array(
					'slug'       => 'programs',
					'with_front' => false
				),
			)
		);
	}

	/**
	 * Add support for taxonomies.
	 */
	public function add_taxonomies() {
		register_taxonomy_for_object_type( 'topic', $this->post_type );
	}

	/**
	 * Enqueue scripts and styles for use on the back end.
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
	 * Add options page link to the menu.
	 */
	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=' . $this->post_type, 'Programs Settings', 'Settings', 'manage_options', 'settings', array( $this, 'programs_settings_page' ) );
	}

	/**
	 * Options page settings.
	 */
	public function admin_init() {
		register_setting( 'programs_options', 'programs_archive_text' );
	}

	/**
	 * Options page content.
	 */
	public function programs_settings_page() {
		?>
		<div class="wrap">
			<h2>Programs Settings</h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'programs_options' ); ?>
				<?php do_settings_sections( 'programs_options' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">Archive Introductory Text</th>
						<td><?php wp_editor( wp_kses_post( get_option( 'programs_archive_text' ) ), 'programs_archive_text' ); ?></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
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
	 * Save custom data associated with the post.
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

		if ( isset( $_POST['program_url'] ) && $_POST['program_url'] != '' ) {
			update_post_meta( $post_id, '_program_url', sanitize_text_field( $_POST['program_url'] ) );
		} else {
			delete_post_meta( $post_id, '_program_url' );
		}

	}

	/**
	 * Enqueue scripts and styles for use on the front end.
	 */
	public function wp_enqueue_scripts() {
		if ( is_post_type_archive( $this->post_type ) ) {
			wp_enqueue_style( 'programs', plugins_url( 'css/programs.css', __FILE__ ), array( 'spine-theme', 'cahnrs' ) );
			wp_enqueue_script( 'programs', plugins_url( 'js/programs.js', __FILE__ ), array( 'jquery' ), '', true );
			wp_localize_script( 'programs', 'programs', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		}
	}

	/**
	 * Add templates for post type.
	 *
	 * @param string $template
	 *
	 * @return string template path
	 */
	public function template_include( $template ) {
		if ( is_post_type_archive( $this->post_type ) ) {
			$template = plugin_dir_path( __FILE__ ) . 'templates/index.php';
		}
		return $template;
	}

	/**
	 * AJAX post requests.
	 */
	public function ajax_post_request() {

		$ajax_args = array(
			'post_type'      => $this->post_type,
			'posts_per_page' => -1,
			'orderby'        => 'name',
			'order'          => 'asc',
		);

		if ( $_POST['type'] ) {
			$ajax_args['tax_query'] = array(
				array(
					'taxonomy' => $_POST['type'],
					'field'    => 'slug',
					'terms'    => $_POST['term'],
				),
			);
		}

		$posts = new WP_Query( $ajax_args );
    if ( $posts->have_posts() ) {
			while ( $posts->have_posts() ) : $posts->the_post();
				load_template( dirname( __FILE__ ) . '/templates/post.php', false );
      endwhile;
		} else {
			echo 'Sorry, no Extension Programs match the criteria.';
		}

		exit;
	}

}

new CAHNRSWP_Plugin_Extension_Programs();