<?php

// Adiciona campos personalizados no checkout do WooCommerce
add_action('woocommerce_before_order_notes', 'detalhes_pessoa');

function detalhes_pessoa($checkout)
{
    global $woocommerce;
    $items = $woocommerce->cart->get_cart();

    $first_item;

    if (!empty($items)) {
        $first_item = reset($items);
        echo '<h3 style="margin-top:15px;color: #000000;margin-bottom: 50px">Adicione as identificações dos participantes na seção abaixo</h3>';
    }

    foreach ($items as $item_key => $item_values) {
        if (isset($item_values['booking']) && isset($item_values['booking']['_qty'])) {
            $qty = $item_values['booking']['_qty'];

            // Criando uma lista de objetos
            $lista_de_objetos = array();

            if (isset($item_values['booking']["Pessoas (Inteira)"]) && $item_values['booking']["Pessoas (Inteira)"] > 0) {
                $objeto_adulto = new stdClass();
                $objeto_adulto->name = "Adultos";
                $objeto_adulto->quantidade = $item_values['booking']["Pessoas (Inteira)"];
                $lista_de_objetos[] = $objeto_adulto;
            }

            // Verificando e criando objeto para crianças até 6 anos
            if (isset($item_values['booking']["Crianças (até 06 anos)"]) && $item_values['booking']["Crianças (até 06 anos)"] > 0) {
                $objeto_crianca = new stdClass();
                $objeto_crianca->name = "Crianças até 6 anos";
                $objeto_crianca->quantidade = $item_values['booking']["Crianças (até 06 anos)"];
                $lista_de_objetos[] = $objeto_crianca;
            }

            // Verificando e criando objeto para crianças de 7 a 12 anos
            if (isset($item_values['booking']["crianças (07 a 12 anos)"]) && $item_values['booking']["crianças (07 a 12 anos)"] > 0) {
                $objeto_crianca2 = new stdClass();
                $objeto_crianca2->name = "Crianças de 7 a 12 anos";
                $objeto_crianca2->quantidade = $item_values['booking']["crianças (07 a 12 anos)"];
                $lista_de_objetos[] = $objeto_crianca2;
            }


            // Verificando e criando objeto para crianças de Pais/Responsáveis
            if (isset($item_values['booking']["Pais/Responsáveis"]) && $item_values['booking']["Pais/Responsáveis"] > 0) {
                $objeto_adulto = new stdClass();
                $objeto_adulto->name = "Pais/Responsáveis";
                $objeto_adulto->quantidade = $item_values['booking']["Pais/Responsáveis"];
                $lista_de_objetos[] = $objeto_adulto;
            }

            // Verificando e criando objeto para Pessoas autistas e crianças até 6 anos
            if (isset($item_values['booking']["Pessoas autistas e crianças até 6 anos"]) && $item_values['booking']["Pessoas autistas e crianças até 6 anos"] > 0) {
                $objeto_crianca2 = new stdClass();
                $objeto_crianca2->name = "Pessoas autistas e crianças até 6 anos";
                $objeto_crianca2->quantidade = $item_values['booking']["Pessoas autistas e crianças até 6 anos"];
                $lista_de_objetos[] = $objeto_crianca2;
            }

            // Exibindo a lista de objetos
            $contador = 0;
            foreach ($lista_de_objetos as $key => $objeto) {
                if ($objeto->quantidade > 0) {
                    for ($i = 1; $i <= $objeto->quantidade; $i++) {
                        echo '<h3 style="margin-top:20px;">Por favor, insira os detalhes do participante - ' . $objeto->name . '</h3>';
                        woocommerce_form_field('cstm_full_name_' . $item_key . '_' . $contador, array(
                            'type'        => 'text',
                            'class'       => array('minha-classe-de-campo form-row-wide'),
                            'label'       => __('Nome Completo - ' . $objeto->name),
                            'placeholder' => __('Insira o nome completo'),
                            'required'    => true,
                        ), $checkout->get_value('cstm_full_name_' . $item_key . '_' . $contador));
                        echo '<div class="clear"></div>';

                        woocommerce_form_field('cstm_rg_' . $item_key . '_' . $contador, array(
                            'type'        => 'number',
                            'class'       => array('minha-classe-de-campo form-row-wide'),
                            'label'       => __('RG - ' . $objeto->name),
                            'placeholder' => __('Insira o RG'),
                            'required'    => true,
                            'custom_attributes' => array(
                                'pattern'     => '[0-9]*',
                            ),
                        ), $checkout->get_value('cstm_rg_' . $item_key . '_' . $contador));
                        $contador++;
                    }
                }
            }
        }
    }
}

// Salva os valores dos campos personalizados no pedido
add_action('woocommerce_checkout_create_order', 'atualizar_meta_pedido_campo_personalizado');

function atualizar_meta_pedido_campo_personalizado($order)
{
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if (isset($cart_item['booking']) && isset($cart_item['booking']['_qty'])) {

            // Criando uma lista de objetos
            $lista_de_objetos = array();

            if (isset($cart_item['booking']["Pessoas (Inteira)"]) && $cart_item['booking']["Pessoas (Inteira)"] > 0) {
                $objeto_adulto = new stdClass();
                $objeto_adulto->name = "Adultos";
                $objeto_adulto->quantidade = $cart_item['booking']["Pessoas (Inteira)"];
                $lista_de_objetos[] = $objeto_adulto;
            }

            // Verificando e criando objeto para crianças até 6 anos
            if (isset($cart_item['booking']["Crianças (até 06 anos)"]) && $cart_item['booking']["Crianças (até 06 anos)"] > 0) {
                $objeto_crianca = new stdClass();
                $objeto_crianca->name = "Crianças até 6 anos";
                $objeto_crianca->quantidade = $cart_item['booking']["Crianças (até 06 anos)"];
                $lista_de_objetos[] = $objeto_crianca;
            }

            // Verificando e criando objeto para crianças de 7 a 12 anos
            if (isset($cart_item['booking']["crianças (07 a 12 anos)"]) && $cart_item['booking']["crianças (07 a 12 anos)"] > 0) {
                $objeto_crianca2 = new stdClass();
                $objeto_crianca2->name = "Crianças de 7 a 12 anos";
                $objeto_crianca2->quantidade = $cart_item['booking']["crianças (07 a 12 anos)"];
                $lista_de_objetos[] = $objeto_crianca2;
            }


            // Verificando e criando objeto para crianças de Pais/Responsáveis
            if (isset($cart_item['booking']["Pais/Responsáveis"]) && $cart_item['booking']["Pais/Responsáveis"] > 0) {
                $objeto_adulto = new stdClass();
                $objeto_adulto->name = "Pais/Responsáveis";
                $objeto_adulto->quantidade = $cart_item['booking']["Pais/Responsáveis"];
                $lista_de_objetos[] = $objeto_adulto;
            }

            // Verificando e criando objeto para Pessoas autistas e crianças até 6 anos
            if (isset($cart_item['booking']["Pessoas autistas e crianças até 6 anos"]) && $cart_item['booking']["Pessoas autistas e crianças até 6 anos"] > 0) {
                $objeto_crianca2 = new stdClass();
                $objeto_crianca2->name = "Pessoas autistas e crianças até 6 anos";
                $objeto_crianca2->quantidade = $cart_item['booking']["Pessoas autistas e crianças até 6 anos"];
                $lista_de_objetos[] = $objeto_crianca2;
            }

            // Exibindo a lista de objetos
            $contador = 0;
            foreach ($lista_de_objetos as $key => $objeto) {
                for ($i = 0; $i < $objeto->quantidade; $i++) {
                    if (!empty($_POST['cstm_full_name_' . $cart_item_key . '_' . $i])) {
                        $order->update_meta_data('Dados para: ' . $objeto->name . '_' . $contador . ' - Nome Completo', sanitize_text_field($_POST['cstm_full_name_' . $cart_item_key . '_' . $contador]));
                    }
                    if (!empty($_POST['cstm_rg_' . $cart_item_key . '_' . $i])) {
                        $order->update_meta_data('Dados para: ' . $objeto->name . '_' . $contador . ' - RG', sanitize_text_field($_POST['cstm_rg_' . $cart_item_key . '_' . $contador]));
                    }
                    $contador++;
                }
            }
        }
    }
    $order->save();
}
