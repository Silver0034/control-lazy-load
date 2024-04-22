<?php

// Set the plugin's namespace
namespace jlCLL;

// Disallow direct access to the plugin
if (!defined('ABSPATH')) die('You shall not pass.');

if (!defined('JLCLL_OPTION_KEY_DISABLE_PER_POST')) return;

/**
 * The class to handle adding settings to the post editor
 * 
 * @since 1.0.0
 * @version 1.0.0
 */
class PostEditor
{
    const SCRIPT_KEY = 'jlcll-post-editor';
    /**
     * The constructor for the class
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct()
    {
        // If the user is an admin, construct the admin hooks
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_scripts']);
        // Register the meta key for the post editor
        add_action('init', [$this, 'register_meta_key']);

        // Add a filter to disable lazy load site-wide
        add_filter('wp_lazy_loading_enabled', [$this, 'disable_lazy_load_per_post']);
    }

    /**
     * Add admin script to the post editor
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function enqueue_scripts()
    {
        // Add the script to the post editor
        wp_enqueue_script(
            $this::SCRIPT_KEY,
            plugin_dir_url(JLCLL_PLUGIN_FILE) . 'js/post-editor.js',
            ['wp-plugins', 'wp-edit-post', 'wp-components', 'wp-data', 'wp-compose', 'wp-element'],
            null,
            true
        );

        // Localize the script with your data
        wp_localize_script(
            $this::SCRIPT_KEY,
            'jlcllData',
            [
                'postEditorKey' => JLCLL_OPTION_KEY_DISABLE_PER_POST,
                'disabledSiteWide' => get_option(JLCLL_OPTION_KEY_DISABLE_SITE_WIDE, false)
            ]
        );
    }

    /**
     * Register the meta key for the post editor
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public static function register_meta_key()
    {
        $post_types = get_post_types(['public' => true]);

        foreach ($post_types as $post_type) {
            register_post_meta($post_type, JLCLL_OPTION_KEY_DISABLE_PER_POST, array(
                'show_in_rest' => true,
                'single' => true,
                'type' => 'string',
            ));
        }
    }

    /**
     * Disable lazy load per post.
     * 
     * If the user has disabled lazy load for the current post, return false. Otherwise, return the default value.
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function disable_lazy_load_per_post($default)
    {
        $post_id = get_the_ID();

        if (!$post_id) return $default;

        $meta = get_post_meta($post_id, JLCLL_OPTION_KEY_DISABLE_PER_POST, true);

        if ($meta === 'true') return false;

        return $default;
    }
}
