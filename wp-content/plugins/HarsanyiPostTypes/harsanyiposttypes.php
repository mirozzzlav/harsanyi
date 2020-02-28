<?php
/**
 * Plugin Name: Harsanyi Post Types
 * Plugin URI: 
 * Description: Manage custom post types (like "Pomohli sme" or "Žiadosti") required for Harsanyi web.
 * Version: 1.0
 * Author: Mirozlav
 * Author URI: http://www.sloncompany.com
 */


////////////////////////////////////////
///// GENERIC PART OF THE CODE ////
////////////////////////////////////////
require_once \WP_PLUGIN_DIR . '/constants.php';

/**
 * Registers a stylesheet.
 */
 function harsanyi_register_plugin_styles() {
    wp_register_style('harsanyiposttypes', plugin_dir_url( __FILE__ ) . 'style.css?'.rand());
	wp_enqueue_style( 'harsanyiposttypes');
}
add_action('admin_init','harsanyi_register_plugin_styles');


/**
 * Hideing POSTs and PAGEs from WP-ADMIN menu 
 */ 
function remove_default_posts_from_menu() {
    remove_menu_page('edit.php');
    //remove_menu_page('edit.php?post_type=page');
}
add_action( 'admin_menu', 'remove_default_posts_from_menu' );


////////////////////////////////////////
///// WEHELPED PART OF THE CODE ////
////////////////////////////////////////

function create_wehelped_post_type() {

	$labels = array(
		'name' => __( 'Pomohli sme' ),
		'singular_name' => __( 'Pomohli sme' ),
		'all_items' => __('Všetky "Pomohli sme"'),
		'add_new' => _x('Pridať nové "Pomohli sme"', 'Ziadosti'),
		'add_new_item' => __('Pridať nové "Pomohli sme"'),
		'edit_item' => __('Edituj "Pomohli sme"'),
		'new_item' => __('Nové "Pomohli sme"'),
		'view_item' => __('Pozri "Pomohli sme"'),
		'search_items' => __('HĽadaj v "Pomohli sme"'),
		'not_found' =>  __('Neboli nájdené žiadne "Pomohli sme" položky'),
		'not_found_in_trash' => __('V koši nie sú žiadne "Pomohli sme" položky'),
		'parent_item_colon' => ''
	);

	$args = array (
		'labels' => $labels,
		'public' => true,
		'has_archive' => true,
		'menu_icon' => '',
		'rewrite' => array('slug' => 'wehelped'),
		'taxonomies' => array( 'category', 'post_tag' ),
		'query_var' => true,
        'supports'	=> array( 'genesis-cpt-archives-settings', 'thumbnail' , 'custom-fields', 'excerpt', 'comments', 'title', 'editor' ),
        'show_in_rest' => true,
        'menu_position' => 5,
        'menu_icon'           => 'dashicons-thumbs-up'

	);

	register_post_type( WEHELPED_POSTTYPE, $args);
}
add_action( 'init', 'create_wehelped_post_type' );



////////////////////////////////////////
///// APPLICATIONS PART OF THE CODE ////
////////////////////////////////////////

// creating application post type
function create_application_post_type() {

	$labels = array(
		'name' => __( 'Žiadosti' ),
		'singular_name' => __( 'Žiadosť' ),
		'all_items' => __('Všetky žiadosti'),
		'add_new' => _x('Pridať novú žiadosť', 'Ziadosti'),
		'add_new_item' => __('Pridať novú žiadosť'),
		'edit_item' => __('Edituj Žiadosť'),
		'new_item' => __('Nová žiadosť'),
		'view_item' => __('Pozri žiadosť'),
		'search_items' => __('HĽadaj v žiadostiach'),
		'not_found' =>  __('Neboli nájdené žiadne žiadosti'),
		'not_found_in_trash' => __('V koši nie sú žiadne žiadosti'),
		'parent_item_colon' => ''
	);

	$args = array (
		'labels' => $labels,
		'public' => true,
		'has_archive' => true,
		'menu_icon' => '',
		'rewrite' => array('slug' => 'application'),
		'taxonomies' => array( 'category', 'post_tag' ),
		'query_var' => true,
		'supports'	=> array( 'genesis-cpt-archives-settings', 'thumbnail' , 'custom-fields', 
			'excerpt', 'title', 'editor'),
        'show_in_rest' => true,
        'menu_position' => 5,
		'menu_icon'           => 'dashicons-smiley',
		'capabilities'    => array(
			'create_posts' => 'do_not_allow',
		),
		'map_meta_cap' => true,

	);

	register_post_type( APPLICATION_POSTTYPE, $args);
}
add_action( 'init', 'create_application_post_type' );

// on plugin activation create supporting table for applications


function createApplicationsTable() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix . APPLICATIONS_TABLE; 
	
	
	if (in_array(APPLICATIONS_TABLE, $wpdb->tables)) {
		return;
	}

	$sql = 
	"CREATE TABLE $table_name (
		id bigint(20) unsigned NOT NULL,
		applicantFullname varchar(50) NOT NULL,
		applicantEmail varchar(50) NULL,
		applicantPhoneNr varchar(50) NOT NULL,
		applicantAddress varchar(100) NULL,
		recipientFullname varchar(50) NOT NULL,
		recipientRelToApplicant varchar(100) NULL,
		recipientPurpose varchar(100) NULL,
		requestText text NOT NULL,
		PRIMARY KEY  (id),
		FOREIGN KEY (id) REFERENCES {$wpdb->prefix}posts(ID)
	) $charset_collate;";


	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
}
register_activation_hook(__FILE__, 'createApplicationsTable');


function sanitizeApplication(Array $application): Array
{
	$application['applicantEmail'] = !empty($application['applicantEmail']) ? 
		$application['applicantEmail'] : '-';
	$application['applicantAddress'] = !empty($application['applicantAddress']) ? 
		$application['applicantAddress'] : '-';
	$application['recipientRelToApplicant'] = !empty($application['recipientRelToApplicant']) ? 
		$application['recipientRelToApplicant'] : '-';
	$application['recipientPurpose'] = !empty($application['recipientPurpose']) ? 
		$application['recipientPurpose'] : '-';

	return $application;
}

function applicationGetHtml(Array $application) 
{
	$ret = "<div class='wpadmin-app-wrapper'>
			<h3 class='wpadmin-app-hl'>Info o žiadateľovi</h3>
			<table class='wpadmin-app-table'>
				<tr>
					<td>Meno a priezvisko</td>
					<td>{$application['applicantFullname']}</td>
				</tr>
				<tr>
					<td>E-mail</td>
					<td>{$application['applicantEmail']}</td>
				</tr>
				<tr>
					<td>Tel. číslo</td>
					<td>{$application['applicantPhoneNr']}</td>
				</tr>
				<tr>
					<td>Adresa</td>
					<td>{$application['applicantAddress']}</td>
				</tr>
			</table>
			<h3 class='wpadmin-app-hl'>Info o príjemcovi podpory</h3>
			<table class='wpadmin-app-table'>
				<tr>
					<td>Meno a priezvisko</td>
					<td>{$application['recipientFullname']}</td>
				</tr>
				<tr>
					<td>Vzťah ku žiadateľovi</td>
					<td>{$application['recipientRelToApplicant']}</td>
				</tr>
				<tr>
					<td>Účel pomoci</td>
					<td>{$application['recipientPurpose']}</td>
				</tr>
			</table>
			<h3 class='wpadmin-app-hl'>Prílohy</h3>"
			. applicationGetAttachmentsHtml($application['id']) .
			"<h3 class='wpadmin-app-hl'>Text žiadosti</h3>
			<p class='wpadmin-app-request-text'>{$application['requestText']}</p>
		</div>
	";
	return $ret;
}

function applicationGetAttachmentsHtml(int $attachmentId): String
{
	$attachments = get_posts(
		[
			'post_type' => 'attachment',
			'posts_per_page' => -1,
			'post_parent' => $attachmentId,
			'exclude'     => get_post_thumbnail_id()
		]
	);
	if (empty($attachments)) {
		return "<div class='wpadmin-app-attachments-wrapper'>Žiadne prílohy</div>";
	}

	$ret = "<div class='wpadmin-app-attachments-wrapper'>";
	
	foreach ($attachments as $attachment) {
		$attachTitle = $attachment->post_title;

		$attachUrl = wp_get_attachment_url($attachment->ID);
		$ret .= "<a class='wpadmin-app-request-link' target='_blank' href='$attachUrl'>$attachTitle</a>";
	}

	return $ret . "</div>";

}

function on_application_post_showup($post_object)
{
	global $pagenow, $wpdb;
	
	if (!preg_match("/(post.+)/i", $pagenow) || $post_object->post_type !== APPLICATION_POSTTYPE) {
		return;
	}

	$postContent = trim($post_object->post_content);
	if(!empty($postContent)) {
		return;
	}
	$tbl = $wpdb->prefix.APPLICATIONS_TABLE;
	$sql = $wpdb->prepare("SELECT * FROM {$tbl} WHERE id=%d", $post_object->ID);
	$application = $wpdb->get_row($sql, ARRAY_A);
	if (empty($application)) {
		return;
	}
	$application = sanitizeApplication($application);
	$post_object->post_content = applicationGetHtml($application);
	
}
add_action( 'the_post', 'on_application_post_showup' );


/**
 * Deletes supporting application table record before deleting actual post
 **/
function delete_application_record(int $post_id)
{
	global $wpdb;
	$post = get_post($post_id);

	if ($post->post_type === APPLICATION_POSTTYPE) {
		$wpdb->delete($wpdb->prefix.APPLICATIONS_TABLE, array( 'id' => $post_id ));
	}
	
}
add_action( 'before_delete_post', 'delete_application_record' ); 


function addLinkViewApplication($actions, $post)
{
    $post_id = $post->ID;
	$post_type = $post->post_type;
	
	unset($actions['view']);
    if($post_type == APPLICATION_POSTTYPE) {
		$viewUrl = menu_page_url('application-view', false) . '&id=' . $post->ID;
		$actions['view'] = "<a href='$viewUrl'>View</a>";
		unset($actions['edit']);
		unset($actions['inline hide-if-no-js']);
		unset($actions['trash']);
    }
	
	
	return $actions;

}
add_filter('post_row_actions', 'addLinkViewApplication', 10, 2);


add_filter( 'get_edit_post_link', function($link) {
	
	preg_match("/post=([0-9]+)/", $link, $matches);
	if (empty($matches[1])) {
		return $link;
	}
	$postId = $matches[1];
	$post = get_post($postId);
	if (!empty($post) && $post->post_type === \APPLICATION_POSTTYPE) {
		$link = menu_page_url('application-view', false) . '&id=' . $postId;
	}
	
	return $link;
	
});



function renderViewApplication() {
	global $wpdb;
	$postId = $_GET['id'];
	$notFoundText = 'Žiadosť nebola nájdená.';
	if (empty($postId)) {
		echo "<div class='wrap'><p>$notFoundText</p></div>";
		return;
	}
	
	$tbl = $wpdb->prefix.APPLICATIONS_TABLE;
	$sql = $wpdb->prepare("SELECT * FROM {$tbl} WHERE id=%d", $postId);
	$application = $wpdb->get_row($sql, ARRAY_A);
	
	$applicaitonPost = get_post($postId);
	
	if (empty($application) || empty ($applicaitonPost) || $applicaitonPost->post_type !== APPLICATION_POSTTYPE) {
		echo "<div class='wrap'><p>$notFoundText</p></div>";
		return;
	}

	$content = applicationGetHtml($application);

	echo "<div class='wrap'>$content</div>";
	
}

add_action( 'admin_menu', function() {
    add_dashboard_page(
        'Pozri žiadosť',
        'Pozri žiadosť',
        'manage_options',
        'application-view',
        'renderViewApplication'
    );
});


add_action( 'admin_head', function() {
    remove_submenu_page( 'index.php', 'application-view');
});