<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The Template for displaying property archives
 *
 * Override this template by copying it to yourtheme/trexanh-property/archive-property.php
 */
if ( !defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

get_header( 'property' );
?>
<section id="primary" class="content-area">
    <main id="main" class="site-main" role="main">
        <header class="page-header">
            <h1 class="page-title">
                <?php
                if ( is_search() ) {
                    if ( get_query_var( 's' ) ) {
                        echo sprintf( __( 'Search Result for &ldquo;%s&rdquo;', 'txp' ), get_search_query() );
                    } else {
                        echo __( 'Search Result', 'txp' );
                    }
                }
                ?>
            </h1>
        </header>
        <?php if ( have_posts() ) : ?>
            <div class="hentry properties-listing">
                <div class="property-items-container">
                    <?php while ( have_posts() ) : the_post(); ?>

                        <?php txp_get_template_part( 'content-property.php' ); ?>

                    <?php endwhile; // end of the loop.  ?>
                </div>
            </div>

            <?php
                // Previous/next page navigation.
                the_posts_pagination( array(
                    'prev_text'          => __( 'Previous page', 'txp' ),
                    'next_text'          => __( 'Next page', 'txp' ),
                    'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'txp' ) . ' </span>',
                ) );
            ?>
        <?php else : ?>

            <p><?php _e( 'No properties were found matching your selection.', 'txp' ); ?></p>

        <?php endif; ?>
    </main>
</section>

<?php get_sidebar(); ?>
<?php get_footer( 'property' ); ?>
