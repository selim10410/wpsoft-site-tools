<?php
require_once __DIR__ . '/includes/class-wpst-header-footer-templates.php';
/**
 * Plugin Name: WPSoft Site Tools
 * Description: Sürükle-bırak hızlı tasarım veya Elementor şablonlarıyla header ve footer yönetimi sağlar.
 * Version: 3.3.10
 * Author: WPSoft
 * Text Domain: wpsoft-site-tools
 */
if ( ! defined( 'ABSPATH' ) ) exit;
define( 'WPST_VERSION', '3.3.10' );
define( 'WPST_FILE', __FILE__ );
define( 'WPST_PATH', plugin_dir_path( __FILE__ ) );
define( 'WPST_URL', plugin_dir_url( __FILE__ ) );
require_once WPST_PATH . 'includes/class-wpst-icon-library.php';
require_once WPST_PATH . 'includes/class-wpst-svg-library.php';
require_once WPST_PATH . 'includes/class-wpst-builder-core.php';
require_once WPST_PATH . 'includes/class-wpst-plugin.php';
require_once WPST_PATH . 'includes/class-wpst-license.php';
require_once WPST_PATH . 'includes/class-wpst-updater.php';
require_once WPST_PATH . 'includes/class-wpst-template-library.php';
require_once WPST_PATH . 'includes/class-wpst-portfolio-manager.php';
require_once WPST_PATH . 'includes/class-wpst-blog-templates.php';
require_once WPST_PATH . 'includes/class-wpst-performance.php';
require_once WPST_PATH . 'includes/class-wpst-widget-catalog.php';
require_once WPST_PATH . 'includes/class-wpst-theme-compatibility.php';
require_once WPST_PATH . 'includes/class-wpst-diagnostics.php';
require_once WPST_PATH . 'includes/class-wpst-display-conditions.php';
require_once WPST_PATH . 'includes/class-wpst-conditions-admin.php';
require_once WPST_PATH . 'includes/elementor/class-wpst-elementor.php';
WPST_License::init();
WPST_Updater::init();
WPST_Icon_Library::init();
WPST_Builder_Core::init();
WPST_Plugin::instance();
WPST_Template_Library::init();
WPST_Portfolio_Manager::init();
WPST_Blog_Templates::init();
WPST_Performance::init();
WPST_Widget_Catalog::init();
WPST_Theme_Compatibility::init();
WPST_Diagnostics::init();

// Elementor may already have fired `elementor/loaded` before WPSoft is loaded.
// In that case waiting for the action would leave the entire integration disabled.
if ( did_action( 'elementor/loaded' ) ) {
    WPST_Elementor::init();
} else {
    add_action( 'elementor/loaded', array( 'WPST_Elementor', 'init' ) );
}

add_action( 'plugins_loaded', function(){ if ( class_exists('WPST_Header_Footer_Templates') ) { WPST_Header_Footer_Templates::init(); } }, 30 );

require_once WPST_PATH . 'includes/class-wpst-mega-menu.php';

add_action('plugins_loaded', function(){ if(class_exists('WPST_Mega_Menu')) WPST_Mega_Menu::init(); }, 35);



register_activation_hook( __FILE__, array( 'WPST_Portfolio_Manager', 'activate' ) );
