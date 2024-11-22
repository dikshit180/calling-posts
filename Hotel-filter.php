<?php
/* Template Name: Hotel Search */
get_header();
?>

<div id="hotel-search">
    <form id="hotel-filter">
        <input type="text" name="title" placeholder="Search by title">
        <?php
        $terms = get_terms('hotel_faq_categories');
        if (!empty($terms)) {
            echo '<select name="category">';
            echo '<option value="">All Categories</option>';
            foreach ($terms as $term) {
                echo '<option value="' . $term->slug . '">' . $term->name . '</option>';
            }
            echo '</select>';
        }
        ?>
    </form>

    <div class="hotel-results">
        <?php
        // Default query to show all hotels
        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
        $default_args = array(
            'post_type' => 'hotel_faq',
            'posts_per_page' => 5,
            'paged' => $paged,
        );
        $default_query = new WP_Query($default_args);

        if ($default_query->have_posts()) {
            echo '<div class="accordion accordion-flush" id="accordionFlushExample">';
            $c = 0;

            while ($default_query->have_posts()) : $default_query->the_post();
                $c++;
                $accordion_id = 'flush-collapse' . $c;
                $heading_id = 'flush-heading' . $c;
                ?>
                
                <div class="accordion-item">
                    <h2 class="accordion-header" id="<?php echo esc_attr($heading_id); ?>">
                        <button class="accordion-button <?php echo ($c === 1) ? '' : 'collapsed'; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#<?php echo esc_attr($accordion_id); ?>" aria-expanded="<?php echo ($counter === 1) ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr($accordion_id); ?>">
                            <?php the_title(); ?>
                        </button>
                    </h2>
                    <div id="<?php echo esc_attr($accordion_id); ?>" class="accordion-collapse collapse <?php echo ($c === 1) ? 'show' : ''; ?>" aria-labelledby="<?php echo esc_attr($heading_id); ?>" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">
                            <?php $content = get_post_meta(get_the_ID(), 'wo_blue_box', true);
                        echo esc_html($content); 
						?>
                        </div>
                    </div>
                </div>

                <?php
            endwhile;
            echo '</div>';

            // Pagination links
            echo '<div class="pagination">';
            echo paginate_links(array(
                'total' => $default_query->max_num_pages,
                'current' => $paged,
                'format' => '?page=%#%',
                'add_args' => false,
            ));
            echo '</div>';

        } else {
            echo 'No results found.';
        }
        wp_reset_postdata();
        ?>
    </div>
</div>
<div>

</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
jQuery(document).ready(function($) {
    function fetchHotels(page) {
        var filter = $('#hotel-filter').serialize();
        var data = filter + '&action=hotel_search&page=' + page;

        $.ajax({
            url: '<?php echo admin_url('admin-ajax.php'); ?>',
            type: 'POST',
            data: data,
            success: function(response) {
            $('.hotel-results').html(response);
			
         }
                
            
        });
    }

    
    $('#hotel-filter input, #hotel-filter select').on('change input', function() {
        fetchHotels(1);
    });

    
    $('.hotel-results').on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];
        fetchHotels(page);
    });
});

</script>

<?php
get_footer();


?>
