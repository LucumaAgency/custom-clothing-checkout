<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WCCDPE_Ajax {

    public function __construct() {
        // Update session on checkout update (update_checkout triggers this)
        add_action( 'woocommerce_checkout_update_order_review', [ $this, 'update_session_from_post' ] );
    }

    /**
     * Parse posted checkout data and store in session so fees can read it.
     */
    public function update_session_from_post( $posted_data ) {
        parse_str( $posted_data, $data );

        $tipo = isset( $data['billing_tipo_entrega'] ) ? sanitize_text_field( wp_unslash( $data['billing_tipo_entrega'] ) ) : '';
        $valid_types = array_keys( WCCDPE_Data::get_delivery_types() );
        if ( ! in_array( $tipo, $valid_types, true ) ) {
            $tipo = '';
        }
        WC()->session->set( 'wccdpe_tipo_entrega', $tipo );

        $distrito = isset( $data['billing_lima_distrito'] ) ? sanitize_text_field( wp_unslash( $data['billing_lima_distrito'] ) ) : '';
        if ( $distrito && ! array_key_exists( $distrito, WCCDPE_Data::get_lima_districts_with_prices() ) ) {
            $distrito = '';
        }
        WC()->session->set( 'wccdpe_lima_distrito', $distrito );
    }
}

new WCCDPE_Ajax();
