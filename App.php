<?php

namespace ForceSSL;

class App
{
    public function __construct()
    {
        add_action('template_redirect', array($this, 'redirectToSSL'));
        add_action('admin_init', array($this, 'redirectToSSL'));
        add_action('all_plugins', array($this, 'preventMultisiteActivation'));
    }

    public function redirectToSSL()
    {
        if (!is_ssl()) {
            wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301);
            exit();
        }
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
