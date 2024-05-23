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
        echo '<h3 style="margin-top:15px;color: #000000;margin-bottom: 15px">OBS: Na compra de mais de um ingresso, preencha as informações dos demais visitantes (nome, telefone e e-mail)</h3>';
        echo '<h3 style="margin-top:15px;color: #FF0000;margin-bottom: 20px">Atenção: É obrigatório apresentar os documentos na entrada</h3>';
        echo '<h3 style="margin-top:15px;color: #000000;margin-bottom: 50px">Em casos de crianças, preencha o telefone e e-mail do responsável, mas coloque o nome da criança no campo (nome completo)</h3>';
    }

    foreach ($items as $item_key => $item_values) {
        if (isset($item_values['booking']) && isset($item_values['booking']['_qty'])) {
            $qty = $item_values['booking']['_qty'];

            // Criando uma lista de objetos
            $lista_de_objetos = array();

            if (isset($item_values['booking']["Pessoas"]) && $item_values['booking']["Pessoas"] > 0) {
                if ($first_item['key'] === $item_key) {
                    $objeto_adulto = new stdClass();
                    $objeto_adulto->name = "Pessoas";
                    $objeto_adulto->quantidade = $item_values['booking']["Pessoas"] - 1;
                    $lista_de_objetos[] = $objeto_adulto;
                } else {
                    $objeto_adulto = new stdClass();
                    $objeto_adulto->name = "Pessoas";
                    $objeto_adulto->quantidade = $item_values['booking']["Pessoas"];
                    $lista_de_objetos[] = $objeto_adulto;
                }
            }

            // Verificando e criando objeto para crianças até 6 anos
            if (isset($item_values['booking']["Crianças de 0 a 05 anos"]) && $item_values['booking']["Crianças de 0 a 05 anos"] > 0) {
                $objeto_crianca = new stdClass();
                $objeto_crianca->name = "Crianças até 5 anos";
                $objeto_crianca->quantidade = $item_values['booking']["Crianças de 0 a 05 anos"];
                $lista_de_objetos[] = $objeto_crianca;
            }

            // Verificando e criando objeto para crianças de 7 a 12 anos
            if (isset($item_values['booking']["Crianças de 06 a 10 anos"]) && $item_values['booking']["Crianças de 06 a 10 anos"] > 0) {
                $objeto_crianca2 = new stdClass();
                $objeto_crianca2->name = "Crianças de 6 a 10 anos";
                $objeto_crianca2->quantidade = $item_values['booking']["Crianças de 06 a 10 anos"];
                $lista_de_objetos[] = $objeto_crianca2;
            }

            // Exibindo a lista de objetos
            $contador = 0;
            foreach ($lista_de_objetos as $key => $objeto) {
                if ($objeto->quantidade > 0) {
                    for ($i = 1; $i <= $objeto->quantidade; $i++) {
                        echo '<h3 style="margin-top:20px; color: #074E9F;">Ingresso: ' . 'Pessoa' . ' ' . $contador + 2 . '</h3>';
                        woocommerce_form_field('cstm_full_name_' . $item_key . '_' . $contador, array(
                            'type'        => 'text',
                            'class'       => array('minha-classe-de-campo form-row-wide'),
                            'label'       => __('Nome Completo'),
                            'placeholder' => __('Insira o nome completo'),
                            'required'    => true,
                        ), $checkout->get_value('cstm_full_name_' . $item_key . '_' . $contador));
                        echo '<div class="clear"></div>';

                        woocommerce_form_field('cstm_telefone_' . $item_key . '_' . $contador, array(
                            'type'        => 'number',
                            'class'       => array('minha-classe-de-campo form-row-wide'),
                            'label'       => __('Telefone'),
                            'placeholder' => __('Insira o número de telefone'),
                            'required'    => true,
                            'custom_attributes' => array(
                                'pattern'     => '[0-9]*',
                            ),
                        ), $checkout->get_value('cstm_telefone_' . $item_key . '_' . $contador));

                        woocommerce_form_field('cstm_email_' . $item_key . '_' . $contador, array(
                            'type'        => 'text',
                            'class'       => array('minha-classe-de-campo form-row-wide'),
                            'label'       => __('Email'),
                            'placeholder' => __('Insira o email'),
                            'required'    => true,
                        ), $checkout->get_value('cstm_email_' . $item_key . '_' . $contador));
                        $contador++;
                    }
                }
            }

            // $qtd = $item_values['booking']['_qty'];
            // if ($item_values['booking']['_qty']) {
            //     if ($first_item['key'] === $item_key) {
            //         $newqtd = $qtd - 1;
            //         for ($i = 1; $i <= $newqtd; $i++) {
            //             echo '<h3 style="margin-top:20px;">tipo ' . $i . '</h3>';
            //         }
            //     } else {
            //         for ($i = 1; $i <= $qtd; $i++) {
            //             echo '<h3 style="margin-top:20px;">tipo ' . $i . '</h3>';
            //         }
            //     }
            // }
        }
    }
}

// Salva os valores dos campos personalizados no pedido
add_action('woocommerce_checkout_create_order', 'atualizar_meta_pedido_campo_personalizado');

function atualizar_meta_pedido_campo_personalizado($order)
{
    $items = WC()->cart->get_cart();

    $first_item;

    if (!empty($items)) {
        $first_item = reset($items);
    }
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        if (isset($cart_item['booking']) && isset($cart_item['booking']['_qty'])) {

            // Criando uma lista de objetos
            $lista_de_objetos = array();

            if (isset($cart_item['booking']["Pessoas"]) && $cart_item['booking']["Pessoas"] > 0) {
                if ($first_item['key'] === $cart_item_key) {
                    $objeto_adulto = new stdClass();
                    $objeto_adulto->name = "Pessoas";
                    $objeto_adulto->quantidade = $cart_item['booking']["Pessoas"] - 1;
                    $lista_de_objetos[] = $objeto_adulto;
                } else {
                    $objeto_adulto = new stdClass();
                    $objeto_adulto->name = "Pessoas";
                    $objeto_adulto->quantidade = $cart_item['booking']["Pessoas"];
                    $lista_de_objetos[] = $objeto_adulto;
                }
            }

            // Verificando e criando objeto para crianças até 6 anos
            if (isset($cart_item['booking']["Crianças de 0 a 05 anos"]) && $cart_item['booking']["Crianças de 0 a 05 anos"] > 0) {
                $objeto_crianca = new stdClass();
                $objeto_crianca->name = "Crianças até 5 anos";
                $objeto_crianca->quantidade = $cart_item['booking']["Crianças de 0 a 05 anos"];
                $lista_de_objetos[] = $objeto_crianca;
            }

            // Verificando e criando objeto para crianças de 7 a 12 anos
            if (isset($cart_item['booking']["Crianças de 06 a 10 anos"]) && $cart_item['booking']["Crianças de 06 a 10 anos"] > 0) {
                $objeto_crianca2 = new stdClass();
                $objeto_crianca2->name = "Crianças de 6 a 10 anos";
                $objeto_crianca2->quantidade = $cart_item['booking']["Crianças de 06 a 10 anos"];
                $lista_de_objetos[] = $objeto_crianca2;
            }

            $qtd = $cart_item['booking']['_qty'];
            if ($cart_item['booking']['_qty']) {
                if ($first_item['key'] === $cart_item_key) {
                    $newqtd = $qtd - 1;
                    $contador = 0;
                    for ($i = 1; $i <= $newqtd; $i++) {
                        if (empty($_POST['cstm_full_name_' . $cart_item_key . '_' . $contador])) {
                            throw new Exception('Por favor preencha todos os campos dos formulários!!');
                        }
                        if (empty($_POST['cstm_telefone_' . $cart_item_key . '_' . $contador])) {
                            throw new Exception('Por favor preencha todos os campos dos formulários!!');
                        }

                        if (empty($_POST['cstm_email_' . $cart_item_key . '_' . $contador])) {
                            throw new Exception('Por favor preencha todos os campos dos formulários!!');
                        }
                        $contador++;
                    }
                } else {
                    $contador = 0;
                    for ($i = 1; $i <= $qtd; $i++) {
                        if (empty($_POST['cstm_full_name_' . $cart_item_key . '_' . $i])) {
                            throw new Exception('Por favor preencha todos os campos dos formulários!!');
                        }
                        if (empty($_POST['cstm_telefone_' . $cart_item_key . '_' . $i])) {
                            throw new Exception('Por favor preencha todos os campos dos formulários!!');
                        }

                        if (empty($_POST['cstm_email_' . $cart_item_key . '_' . $i])) {
                            throw new Exception('Por favor preencha todos os campos dos formulários!!');
                        }
                        $contador++;
                    }
                }
            }

            // Exibindo a lista de objetos
            $contador = 0;
            foreach ($lista_de_objetos as $key => $objeto) {
                for ($i = 0; $i < $objeto->quantidade; $i++) {
                    if (!empty($_POST['cstm_full_name_' . $cart_item_key . '_' . $i])) {
                        $order->update_meta_data('Dados para: ' . 'Pessoa' . '_' . $contador . ' - Nome Completo', sanitize_text_field($_POST['cstm_full_name_' . $cart_item_key . '_' . $contador]));
                    }
                    if (!empty($_POST['cstm_telefone_' . $cart_item_key . '_' . $i])) {
                        $order->update_meta_data('Dados para: ' . 'Pessoa' . '_' . $contador . ' - Telefone', sanitize_text_field($_POST['cstm_telefone_' . $cart_item_key . '_' . $contador]));
                    }

                    if (!empty($_POST['cstm_email_' . $cart_item_key . '_' . $i])) {
                        $order->update_meta_data('Dados para: ' . 'Pessoa' . '_' . $contador . ' - Email', sanitize_text_field($_POST['cstm_email_' . $cart_item_key . '_' . $contador]));
                    }
                    $contador++;
                }
            }
        }
    }
    $order->save();
}
