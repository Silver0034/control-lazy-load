# Control Lazy Load

Control Lazy Load is a WordPress plugin that allows you to disable WordPress Core's image lazy load on a per-image, per-post, or site-wide basis.

## Table of Contents

-   [About the Plugin](#about-the-plugin)
    -   [Features](#features)
    -   [Why This Plugin Is Needed](#why-this-plugin-is-needed)
    -   [Requirements](#requirements)
    -   [Plugin Contents](#plugin-contents)
-   [Installation](#installation)
-   [Usage](#usage)
-   [Author](#author)

## About the Plugin

### Features

-   Use the added settings page to disable lazy loading site-wide.
-   Use the post editor to disable lazy loading for individual posts.
-   Use the block editor to disable lazy loading for individual images.
-   Stay up-to-date with the latest GitHub release directly through the WordPress updater.

### Why This Plugin Is Needed

WordPress 5.5 introduced image lazy loading as a native feature, and by default all images, except for the first one on the page, will have the lazy-load attribute added. Sometimes, the first image on the page is not the page's LCP (Largest Contentful Paint), and this plugin allows you to disable lazy loading site-wide, per post, or for an individual image to help improve Lighthouse scores.

### Requirements

This plugin requires WordPress 5.5 or later.

### Plugin Contents

This plugin contains the following files:

-   `control-lazy-load.php`: The main plugin file.
-   `readme.md`: The plugin's readme file.
-   `LICENSE`: The plugin's license file.
-   `js/`: The plugin's javascript directory.
    -   `block-editor.js`: Adds block-level settings.
    -   `post-editor.js`: Adds post-level settings.
-   `includes/`: The plugin's includes directory.
    -   `class-block-editor.php`: Adds block-level settings.
    -   `class-post-editor.php`: Adds post-level settings.
    -   `class-site-wide.php`: Adds site-wide settings.
    -   `class-updater.php`: Adds GitHub updater.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/control-lazy-load` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.

## Usage

After installation, you can find the settings for this plugin in the following locations:

-   Global settings: Navigate to Settings > Control Lazy Load in the WordPress admin area.
-   Per-post settings: These are available in the post block editor under the post's settings.
-   Per-block settings: These are available under the block's settings when you have an image block selected in the block editor.

## Author

This plugin was created by [Jacob Lodes](https://www.jlodes.com/).
