<?php
$image_style = '';
if ( has_post_thumbnail() ) {
	$image_array = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
	$image_style = ' style="background-image: url(' . esc_url( $image_array[0] ) . ');"';
}
?>
<dl class="cahnrs-accordion"<?php echo $image_style; ?>>

	<dt>
		<h2><?php the_title(); ?></h2>
	</dt>
							
	<dd>
		<?php
			the_content();
			$url = get_post_meta( get_the_ID(), '_program_url', true );
			if ( $url ) {
			?>
				<p class="more-button center"><a title="Visit the <?php the_title(); ?> website" href="<?php echo esc_url( $url ); ?>" target="_blank">Visit the website</a></p>
			<?php
			}
		?>
	</dd>
</dl>