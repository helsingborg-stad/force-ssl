<?php

namespace ForceSSL;

class App
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'backend'));
        add_action('template_redirect', array($this, 'frontend'));
        add_action('all_plugins', array($this, 'preventMultisiteActivation'));
    }

    public function frontend()
    {
        if (!is_ssl()) {
            wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
            exit();
        }
    }

    public function backend()
    {
        define('FORCE_SSL_ADMIN', true);
    }

    public function preventMultisiteActivation()
    {
        global $current_screen;

        if ($current_screen->is_network) {
            unset($all['force-ssl/force-ssl.php']);
        }
        return $all;
    }
}
