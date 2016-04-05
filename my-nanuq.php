<?php

/*
Plugin Name: My Nanuq
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 0.1.0
Author: yannick
Author URI: http://URI_Of_The_Plugin_Author
License: A "Slug" license name e.g. GPL2
*/

if (!defined('WPINC')) {
    echo "What do you think you're doing ??";
    error_log("Direct inclusion...");
    die;
}

// Check if WooCommerce is active
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action("woocommerce_shipping_init", function() {
        if (!class_exists('WC_MyNanuq_Livraison')) {
            class WC_MyNanuq_Livraison extends WC_Shipping_Method {
                /**
                 * Constructor
                 *
                 * @access public
                 * @return void
                 */
                public function __construct()
                {
                    $this->id = 'my_nanuq_livraison';
                    $this->title = __('Via La Poste, en fonction du poids');
                    $this->method_description = __('Livraison par La Poste suisse, tarif en fonction du poids.');
                    $this->enabled = 'yes';
                    $this->init();
                }

                /**
                 * Init settings
                 *
                 * @access public
                 * @return void
                 */
                public function init()
                {
                    // Load the settings API
                    $this->init_form_fields();
                    $this->init_settings();
                    add_action("woocommerce_update_options_shipping_" . $this->id, [$this, 'process_admin_options']);
                }

                /**
                 * Calculate shipping
                 *
                 * @access public
                 * @param mixed $package
                 * @return void
                 */
                public function calculate_shipping($package)
                {
                    error_log("package : " . json_encode($package), 4);
                    $rate = [
                        "id" => $this->id,
                        "label" => $this->title,
                        "cost" => "10.99",
                        "calc_tax" => "per_order"
                    ];

                    // Register the rate
                    $this->add_rate($rate);
                }
            }
        }
    });

    add_filter("woocommerce_shipping_methods", function($methods) {
        $methods[] = "WC_MyNanuq_Livraison";
        return $methods;
    });

}
