<?php
/**
 * vipservice functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package vipservice
 */

if ( ! function_exists( 'vipservice_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function vipservice_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on vipservice, use a find and replace
		 * to change 'vipservice' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'vipservice', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'vipservice' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'vipservice_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'vipservice_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function vipservice_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'vipservice_content_width', 640 );
}
add_action( 'after_setup_theme', 'vipservice_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function vipservice_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'vipservice' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'vipservice' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'vipservice_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function vipservice_scripts() {

	if (!is_admin()) {
		wp_deregister_script('jquery');
    	wp_register_script('jquery', includes_url( '/js/jquery/jquery.js' ), false, NULL, true);
		wp_enqueue_script('jquery');
		
		wp_enqueue_script( 'jquery-v3-js', 'https://code.jquery.com/jquery-3.3.1.slim.min.js', array(), false, true );
		wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/js/bootstrap.min.js', array(), false, true );
		wp_enqueue_script( 'owl-js', get_template_directory_uri() . '/js/owl.carousel.min.js', array(), false, true );
		wp_enqueue_script( 'smooth-scroll-js', get_template_directory_uri() . '/js/smooth-scroll.min.js', array(), false, true );
		wp_enqueue_script( 'vue-js', 'https://cdn.jsdelivr.net/npm/vue/dist/vue.js', array(), false, true );
		wp_enqueue_script( 'axios-js', 'https://cdn.jsdelivr.net/npm/axios@0.12.0/dist/axios.min.js', array(), false, true );
		wp_enqueue_script( 'loadash-js', 'https://cdn.jsdelivr.net/npm/lodash@4.13.1/lodash.min.js', array(), false, true );
		wp_enqueue_script( 'vipservice-js', get_template_directory_uri() . '/js/scripts.js', array(), false, true );
	}

	

	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/css/bootstrap.min.css', array(), false );
	wp_enqueue_style( 'owl-css', get_template_directory_uri() . '/css/owl.carousel.min.css', array(), false );
	wp_enqueue_style( 'style-css', get_template_directory_uri() . '/css/estyle.css', array(), false );
	wp_enqueue_style( 'style-fonts-css', get_template_directory_uri() . '/css/vipservice-codes.css', array(), false );
	wp_enqueue_style( 'vipservice-style', get_stylesheet_uri() );
	wp_enqueue_style( 'vipservice-css', get_template_directory_uri() . '/css/style.css' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}	
}
add_action( 'wp_enqueue_scripts', 'vipservice_scripts' );

function vipservice_admin_scripts(){
	wp_enqueue_media();
	wp_enqueue_script( 'tax-meta-class',  get_template_directory_uri() . '/js/custom-meta.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'custom-admin-js',  get_template_directory_uri() . '/js/custom-admin.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'custom-imagem-options',  get_template_directory_uri() . '/js/custom-imagem-options.js', array( 'jquery' ), null, true );
}

add_action('admin_enqueue_scripts', 'vipservice_admin_scripts');

function log_mailer_errors( $wp_error ){
  $fn = ABSPATH . '/mail.log'; // say you've got a mail.log file in your server root
  $fp = fopen($fn, 'a');
  fputs($fp, "Mailer Error: " . $wp_error->get_error_message() ."\n");
  fclose($fp);
}

add_action('wp_mail_failed', 'log_mailer_errors', 10, 1);

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Funções do tema
 */
require_once get_template_directory() . '/inc/custom-theme-functions.php';
require_once get_template_directory() . '/inc/custom-images.php';

/**
 * Endpoints customizados
 */
require_once get_template_directory() . '/inc/endpoints/endpoint-orcamento.php';

/*
* Custom post types
*/
require_once get_template_directory() . '/inc/custom-post-type/custom-type-banner.php';
require_once get_template_directory() . '/inc/custom-post-type/custom-type-servicos.php';
require_once get_template_directory() . '/inc/custom-post-type/custom-type-servicos-orcamentos.php';
require_once get_template_directory() . '/inc/custom-post-type/custom-type-orcamento.php';