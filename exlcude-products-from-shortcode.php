<?php
function custom_products_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'on_sale' => 'true', // Display only products on sale
        'exclude_category' => 'outlet', // Category slug to exclude
        'columns' => '4', // Number of columns for product display
    ), $atts, 'custom_products' );

    // Set the number of columns
    global $woocommerce_loop;
    $woocommerce_loop['columns'] = $atts['columns'];

    // Query arguments
    $args = array(
        'post_type' => 'product',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_sale_price',
                'value' => 0,
                'compare' => '>',
                'type' => 'NUMERIC'
            )
        )
    );

    // Exclude category if provided
    if ( ! empty( $atts['exclude_category'] ) ) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'product_cat',
                'field' => 'slug',
                'terms' => $atts['exclude_category'],
                'operator' => 'NOT IN'
            )
        );
    }

    // Get products
    $products = new WP_Query( $args );

    // Output products
    ob_start();

    if ( $products->have_posts() ) {
        woocommerce_product_loop_start();
        while ( $products->have_posts() ) {
            $products->the_post();
            wc_get_template_part( 'content', 'product' );
        }
        woocommerce_product_loop_end();
    } else {
        echo 'No products found';
    }

    wp_reset_postdata();

    return ob_get_clean();
}
add_shortcode( 'exclude_products', 'custom_products_shortcode' );

// Shortcode - [exclude_products exclude_category="outlet" on_sale="true" columns="4" ]