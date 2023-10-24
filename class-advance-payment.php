<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Advance_Payment')) {

    class Advance_Payment {

        /**
         * Initialize the class
         */
        public static function init() {

           $isChecked = Advance_Payment_Settings::pluign_check_enabled();
           if ($isChecked) {

            add_action('woocommerce_checkout_create_order', array(__CLASS__, 'add_advance_payment_to_order'), 10, 2);
            add_action('woocommerce_review_order_before_payment', array(__CLASS__, 'display_advance_payment_info'));
            add_action('woocommerce_admin_order_totals_after_total', array(__CLASS__, 'display_advance_payment_details'));
            add_action('woocommerce_email_order_meta', array(__CLASS__, 'add_advance_payment_details_to_email'), 10, 3);
            }
        
        }

        /**
         * Add advance payment to the order
         *
         * @param WC_Order $order
         * 
         */
        public static function add_advance_payment_to_order($order) {
           $isChecked = Advance_Payment_Settings::pluign_check_enabled();
           if ($isChecked) {
            
            // Save payment information to order meta
            $payment_information = isset($_POST['payment_information']) ? sanitize_textarea_field($_POST['payment_information']) : '';
            $order->update_meta_data('payment_information', $payment_information);

            $order->save();
        }
       } 


        /**
        * Display advance payment information on the checkout page
        */
        public static function display_advance_payment_info() {

           $isChecked = Advance_Payment_Settings::pluign_check_enabled();
           if ($isChecked) {

                //Display payment information box
                woocommerce_form_field('payment_information', array(
                    'type' => 'textarea',
                    'class' => array('form-row-wide'),
                    'label' => __('Payment Information', 'wc-advance-payment'),
                    'required' => true,
                ));

                echo '</div>';
        }
       } 

        /**
         * Display advance payment details in admin order page
         *
         * 
         */
        public static function display_advance_payment_details() {

           $isChecked = Advance_Payment_Settings::pluign_check_enabled();
           if ($isChecked) {

            global $post;
            // Check if we are on an order edit page
            if (is_admin() && get_post_type($post) === 'shop_order') {
                $order = wc_get_order($post);
                $payment_information = $order->get_meta('payment_information');
                echo '<div class="advance-payment-details" style="float: left; margin-left: 10px;">';
                echo '<p><strong>'. __('Payment Information:', 'wc-advance-payment') . '</strong> ' . esc_html($payment_information) . '</p>';
                echo '</div>';
            }        
         }
         
        } 
        /**
         * Add advance payment details to order email
         *
         * @param WC_Order $order
         * @param bool     $sent_to_admin
         * @param bool     $plain_text
         */
        public static function add_advance_payment_details_to_email($order, $sent_to_admin, $plain_text) {

          $isChecked = Advance_Payment_Settings::pluign_check_enabled();
          if ($isChecked) {

            $advance_payment = $order->get_meta('advance_payment');
            $payment_information = $order->get_meta('payment_information');

            echo '<h2>' . __('Advance Payment Details', 'wc-advance-payment') . '</h2>';
            echo '<p><strong>' . __('Advance Payment Amount:', 'wc-advance-payment') . '</strong> ' . wc_price($advance_payment) . '</p>';
            echo '<p><strong>' . __('Payment Information:', 'wc-advance-payment') . '</strong> ' . esc_html_e($payment_information,'wc-advance-payment') . '</p>';
          }
        }   

   } 
    Advance_Payment::init();
}