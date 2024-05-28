<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
get_header();
?>

	<?php
	global $post; global $gbspc_currency;
	while (have_posts() ) : the_post(); 
	$price = get_post_meta( $post->ID , 'gbspc_price', true );
	if(!$price){
		$price_currency = '';
	  }else{
	    $price_currency = $gbspc_currency;
	  }
	$pterms = gbspc_get_product_taxonomies();
	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="gbs-product-container">
		<div class="gbs-product-wrapper">
		    <div class="product-content">
				<div class="product-box">
			        <?php if ( has_post_thumbnail() ) : ?>
			        <div class="product-image">
			               <?php the_post_thumbnail('full'); ?>
			        </div>
			        <?php else : ?>
						<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/default-250x200.jpg" /></a>
			        <?php endif; ?>
        	
			        <div class="inquiry-form">
				    	<h2>Product Enquiry Form</h2>
						<form name="contactform" id="gbspc-contact-form" method="post">
						<input type="hidden" name="action" value="submit_inquiry">
						<table width="100%">
							<tr>
							 	<td valign="top">
							  		<label for="name">Your Name (required)</label>
								</td>
								<td valign="top">
								  	<input  type="text" name="your_name" id="your_name" maxlength="50" size="30">
								</td>
							</tr>
							<tr>
								<td valign="top">
								  	<label for="email">Your Email (required)</label>
								</td>
								<td valign="top">
								  	<input  type="text" name="your_email" id="your_email" maxlength="80" size="30">
								</td>
							</tr>
							<tr>
								<td valign="top">
								  	<label for="product-name">Product Name</label>
								</td>
								<td valign="top">
								  	<input  type="text" id="product_name" name="product_name" maxlength="30" size="30" value="<?php the_title();?>">
								</td>
							</tr>
							<tr>
							 	<td valign="top">
							  		<label for="comments">Your Message</label>
								</td>
							 	<td valign="top">
							  		<textarea name="your_message" id="your_message" maxlength="1000" cols="25" rows="4"></textarea>
							 	</td>
							</tr>
							<tr>
							 	<td colspan="2" style="text-align:center">
							 		<input id="submit" class="button" name="submit" type="submit" value="Send">
							 	</td>
							</tr>
						</table>
						</form>
					</div>							
				</div>
				<div class="product-desc">
				    <span class="product-title"><?php the_title();?></span>
				    <span class="price"><span class="currency"><?php echo gbspc_get_currency_symbol($price_currency);?></span><?php echo $price;?></span>
				    <p><?php echo get_the_content();?></p>
		        </div>
		        <div class="product-footer"><span class="meta"><?php echo $pterms;?></span></div>     
		    </div><!-- .product-content -->
	  	</div><!-- .product-wrapper -->
	  	<?php the_post_navigation();?>
	  	</div><!-- .product-container --> 		
	</article><!-- #post-## -->
	<?php endwhile; // End of the loop ?>



<script type="text/javascript">
jQuery(function() {
    jQuery("#gbspc-contact-form .button").click(function() {       
		var pdata = jQuery("#gbspc-contact-form").serialize();
        jQuery.ajax({
            type: "POST",
            url: ajaxurl,
            data: pdata,
            success: function(data){
            	console.log(data);
            },
            error:function(){
            	console.log("Email not sent");
            }
        });
        return false;
    });
});
</script>
<?php get_footer();?>