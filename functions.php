<?php
/**
 * Twenty Twenty-Two functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Two
 * @since Twenty Twenty-Two 1.0
 */


if ( ! function_exists( 'twentytwentytwo_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_support() {

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );
	}

endif;

add_action( 'after_setup_theme', 'twentytwentytwo_support' );

if ( ! function_exists( 'twentytwentytwo_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since Twenty Twenty-Two 1.0
	 *
	 * @return void
	 */
	function twentytwentytwo_styles() {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style(
			'twentytwentytwo-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'twentytwentytwo-style' );
	}

endif;

add_action( 'wp_enqueue_scripts', 'twentytwentytwo_styles' );

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';


// Adicionar suporte a thumbnails
add_theme_support('post-thumbnails');

// Enqueue scripts
function my_theme_enqueue_scripts() {
    wp_enqueue_script('jquery');
    wp_enqueue_script('ajax-script', get_template_directory_uri() . '/js/ajax.js', array('jquery'), null, true);
    wp_localize_script('ajax-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('wp_enqueue_scripts', 'my_theme_enqueue_scripts');

// Criar endpoints para o app
function my_theme_endpoints() {
    register_rest_route('mytheme/v1', '/categories', array(
        'methods' => 'GET',
        'callback' => 'my_theme_get_categories',
    ));
    
    register_rest_route('mytheme/v1', '/posts', array(
        'methods' => 'GET',
        'callback' => 'my_theme_get_posts',
    ));
    
    register_rest_route('mytheme/v1', '/post/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'my_theme_get_post_content',
    ));
}
add_action('rest_api_init', 'my_theme_endpoints');


// Função para obter as categorias com imagens
function my_theme_get_categories() {
    $categories = get_categories();
    $image_index = 1;
    $response = array();

    foreach ($categories as $category) {
        $image_path = get_template_directory_uri() . '/images/' . str_pad($image_index, 2, '0', STR_PAD_LEFT) . '.jpg';
        $response[] = array(
            'id' => $category->term_id,
            'name' => $category->name,
            'image' => $image_path
        );
        $image_index++;
    }

    return new WP_REST_Response($response, 200);
}

function my_theme_get_posts($request) {
    $category_id = $request->get_param('category_id');
    $args = array(
        'category' => $category_id,
        'post_status' => 'publish'
    );
    $posts = get_posts($args);
    return new WP_REST_Response($posts, 200);
}

function my_theme_get_post_content($request) {
    $post_id = $request['id'];
    $post = get_post($post_id);
    return new WP_REST_Response($post, 200);
}


// Função para o AJAX de filtro de posts por categoria
function filter_posts_by_category() {
    $category_id = $_POST['category_id'];
    $args = array(
        'category' => $category_id,
        'post_status' => 'publish'
    );
    $posts = get_posts($args);

    if ($posts) {
        foreach ($posts as $post) {
            setup_postdata($post);
            echo '<li><a href="' . get_permalink($post) . '">' . get_the_title($post) . '</a></li>';
        }
        wp_reset_postdata();
    } else {
        echo '<li>Nenhum post encontrado.</li>';
    }

    wp_die();
}
add_action('wp_ajax_filter_posts_by_category', 'filter_posts_by_category');
add_action('wp_ajax_nopriv_filter_posts_by_category', 'filter_posts_by_category');
