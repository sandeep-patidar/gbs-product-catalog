<?php
/**
* A Simple Category Template
*/
get_header(); ?>

<?php 
	global $post; global $gbspc_currency;
	$classes = get_post_class( '', $post->ID );
?>
<article id="post-<?php echo get_the_id()?>" class="<?php echo esc_attr( implode( ' ', $classes ) )?>">
	<h1 class="archive-title"><?php single_term_title(); ?></h1>
	<div class="gbs-product-container">
	<div class="gbs-product-wrapper">
	<?php if ( have_posts() ) : while (have_posts() ) : the_post(); 
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
	    </div><!-- .product-wrap -->
	   <?php endwhile; 
	endif;
	echo "</div></div></article>";
get_footer(); ?>