<?php

namespace ForceSSL;

class App
{
    public function __construct()
    {
        //Redirects
        add_action('template_redirect', array($this, 'redirectToSSL'));
        add_action('admin_init', array($this, 'redirectToSSL'));
        add_action('login_init', array($this, 'redirectToSSL'));
        add_action('rest_api_init', array($this, 'redirectToSSL'));

        //Admin interface
        add_action('all_plugins', array($this, 'preventMultisiteActivation'));

        //Sanitazion
        add_filter('the_permalink', array($this, 'makeUrlProtocolLess'));
        add_filter('wp_get_attachment_url', array($this, 'makeUrlProtocolLess'));
        add_filter('wp_get_attachment_image_src', array($this, 'makeUrlProtocolLess'));
        add_filter('script_loader_src', array($this, 'makeUrlProtocolLess'));
        add_filter('style_loader_src', array($this, 'makeUrlProtocolLess'));
        add_filter('the_content', array($this, 'replaceInlineUrls'), 700);
        add_filter('widget_text', array($this, 'replaceInlineUrls'), 700);

        //Fix site url / home url
        add_filter('option_siteurl', array($this, 'makeUrlHttps'), 700);
        add_filter('option_home', array($this, 'makeUrlHttps'), 700);
    }

    public function redirectToSSL()
    {
        if (!is_ssl()) {
            if (!defined('NO_SSL_REDIRECT') || (defined('NO_SSL_REDIRECT') && NO_SSL_REDIRECT !== true)) {
                wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
                exit();
            }
        }
    }

    public function makeUrlProtocolLess($url)
    {
        return preg_replace("(^https?://)", "//", $url);
    }

    public function makeUrlHttps($url)
    {
        return preg_replace("(^https?://)", "https://", $url);
    }

    public function replaceInlineUrls($content)
    {
        return str_replace(home_url("/", "http"), home_url("/", "https"), $content);
    }

    public function preventMultisiteActivation($avabile_plugins)
    {
        global $current_screen;
        if ($current_screen->is_network) {
            unset($avabile_plugins['force-ssl/force-ssl.php']);
        }
        return $avabile_plugins;
    }
}
