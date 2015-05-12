<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'property' ); ?>

<?php

	?>

		<?php while ( have_posts() ) : the_post();?>
			<?php txp_get_template_part( 'content-single-property.php' ); ?>
		<?php endwhile; // end of the loop. ?>

<?php get_footer( 'property' ); ?>
