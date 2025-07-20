<?php
/**
 * Plugin Name: Masonry Post Block
 * Description: Gutenberg block to display posts with pagination and masonry layout.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) exit;

function mpb_register_block_assets() {
    wp_register_script(
        'mpb-block',
        plugins_url('build/index.js', __FILE__),
        ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-data', 'wp-block-editor'],
        filemtime(plugin_dir_path(__FILE__) . 'build/index.js')
    );

    wp_register_style(
        'mpb-masonry-style',
        plugins_url('css/masonry.css', __FILE__),
        [],
        filemtime(plugin_dir_path(__FILE__) . 'css/masonry.css')
    );

    register_block_type('mpb/masonry-post-block', [
        'editor_script' => 'mpb-block',
        'style' => 'mpb-masonry-style',
        'render_callback' => 'mpb_render_posts_block',
        'attributes' => [
            'postsPerPage' => ['type' => 'number', 'default' => 6],
            'useMasonry' => ['type' => 'boolean', 'default' => true],
            'paginate' => ['type' => 'boolean', 'default' => true],
        ],
    ]);
}
add_action('init', 'mpb_register_block_assets');

function mpb_render_posts_block($attributes) {
    $paged = get_query_var('paged') ?: 1;
    $args = [
        'posts_per_page' => $attributes['postsPerPage'],
        'paged' => $paged,
    ];
    $query = new WP_Query($args);
    ob_start();
    ?>
    <div class="mpb-posts<?php echo $attributes['useMasonry'] ? ' masonry' : ''; ?>">
        <?php while ($query->have_posts()): $query->the_post(); ?>
            <div class="mpb-post">
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            </div>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
    <?php
    if ($attributes['paginate']) {
        echo paginate_links([
            'total' => $query->max_num_pages,
        ]);
    }
    return ob_get_clean();
}

