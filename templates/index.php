<?php get_header(); ?>

<main class="spine-archive-index">

	<?php get_template_part('parts/headers'); ?>

	<section class="row single gutter pad-ends">

		<div class="column one">

			<?php echo wpautop( wp_kses_post( get_option( 'programs_archive_text' ) ) ); ?>

		</div><!--/column-->

	</section>

	<section class="row side-right gutter pad-ends full-bleed gray-darker-back">

		<div class="column one program-list">

			<?php
				global $query_string;
				query_posts( $query_string . '&posts_per_page=-1&orderby=name&order=asc' );
				if ( have_posts() ) :
					while ( have_posts() ) : the_post();
					?>
						<?php
							$image_class = '';
							$image_style = '';
							if ( has_post_thumbnail() ) {
								$image_class = ' has-image';
								$image_array = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
								$image_style = ' style="background-image: url(' . esc_url( $image_array[0] ) . ');"';
							}
						?>
						<dl class="<?php echo $image_class; ?>"<?php echo $image_style; ?>>
							<dt>
								<h4><?php the_title(); ?></h4>
							</dt>
							
							<dd>
								<?php
									the_content();
									$url = get_post_meta( get_the_ID(), '_program_url', true );
									if ( $url ) {
									?>
										<p class="more-button center"><a title="Visit the <?php the_title(); ?> website" href="<?php echo esc_url( $url ); ?>">Visit the website</a></p>
									<?php
									}
									/*if ( has_post_thumbnail() ) {
										the_post_thumbnail( 'medium' );
									}*/
								?>
							</dd>
						</dl>
					<?php
					endwhile;
				endif;
			?>

		</div><div class="column two">

			<h2>Topics</h2>
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