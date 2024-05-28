<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$gbspc_currency = get_option('currency');
function gbspc_display(){
    global $post; global $gbspc_currency;
    $max_products = get_option('max_products');
    $products_order = get_option('products_order');

    $price_currency = '';
    if ( get_query_var('paged') ) {
        $paged = get_query_var('paged');
    } elseif ( get_query_var('page') ) {
        $paged = get_query_var('page');
    } else {
        $paged = 1;
    }

    $args = array('post_type'=>'gbspc-product', 'paged'=>$paged, 'posts_per_page'=>$max_products ,'order' => $products_order);
    $loop = new WP_Query( $args );

    echo '<div class="gbs-product-wrapper">';
    while ( $loop->have_posts() ) : $loop->the_post(); 
    	$price = get_post_meta( $post->ID , 'gbspc_price', true );
    if(!$price){
        $price_currency = '';
    }else{
        $price_currency = $gbspc_currency;
    }

    $pterms = gbspc_get_product_taxonomies();
?>
    <div class="product-wrap">         
		<div class="image"><a href="<?php the_permalink();?>"><?php the_post_thumbnail(array(460, 300))?></a></div>
        <div class="desc">
            <span class="title"><a href="<?php the_permalink();?>"><?php the_title()?></a></span>
            <span class="price"><span class="currency"><?php echo gbspc_get_currency_symbol($price_currency);?></span><?php echo $price;?></span>
            <?php the_excerpt();?>
        </div>
        <div class="meta"><?php echo $pterms;?></div>
    </div>  
   <?php endwhile; 
   echo  "</div>";
}
add_shortcode('gbscatalog','gbspc_display');