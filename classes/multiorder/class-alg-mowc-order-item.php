<?php
/**
 * Multi order for WooCommerce - Order Item meta
 *
 * @version 1.0.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! class_exists( 'Alg_MOWC_Order_Item' ) ) {

	class Alg_MOWC_Order_Item {

		/**
		 * Constructor
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 */
		function __construct() {

			// Displays the suborder item meta on order page
			add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'format_order_item_meta_data' ), 10, 2 );

			// Hides order item quantity
			add_filter( 'woocommerce_order_item_quantity_html', array( $this, 'hides_order_item_quantity' ) );

			Displays suborders on order received / order pay page
			add_filter( 'woocommerce_display_item_meta', array( $this, 'woocommerce_display_item_meta' ), 10, 3 );
		}

		/**
		 * Displays suborders on order received / order pay page
		 *
		 * @version  1.0.0
		 * @since    1.0.0
		 *
		 * @param                       $html
		 * @param WC_Order_Item_Product $item
		 * @param                       $args
		 *
		 * @return string
		 */
		public function woocommerce_display_item_meta( $html, WC_Order_Item_Product $item, $args ) {
			foreach ( $item->get_meta_data() as $meta ) {
				if ( $meta->key == Alg_MOWC_Order_Item_Metas::SUB_ORDER ) {
					$order = wc_get_order( (int) $meta->value );
					if ( $order ) {
						$html         = '<br /><strong>' . __( 'Suborder', 'multi-order-for-woocommerce' );
						$order_number = apply_filters( 'woocommerce_order_number', $order->get_id(), $order );
						$html         .= ' <a href="' . $order->get_view_order_url() . '">#' . $order_number . '</a></strong>';
					}
				}
			}

			return $html;
		}

		/**
		 * Hides order item quantity
		 *
		 * @version  1.0.0
		 * @since    1.0.0
		 *
		 * @param $html
		 *
		 * @return string
		 */
		public function hides_order_item_quantity( $html ) {
			if ( filter_var( get_option( Alg_MOWC_Settings_General::OPTION_DISABLE_ORDER_ITEM_QTY ), FILTER_VALIDATE_BOOLEAN ) ) {
				$html = '';
			}
			return $html;
		}

		/**
		 * Displays the suborder item meta on order page
		 *
		 * @version 1.0.0
		 * @since   1.0.0
		 *
		 * @param               $formatted_meta
		 * @param WC_Order_Item $order_item
		 *
		 * @return mixed
		 */
		public function format_order_item_meta_data( $formatted_meta, WC_Order_Item $order_item ) {
			if ( empty( $formatted_meta ) ) {
				return $formatted_meta;
			}

			foreach ($formatted_meta as $meta){
				if($meta->key==Alg_MOWC_Order_Item_Metas::SUB_ORDER){
					$order                                 = wc_get_order( (int) $meta->value );
					if($order){
						$meta->display_key   = __( 'Suborder', 'multi-order-for-woocommerce' );
						$order_number                          = apply_filters( 'woocommerce_order_number', $order->get_id(), $order );
						$meta->display_value = '<a href="' . admin_url( 'post.php?post=' . absint( $order->get_id() ) . '&action=edit' ) . '">#' . $order_number . '</a>';
					}
				}
			}

			return $formatted_meta;
		}


	}
}