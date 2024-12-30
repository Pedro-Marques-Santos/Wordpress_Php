<?php

add_action('woocommerce_before_order_notes', 'detalhes_pessoa');

function detalhes_pessoa($checkout)
{
    global $woocommerce;
    $items = $woocommerce->cart->get_cart();

    $first_item;

    if (!empty($items)) {
        $first_item = reset($items);
        echo '<h3 style="margin-top:15px;color: #FF0000;margin-bottom: 50px">ATENÇÃO: É obrigatória a apresentação de documentos na entrada! Preencha abaixo:</h3>';
    }

    foreach ($items as $item_key => $item_values) {
        if (isset($item_values['booking']) && isset($item_values['booking']['_qty'])) {
            $qty = $item_values['booking']['_qty'];

            // Criando uma lista de objetos
            $lista_de_objetos = array();

            if (isset($item_values['booking']["Adultos (a partir de 12 anos)"]) && $item_values['booking']["Adultos (a partir de 12 anos)"] > 0) {
                $objeto_adulto = new stdClass();
                $objeto_adulto->name = "Adultos";
                $objeto_adulto->quantidade = $item_values['booking']["Adultos (a partir de 12 anos)"];
                $lista_de_objetos[] = $objeto_adulto;
            }

            // Verificando e criando objeto para crianças até 6 anos
            if (isset($item_values['booking']["Crianças (de 4 a 11 anos)"]) && $item_values['booking']["Crianças (de 4 a 11 anos)"] > 0) {
                $objeto_crianca = new stdClass();
                $objeto_crianca->name = "Crianças (de 4 a 11 anos)";
                $objeto_crianca->quantidade = $item_values['booking']["Crianças (de 4 a 11 anos)"];
                $lista_de_objetos[] = $objeto_crianca;
            }

            // Verificando e criando objeto para crianças de 7 a 12 anos
            if (isset($item_values['booking']["Crianças (até 03 anos)"]) && $item_values['booking']["Crianças (até 03 anos)"] > 0) {
                $objeto_crianca2 = new stdClass();
                $objeto_crianca2->name = "Crianças (até 03 anos)";
                $objeto_crianca2->quantidade = $item_values['booking']["Crianças (até 03 anos)"];
                $lista_de_objetos[] = $objeto_crianca2;
            }


            // Verificando e criando objeto para Adultos (a partir de 12 anos): Bariátricos, PCD, e Idosos (+60 anos)
            if (isset($item_values['booking']["Adultos (a partir de 12 anos): Bariátricos, PCD, e Idosos (+60 anos)"]) && $item_values['booking']["Adultos (a partir de 12 anos): Bariátricos, PCD, e Idosos (+60 anos)"] > 0) {
                $objeto_adulto = new stdClass();
                $objeto_adulto->name = "Bariátricos, PCD, e Idosos (+60 anos)";
                $objeto_adulto->quantidade = $item_values['booking']["Adultos (a partir de 12 anos): Bariátricos, PCD, e Idosos (+60 anos)"];
                $lista_de_objetos[] = $objeto_adulto;
            }

            // Verificando e criando objeto para Crianças (de 4 a 11 anos): PCD
            if (isset($item_values['booking']["Crianças (de 4 a 11 anos): PCD"]) && $item_values['booking']["Crianças (de 4 a 11 anos): PCD"] > 0) {
                $objeto_crianca3 = new stdClass();
                $objeto_crianca3->name = "Crianças (de 4 a 11 anos): PCD";
                $objeto_crianca3->quantidade = $item_values['booking']["Crianças (de 4 a 11 anos): PCD"];
                $lista_de_objetos[] = $objeto_crianca3;
            }

            // Verificando e criando objeto para Pais ou Tutores
            if (isset($item_values['booking']["Pais ou Tutores"]) && $item_values['booking']["Pais ou Tutores"] > 0) {
                $objeto_adulto = new stdClass();
                $objeto_adulto->name = "Pais ou Tutores";
                $objeto_adulto->quantidade = $item_values['booking']["Pais ou Tutores"];
                $lista_de_objetos[] = $objeto_adulto;
            }

            // Verificando e criando objeto para Crianças (de 4 a 11 anos): PCD
            if (isset($item_values['booking']["Pessoa com Autismo"]) && $item_values['booking']["Pessoa com Autismo"] > 0) {
                $objeto_crianca3 = new stdClass();
                $objeto_crianca3->name = "Pessoa com Autismo";
                $objeto_crianca3->quantidade = $item_values['booking']["Pessoa com Autismo"];
                $lista_de_objetos[] = $objeto_crianca3;
            }

            // Exibindo a lista de objetos
            $contador = 0;
            foreach ($lista_de_objetos as $key => $objeto) {
                if ($objeto->quantidade > 0) {
                    for ($i = 1; $i <= $objeto->quantidade; $i++) {
                        echo '<h3 style="margin-top:20px; color: #074E9F;">Dados para: ' . $objeto->name . '</h3>';
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

            if (isset($cart_item['booking']["Adultos (a partir de 12 anos)"]) && $cart_item['booking']["Adultos (a partir de 12 anos)"] > 0) {
                $objeto_adulto = new stdClass();
                $objeto_adulto->name = "Adultos";
                $objeto_adulto->quantidade = $cart_item['booking']["Adultos (a partir de 12 anos)"];
                $lista_de_objetos[] = $objeto_adulto;
            }

            // Verificando e criando objeto para crianças até 6 anos
            if (isset($cart_item['booking']["Crianças (de 4 a 11 anos)"]) && $cart_item['booking']["Crianças (de 4 a 11 anos)"] > 0) {
                $objeto_crianca = new stdClass();
                $objeto_crianca->name = "Crianças (de 4 a 11 anos)";
                $objeto_crianca->quantidade = $cart_item['booking']["Crianças (de 4 a 11 anos)"];
                $lista_de_objetos[] = $objeto_crianca;
            }

            // Verificando e criando objeto para crianças de 7 a 12 anos
            if (isset($cart_item['booking']["Crianças (até 03 anos)"]) && $cart_item['booking']["Crianças (até 03 anos)"] > 0) {
                $objeto_crianca2 = new stdClass();
                $objeto_crianca2->name = "Crianças (até 03 anos)";
                $objeto_crianca2->quantidade = $cart_item['booking']["Crianças (até 03 anos)"];
                $lista_de_objetos[] = $objeto_crianca2;
            }


            // Verificando e criando objeto para crianças de Pais/Responsáveis
            if (isset($cart_item['booking']["Adultos (a partir de 12 anos): Bariátricos, PCD, e Idosos (+60 anos)"]) && $cart_item['booking']["Adultos (a partir de 12 anos): Bariátricos, PCD, e Idosos (+60 anos)"] > 0) {
                $objeto_adulto = new stdClass();
                $objeto_adulto->name = "Adultos: Bariátricos, PCD, e Idosos (+60 anos)";
                $objeto_adulto->quantidade = $cart_item['booking']["Adultos (a partir de 12 anos): Bariátricos, PCD, e Idosos (+60 anos)"];
                $lista_de_objetos[] = $objeto_adulto;
            }

            // Verificando e criando objeto para Pessoas autistas e crianças até 6 anos
            if (isset($cart_item['booking']["Crianças (de 4 a 11 anos): PCD"]) && $cart_item['booking']["Crianças (de 4 a 11 anos): PCD"] > 0) {
                $objeto_crianca2 = new stdClass();
                $objeto_crianca2->name = "Crianças (de 4 a 11 anos): PCD";
                $objeto_crianca2->quantidade = $cart_item['booking']["Crianças (de 4 a 11 anos): PCD"];
                $lista_de_objetos[] = $objeto_crianca2;
            }

            // Verificando e criando objeto para Pessoas autistas e crianças até 6 anos
            if (isset($cart_item['booking']["Pais ou Tutores"]) && $cart_item['booking']["Pais ou Tutores"] > 0) {
                $objeto_crianca2 = new stdClass();
                $objeto_crianca2->name = "Pais ou Tutores";
                $objeto_crianca2->quantidade = $cart_item['booking']["Pais ou Tutores"];
                $lista_de_objetos[] = $objeto_crianca2;
            }

            // Verificando e criando objeto para Pessoas autistas e crianças até 6 anos
            if (isset($cart_item['booking']["Pessoa com Autismo"]) && $cart_item['booking']["Pessoa com Autismo"] > 0) {
                $objeto_crianca2 = new stdClass();
                $objeto_crianca2->name = "Pessoa com Autismo";
                $objeto_crianca2->quantidade = $cart_item['booking']["Pessoa com Autismo"];
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
