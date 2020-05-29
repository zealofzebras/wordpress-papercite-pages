<?php
/**
 * Plugin Name: Pages for Papercite
 * Plugin URI: https://zeal.global/projects
 * Description: Automatic pages for papercite
 * Version: 1.0
 * Author: zeal
 * Author URI: https://zeal.global
 */


 
function papercite_pages_ispublicationpage() {
    global $post;
    static $ispublication;
    if ( empty( $ispublication ) ) {
        $ispublication = false;

        if ( strtolower($post->post_title) == "publication" && preg_match( '#\[singletex.*\]#', $post->post_content, $matches ) === 1 ) {
            $ispublication = true;
        }
    }
    return $ispublication;
}

function papercite_pages_publication_title() {
    static $title;
    if ( empty( $title ) ) {
        if (papercite_pages_ispublicationpage()) {
            $pub_name = preg_quote(urldecode(get_query_var( 'pub_name' )), "/");
            if($pub_name != "") {
                $pubtitle = trim (strip_tags ( apply_filters( 'the_content', '[bibtex filter:urltitle="/^' . $pub_name . '$/mi" bibtex_template="title-bibtex"]', "")));
                //$pubtitle = "none";
                if ($pubtitle != "") {
                    $title = $pubtitle;
                } else {
                    $title = "[NOTFOUND]";
                }
            } else {
                $title = "[NONE]";
            }  
        } else {
            $title = "[NOTPUB]";
        }
    }
    return $title;
}

function papercite_pages_publication_redirect() {
    if (papercite_pages_ispublicationpage()) {
        $title = papercite_pages_publication_title();   
        if ($title == "[NONE]" || $title == "[NOTFOUND]") {
            wp_redirect( home_url( '/publications/' ) );
            die;  
        }
    }
}
add_action( 'template_redirect', 'papercite_pages_publication_redirect' );

function papercite_pages_document_title_parts ( $title ) {  
    global $post;
    if ( ! $post || ! $post->post_content || !is_page() ) {
        return $title;
    }
    if (papercite_pages_ispublicationpage()) {
        $title['title'] = papercite_pages_publication_title();     
    }
    return  $title;
}
add_filter('document_title_parts', 'papercite_pages_document_title_parts');

function papercite_pages_page_title($title) {
    global $post;
    if ( ! $post || ! $post->post_content || !is_page() ) {
        return $title;
    }
    if (strtolower($title) == "publication" && papercite_pages_ispublicationpage()) {
        return papercite_pages_publication_title();  
    }
    return $title;
}
add_filter('the_title', 'papercite_pages_page_title', 99);

function papercite_pages_query_vars($aVars) {
    $aVars[] = "pub_name"; // represents the name of the product category as shown in the URL
    return $aVars;
}     
add_filter('query_vars', 'papercite_pages_query_vars');

function papercite_pages_singletex($attr,$content){
    $pub_name = preg_quote(urldecode(get_query_var( 'pub_name' )), "/");
    if($pub_name != "") {
        return apply_filters( 'the_content', '[bibtex format="ieee" filter:urltitle="/^' . $pub_name . '$/mi" bibtex_template="item-bibtex"]', "");
        //return "test";
    } else {
        return "NO PUB";
    }
}
add_shortcode("singletex","papercite_pages_singletex");


function papercite_pages_init() {
    function add_rewrite_rules($aRules) {
        $aNewRules = array('publications/([^/]+)/?$' => 'index.php?pagename=publications/publication&pub_name=$matches[1]');
        $aRules = $aNewRules + $aRules;
        return $aRules;
    }        
    add_filter('rewrite_rules_array', 'add_rewrite_rules', 'top');
}
add_action('init', 'papercite_pages_init');
