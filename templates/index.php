<?php get_header(); ?>

<main class="spine-archive-index">

	<?php get_template_part('parts/headers'); ?>

	<section class="row single gutter pad-ends">

		<div class="column one">

			<?php echo wpautop( wp_kses_post( get_option( 'programs_archive_text' ) ) ); ?>

		</div><!--/column-->

	</section>

	<section class="row side-right gutter pad-ends programs full-bleed gray-darker-back gray-er-text">

		<div class="column one program-list">

			<?php
				global $query_string;
				query_posts( $query_string . '&posts_per_page=-1&orderby=title&order=ASC' );
				if ( have_posts() ) :
					while ( have_posts() ) : the_post();
						load_template( dirname( __FILE__ ) . '/post.php', false );
					endwhile;
				endif;
			?>

		</div>

		<div class="column two">

			<h2>Filter by Topic</h2>
			<?php
      	$topics = get_terms( 'topic', array( 'parent' => 0 ) );
				if ( ! empty( $topics ) && ! is_wp_error( $topics ) ) :
				?>
					<ul class="browse-terms topics">
					<?php foreach ( $topics as $topic ) : ?>
						<li class="topic-<?php echo $topic->slug; ?>">
							<a href="<?php echo get_term_link( $topic ); ?>" data-type="topic" data-slug="<?php echo $topic->slug; ?>" data-name="<?php echo $topic->name; ?>"><?php echo $topic->name; ?></a>
						</li>
					<?php endforeach; ?>
					</ul>
			<?php endif; ?>

		</div>

	</section>

</main>

<?php

get_footer();