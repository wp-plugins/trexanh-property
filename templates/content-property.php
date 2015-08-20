<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The default template for displaying property
 *
 */
global $property;
?>

<article id="post-<?php the_ID(); ?>" class="property-item">
    <?php do_action( 'trexanhproperty_before_listing_loop_item' ); ?>
	<a href="<?php the_permalink(); ?>">

		<?php
			/**
			 * trexanhproperty_before_listing_loop_item_title hook
			 *
			 * @hooked trexanhproperty_template_loop_listing_thumbnail - 10
			 */
			do_action( 'trexanhproperty_before_listing_loop_item_title' );
		?>

		<h3><?php the_title(); ?></h3>

		<?php
			/**
			 * trexanhproperty_after_shop_loop_item_title hook
			 *
			 * @hooked trexanhproperty_template_loop_save_property - 5
			 * @hooked trexanhproperty_template_loop_summary - 10
			 */
			do_action( 'trexanhproperty_after_listing_loop_item_title' );
		?>

	</a>

	<?php

		/**
		 * trexanhproperty_after_shop_loop_item hook
		 *
		 */
		do_action( 'trexanhproperty_after_listing_loop_item' );

	?>                
</article>
