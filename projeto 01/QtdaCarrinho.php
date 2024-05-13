<?php

add_filter('woocommerce_cart_item_quantity', 'replace_cart_quantity_for_bookings', 20, 3);
function replace_cart_quantity_for_bookings($quantity, $cart_item_key, $cart_item)
{
    // Somente para itens de produto reservável
    if (isset($cart_item['booking']) && isset($cart_item['booking']['_qty'])) {
        // Atualiza a quantidade no carrinho
        //WC()->cart->set_quantity( $cart_item_key, $cart_item['booking']['_qty'] );

        // Retorna a quantidade formatada
        return $cart_item['booking']['_qty'] . ' <span style="text-align:center; display:inline-block; line-height:1px"><br>
        <small>(' . __('Ingressos', 'woocommerce') . ')</small><span>';
    }

    return $quantity;
}
