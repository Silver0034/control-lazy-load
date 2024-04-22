<?php

/**
 * Control Lazy Load
 * 
 * Disable WordPress Core's image lazy load per-image, per-post, or site-wide.
 * 
 * @link     https://www.jlodes.com/
 * @since    1.0.0
 * @version  1.0.0
 * 
 * @wordpress-plugin
 * Plugin Name:    Control Lazy Load
 * Plugin URI:     https://www.jlodes.com/
 * Description:    Disable lazy loading on your LCP through per-image, per-post, & site-wide controls.
 * Version:        1.0.0
 * Author:         Jacob Lodes
 * Author URI:     https://www.jlodes.com/
 */

// Set the plugin's namespace
namespace jlCLL;

// Disallow direct access to the plugin
if (!defined('ABSPATH')) die('You shall not pass.');

// Define the plugin's constants
define('JLCLL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('JLCLL_PLUGIN_FILE', __FILE__);
define('JLCLL_OPTION_KEY_DISABLE_SITE_WIDE', 'jlcll_disable_lazy_load_site_wide');
define('JLCLL_OPTION_KEY_DISABLE_PER_POST', 'jlcll_disable_lazy_load_per_post');
define('JLCLL_OPTION_KEY_DISABLE_IMAGE_BLOCK', 'jlcll_disable_lazy_load_image_block');


// Include the plugin's files
require_once JLCLL_PLUGIN_PATH . 'includes/class-site-wide.php';
require_once JLCLL_PLUGIN_PATH . 'includes/class-post-editor.php';
require_once JLCLL_PLUGIN_PATH . 'includes/class-block-editor.php';
require_once JLCLL_PLUGIN_PATH . 'includes/class-updater.php';

// Instantiate the plugin's classes
new SiteWide();
new PostEditor();
new ImageBlockEditor();
new Updater();

// On plugin deactivation, run each class's deactivate method
register_deactivation_hook(__FILE__, function () {
    // Deactivate the site-wide settings
    (new SiteWide())->deactivate();
});
