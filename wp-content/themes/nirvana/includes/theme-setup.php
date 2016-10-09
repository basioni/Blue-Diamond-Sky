<?php
/*
 * Theme setup functions. Theme initialization, theme support , widgets , navigation
 *
 * @package nirvana
 * @subpackage Functions
 */

// Bringing up Nirvana Settings page after install
/*
if ( is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {
	wp_redirect( 'themes.php?page=nirvana-page' );
}*/

 $nirvana_totalSize = $nirvana_sidebar + $nirvana_sidewidth;

 /**

 *
 * @package Cryout Creations
 * @subpackage nirvana
 * @since nirvana 0.5
 */

/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * Used to set the width of images and content. Should be equal to the width the theme
 * is designed for, generally via the style.css stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = $nirvana_sidewidth;

/** Tell WordPress to run nirvana_setup() when the 'after_setup_theme' hook is run. */
add_action( 'after_setup_theme', 'nirvana_setup' );

if ( ! function_exists( 'nirvana_setup' ) ):
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override nirvana_setup() in a child theme, add your own nirvana_setup to your child theme's
 * functions.php file.
 *
 * @uses add_theme_support() To add support for post thumbnails and automatic feed links.
 * @uses register_nav_menus() To add support for navigation menus.
 * @uses add_custom_background() To add support for a custom background.
 * @uses add_editor_style() To style the visual editor.
 * @uses load_theme_textdomain() For translation/localization support.
 * @uses add_custom_image_header() To add support for a custom header.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size() To set a custom post thumbnail size.
 *
 * @since nirvana 0.5
 */
function nirvana_setup() {

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style( "styles/editor-style.css" );

	// This theme uses post thumbnails
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 150, 150 ); // default Post Thumbnail dimensions (cropped)

	// Add default posts and comments RSS feed links to head

	add_theme_support( 'automatic-feed-links' );
	add_theme_support('post-formats', array( 'aside', 'chat', 'gallery', 'image', 'link', 'quote', 'status','audio', 'video'));

	// Make theme available for translation
	// Translations can be filed in the /languages/ directory
	load_theme_textdomain( 'nirvana', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );



	// This theme uses wp_nav_menu() in 3 locations.
	register_nav_menus( array(
		'primary' => __( 'Primary Navigation', 'nirvana' ),
		'top' => __( 'Top Navigation', 'nirvana' ),
		'footer' => __( 'Footer Navigation', 'nirvana' ),
	) );

	// This theme allows users to set a custom background
	add_theme_support( 'custom-background' );

	// We'll be using post thumbnails for custom header images on posts and pages.
	// We want them to be the same size as the header.
	// Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	global $nirvana_hheight;
	$nirvana_hheight=(int)$nirvana_hheight;
	global $nirvana_totalSize;
	define( 'HEADER_IMAGE_WIDTH', apply_filters( 'nirvana_header_image_width', $nirvana_totalSize ) );
	define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'nirvana_header_image_height', $nirvana_hheight) );
	//set_post_thumbnail_size( HEADER_IMAGE_WIDTH, HEADER_IMAGE_HEIGHT, true );
	add_image_size('header',HEADER_IMAGE_WIDTH,HEADER_IMAGE_HEIGHT,true);	

	global $nirvana_fpsliderwidth; global $nirvana_fpsliderheight;
	global $nirvana_colimageheight; global $nirvana_colimagewidth;
	add_image_size('slider',$nirvana_fpsliderwidth,$nirvana_fpsliderheight,true);
	add_image_size('columns',$nirvana_colimagewidth,$nirvana_colimageheight,true);
	// Add a way for the custom header to be styled in the admin panel that controls
	// custom headers. See nirvana_admin_header_style(), below.
	define( 'NO_HEADER_TEXT', true );
	// Add support for flexible headers
	$header_args = array(
		'flex-height' => true,
		'height' => HEADER_IMAGE_HEIGHT,
		'flex-width' => true,
		'width' => $nirvana_totalSize,
		'max-width' => 1920,
		'default-image' => '',
		'admin-head-callback' => 'nirvana_admin_header_style',
	);
	add_theme_support( 'custom-header',$header_args );
	// ... and thus ends the changeable header business.

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'nirvana' => array(
			'url' => '%s/images/headers/nirvana.png',
			'thumbnail_url' => '%s/images/headers/nirvana_thumbnail.png',
			'description' => __( 'Nirvana Default Header Image', 'nirvana' )
		),

	) );
}
endif;

if ( ! function_exists( 'nirvana_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_custom_image_header() in nirvana_setup().
 *
 * @since nirvana 0.5
 */
function nirvana_admin_header_style() {
?>
<style type="text/css">
/* Shows the same border as on front end */
#headimg {
	border-bottom: 1px solid #000;
	border-top: 4px solid #000;
}
/* If NO_HEADER_TEXT is false, you would style the text with these selectors:
	#headimg #name { }
	#headimg #desc { }
*/
</style>
<?php
}
endif;

/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * To override this in a child theme, remove the filter and optionally add
 * your own function tied to the wp_page_menu_args filter hook.
 *
 * @since nirvana 0.5
 */
function nirvana_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'nirvana_page_menu_args' );

/**
 * Create menus
 */

// TOP MENU
function nirvana_top_menu() {
 if ( has_nav_menu( 'top' ) ) 
 wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'topmenu', 'theme_location' => 'top', 'depth' =>1 ) );
 }

 add_action ('cryout_topbar_hook','nirvana_top_menu',15);

 // MAIN MENU
 function nirvana_main_menu() {
  /*  Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff */ ?>
<div class="skip-link screen-reader-text"><a href="#content" title="<?php esc_attr_e( 'Skip to content', 'nirvana' ); ?>"><?php _e( 'Skip to content', 'nirvana' ); ?></a></div>
<?php /* Main navigation menu.  If one isn't filled out, wp_nav_menu falls back to wp_page_menu.  The menu assiged to the primary position is the one used.  If none is assigned, the menu with the lowest ID is used.  */
wp_nav_menu( array( 'container_class' => 'menu', 'menu_id' =>'prime_nav', 'theme_location' => 'primary', 'link_before' => '<span>', 'link_after' => '</span>' ) );
}

add_action ('cryout_access_hook','nirvana_main_menu');

// FOOTER MENU
function nirvana_footer_menu() {
 if ( has_nav_menu( 'footer' ) ) 
	wp_nav_menu( array( 'container' => 'nav', 'container_class' => 'footermenu', 'theme_location' => 'footer', 'depth' =>1 ) );
 
}

  add_action ('cryout_footer_hook','nirvana_footer_menu',10);


/**
 * Register widgetized areas, including two sidebars and four widget-ready columns in the footer.
 *
 * To override nirvana_widgets_init() in a child theme, remove the action hook and add your own
 * function tied to the init hook.
 *
 * @since nirvana 0.5
 * @uses register_sidebar
 */
function nirvana_widgets_init() {	
	// Area 1, located at the top of the sidebar.
	register_sidebar( array(
		'name' => __( 'Left Sidebar', 'nirvana' ),
		'id' => 'left-widget-area',
		'description' => __( 'Left sidebar', 'nirvana' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</h3></span>',
	) );

	// Area 2, located below the Primary Widget Area in the sidebar. Empty by default.
	register_sidebar( array(
		'name' => __( 'Right Sidebar', 'nirvana' ),
		'id' => 'right-widget-area',
		'description' => __( 'Right sidebar', 'nirvana' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</h3></span>',
	) );

	// Area 5, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'First Footer Area', 'nirvana' ),
		'id' => 'first-footer-widget-area',
		'description' => __( 'First footer area', 'nirvana' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</h3></span>',
	) );

	// Area 6, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Second Footer Area', 'nirvana' ),
		'id' => 'second-footer-widget-area',
		'description' => __( 'Second footer area', 'nirvana' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</h3></span>',
	) );

	// Area 7, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Third Footer Area', 'nirvana' ),
		'id' => 'third-footer-widget-area',
		'description' => __( 'The third footer area', 'nirvana' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</h3></span>',
	) );

	// Area 8, located in the footer. Empty by default.
	register_sidebar( array(
		'name' => __( 'Fourth Footer Area', 'nirvana' ),
		'id' => 'fourth-footer-widget-area',
		'description' => __( 'The fourth footer area', 'nirvana' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</h3></span>',
	) );

		// Area 9, located above the content area. Empty by default.
	register_sidebar( array(
		'name' => __( 'Above Content Area', 'nirvana' ),
		'id' => 'above-content-widget-area',
		'description' => __( 'Above Content Area', 'nirvana' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</h3></span>',
	) );

		// Area 10, located below the content area. Empty by default.
	register_sidebar( array(
		'name' => __( 'Below Content Area', 'nirvana' ),
		'id' => 'below-content-widget-area',
		'description' => __( 'Below Content Area', 'nirvana' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</h3></span>',
	) );
	
		// Area 0, located inside the header
	register_sidebar( array(
		'name' => __( 'Header Widgets', 'nirvana' ),
		'id' => 'header-widget-area',
		'description' => __( 'Header Widgets', 'nirvana' ),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title"><span>',
		'after_title' => '</h3></span>',
	) );
	global $nirvana_colimagewidth;
	global $nirvana_colimageheight;
	
	// Area 11, the presentation page columns
	register_sidebar( array(
		'name' => __( 'Presentation Page Columns', 'nirvana' ),
		'id' => 'presentation-page-columns-area',
		'description' => sprintf(__('Only drag [Cryout Column] widgets here. Recommended size for uploaded images: %1$dpx (width) x %2$dpx (height). Go to the Nirvana Settings page >> Presentation Page Settings >> Columns to edit sizes and more.','nirvana' ),$nirvana_colimagewidth,$nirvana_colimageheight),
		'before_widget' => '<li id="%1$s" class="widget-container %2$s">',
		'after_widget' => '</li>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
/** Register sidebars by running nirvana_widgets_init() on the widgets_init hook. */
add_action( 'widgets_init', 'nirvana_widgets_init' );


/**
 * Creates different class names for footer widgets depending on their number.
 * This way they can fit the footer area.
 */

function nirvana_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'first-footer-widget-area' ) )
		$count++;

	if ( is_active_sidebar( 'second-footer-widget-area' ) )
		$count++;

	if ( is_active_sidebar( 'third-footer-widget-area' ) )
		$count++;

	if ( is_active_sidebar( 'fourth-footer-widget-area' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
		case '4':
			$class = 'four';
			break;
	}

	if ( $class )
		echo 'class="footer' . $class . '"';
}


function nirvana_header_widget() {
 if ( is_active_sidebar( 'header-widget-area' )) { ?>
		<div id="header-widget-area">
			<ul class="yoyo">
				<?php dynamic_sidebar( 'header-widget-area' ); ?>
			</ul>
		</div>
		<?php } }
		
add_action ('cryout_header_widgets_hook','nirvana_header_widget');

 function nirvana_above_widget() {
 if ( is_active_sidebar( 'above-content-widget-area' )) { ?>
			<ul class="yoyo">
				<?php dynamic_sidebar( 'above-content-widget-area' ); ?>
			</ul>
		<?php } }

function nirvana_below_widget() {
 if ( is_active_sidebar( 'below-content-widget-area' )) { ?>
			<ul class="yoyo">
				<?php dynamic_sidebar( 'below-content-widget-area' ); ?>
			</ul>
		<?php } }

add_action ('cryout_before_content_hook','nirvana_above_widget');
add_action ('cryout_after_content_hook','nirvana_below_widget');

?>