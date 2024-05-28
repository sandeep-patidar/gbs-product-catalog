<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
$gbspc_currencies = array(
    "USD" => "&#36;" , //U.S. Dollar
    "AUD" => "&#36;" , //Australian Dollar
    "BRL" => "R&#36;" , //Brazilian Real
    "CAD" => "C&#36;" , //Canadian Dollar
    "CZK" => "K&#269;" , //Czech Koruna
    "DKK" => "kr" , //Danish Krone
    "EUR" => "&euro;" , //Euro
    "HKD" => "&#36" , //Hong Kong Dollar
    "HUF" => "Ft" , //Hungarian Forint
    "ILS" => "&#x20aa;" , //Israeli New Sheqel
    "INR" => "&#8377;", //Indian Rupee
    "JPY" => "&yen;" , //Japanese Yen
    "MYR" => "RM" , //Malaysian Ringgit
    "MXN" => "&#36" , //Mexican Peso
    "NOK" => "kr" , //Norwegian Krone
    "NZD" => "&#36" , //New Zealand Dollar
    "PHP" => "&#x20b1;" , //Philippine Peso
    "PLN" => "&#122;&#322;" ,//Polish Zloty
    "GBP" => "&pound;" , //Pound Sterling
    "SEK" => "kr" , //Swedish Krona
    "CHF" => "Fr" , //Swiss Franc
    "TWD" => "&#36;" , //Taiwan New Dollar
    "THB" => "&#3647;" , //Thai Baht
    "TRY" => "&#8378;" //Turkish Lira

);

function gbspc_get_currency_symbol($iso){
    $symbol = '';
    $iso = 'USD';
    global $gbspc_currencies;
    $symbol = $gbspc_currencies[$iso];
    return $symbol;
}


function gbspc_custom_excerpt_length( $length ) {
    return 10;
}
add_filter( 'excerpt_length', 'gbspc_custom_excerpt_length', 999 );


/* Filter the single_template with our custom function*/
add_filter('single_template', 'gbspc_single_template');
function gbspc_single_template($single) {
    global $wp_query, $post;

    /* Checks for single template by post type */
    if ($post->post_type == "gbspc-product"){ 
    	$single = load_template(GBSPC_PATH. 'includes/single-gbspc-product.php');        
    }
    return $single;
}


add_filter('taxonomy_template', 'gbspc_taxonomy_template');
function gbspc_taxonomy_template(){
    global $post;
    $taxonomy_slug = get_query_var('taxonomy');
    if($taxonomy_slug == "gbspc-categories"){
        load_template(GBSPC_PATH.'includes/taxonomy-'.$taxonomy_slug.'.php');
    }  
}

function gbspc_get_product_taxonomies(){
    global $post;
    $taxonomy = 'gbspc-categories';

    // get the term IDs assigned to post.
    $post_terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'fields' => 'ids' ) );
    // separator between links
    $separator = ', ';

    if ( !empty( $post_terms ) && !is_wp_error( $post_terms ) ) {
       $term_ids = implode( ',' , $post_terms );
       $terms = wp_list_categories( 'title_li=&style=none&echo=0&taxonomy=' . $taxonomy . '&include=' . $term_ids );
       $terms = rtrim( trim( str_replace( '<br />',  $separator , $terms) ) , $separator );

        // display post categories
     return  $terms;
    }
}