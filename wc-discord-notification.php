<?php
/**
 * Plugin Name: WC Discord Notification
 * Description: Sends a notification to a Discord channel when a new product is added.
 * Version: 1.0
 * Author: Mediax
 */

if (!defined('ABSPATH')) {
    exit;
}


add_action('publish_product', 'send_discord_notification', 10, 2);

function send_discord_notification($ID, $post) {
    
    $product = wc_get_product($ID);
    $product_name = $product->get_name();
    $product_url = get_permalink($ID);
    $product_image = get_the_post_thumbnail_url($ID);
    $product_price = $product->get_price();
    $currency_symbol = html_entity_decode(get_woocommerce_currency_symbol(), ENT_QUOTES, 'UTF-8');

    
    $embed = array(
        'title' => 'New Product Added',
        'description' => "**{$product_name}** has been added to your store!",
        'url' => $product_url,
        'color' => hexdec('7289DA'),
        'thumbnail' => array(
            'url' => $product_image
        ),
        'fields' => array(
            array(
                'name' => 'Price',
                'value' => $currency_symbol . ' ' . $product_price,
                'inline' => true
            )
        )
    );

    $message = array(
        'embeds' => array($embed)
    );

    $webhook_url = 'https://discord.com/api/webhooks/your_webhook_id/your_webhook_token';

    $response = wp_remote_post($webhook_url, array(
        'body'        => json_encode($message),
        'headers'     => array('Content-Type' => 'application/json'),
        'data_format' => 'body',
    ));


    if (is_wp_error($response)) {
        error_log('Discord Webhook Error: ' . $response->get_error_message());
    }
}
?>