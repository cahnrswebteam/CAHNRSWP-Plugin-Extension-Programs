<?php get_header(); ?>

<main class="spine-archive-index">

	<?php get_template_part('parts/headers'); ?>

	<section class="row single gutter pad-ends">

		<div class="column one">

			<?php echo wpautop( wp_kses_post( get_option( 'programs_archive_text' ) ) ); ?>

		</div><!--/column-->

	</section>

	<section class="row side-right gutter pad-ends full-bleed gray-darker-back">

		<div class="column one">

			<?php
				global $query_string;
				query_posts( $query_string . '&posts_per_page=-1&orderby=name&order=asc' );
				if ( have_posts() ) :
				?>
					<ul id="ext-programs">
					<?php while ( have_posts() ) : the_post(); ?>
						<?php
							$url = get_post_meta( get_the_ID(), '_program_url', true );
							$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
						?>
						<li><a href="<?php echo esc_url( $url ); ?>" data-desc="<?php the_content(); ?>" data-img="<?php esc_attr_e( $image[0] ); ?>"><?php the_title(); ?></a></li>
					<?php endwhile; ?>
					</ul>
				<?php endif; ?>
		</div><div class="column two">

			<h2>Topics</h2>
			<?php
      	$topics = get_terms( 'topic', array( 'parent' => 0 ) );
				if ( ! empty( $topics ) && ! is_wp_error( $topics ) ) :
				?>
					<ul class="browse-terms topics">
					<?php foreach ( $topics as $topic ) : ?>
						<li class="topic-'<?php echo $topic->slug; ?>">
							<a href="<?php echo get_term_link( $topic ); ?>" data-type="topic" data-slug="<?php echo $topic->slug; ?>" data-name="<?php echo $topic->name; ?>"><?php echo $topic->name; ?></a>
						</li>
					<?php endforeach; ?>
					</ul>
			<?php endif; ?>

			<div id="ext-program-preview">

				<?php
					$featured = new WP_Query( 'post_type=extension_program&orderby=rand&posts_per_page=1' );
					if ( $featured->have_posts() ) :
						while ( $featured->have_posts() ) : $featured->the_post();
						?>
							<?php $url = get_post_meta( get_the_ID(), '_program_url', true ); ?>
							<header class="article-title">
								<h4>
									<a title="Go to the <?php the_title(); ?> website" href="<?php echo esc_url( $url ); ?>"><?php the_title(); ?> <span class="dashicons dashicons-external"></span></a>
								</h4>
							</header>
							<div class="article-summary">
								<?php the_content(); ?>
								<?php
									if ( has_post_thumbnail() ) {
										the_post_thumbnail( 'medium' );
									}
								?>
							</div>
						<?php
						endwhile;
					endif;
					wp_reset_postdata();
				?>

			</div>

		</div>

	</section>

	<hr class="ext-preview-stopper" />

</main>

<?php

get_footer();