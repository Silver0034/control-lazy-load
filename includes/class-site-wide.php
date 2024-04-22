<?php

// Set the plugin's namespace
namespace jlCLL;

// Disallow direct access to the plugin
if (!defined('ABSPATH')) die('You shall not pass.');

// If the key for site wide settings is not defined, stop
if (!defined('JLCLL_OPTION_KEY_DISABLE_SITE_WIDE')) return;

/**
 * The class to handle site-wide settings for the plugin
 * 
 * @since 1.0.0
 * @version 1.0.0
 */
class SiteWide
{
    const MENU_SLUG = 'control-lazy-load';
    private $menu_title;
    private $menu_why_use_plugin;
    private $menu_description;
    private $setting_section_name;
    private $setting_section_description;
    private $setting_label;
    /**
     * The constructor for the class
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function __construct()
    {
        // If the user is an admin, construct the admin hooks
        if (is_admin()) {
            $this->construct_admin();
        }

        // Add a filter to disable lazy load site-wide
        add_filter('wp_lazy_loading_enabled', [$this, 'disable_lazy_load_site_wide']);
    }

    /**
     * Constructor for admin hooks
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function construct_admin()
    {
        // Set the text for the admin page
        $this->menu_title = __('Control Lazy Load', 'jlCLL');
        $this->menu_why_use_plugin = __('WordPress 5.5 introduced image lazy loading as a native feature, and by default all images, except for the first on on the page, will have the lazy-load attribute added. Sometimes, the first image on the page is not the pages LCP, and this plugin allows you to disable lazy loading site-wide, per post, or for an individual image to help improve Lighthouse scores.', 'jlCLL');
        $this->menu_description = __(
            'Use the setting on this page to disable the lazy load feature of WordPress Core on every image on your site. If you wish to disable lazy load on a per-post basis, you can do so under the page/post settings in the post editor. If you wish to disable lazy load on a per-image basis, you can do so under the image settings in the image block editor. Disabling lazy load site-wide will disable lazy load on all images on your site, regardless of the post or image settings. Settings are automatically deleted when the plugin is deactivated.',
            'jlCLL'
        );
        $this->setting_section_name = __('Site-Wide Settings', 'jlCLL');
        $this->setting_section_description = __('The below option applies to the entire site and overrides per-post or per-image settings.', 'jlCLL');
        $this->setting_label = __('Disable lazy load site-wide', 'jlCLL');

        // Add an admin page to the settings menu
        add_action('admin_menu', [$this, 'add_menu']);

        // Add a link to settings before hte deactivation link on the plugins page
        add_filter('plugin_action_links_' . plugin_basename(JLCLL_PLUGIN_FILE), [$this, 'add_settings_link']);

        // Register settings for disabling lazy load site-wide
        add_action('admin_init', [$this, 'register_settings']);

        // Add settings sections to the admin page
        add_action('admin_init', [$this, 'add_settings_sections']);

        // Add settings fields to the admin page
        add_action('admin_init', [$this, 'add_settings_fields']);
    }

    /**
     * Register settings for disabling lazy load site-wide
     * 
     * @since 1.0.0
     * @version 1.0.0 
     */
    public function register_settings()
    {
        register_setting(
            $this::MENU_SLUG,
            JLCLL_OPTION_KEY_DISABLE_SITE_WIDE,
            [
                'type' => 'boolean',
                'description' => $this->setting_label,
                'sanitize_callback' => 'sanitize_text_field',
                'default' => false
            ]
        );
    }

    /**
     * Add an admin page to the settings menu
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function add_menu()
    {
        add_options_page(
            $this->menu_title,
            $this->menu_title,
            'manage_options',
            $this::MENU_SLUG,
            [$this, 'settings_page']
        );
    }

    /**
     * Add a link to the admin page on the plugin's row in the plugins list
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function add_settings_link($links)
    {
        $settings_link = '<a href="options-general.php?page=' . $this::MENU_SLUG . '">' . __('Settings', 'jlCLL') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    /**
     * Add settings sections to the admin page
     * 
     * @since 1.0.0
     * @version 1.0.0
     * 
     */
    public function add_settings_sections()
    {
        add_settings_section(
            JLCLL_OPTION_KEY_DISABLE_SITE_WIDE,
            $this->setting_section_name,
            function () {
                echo '<p>' . $this->setting_section_description . '</p>';
            },
            $this::MENU_SLUG
        );
    }

    /**
     * Add settings fields to the admin page
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function add_settings_fields()
    {
        add_settings_field(
            JLCLL_OPTION_KEY_DISABLE_SITE_WIDE,
            '<label for="' . JLCLL_OPTION_KEY_DISABLE_SITE_WIDE . '">' . $this->setting_label . '</form>',
            function () {
                echo '<input type="checkbox" name="' . JLCLL_OPTION_KEY_DISABLE_SITE_WIDE . '" id="' . JLCLL_OPTION_KEY_DISABLE_SITE_WIDE . '" value="1" ' . checked(get_option(JLCLL_OPTION_KEY_DISABLE_SITE_WIDE), true, false) . '>';
            },
            $this::MENU_SLUG,
            JLCLL_OPTION_KEY_DISABLE_SITE_WIDE
        );
    }

    /**
     * The settings page for the plugin
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function settings_page()
    {
        echo <<< HTML
        <div class="wrap">
            <h1>$this->menu_title</h1>
            <p>$this->menu_why_use_plugin</p>
            <p>$this->menu_description</p>
            <form method="post" action="options.php">
        HTML;

        do_settings_sections($this::MENU_SLUG);
        settings_fields($this::MENU_SLUG);
        submit_button();

        echo <<< HTML
            </form>
        </div>
        HTML;
    }

    /**
     * Disable lazy load site-wide
     * 
     * If the user has disabled lazy load site-wide, return false. Otherwise, return the default value.
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function disable_lazy_load_site_wide($default)
    {
        $disable_site_wide = get_option(JLCLL_OPTION_KEY_DISABLE_SITE_WIDE);

        if ($disable_site_wide) return false;

        return $default;
    }

    /**
     * Deactivate the site-wide settings
     * 
     * @since 1.0.0
     * @version 1.0.0
     */
    public function deactivate()
    {
        delete_option(JLCLL_OPTION_KEY_DISABLE_SITE_WIDE);
    }
}
