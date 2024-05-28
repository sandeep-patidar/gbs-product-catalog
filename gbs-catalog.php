<?php
/* 
Plugin Name: GBS Product Catalog
Plugin URI: https://wordpress.org/plugins/gbs-product-catalog/
Description: GBS Product Catalog plugin allows you to display products catalog, display your projects or Business Catalog. Its easy to use and easily customizable with various settings.
Author: GBS Developer
Author URI: https://globalbizsol.com/
Version: 1.1
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

define('GBSPC_PATH', plugin_dir_path(__FILE__));
require(GBSPC_PATH . '/includes/function.php');
require(GBSPC_PATH . '/includes/catalog.php');

add_action( 'wp_ajax_submit_inquiry', 'gbspc_received_enquiry' );
add_action( 'wp_ajax_nopriv_submit_inquiry', 'gbspc_received_enquiry' );
function gbspc_received_enquiry(){
if($_POST){
$name = sanitize_text_field($_POST['your_name']);
$email = sanitize_email($_POST['your_email']);
$product_name = sanitize_text_field($_POST['product_name']);
$message = sanitize_text_field($_POST['your_message']);
$txt = '<html>
    <head>
      <title></title>
    </head>
    <body>
    <p>Name: '.$name.'</p>
    <p>Email: '.$email.'</p>
    <p>Product: '.$product_name.'</p>
    <p>Message: '.$message.'</p>
    </body>
    </html>';

    // Always set content-type when sending HTML email
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

    $to = get_option( 'admin_email' );
    $subject = "Product Enquiry";
    mail($to,$subject,$txt,$headers);
}
    echo json_encode($data);
exit();
}

add_action('wp_head','gbspc_ajaxurl');
function gbspc_ajaxurl() { ?>
    <script type="text/javascript">
    var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
    </script>
<?php }

function gbspc_custom_admin_style() {
      wp_enqueue_style( 'gbspc-custom-admin-style',plugin_dir_url(__FILE__) . 'css/admin-style.css', false, '1.0.0' );
      wp_enqueue_style( 'gbspc-custom-admin-style' );
}
add_action( 'admin_enqueue_scripts', 'gbspc_custom_admin_style' );


add_action( 'wp_enqueue_scripts', 'gbspc_scripts' );
function gbspc_scripts() {
    wp_enqueue_style( 'gbspc-style', plugin_dir_url(__FILE__) . 'css/style.css' );    
    wp_enqueue_script( 'jquery' );
    $custom_css = get_option('custom_css');
    wp_add_inline_style( 'custom-style', $custom_css );
}

add_action('init','gbspc_setup_post_type');
function gbspc_install() {
    // Trigger our function that registers the custom post type
    gbspc_setup_post_type();
    // Clear the permalinks after the post type has been registered
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'gbspc_install' );

function gbspc_deactivate() {
    // Clear the permalinks after the post type has been registered
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'gbspc_deactivate' );

function gbspc_setup_post_type(){
$args = array(
  'labels'	=>	array( 
  	 		    'name'    =>  'GBS Product',
				'all_items'           => 	'GBS Products',
				'menu_name'	          =>	'GBS Product',
				'singular_name'       =>	'GBS Product',
                'add_new_item'        =>  'Add New GBS Product',
				'edit_item'           =>	'Edit GBS Product',
				'new_item'            =>	'New GBS Product',
				'view_item'           =>	'View GBS Product',
				'items_archive'       =>	'GBS Product Archive',
				'search_items'        =>	'Search GBS Products',
				'not_found'	          =>	'No GBS Products found.',
				'not_found_in_trash'  => 'No GBS Products found in trash.'
			),
            	'supports'      =>	array( 'title', 'editor', 'revisions','thumbnail','custom-fields','comments','excerpt' ),
            	'public'		    =>	true,
            	'publicly_queryable' => true,
            	'show_ui'            => true,
            	'show_in_menu'       => true,
            	'query_var'          => true,
            	'rewrite'            => array( 'slug' => 'gbspc-product' ),
            	'capability_type'    => 'post',
            	'has_archive'        => true,
            	'hierarchical'       => true,
                'show_in_rest'       => true
	);

register_taxonomy(  
        'gbspc-categories',  //The name of the taxonomy. Name should be in slug form (must not contain capital letters or spaces). 
        'gbspc-product',        //post type name
        array(  
            'hierarchical' => true,  
            'label' => 'GBSPC Categories',  //Display name
            'query_var' => true,
            'show_ui' => true,
            'show_admin_column' => true,    
            'update_count_callback' => '_update_post_term_count',        
            'rewrite' => array(
            'slug' => 'gbspc-category', // This controls the base slug that will display before each term
            )
        )  
    );  

register_post_type( 'gbspc-product', $args );
}

add_action( 'load-post.php', 'gbspc_metabox' );
add_action( 'load-post-new.php', 'gbspc_metabox' );
function gbspc_metabox(){
    add_action('add_meta_boxes','gbspc_add_price_metabox');
    add_action( 'save_post', 'gbspc_save_price_metabox', 10, 2 );
}

function gbspc_add_price_metabox(){
  add_meta_box('gbspc-price','Price','gbspc_price_metabox','gbspc-product','side','default');
}

/* Display the post meta box. */
function gbspc_price_metabox( $object, $box ) { ?>
    <?php wp_nonce_field( basename( __FILE__ ), 'gbspc_price_nonce' ); ?>
    <label for="gbspc-price"></label>
    <input class="widefat" type="text" name="gbspc-price" id="gbspc-price-meta" value="<?php echo esc_attr( get_post_meta( $object->ID, 'gbspc_price', true ) ); ?>" size="30" />
<?php }


/* Save the meta box's post metadata. */
function gbspc_save_price_metabox( $post_id, $post ) {
  /* Verify the nonce before proceeding. */
  if ( !isset( $_POST['gbspc_price_nonce'] ) || !wp_verify_nonce( $_POST['gbspc_price_nonce'], basename( __FILE__ ) ) )
    return $post_id;

  /* Get the post type object. */
  $post_type = get_post_type_object( $post->post_type );

  /* Check if the current user has permission to edit the post. */
  if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
    return $post_id;

  /* Get the posted data and sanitize it for use as an HTML class. */
  $new_meta_value = ( isset( $_POST['gbspc-price'] ) ? sanitize_html_class( $_POST['gbspc-price'] ) : '' );

  /* Get the meta key. */
  $meta_key = 'gbspc_price';

  /* Get the meta value of the custom field key. */
  $meta_value = get_post_meta( $post_id, $meta_key, true );

  /* If a new meta value was added and there was no previous value, add it. */
  if ( $new_meta_value && '' == $meta_value )
    add_post_meta( $post_id, $meta_key, $new_meta_value, true );

  /* If the new meta value does not match the old value, update it. */
  elseif ( $new_meta_value && $new_meta_value != $meta_value )
    update_post_meta( $post_id, $meta_key, $new_meta_value );

  /* If there is no new meta value but an old value exists, delete it. */
  elseif ( '' == $new_meta_value && $meta_value )
    delete_post_meta( $post_id, $meta_key, $meta_value );
}


add_action('admin_menu','gbspc_add_admin_menu');
function gbspc_add_admin_menu(){
  add_options_page('GBS Product Catalog', 'GBS Product Catalog', 'manage_options', 'gbscatalog', 'gbspc_options_page');
}


function gbspc_options_page(){ 
    global $gbspc_currencies;
    if(!current_user_can( 'administrator' ))
    return;

    if(isset($_POST['setting_submit'])){  
        if ( !isset( $_POST['gbspc_settings_nonce'] ) || !wp_verify_nonce( $_POST['gbspc_settings_nonce'], basename( __FILE__ ) ) )
    	return;
        $gbspc_currency = sanitize_text_field($_POST['currency_select']);
        $max_products = sanitize_text_field($_POST['max_products']);
        $products_order = sanitize_text_field($_POST['products_order']);
        $custom_css = sanitize_text_field($_POST['custom_css']);
  
        update_option("currency",sanitize_text_field($gbspc_currency));
        update_option("max_products",sanitize_text_field($max_products));
        update_option("products_order",sanitize_text_field($products_order));
        update_option("custom_css",sanitize_text_field($custom_css));
    } 

    $gbspc_currency = esc_attr(get_option('currency'));
    $max_products = esc_attr(get_option('max_products'));
    $products_order = esc_attr(get_option('products_order'));
    $css = esc_attr(get_option('custom_css'));

?>
<div class="gbspc_setting">
<form method="post" class="gbspc_setting">
<h1>GBS Product Catlog Setting</h1>
<table>
<tbody>
    <tr>
        <td>Max no. of products per page</td>
        <td><input type="text" name="max_products" value="<?php echo $max_products;?>"/></td>
    </tr>
    <tr>
        <td>Products display order</td>
        <td><select name="products_order" id="products_order">
            <option value="asc" <?php echo (($products_order=='asc')?'selected=selected':"");?>>Ascending</option>
            <option value="desc" <?php echo (($products_order=='desc')?'selected=selected':"");?>>Descending</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>Select Currency</td>
        <td>
            <select name="currency_select" id="currencyselect">
            <?php 
            foreach($gbspc_currencies as $iso => $symbol) {
                if($gbspc_currency == $iso){
                  echo '<option value="'.$iso.'"  selected="selected">'.$iso. '(' .$symbol. ')'.'</option>';    
                }else{
                  echo '<option value="'.$iso.'">'.$iso. '(' .$symbol. ')'.'</option>';
                }
            }?>
            </select>
        </td>
    </tr>
    <tr>
        <td>Custom CSS</td>
        <td><textarea name="custom_css" id="customcss" class="customcss" placeholder="/* Custom Css */"><?php echo $css;?></textarea></td>
    </tr>
    <tr>
        <td><input type="submit" name="setting_submit" value="Save Setting" class="button"></td>
    </tr>
</tbody>
</table>
<?php wp_nonce_field( basename( __FILE__ ), 'gbspc_settings_nonce' ); ?>
</form>   
</div>
<?php } ?>