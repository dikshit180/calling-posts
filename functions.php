<?php

// Handle AJAX request for hotel search
function hotel_search_handler() {
    // Get filter values
    $title = sanitize_text_field($_POST['title']);
    $category = sanitize_text_field($_POST['category']);
    $paged = isset($_POST['page']) ? absint($_POST['page']) : 1;

    // Build query arguments
    $args = array(
        'post_type' => 'hotel_faq',
        'posts_per_page' => 1,
        'paged' => $paged,
    );

    if (!empty($title)) {
        $args['s'] = $title;
    }

    if (!empty($category)) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'hotel_faq_categories',
                'field'    => 'slug',
                'terms'    => $category,
            ),
        );
    }

    // Query posts
    $query = new WP_Query($args);

    // Display results
    if ($query->have_posts()) {
        echo '<div class="accordion accordion-flush" id="accordionFlushExample">';
        $c = 0;

        while ($query->have_posts()) : $query->the_post();
            $c++;
            $accordion_id = 'flush-collapse' . $c;
            $heading_id = 'flush-heading' . $c;
            ?>

            <div class="accordion-item">
                <h2 class="accordion-header" id="<?php echo esc_attr($heading_id); ?>">
                    <button class="accordion-button <?php echo ($c === 1) ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr($accordion_id); ?>" aria-expanded="<?php echo ($c === 1) ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($accordion_id); ?>">
                        <?php the_title(); ?>
                    </button>
                </h2>
                <div id="<?php echo esc_attr($accordion_id); ?>" class="accordion-collapse collapse <?php echo ($c === 1) ? 'show' : ''; ?>" aria-labelledby="<?php echo esc_attr($heading_id); ?>" data-bs-parent="#accordionFlushExample">
                    <div class="accordion-body">
                        <?php $content = get_post_meta(get_the_ID(), 'wo_blue_box', true);
                        echo esc_html($content); ?>
                    </div>
                </div>
            </div>

            <?php
        endwhile;
        echo '</div>';

        // Pagination links
        echo '<div class="pagination">';
        echo paginate_links(array(
            'total' => $query->max_num_pages,
            'current' => $paged,
            'format' => '?page=%#%',
            'add_args' => false,
        ));
        echo '</div>';
    } else {
        echo 'No results found.';
    }

    wp_reset_postdata();

    // Terminate script to avoid additional output
    wp_die();
}
add_action('wp_ajax_hotel_search', 'hotel_search_handler');
add_action('wp_ajax_nopriv_hotel_search', 'hotel_search_handler');
