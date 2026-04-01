<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class WCCDPE_Validation {

    public function __construct() {
        add_action( 'woocommerce_checkout_process', [ $this, 'validate_fields' ] );
    }

    public function validate_fields() {
        if ( ! isset( $_POST['wccdpe_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wccdpe_nonce'] ) ), 'wccdpe_checkout' ) ) {
            wc_add_notice( 'Error de seguridad. Por favor recarga la página e intenta de nuevo.', 'error' );
            return;
        }

        $dni = isset( $_POST['billing_dni'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_dni'] ) ) : '';
        if ( empty( $dni ) ) {
            wc_add_notice( 'Por favor ingresa tu número de DNI.', 'error' );
        } elseif ( ! preg_match( '/^\d{8}$/', $dni ) ) {
            wc_add_notice( 'El DNI debe tener exactamente 8 dígitos.', 'error' );
        }

        $tipo = isset( $_POST['billing_tipo_entrega'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_tipo_entrega'] ) ) : '';

        if ( empty( $tipo ) ) {
            wc_add_notice( 'Por favor selecciona un tipo de entrega.', 'error' );
            return;
        }

        $valid_types = array_keys( WCCDPE_Data::get_delivery_types() );
        if ( ! in_array( $tipo, $valid_types, true ) ) {
            wc_add_notice( 'Tipo de entrega no válido.', 'error' );
            return;
        }

        switch ( $tipo ) {
            case 'lima_24h':
            case 'lima_48h':
                $distrito = isset( $_POST['billing_lima_distrito'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_lima_distrito'] ) ) : '';
                if ( empty( $distrito ) ) {
                    wc_add_notice( 'Por favor selecciona un distrito de Lima.', 'error' );
                } elseif ( $tipo === 'lima_24h' && ! array_key_exists( $distrito, WCCDPE_Data::get_lima_districts_with_prices() ) ) {
                    wc_add_notice( 'Distrito de Lima no válido.', 'error' );
                }
                $direccion = isset( $_POST['billing_direccion'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_direccion'] ) ) : '';
                if ( empty( $direccion ) ) {
                    wc_add_notice( 'Por favor ingresa tu dirección de entrega.', 'error' );
                }
                break;

            case 'provincia_shalom':
            case 'contraentrega_shalom':
                $depto = isset( $_POST['billing_departamento'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_departamento'] ) ) : '';
                if ( empty( $depto ) ) {
                    wc_add_notice( 'Por favor selecciona un departamento.', 'error' );
                }
                $agencia = isset( $_POST['billing_agencia_shalom'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_agencia_shalom'] ) ) : '';
                if ( empty( $agencia ) ) {
                    wc_add_notice( 'Por favor ingresa el nombre de la agencia Shalom.', 'error' );
                }
                break;

            case 'provincia_olva':
                $depto = isset( $_POST['billing_olva_departamento'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_olva_departamento'] ) ) : '';
                if ( empty( $depto ) ) {
                    wc_add_notice( 'Por favor selecciona un departamento.', 'error' );
                }
                $sub = isset( $_POST['billing_olva_sub_tipo'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_olva_sub_tipo'] ) ) : '';
                if ( empty( $sub ) ) {
                    wc_add_notice( 'Por favor selecciona si deseas envío a domicilio o recojo en agencia Olva.', 'error' );
                } elseif ( ! in_array( $sub, [ 'domicilio', 'agencia' ], true ) ) {
                    wc_add_notice( 'Opción de recepción Olva no válida.', 'error' );
                } elseif ( $sub === 'domicilio' ) {
                    $dir = isset( $_POST['billing_olva_direccion'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_olva_direccion'] ) ) : '';
                    if ( empty( $dir ) ) {
                        wc_add_notice( 'Por favor ingresa tu dirección para el envío a domicilio (Olva).', 'error' );
                    }
                } elseif ( $sub === 'agencia' ) {
                    $ag = isset( $_POST['billing_olva_agencia_nombre'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_olva_agencia_nombre'] ) ) : '';
                    if ( empty( $ag ) ) {
                        wc_add_notice( 'Por favor ingresa el nombre de la agencia Olva.', 'error' );
                    }
                }
                break;

            case 'recojo_tienda':
                $tienda = isset( $_POST['billing_tienda_especifica'] ) ? sanitize_text_field( wp_unslash( $_POST['billing_tienda_especifica'] ) ) : '';
                if ( empty( $tienda ) ) {
                    wc_add_notice( 'Por favor selecciona una tienda para recojo.', 'error' );
                }
                break;
        }
    }
}

new WCCDPE_Validation();
