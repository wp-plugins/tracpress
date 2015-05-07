<?php
/*
Plugin Name: TracPress
Plugin URI: http://getbutterfly.com/wordpress-plugins-free/
Description: TracPress is an enhanced issue tracking system for software development projects. TracPress uses a minimalistic approach to web-based software project management. TracPress is a WordPress-powered ticket manager and issue tracker featuring multiple projects, multiple users, milestones, attachments and much more.
Version: 1.4
License: GPLv3
Author: Ciprian Popescu
Author URI: http://getbutterfly.com/

Copyright 2014, 2015 Ciprian Popescu (email: getbutterfly@gmail.com)

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

define('TP_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)));
define('TP_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . dirname(plugin_basename(__FILE__)));
define('TP_PLUGIN_VERSION', '1.4');

// plugin localization
load_plugin_textdomain('tracpress', false, dirname(plugin_basename(__FILE__)) . '/languages/');

include(TP_PLUGIN_PATH . '/includes/functions.php');
include(TP_PLUGIN_PATH . '/includes/page-settings.php');

add_action('init', 'tracpress_registration');

add_action('wp_ajax_nopriv_post-like', 'post_like');
add_action('wp_ajax_post-like', 'post_like');

add_action('admin_menu', 'tracpress_menu'); // settings menu
add_action('admin_menu', 'tracpress_menu_bubble');

add_filter('transition_post_status', 'notify_status', 10, 3); // email notifications
add_filter('widget_text', 'do_shortcode');

function tracpress_menu() {
    add_submenu_page('edit.php?post_type=' . get_option('ticket_slug'), 'TracPress Settings', 'TracPress Settings', 'manage_options', 'tracpress_admin_page', 'tracpress_admin_page');
}

add_shortcode('tracpress-add', 'tracpress_add');
add_shortcode('tracpress-show', 'tracpress_show');
add_shortcode('tracpress-timeline', 'tracpress_timeline');
add_shortcode('tracpress-search', 'tracpress_search');
add_shortcode('tracpress-milestone', 'tracpress_milestone');

function tracpress_add($atts, $content = null) {
	extract(shortcode_atts(array(
		'category' => ''
	), $atts));

    global $current_user;
	$out = '';

	if(isset($_POST['tracpress_create_ticket_form_submitted']) && wp_verify_nonce($_POST['tracpress_create_ticket_form_submitted'], 'tracpress_create_ticket_form')) {
		if(get_option('tp_moderate') == 0)
			$tp_status = 'pending';
		if(get_option('tp_moderate') == 1)
			$tp_status = 'publish';

		if(get_option('tp_createusers') == 1) {
            // create new user
			$tracpress_author = sanitize_user($_POST['tracpress_author']);
			$tracpress_email = sanitize_email($_POST['tracpress_email']);

			$user_id = username_exists($tracpress_author);
            if(!$user_id and email_exists($tracpress_email) == false) {
                $random_password = wp_generate_password($length = 12, $include_standard_special_chars = false);
                $user_id = wp_create_user($tracpress_author, $random_password, $tracpress_email);
            } else {
                $random_password = __('User already exists. Password inherited.');
            }

            $tp_image_author = $user_id;
        }
        if(get_option('tp_createusers') == 0) {
            $tp_image_author = $current_user->ID;
        }
        $ticket_data = array(
            'post_title' => sanitize_text_field($_POST['ticket_summary']),
            'post_content' => sanitize_text_field($_POST['ticket_description']),
            'post_status' => $tp_status,
            'post_author' => $tp_image_author,
            'post_type' => get_option('ticket_slug')
        );

        // send notification email to administrator
        $tp_notification_email = get_option('tp_notification_email');
        $tp_notification_subject = __('New ticket submitted!', 'tracpress') . ' | ' . get_bloginfo('name');
        $tp_notification_message = __('New ticket submitted!', 'tracpress') . ' | ' . get_bloginfo('name');

        if($post_id = wp_insert_post($ticket_data)) {
            // multiple images
            if(1 == get_option('tp_upload_secondary')) {
                $files = $_FILES['tracpress_additional'];
                if($files) {
                    require_once(ABSPATH . 'wp-admin' . '/includes/image.php');
                    require_once(ABSPATH . 'wp-admin' . '/includes/file.php');
                    require_once(ABSPATH . 'wp-admin' . '/includes/media.php');

                    foreach($files['name'] as $key => $value) {
                        if($files['name'][$key]) {
                            $file = array(
                                'name' => $files['name'][$key],
                                'type' => $files['type'][$key],
                                'tmp_name' => $files['tmp_name'][$key],
                                'error' => $files['error'][$key],
                                'size' => $files['size'][$key]
                                );  
                        }
                        $_FILES = array("attachment" => $file);
                        foreach($_FILES as $file => $array) {
                            $attach_id = media_handle_upload($file, $post_id, array(), array('test_form' => false));
                            if($attach_id < 0) { $post_error = true; }
                        }
                    }
                }
            }
            // end multiple images

            wp_set_object_terms($post_id, (int)$_POST['tracpress_ticket_type'], 'tracpress_ticket_type');
            wp_set_object_terms($post_id, (int)$_POST['tracpress_ticket_component'], 'tracpress_ticket_component');

            $tags = explode(',', sanitize_text_field($_POST['tracpress_ticket_tags']));
            wp_set_post_terms($post_id, $tags, 'tracpress_ticket_tag', false);

            add_post_meta($post_id, 'votes_count', 0, true);

            if(isset($_POST['ticket_version']))
                add_post_meta($post_id, 'ticket_version', sanitize_text_field($_POST['ticket_version']), true);
            else
                add_post_meta($post_id, 'ticket_version', '', true);

            $headers[] = "MIME-Version: 1.0\r\n";
            $headers[] = "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\r\n";
            wp_mail($tp_notification_email, $tp_notification_subject, $tp_notification_message, $headers);
        }

        $out .= '<p class="message">' . __('Ticket created!', 'tracpress') . '</p>';
        if(get_option('tp_moderate') == 0)
            $out .= '<p class="message">' . __('Your ticket needs to be accepted/moderated by an administrator.', 'tracpress') . '</p>';
        if(get_option('tp_moderate') == 1)
            $out .= '<p class="message"><a href="' . get_permalink($post_id) . '">' . __('Click here to view your ticket.', 'tracpress') . '</a></p>';
	}

	if(get_option('tp_registration') == 0 && !is_user_logged_in()) {
		$out .= '<p>' . __('You need to be logged in to create a ticket.', 'tracpress') . '</p>';
	}
	if((get_option('tp_registration') == 0 && is_user_logged_in()) || get_option('tp_registration') == 1) {
		$out .= tracpress_get_ticket_form($ticket_summary = $_POST['ticket_summary'], $tracpress_ticket_type = $_POST['tracpress_ticket_type'], $ticket_description = $_POST['ticket_description'], $category);
	}

	return $out;
}

function tracpress_process_image_secondary($file, $post_id, $summary) {
	require_once(ABSPATH . 'wp-admin' . '/includes/image.php');
	require_once(ABSPATH . 'wp-admin' . '/includes/file.php');
	require_once(ABSPATH . 'wp-admin' . '/includes/media.php');

	$attachment_id = media_handle_upload($file, $post_id);

	$attachment_data = array(
		'ID' => $attachment_id,
		'post_excerpt' => $summary
	);
	wp_update_post($attachment_data);

	return $attachment_id;
}

function tracpress_get_ticket_form($ticket_summary = '', $tracpress_ticket_type = 0, $ticket_description = '', $tracpress_hardcoded_category) {
    global $current_user;
    get_currentuserinfo();

    // upload form
	$out = '<div class="tp-uploader">';
        $out .= '<form id="tracpress_create_ticket_form" method="post" action="" enctype="multipart/form-data" class="tracpress-form">';
            $out .= wp_nonce_field('tracpress_create_ticket_form', 'tracpress_create_ticket_form_submitted');
            // name and email
            if(get_option('tp_registration') == 0) {
                $out .= '<input type="hidden" name="tracpress_author" value="' . $current_user->display_name . '">';
                $out .= '<input type="hidden" name="tracpress_email" value="' . $current_user->user_email . '">';
            }
            if(get_option('tp_registration') == 1 && !is_user_logged_in()) {
                $out .= '<input type="text" name="tracpress_author" value="' . $current_user->display_name . '" placeholder="Name" required>';
                $out .= '<input type="email" name="tracpress_email" value="' . $current_user->user_email . '" placeholder="Email Address" required>';
            }

            $out .= '<p><input type="text" id="ticket_summary" name="ticket_summary" placeholder="' . get_option('ticket_summary_label') . '" required></p>';
            $ticket_description_label = get_option('ticket_description_label');
            if(!empty($ticket_description_label))
                $out .= '<p><textarea id="ticket_description" name="ticket_description" placeholder="' . get_option('ticket_description_label') . '" rows="6"></textarea></p>';

            $out .= '<p>';
                if('' != $tracpress_hardcoded_category) {
                    $iphcc = get_term_by('slug', $tracpress_hardcoded_category, 'tracpress_ticket_type'); // TracPress hard-coded category
                    $out .= '<input type="hidden" id="tracpress_ticket_type" name="tracpress_ticket_type" value="' . $iphcc->term_id . '">';
                }
                else {
                    $out .= tracpress_get_categories_dropdown('tracpress_ticket_type', '') . '';
                }

                if(get_option('tracpress_allow_components') == 1)
                    $out .= tracpress_get_tags_dropdown('tracpress_ticket_component', '') . '';
            $out .= '</p>';

            if('' != get_option('ticket_version_label'))
                $out .= '<p><input type="text" id="ticket_version" name="ticket_version" placeholder="' . get_option('ticket_version_label') . '"></p>';
            if('' != get_option('ticket_tags_label'))
                $out .= '<p><input type="text" id="tracpress_ticket_tags" name="tracpress_ticket_tags" placeholder="' . get_option('ticket_tags_label') . '"></p>';

            if(1 == get_option('tp_upload_secondary'))
                $out .= '<hr>';
                $out .= '<p><label for="tracpress_additional"><i class="fa fa-cloud-upload"></i> Select file(s)...</label><br><input type="file" name="tracpress_additional[]" id="tracpress_additional" multiple><br><small>Additional files (screenshots, patches, documents)</small></p><hr>';

            $out .= '<p>';
                $out .= '<input type="submit" id="tracpress_submit" name="tracpress_submit" value="' . get_option('ticket_create_label') . '" class="button">';
                $out .= ' <span id="ipload"></span>';
            $out .= '</p>';
        $out .= '</form>';
    $out .= '</div>';

	return $out;
}

function tracpress_get_categories_dropdown($taxonomy, $selected) {
	return wp_dropdown_categories(array(
		'taxonomy' => $taxonomy,
		'name' => 'tracpress_ticket_type',
		'selected' => $selected,
		'hide_empty' => 0,
		'echo' => 0,
		'show_option_all' => get_option('ticket_type_label')
	));
}
function tracpress_get_tags_dropdown($taxonomy, $selected) {
	return wp_dropdown_categories(array(
		'taxonomy' => $taxonomy,
		'name' => 'tracpress_ticket_component',
		'selected' => $selected,
		'hide_empty' => 0,
		'echo' => 0,
		'show_option_all' => get_option('ticket_component_label')
	));
}

function tracpress_activate() {
	add_option('ticket_slug', 'ticket');

	add_option('tp_moderate', 0);
	add_option('tp_registration', 1);

	add_option('tp_order', 'DESC');
	add_option('tp_orderby', 'date');

	add_option('approvednotification', 'yes');
	add_option('declinednotification', 'yes');

	add_option('ticket_summary_label', 'Ticket summary');
	add_option('ticket_type_label', 'Ticket type');
	add_option('ticket_component_label', 'Component');
	add_option('ticket_version_label', 'Version (optional)');
	add_option('ticket_description_label', 'Ticket description');
	add_option('ticket_create_label', 'Create ticket');
	add_option('ticket_tags_label', 'Ticket tags (optional, separate with comma)');

	add_option('tp_timebeforerevote', 24);

	add_option('tp_createusers', 0);

    // configurator options
    add_option('tp_id_optional', 1);
    add_option('tp_summary_optional', 1);
    add_option('tp_author_optional', 1);
    add_option('tp_component_optional', 1);
    add_option('tp_priority_optional', 1);
    add_option('tp_severity_optional', 1);
    add_option('tp_milestone_optional', 1);
    add_option('tp_type_optional', 1);
    add_option('tp_workflow_optional', 1);
    add_option('tp_comments_optional', 1);
    add_option('tp_plus_optional', 1);
    add_option('tp_date_optional', 1);
    //
    add_option('tp_upload_secondary', 1);
    add_option('tracpress_allow_components', 1);
}

function tracpress_deactivate() {
    flush_rewrite_rules();
}

register_activation_hook(__FILE__, 'tracpress_activate');
register_deactivation_hook(__FILE__, 'tracpress_deactivate');
//register_uninstall_hook( __FILE__, 'tracpress_uninstall');

// enqueue scripts and styles
add_action('wp_enqueue_scripts', 'tp_enqueue_scripts');
function tp_enqueue_scripts($hook_suffix) {
    wp_enqueue_style('fa', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css');

    // minify with http://gpbmike.github.io/refresh-sf/
	wp_enqueue_style('tp.bootstrap', plugins_url('css/tp.bootstrap.css', __FILE__));

    wp_enqueue_script('slimtable', plugins_url('js/slimtable.min.js', __FILE__), array('jquery'), '', true);

	wp_enqueue_script('jquery-tracpress', plugins_url('js/jquery.main.js', __FILE__), array('jquery'), '', true);
	wp_localize_script('jquery-tracpress', 'ajax_var', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('ajax-nonce')
	));
}
// end

function tracpress_search($atts, $content = null) {
	extract(shortcode_atts(array(
		'type' => '',
	), $atts));

	$display = '<form role="search" method="get" action="' . home_url() . '" class="tracpress-form">
			<div>
				<input type="search" name="s" id="s" placeholder="' . __('Search tickets&hellip;', 'tracpress') . '"> 
				<input type="submit" id="searchsubmit" value="' . __('Search', 'tracpress') . '">
				<input type="hidden" name="post_type" value="' . get_option('ticket_slug') . '">
			</div>
		</form>';

	return $display;
}

function tracpress_timeline($atts, $content = null) {
	extract(shortcode_atts(array(
		'milestone'    => '',
		'count'       => 0,
        'limit'       => 999999,
		'user'        => 0,
	), $atts));

	global $current_user;

    // all filters should be applied here
    $tp_order = get_option('tp_orderby');

	if($user > 0)
		$author = $user;
	if(isset($_POST['user']))
		$author = sanitize_user($_POST['user']);

    // defaults
    $tp_order_asc_desc = get_option('tp_order');
    //
	$args = array(
		'post_type' 				=> get_option('ticket_slug'),
		'posts_per_page' 			=> $limit,
		'orderby' 					=> $tp_order,
		'order' 					=> $tp_order_asc_desc,
		'author' 					=> $author,

        'tax_query' => array(
            array(
                'taxonomy' => 'tracpress_ticket_milestone',
                'field' => 'id',
                'terms' => $milestone,
                'include_children' => true
            )
        ),

        'cache_results' => false,
        'update_post_term_cache' => false,
        'update_post_meta_cache' => false,
        'no_found_rows' => true,
	);
    $posts = get_posts($args);
    //

    $out = '';

	if($posts) {
        foreach($posts as $ticket) {
            setup_postdata($ticket);

			$user_info = get_userdata($ticket->post_author);

            //statuses: assigned, reopened, new, reviewing, accepted, closed
            $ticket_status = get_post_meta($ticket->ID, '_ticket_status', true);
            $ticket_resolution = get_post_meta($ticket->ID, '_ticket_resolution', true);

            if($ticket_status == 'new') $icon = '<i class="fa fa-file-o"></i>';
            if($ticket_status == 'accepted') $icon = '<i class="fa fa-toggle-on"></i>';
            if($ticket_status == 'assigned') $icon = '<i class="fa fa-user"></i>';
            if($ticket_status == 'reviewing') $icon = '<i class="fa fa-wrench"></i>';
            if($ticket_status == 'closed') $icon = '<i class="fa fa-file-o"></i>';
            if($ticket_status == 'reopened') $icon = '<i class="fa fa-check"></i>';

            if($ticket_status == '') {
                $icon = '<i class="fa fa-question"></i>';
                $ticket_status = 'unopened';
            }
            if($ticket_resolution == '') {
                $ticket_resolution = 'unmarked';
            }

            $out .= '<div class="tp-item">';
                $out .= '<div><small><code>' . $icon . ' ' . $ticket_status . '</code> <span class="tp-date">' . get_the_modified_date(get_option('date_format')) . ' ' . get_the_modified_date('H:i:s') . '</span></small></div>';
                $out .= '<div>Ticket #' . $ticket->ID . ': <a href="' . get_permalink($ticket->ID) . '">' . get_the_title($ticket->ID) . '</a> created by ' . $user_info->display_name . '</div>';
                $out .= '<small><code><i class="fa fa-cog"></i> [' . $ticket_resolution . ']</code> ';

                $args = array('post_id' => $ticket->ID, 'post_type' => get_option('ticket_slug'), 'number' => '1', 'orderby' => 'date', 'order' => 'DESC');
                $comments = get_comments($args);
                foreach($comments as $comment) :
                    $out .= '<span class="tp-comment">' . $comment->comment_author . ': ' . substr($comment->comment_content, 0, 90) . '&hellip;</span>';
                endforeach;
            $out .= '</small></div>';
		}

		return $out;
	} else {
		$out .= __('No tickets found!', 'tracpress');
		return $out;
	}

    return $out;
}




function tracpress_milestone($atts, $content = null) {
	extract(shortcode_atts(array(
		'category' => ''
	), $atts));

    $out = '';

	$args = array(
		'post_type' => get_option('ticket_slug'),
		'posts_per_page' => -1,

        'tax_query' => array(
            array(
                'taxonomy' => 'tracpress_ticket_milestone',
                'field' => 'id',
                'terms' => $category
            )
        )
	);
    $openposts = get_posts($args);
    $openposts = count($openposts);

	$args = array(
		'post_type' => get_option('ticket_slug'),
		'posts_per_page' => -1,

        'tax_query' => array(
            array(
                'taxonomy' => 'tracpress_ticket_milestone',
                'field' => 'id',
                'terms' => $category
            )
        ),

        'meta_key'                  => '_ticket_status',
        'meta_query'                => array(
            array(
                'key'           => '_ticket_status',
                'value'         => 'closed'
            )
        )
	);
    $closedposts = get_posts($args);
    $closedposts = count($closedposts);

    $out .= '<meter class="meter" value="' . $closedposts . '" min="0" max="' . $openposts . '" low="" high="" optimum="">' . $closedposts . '/' . $openposts . '</meter><div class="tp-meter-details">Total number of tickets: <b>' . $openposts . '</b> (closed: <b>' . $closedposts . '</b>, active: <b>' . ($openposts - $closedposts) . '</b>)</div>';
    return $out;
}


/*
 * Main shortcode function [tracpress_show]
 *
 */
function tracpress_show($atts, $content = null) {
	extract(shortcode_atts(array(
		'component'    => '',
		'count'       => 0,
        'limit'       => 999999,
		'user'        => 0,
	), $atts));

	global $current_user;

    $tp_order = get_option('tp_orderby');

	if($user > 0)
		$author = $user;
	if(isset($_POST['user']))
		$author = sanitize_user($_POST['user']);

    // defaults
    $tp_order_asc_desc = get_option('tp_order');
    //

    // main tickets query
	$out = '';

    // all filters should be applied here
    if(!empty($category))
        $args = array(
            'post_type' 				=> get_option('ticket_slug'),
            'posts_per_page' 			=> $limit,
            'orderby' 					=> $tp_order,
            'order' 					=> $tp_order_asc_desc,
            'author' 					=> $author,

            'tax_query' => array(
                array(
                    'taxonomy' => 'tracpress_ticket_component',
                    'field' => 'id',
                    'terms' => $component,
                    'include_children' => false
                )
            ),

            'cache_results' => false,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'no_found_rows' => true,
        );
    else
        $args = array(
            'post_type' 				=> get_option('ticket_slug'),
            'posts_per_page' 			=> $limit,
            'orderby' 					=> $tp_order,
            'order' 					=> $tp_order_asc_desc,
            'author' 					=> $author,

            'cache_results' => false,
            'update_post_term_cache' => false,
            'update_post_meta_cache' => false,
            'no_found_rows' => true,
        );
    $posts = get_posts($args);
    //

    $u = uniqid();

    if($posts) {
        $out .= '<script>
        jQuery(document).ready(function(){
            jQuery(".tracpress-' . $u . '").slimtable();
        });
        </script>';

        $out .= '<table id="sortme" class="tracpress-' . $u . '"><thead><tr>';
                    if(get_option('tp_id_optional') == 1)
                        $out .= '<th class="no-sort">Ticket</th>';
                    if(get_option('tp_summary_optional') == 1)
                        $out .= '<th>Summary</th>';
                    if(get_option('tp_author_optional') == 1)
                        $out .= '<th>Reporter</th>';
                    if(get_option('tp_component_optional') == 1)
                        $out .= '<th>Component</th>';
                    if(get_option('tp_priority_optional') == 1)
                        $out .= '<th>Priority</th>';
                    if(get_option('tp_severity_optional') == 1)
                        $out .= '<th>Severity</th>';
                    if(get_option('tp_milestone_optional') == 1)
                        $out .= '<th>Milestone</th>';
                    if(get_option('tp_type_optional') == 1)
                        $out .= '<th>Type</th>';
                    if(get_option('tp_workflow_optional') == 1)
                        $out .= '<th>Workflow</th>';
                    if(get_option('tp_comments_optional') == 1)
                        $out .= '<th><i class="fa fa-comments"></i></th>';
                    if(get_option('tp_plus_optional') == 1)
                        $out .= '<th>+1s</th>';
                    if(get_option('tp_date_optional') == 1)
                        $out .= '<th class="sort-default">Date</th>';
                $out .= '</tr></thead>';
        foreach($posts as $ticket) {
            setup_postdata($ticket);

			$user_info = get_userdata($ticket->post_author);

            $out .= '<tr>';
                if(get_option('tp_id_optional') == 1)
                    $out .= '<td><code>#' . $ticket->ID . '</code></td>';
                if(get_option('tp_summary_optional') == 1)
                    $out .= '<td><a href="' . get_permalink($ticket->ID) . '">' . get_the_title($ticket->ID) . '</a></td>';
                if(get_option('tp_author_optional') == 1)
                    $out .= '<td>' . $user_info->display_name . '</td>';
                if(get_option('tp_component_optional') == 1)
                    $out .= '<td>' . get_the_term_list($ticket->ID, 'tracpress_ticket_component', '', ', ', '') . '</td>';
                if(get_option('tp_priority_optional') == 1)
                    $out .= '<td>' . get_the_term_list($ticket->ID, 'tracpress_ticket_priority', '', ', ', '') . '</td>';
                if(get_option('tp_severity_optional') == 1)
                    $out .= '<td>' . get_the_term_list($ticket->ID, 'tracpress_ticket_severity', '', ', ', '') . '</td>';
                if(get_option('tp_milestone_optional') == 1)
                    $out .= '<td>' . get_the_term_list($ticket->ID, 'tracpress_ticket_milestone', '', ', ', '') . '</td>';
                if(get_option('tp_type_optional') == 1)
                    $out .= '<td>' . get_the_term_list($ticket->ID, 'tracpress_ticket_type', '', ', ', '') . '</td>';
                if(get_option('tp_workflow_optional') == 1)
                    $out .= '<td>' . get_the_term_list($ticket->ID, 'tracpress_ticket_workflow', '', ', ', '') . '</td>';
                if(get_option('tp_comments_optional') == 1)
                    $out .= '<td>' . get_comments_number($ticket->ID) . '</td>';
                if(get_option('tp_plus_optional') == 1)
                    $out .= '<td>' . getPostLikeLink($ticket->ID, false) . '</td>';
                if(get_option('tp_date_optional') == 1)
                    $out .= '<td>' . get_the_time('d/m/Y', $ticket->ID) . '</td>';
            $out .= '</tr>';
		}
        $out .= '</table>';

		return $out;
	} else {
		$out .= __('No tickets found!', 'tracpress');
		return $out;
	}

	return $out;
}

function tracpress_menu_bubble() {
	global $menu, $submenu;

	$args = array(
		'post_type' => get_option('ticket_slug'),
		'post_status' => 'pending',
		'showposts' => -1,
		'ignore_sticky_posts'=> 1
	);
	$draft_tp_links = count(get_posts($args));

	if($draft_tp_links) {
		foreach($menu as $key => $value) {
			if($menu[$key][2] == 'edit.php?post_type=' . get_option('ticket_slug')) {
				$menu[$key][0] .= ' <span class="update-plugins count-' . $draft_tp_links . '"><span class="plugin-count">' . $draft_tp_links . '</span></span>';
				return;
			}
		}
	}
	if($draft_tp_links) {
		foreach($submenu as $key => $value) {
			if($submenu[$key][2] == 'edit.php?post_type=' . get_option('ticket_slug')) {
				$submenu[$key][0] .= ' <span class="update-plugins count-' . $draft_tp_links . '"><span class="plugin-count">' . $draft_tp_links . '</span></span>';
				return;
			}
		}
	}
}

function notify_status($new_status, $old_status, $post) {
	global $current_user;
	$contributor = get_userdata($post->post_author);

	$headers[] = "MIME-Version: 1.0\r\n";
	$headers[] = "Content-Type: text/html; charset=\"" . get_option('blog_charset') . "\"\r\n";

	if($old_status != 'pending' && $new_status == 'pending') {
		$emails = get_option('tp_notification_email');
		if(strlen($emails)) {
			$subject = '[' . get_option('blogname') . '] "' . $post->post_title . '" pending review';
			$message = "<p>A new ticket by {$contributor->display_name} is pending review.</p>";
			$message .= "<p>Author: {$contributor->user_login} <{$contributor->user_email}> (IP: {$_SERVER['REMOTE_ADDR']})</p>";
			$message .= "<p>Title: {$post->post_title}</p>";
			$category = get_the_category($post->ID);
			if(isset($category[0])) 
				$message .= "<p>Category: {$category[0]->name}</p>";
			wp_mail($emails, $subject, $message, $headers);
		}
	}
	elseif($old_status == 'pending' && $new_status == 'publish') {
		if(get_option('approvednotification') == 'yes') {
			$subject = '[' . get_option('blogname') . '] "' . $post->post_title . '" approved';
			$message = "<p>{$contributor->display_name}, your ticket has been approved and published at " . get_permalink($post->ID) . ".</p>";
			wp_mail($contributor->user_email, $subject, $message, $headers);
		}
	}
	elseif($old_status == 'pending' && $new_status == 'draft' && $current_user->ID != $contributor->ID) {
		if(get_option('declinednotification') == 'yes') {
			$subject = '[' . get_option('blogname') . '] "' . $post->post_title . '" declined';
			$message = "<p>{$contributor->display_name}, your ticket has not been approved.</p>";
			wp_mail($contributor->user_email, $subject, $message, $headers);
		}
	}
}
?>
