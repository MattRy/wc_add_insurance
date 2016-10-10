<?php
/**
 * Add an insurance surcharge to your cart / checkout
 * change the insurance amount based on value of cart
 * Uses the WooCommerce fees API
 * @author Matt Ryan | 10/05/2016
 *
 */
add_action( 'woocommerce_cart_calculate_fees','emare_woocommerce_add_insurance_surcharge' );
function emare_woocommerce_add_insurance_surcharge() {
  global $woocommerce;

    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;

    $chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
    $chosen_shipping = $chosen_methods[0];
    if ( strpos($chosen_shipping, 'usps') !== false ) {
        $insurance_fee = 0;
        $check_val = $woocommerce->cart->cart_contents_total; 

/**
 * USPS insurance rates from http://pe.usps.com/text/dmm300/Notice123.htm
 *  $5000 max insured value domestically/internationally
 */
        $insurance_fee = 0;
        if ( $woocommerce->customer->get_shipping_country() == 'US' ) {
            $cart_label = "Insurance";
            if ( ( 0 < $check_val ) && ( $check_val <= 50 ) )           { $insurance_fee = 0;     // no insurance required
            } elseif ( ( 50  < $check_val ) && ( $check_val <= 100 ) )  { $insurance_fee = 2.65; 
            } elseif ( ( 100 < $check_val ) && ( $check_val <= 200 ) )  { $insurance_fee = 3.35; 
            } elseif ( ( 200 < $check_val ) && ( $check_val <= 300 ) )  { $insurance_fee = 4.35; 
            } elseif ( ( 300 < $check_val ) && ( $check_val <= 400 ) )  { $insurance_fee = 5.50; 
            } elseif ( ( 400 < $check_val ) && ( $check_val <= 500 ) )  { $insurance_fee = 6.65; 
            } elseif ( ( 500 < $check_val ) && ( $check_val <= 600 ) )  { $insurance_fee = 9.05; 
            } elseif ( ( 600 < $check_val ) && ( $check_val <= 5000 ) ) { $insurance_fee = 9.05 + 1.25 * ( intval( ( $check_val - 600 ) / 100 + 1 ) ); 
            } else $insurance_fee = -1;     // no insurance available cart contents over the limit
        } else { // international Priority Mail Express & Priority Mail shipping to Canada
            $cart_label = "Int'l Insurance";
            if ( ( 0 < $check_val ) && ( $check_val <= 200 ) )              { $insurance_fee = 0;     // no insurance required
                } elseif ( ( 200 < $check_val ) && ( $check_val <= 300 ) )  { $insurance_fee = 5.00;
                } elseif ( ( 300 < $check_val ) && ( $check_val <= 400 ) )  { $insurance_fee = 6.15; 
                } elseif ( ( 400 < $check_val ) && ( $check_val <= 500 ) )  { $insurance_fee = 7.30; 
                } elseif ( ( 500 < $check_val ) && ( $check_val <= 600 ) )  { $insurance_fee = 8.45; 
                } elseif ( ( 600 < $check_val ) && ( $check_val <= 700 ) )  { $insurance_fee = 9.60; 
                } elseif ( ( 700 < $check_val ) && ( $check_val <= 800 ) )  { $insurance_fee = 10.75; 
                } elseif ( ( 800 < $check_val ) && ( $check_val <= 900 ) )  { $insurance_fee = 11.90; 
                } elseif ( ( 900 < $check_val ) && ( $check_val <= 5000 ) ) { $insurance_fee = 11.90 + 1.15 * ( intval( ( $check_val - 900 ) / 100 + 1 ) ); 
                } else $insurance_fee = -1;     // no insurance available cart contents over the limit
        }

        if ( $insurance_fee > 0 ) $woocommerce->cart->add_fee( $cart_label, $insurance_fee, true, '' );
        
    }
    return;
