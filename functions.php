<?php // smart jquery inclusion
if (!is_admin()) {
	wp_deregister_script('jquery');
	wp_register_script('jquery', ("https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"), false);
	wp_enqueue_script('jquery');
}
// remove junk from head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);

function my_assets() {
    // wp_register_script( 'app', get_stylesheet_directory_uri() . '/assets/js/app.js',  true  );
    wp_register_script( 'bundle', get_stylesheet_directory_uri() . '/dist/bundle.js',  true  );

    // wp_enqueue_script( 'app' );
    wp_enqueue_script( 'bundle' );
//load script
wp_enqueue_script( 'my-post-submitter', get_stylesheet_directory_uri() . '/assets/js/post-submitter.js', array( 'jquery' ) );
wp_enqueue_script( 'parsley', get_stylesheet_directory_uri() . '/assets/js/parsley.min.js', array( 'jquery' ) );
 
//localize data for script
wp_localize_script( 'my-post-submitter', 'POST_SUBMITTER', array(
        'root' => esc_url_raw( rest_url() ),
        'nonce' => wp_create_nonce( 'wp_rest' ),
        'success' => __( 'Thanks for your submission!', 'your-text-domain' ),
        'failure' => __( 'Your submission could not be processed.', 'your-text-domain' )
            )
);
}
add_action( 'wp_enqueue_scripts', 'my_assets' );

function head_scripts() { ?>
<script type="text/javascript">
  WebFontConfig = {
    google: { families: [ 'Open+Sans:300,400,700,900' ] }
  };
  (function() {
    var wf = document.createElement('script');
    wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
      '://ajax.googleapis.com/ajax/libs/webfont/1.5.18/webfont.js';
    wf.type = 'text/javascript';
    wf.async = 'true';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(wf, s);
  })(); </script>
<?php 
}
function create_post_type_highlights() {
	
	register_post_type( 'leads',
		// CPT Options
		array(
			'labels'      => array(
				'name'          => __( 'Leads' ),
				'singular_name' => __( 'Lead' )
			),
            'public'      => true,
            'show_in_rest' =>true,
            'register_meta_box_cb' => 'wpt_add_lead_metaboxes',
			'supports'    => array(  'title', 'excerpt', 'custom-fields', 'name', 'email', 'phone' ),
			'has_archive' => false,
			'rewrite'     => array( 'slug' => 'lead' ),
		)
	);
}
add_action( 'init', 'create_post_type_highlights' );

function get_posts_via_rest() {

	// Initialize variable.
	$allposts = '';
	
	// Enter the name of your blog here followed by /wp-json/wp/v2/posts and add filters like this one that limits the result to 2 posts.
	$response = wp_remote_get( 'http://homework/wp-json/wp/v2/posts?per_page=2' );

	// Exit if error.
	if ( is_wp_error( $response ) ) {
		return;
	}

	// Get the body.
	$posts = json_decode( wp_remote_retrieve_body( $response ) );

	// Exit if nothing is returned.
	if ( empty( $posts ) ) {
		return;
	}

	// If there are posts.
	if ( ! empty( $posts ) ) {

		// For each post.
		foreach ( $posts as $post ) {

			// Use print_r($post); to get the details of the post and all available fields
			// Format the date.
			$fordate = date( 'n/j/Y', strtotime( $post->modified ) );

			// Show a linked title and post date.
			$allposts .= '<a href="' . esc_url( $post->link ) . '" target=\"_blank\">' . esc_html( $post->title->rendered ) . '</a>  ' . esc_html( $fordate ) . '<br />';
		}
		
		return $allposts;
	}

}
// Register as a shortcode to be used on the site.
add_shortcode( 'sc_get_posts_via_rest', 'get_posts_via_rest' );
