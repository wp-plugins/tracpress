<?php
function tracpress_registration() {
    $ticket_slug = get_option('ticket_slug');

    // tickets
	$ticket_type_labels = array(
		'name' 					=> _x('Tickets', 'post type general name'),
		'singular_name' 		=> _x('Ticket', 'post type singular name'),
		'add_new' 				=> _x('Add New Ticket', 'image'),
		'add_new_item' 			=> __('Add New Ticket'),
		'edit_item' 			=> __('Edit Ticket'),
		'new_item' 				=> __('Add New Ticket'),
		'all_items' 			=> __('View Tickets'),
		'view_item' 			=> __('View Ticket'),
		'search_items' 			=> __('Search Tickets'),
		'not_found' 			=> __('No tickets found'),
		'not_found_in_trash' 	=> __('No tickets found in trash'), 
		'parent_item_colon' 	=> '',
		'menu_name' 			=> __('TracPress', 'tracpress')
	);

	$ticket_type_args = array(
		'labels' 				=> $ticket_type_labels,
		'public' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> true,
		'capability_type' 		=> 'post',
		'has_archive' 			=> true,
		'hierarchical' 			=> false,
		'map_meta_cap' 			=> true,
		'menu_position' 		=> null,
		'supports' 				=> array('title', 'editor', 'author', 'comments'),
		'menu_icon' 			=> 'dashicons-flag',
	);

	register_post_type($ticket_slug, $ticket_type_args);

    // types
	$ticket_type_labels = array(
		'name' 					=> _x('Ticket Types', 'taxonomy general name'),
		'singular_name' 		=> _x('Ticket Type', 'taxonomy singular name'),
		'search_items' 			=> __('Search Ticket Types'),
		'all_items' 			=> __('All Ticket Types'),
		'parent_item' 			=> __('Parent Ticket Type'),
		'parent_item_colon' 	=> __('Parent Ticket Type:'),
		'edit_item' 			=> __('Edit Ticket Type'), 
		'update_item' 			=> __('Update Ticket Type'),
		'add_new_item' 			=> __('Add New Ticket Type'),
		'new_item_name' 		=> __('New Ticket Type Name'),
		'menu_name' 			=> __('Ticket Types'),
	);

	$ticket_type_args = array(
		'hierarchical' 			=> true,
		'labels' 				=> $ticket_type_labels,
		'show_ui' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> array('slug' => 'type'),
	);

	register_taxonomy('tracpress_ticket_type', array($ticket_slug), $ticket_type_args);

    // components
	$ticket_component_labels = array(
		'name' 					=> _x('Ticket Components', 'taxonomy general name'),
		'singular_name' 		=> _x('Ticket Component', 'taxonomy singular name'),
		'search_items' 			=> __('Search Ticket Components'),
		'all_items' 			=> __('All Ticket Components'),
		'parent_item' 			=> __('Parent Ticket Component'),
		'parent_item_colon' 	=> __('Parent Ticket Component:'),
		'edit_item' 			=> __('Edit Ticket Component'), 
		'update_item' 			=> __('Update Ticket Component'),
		'add_new_item' 			=> __('Add New Ticket Component'),
		'new_item_name' 		=> __('New Ticket Component Name'),
		'menu_name' 			=> __('Ticket Components'),
	);

	$ticket_component_args = array(
		'hierarchical' 			=> true,
		'labels' 				=> $ticket_component_labels,
		'show_ui' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> array('slug' => 'component'),
	);

	register_taxonomy('tracpress_ticket_component', array($ticket_slug), $ticket_component_args);

    // severity
	$ticket_severity_labels = array(
		'name' 					=> _x('Ticket Severities', 'taxonomy general name'),
		'singular_name' 		=> _x('Ticket Severity', 'taxonomy singular name'),
		'search_items' 			=> __('Search Ticket Severities'),
		'all_items' 			=> __('All Ticket Severities'),
		'parent_item' 			=> __('Parent Ticket Severity'),
		'parent_item_colon' 	=> __('Parent Ticket Severity:'),
		'edit_item' 			=> __('Edit Ticket Severity'), 
		'update_item' 			=> __('Update Ticket Severity'),
		'add_new_item' 			=> __('Add New Ticket Severity'),
		'new_item_name' 		=> __('New Ticket Severity Name'),
		'menu_name' 			=> __('Ticket Severities'),
	);

	$ticket_severity_args = array(
		'hierarchical' 			=> true,
		'labels' 				=> $ticket_severity_labels,
		'show_ui' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> array('slug' => 'severity'),
	);

	register_taxonomy('tracpress_ticket_severity', array($ticket_slug), $ticket_severity_args);

    // priority
	$ticket_priority_labels = array(
		'name' 					=> _x('Ticket Priorities', 'taxonomy general name'),
		'singular_name' 		=> _x('Ticket Priority', 'taxonomy singular name'),
		'search_items' 			=> __('Search Ticket Priorities'),
		'all_items' 			=> __('All Ticket Priorities'),
		'parent_item' 			=> __('Parent Ticket Priority'),
		'parent_item_colon' 	=> __('Parent Ticket Priority:'),
		'edit_item' 			=> __('Edit Ticket Priority'), 
		'update_item' 			=> __('Update Ticket Priority'),
		'add_new_item' 			=> __('Add New Ticket Priority'),
		'new_item_name' 		=> __('New Ticket Priority Name'),
		'menu_name' 			=> __('Ticket Priorities'),
	);

	$ticket_priority_args = array(
		'hierarchical' 			=> true,
		'labels' 				=> $ticket_priority_labels,
		'show_ui' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> array('slug' => 'priority'),
	);

	register_taxonomy('tracpress_ticket_priority', array($ticket_slug), $ticket_priority_args);

    // milestone
	$ticket_milestone_labels = array(
		'name' 					=> _x('Milestones', 'taxonomy general name'),
		'singular_name' 		=> _x('Milestone', 'taxonomy singular name'),
		'search_items' 			=> __('Search Milestones'),
		'all_items' 			=> __('All Milestones'),
		'parent_item' 			=> __('Parent Milestone'),
		'parent_item_colon' 	=> __('Parent Milestone:'),
		'edit_item' 			=> __('Edit Milestone'), 
		'update_item' 			=> __('Update Milestone'),
		'add_new_item' 			=> __('Add New Milestone'),
		'new_item_name' 		=> __('New Milestone Name'),
		'menu_name' 			=> __('Milestones'),
	);

	$ticket_milestone_args = array(
		'hierarchical' 			=> true,
		'labels' 				=> $ticket_milestone_labels,
		'show_ui' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> array('slug' => 'milestone'),
	);

	register_taxonomy('tracpress_ticket_milestone', array($ticket_slug), $ticket_milestone_args);

    // workflow
	$ticket_workflow_labels = array(
		'name' 					=> _x('Ticket Workflows', 'taxonomy general name'),
		'singular_name' 		=> _x('Ticket Workflow', 'taxonomy singular name'),
		'search_items' 			=> __('Search Ticket Workflows'),
		'all_items' 			=> __('All Ticket Workflows'),
		'parent_item' 			=> __('Parent Ticket Workflow'),
		'parent_item_colon' 	=> __('Parent Ticket Workflow:'),
		'edit_item' 			=> __('Edit Ticket Workflow'), 
		'update_item' 			=> __('Update Ticket Workflow'),
		'add_new_item' 			=> __('Add New Ticket Workflow'),
		'new_item_name' 		=> __('New Ticket Workflow Name'),
		'menu_name' 			=> __('Ticket Workflows'),
	);

	$ticket_workflow_args = array(
		'hierarchical' 			=> true,
		'labels' 				=> $ticket_workflow_labels,
		'show_ui' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> array('slug' => 'workflow'),
	);

	register_taxonomy('tracpress_ticket_workflow', array($ticket_slug), $ticket_workflow_args);

    // tags
    $labels = array(
		'name'                       => _x('Ticket Tags', 'Taxonomy General Name', 'tracpress'),
		'singular_name'              => _x('Ticket Tag', 'Taxonomy Singular Name', 'tracpress'),
		'menu_name'                  => __('Ticket Tags', 'tracpress'),
		'all_items'                  => __('All Tags', 'tracpress'),
		'parent_item'                => __('Parent Tag', 'tracpress'),
		'parent_item_colon'          => __('Parent Tag:', 'tracpress'),
		'new_item_name'              => __('New Tag Name', 'tracpress'),
		'add_new_item'               => __('Add New Tag', 'tracpress'),
		'edit_item'                  => __('Edit Tag', 'tracpress'),
		'update_item'                => __('Update Tag', 'tracpress'),
		'separate_items_with_commas' => __('Separate tags with commas', 'tracpress'),
		'search_items'               => __('Search Tags', 'tracpress'),
		'add_or_remove_items'        => __('Add or remove tags', 'tracpress'),
		'choose_from_most_used'      => __('Choose from the most used tags', 'tracpress'),
		'not_found'                  => __('Not Found', 'tracpress'),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => false,
	);

    register_taxonomy('tracpress_ticket_tag', array($ticket_slug), $args);
}

$timebeforerevote = get_option('tp_timebeforerevote'); // in hours

if(!function_exists('post_like')) {
    function post_like() {
        $nonce = $_POST['nonce'];
        if(!wp_verify_nonce($nonce, 'ajax-nonce'))
            die();

        if(isset($_POST['post_like'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
            $post_id = intval($_POST['post_id']);

            $meta_IP = get_post_meta($post_id, 'voted_IP');

            $voted_IP = $meta_IP[0];
            if(!is_array($voted_IP))
                $voted_IP = array();

            $meta_count = get_post_meta($post_id, "votes_count", true);

            if(!hasAlreadyVoted($post_id)) {
                $voted_IP[$ip] = time();

                update_post_meta($post_id, "voted_IP", $voted_IP);
                update_post_meta($post_id, "votes_count", ++$meta_count);

                echo $meta_count;
            }
            else
                echo 'already';
        }
        exit;
    }
}

if(!function_exists('hasAlreadyVoted')) {
    function hasAlreadyVoted($post_id) {
        global $timebeforerevote;

        $meta_IP = get_post_meta($post_id, 'voted_IP');
        if(!empty($meta_IP[0]))
            $voted_IP = $meta_IP[0];
        else
            $voted_IP = '';

        if(!is_array($voted_IP))
            $voted_IP = array();

        $ip = $_SERVER['REMOTE_ADDR'];

        if(in_array($ip, array_keys($voted_IP))) {
            $time = $voted_IP[$ip];
            $now = time();

            if(round(($now - $time) / 60) > $timebeforerevote)
                return false;

            return true;
        }

        return false;
    }
}

if(!function_exists('getPostLikeLink')) {
    function getPostLikeLink($post_id, $enable = true) {
        $vote_count = get_post_meta($post_id, 'votes_count', true);
        if(empty($vote_count))
            $vote_count = 0;

        if($enable == true) {
            if(hasAlreadyVoted($post_id))
                $output = '<span class="post-like"><a class="hasvoted" href="#" data-post_id="' . $post_id . '">+1</a> ' . $vote_count . '</span>';
            else
                $output = '<span class="post-like"><a class="hasnotvoted" href="#" data-post_id="' . $post_id . '">+1</a> ' . $vote_count . '</span>';
        }
        else
            $output = '<span class="post-like">' . $vote_count . '</span>';

        return $output;
    }
}

// front-end image editor
function wp_get_object_terms_exclude_filter($terms, $object_ids, $taxonomies, $args) {
    if(isset($args['exclude']) && $args['fields'] == 'all') {
        foreach($terms as $key => $term) {
            foreach($args['exclude'] as $exclude_term) {
                if($term->term_id == $exclude_term) {
                    unset($terms[$key]);
                }
            }
        }
    }
    $terms = array_values($terms);
    return $terms;
}
add_filter('wp_get_object_terms', 'wp_get_object_terms_exclude_filter', 10, 4);

// frontend image editor
function tp_editor() {
    global $post, $current_user;

    get_currentuserinfo();

    // check if user is author // show author tools
    if($post->post_author == $current_user->ID) { ?>
        <section>
            <p><a href="#" class="tp-editor-display"><i class="fa fa-wrench"></i> Reporter tools</a></p>
        </section>
        <?php
        $edit_id = get_the_ID();

        if(isset($_GET['d'])) {
            $post_id = $_GET['d'];
            wp_delete_post($post_id);
            echo '<script>window.location.href="' . home_url() . '?deleted"</script>';
        }
        if('POST' == $_SERVER['REQUEST_METHOD'] && !empty($_POST['post_id']) && !empty($_POST['post_title']) && isset($_POST['update_post_nonce']) && isset($_POST['postcontent'])) {
            $post_id = intval($_POST['post_id']);
            $post_type = get_post_type($post_id);
            $capability = ('page' == $post_type) ? 'edit_page' : 'edit_post';
            if(current_user_can($capability, $post_id) && wp_verify_nonce($_POST['update_post_nonce'], 'update_post_'. $post_id)) {
                $post = array(
                    'ID'             => esc_sql($post_id),
                    'post_content'   => (stripslashes($_POST['postcontent'])),
                    'post_title'     => esc_sql($_POST['post_title'])
                );
                wp_update_post($post);

                // multiple images
                if(1 == get_option('tp_upload_secondary')) {
                    $files = $_FILES['tracpress_additional'];
                    if($files) {
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
                            $_FILES = array("tracpress_additional" => $file);
                            foreach($_FILES as $file => $array) {
                                tracpress_process_image_secondary('tracpress_additional', $post_id, '');
                            }
                        }
                    }
                }
                // end multiple images

                wp_set_object_terms($post_id, (int)$_POST['tracpress_ticket_type'], 'tracpress_ticket_type');
                if(get_option('tracpress_allow_components') == 1)
                    wp_set_object_terms($post_id, (int)$_POST['tracpress_ticket_component'], 'tracpress_ticket_component');

                if('' != get_option('ticket_version_label'))
                    update_post_meta((int)$post_id, 'ticket_version', (string)$_POST['ticket_version']);

                echo '<script>window.location.href="' . $_SERVER['REQUEST_URI'] . '"</script>';
            }
            else {
                wp_die("You can't do that");
            }
        }
        ?>
        <div id="info" class="tp-editor">
            <form id="post" class="post-edit front-end-form tracpress-form" method="post" enctype="multipart/form-data">
                <input type="hidden" name="post_id" value="<?php echo $edit_id; ?>">
                <?php wp_nonce_field('update_post_' . $edit_id, 'update_post_nonce'); ?>

                <p><input type="text" id="post_title" name="post_title" value="<?php echo get_the_title($edit_id); ?>"></p>
                <p><textarea name="postcontent" rows="3"><?php echo strip_tags(get_post_field('post_content', $edit_id)); ?></textarea></p>
                <hr>
                <?php if('' != get_option('ticket_version_label')) { ?>
                    <p><input type="text" name="ticket_version" value="<?php echo get_post_meta($edit_id, 'ticket_version', true); ?>" placeholder="<?php echo get_option('ticket_version_label'); ?>"></p>
                <?php } ?>
                <hr>

                <?php $tp_category = wp_get_object_terms($edit_id, 'tracpress_ticket_type', array('exclude' => array(4))); ?>
                <?php if(get_option('tracpress_allow_components') == 1) $tp_tag = wp_get_post_terms($edit_id, 'tracpress_ticket_component'); ?>

                <p>
                    <?php echo tracpress_get_categories_dropdown('tracpress_ticket_type', $tp_category[0]->term_id); ?> 
                    <?php if(get_option('tracpress_allow_components') == 1) echo tracpress_get_tags_dropdown('tracpress_ticket_component', $tp_tag[0]->term_id); ?> 
                </p>

                <?php if(1 == get_option('tp_upload_secondary')) { ?>
                    <hr>
                    <?php
                    $media = get_attached_media('', $edit_id);
                    if($media) {
                        foreach($media as $attachment) {
                            echo '<a href="#" data-id="' . $attachment->ID . '" data-nonce="' . wp_create_nonce('my_delete_post_nonce') . '" class="delete-post tp-action-icon"><i class="fa fa-times-circle"></i></a> ' . $attachment->post_title . '</a> <small>(' . $attachment->post_mime_type . ' | ' . $attachment->post_date . ')</small><br>';
                        }
                    }
                    ?>
                    <hr>

                    <p><label for="tracpress_additional"><i class="fa fa-cloud-upload"></i> Add more files...</label><br><input type="file" name="tracpress_additional[]" id="tracpress_additional" multiple></p>
                <?php } ?>

                <hr>
                <p>
                    <input type="submit" id="submit" value="Update ticket" class="button noir-secondary">
                    <a href="?d=<?php echo get_the_ID(); ?>" class="ask button tp-floatright"><i class="fa fa-trash-o"></i></a>
                </p>
            </form>
        </div>
        <?php wp_reset_query(); ?>
    <?php }
}

// tp_editor() related actions
add_action('wp_ajax_my_delete_post', 'my_delete_post');
function my_delete_post() {
    $permission = check_ajax_referer('my_delete_post_nonce', 'nonce', false);
    if($permission == false) {
        echo 'error';
    }
    else {
        wp_delete_post($_REQUEST['id']);
        echo 'success';
    }
    die();
}



// main TracPress image function
function tp_main($i) {
    // show image editor
    tp_editor();
    ?>

    <b>#<?php echo $i; ?></b> <?php echo getPostLikeLink($i); ?>
    <h3><?php echo get_the_title($i); ?></h3>
    <p>
        <?php
        if(get_option('tracpress_allow_components') == 1)
            echo '<i class="fa fa-fw fa-info-circle"></i> Component: ' . get_the_term_list(get_the_ID(), 'tracpress_ticket_component', '', ', ', '') . '<br>';
        ?>
        <i class="fa fa-fw fa-user"></i> Reported by <b><?php the_author_posts_link(); ?></b> <time title="<?php the_time(get_option('date_format')); ?>"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></time> as <?php echo get_the_term_list(get_the_ID(), 'tracpress_ticket_type', '', ', ', ''); ?>
    </p>
    <p>
        <small>
            <b><i class="fa fa-fw fa-info-circle"></i> Priority:</b> <?php echo get_the_term_list(get_the_ID(), 'tracpress_ticket_priority', '', ', ', ''); ?> | 
            <b>Severity:</b> <?php echo get_the_term_list(get_the_ID(), 'tracpress_ticket_severity', '', ', ', ''); ?>
        </small>
        <br><small><i class="fa fa-fw fa-clock-o"></i> Last modified: <?php the_modified_date(get_option('date_format')); ?> <?php the_modified_time('H:i:s'); ?></small>
    </p>

    <section>
        <hr>
        <?php echo wpautop(make_clickable(get_the_content())); ?>
        <p>
            <?php
            $media = get_attached_media('', $i);
            if($media) {
                echo '<hr>';
                foreach($media as $attachment) {
                    echo '<a href="' . $attachment->guid . '">' . $attachment->post_title . '</a> <small>(' . $attachment->post_mime_type . ' | ' . $attachment->post_date . ')</small></a><br>';
                }
            }
            ?>
        </p>
        <hr>
    </section>

    <section role="navigation">
        <?php previous_post_link('%link', '<i class="fa fa-fw fa-chevron-left"></i> Previous'); ?>
        <?php next_post_link('%link', 'Next <i class="fa fa-fw fa-chevron-right"></i>'); ?>
    </section>

    <?php
}




/**
 * Adds a box to the main column on the ticket edit screens.
 */
function tracpress_add_meta_box() {
    $screens = array(get_option('ticket_slug'));
	foreach($screens as $screen) {
        add_meta_box('tracpress_x', __('Ticket Status', 'tracpress'), 'tracpress_meta_box_callback', $screen, 'side', 'high');
    }
}
add_action('add_meta_boxes', 'tracpress_add_meta_box');

function tracpress_meta_box_callback($post) {
    wp_nonce_field('tracpress_meta_box', 'tracpress_meta_box_nonce');
    $value1 = get_post_meta($post->ID, '_ticket_status', true);
    $value2 = get_post_meta($post->ID, '_ticket_resolution', true);

    echo '<p><label for="tracpress_status">Current status of the ticket</label><br>';
    echo '<select id="tracpress_status" name="tracpress_status">
        <option value="' . esc_attr( $value1 ) . '" selected>' . esc_attr( $value1 ) . '</option>
        <option value="new">new</option>
        <option value="accepted">accepted</option>
        <option value="assigned">assigned</option>
        <option value="reviewing">reviewing</option>
        <option value="closed">closed</option>
        <option value="reopened">reopened</option>
    </select></p>';

        echo '<p><label for="tracpress_resolution">Current resolution of the ticket</label><br>';
    echo '<select id="tracpress_resolution" name="tracpress_resolution">
        <option value="' . esc_attr( $value2 ) . '" selected>' . esc_attr( $value2 ) . '</option>
        <option value="fixed">fixed</option>
        <option value="invalid">invalid</option>
        <option value="wontfix">wontfix</option>
        <option value="done">done</option>
        <option value="wontdo">wontdo</option>
        <option value="postpone">postpone</option>
        <option value="resolved">resolved</option>
    </select></p>';

}

function tracpress_save_meta_box_data($post_id) {
    if(!isset($_POST['tracpress_meta_box_nonce']))
		return;
    if(!wp_verify_nonce($_POST['tracpress_meta_box_nonce'], 'tracpress_meta_box'))
		return;
    if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
		return;

	if(isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
        if(!current_user_can('edit_page', $post_id))
			return;
	}
    else {
		if(!current_user_can('edit_post', $post_id))
            return;
    }

//	if(!isset($_POST['tracpress_status']))
//        return;

	$tracpress_status = sanitize_text_field($_POST['tracpress_status']);
	$tracpress_resolution = sanitize_text_field($_POST['tracpress_resolution']);

	update_post_meta($post_id, '_ticket_status', $tracpress_status);
	update_post_meta($post_id, '_ticket_resolution', $tracpress_resolution);
}
add_action('save_post', 'tracpress_save_meta_box_data');
?>
