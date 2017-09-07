<?php
/*
Plugin Name: WP Tab HunterKing
Description: WP Tab HunterKing
Author: HunterKing
Version: 1.0.0
*/

/*
 * Khởi tạo widget item
 */

function create_hunterking_widget() {
        register_widget('Hunterking_Tabs_Widget');
}

function add_scripts()
{
    wp_register_script( 'custom-script', plugins_url( '/js/widget_tab.js', __FILE__ ), array( 'jquery', 'jquery-ui-core' ), '20120208', true );
    wp_enqueue_script( 'custom-script' );
}


function add_styles()
{
    wp_register_style( 'custom-style', plugins_url( '/css/widget_tab.css', __FILE__ ), array(), '20120208', 'all' );
    wp_enqueue_style( 'custom-style' );
}

add_action( 'wp_enqueue_scripts', 'add_scripts' );
add_action( 'widgets_init', 'create_hunterking_widget' );
add_action( 'wp_enqueue_scripts', 'add_styles' );

class Hunterking_Tabs_Widget extends WP_Widget {

	/**
	 * Sets up the widgets.
	 *
	 * @since 1.0.0
	 */
	function __construct() {
		// Set up the widget options.
		$widget_options = array(
			'classname'   => 'widget-hunterking-tabs widget_tabs posts-thumbnail-widget',
			'description' => esc_html__( 'Display popular posts, recent posts, recent comments and tags in tabs.', 'hunterking' )
		);

		 parent::__construct(
            'sidebar_post',
            'HunterKing',
			$widget_options
        );
	}


	/**
	 * Outputs the widget based on the arguments input through the widget controls.
	 *
	 * @since 1.0.0
	 */
	function widget( $args, $instance ) {
		extract( $args );


		// Output the theme's $before_widget wrapper.
		echo $before_widget;
		?>

		<ul class="tabs-nav">
			<li class="active" ><a href="#tab1" title="<?php esc_attr_e( 'Bài mới', 'hunterking' ); ?>"><?php esc_html_e( 'Bài mới', 'hunterking' ); ?></a></li>
			<li ><a href="#tab2"  title="<?php esc_attr_e( 'Liên quan', 'hunterking' ); ?>"><?php esc_html_e( 'Liên quan', 'hunterking' ); ?></a></li>
			<!-- <li><a href="#tab3" title="<?php esc_attr_e( 'Comments', 'hunterking' ); ?>"><?php esc_html_e( 'Comments', 'hunterking' ); ?></a></li> -->
			<li><a href="#tab4" title="<?php esc_attr_e( 'Tags', 'hunterking' ); ?>"><?php esc_html_e( 'Tags', 'hunterking' ); ?></a></li>
		</ul>

		<div class="tabs-container">

			<div class="tab-content" id="tab1">
				<?php echo hunterking_latest_posts( $instance['recent_num'] ); ?>
			</div>

			<div class="tab-content" id="tab2">
				<?php echo hunterking_popular_posts( $instance['popular_num'] ); ?>
			</div>

			

			<div class="tab-content" id="tab3">
				<?php $comments = get_comments( array( 'number' => $instance['comments_num'], 'status' => 'approve', 'post_status' => 'publish' ) ); ?>
				<?php if ( $comments ) : ?>
					<ul>
						<?php foreach( $comments as $comment ) : ?>
							<li class="clearfix">
								<a href="<?php echo get_comment_link( $comment->comment_ID ) ?>">
									<span class="entry-thumbnail"><?php echo get_avatar( $comment->comment_author_email, '64' ); ?></span>
									<strong><?php echo $comment->comment_author; ?></strong>
									<span><?php echo wp_html_excerpt( $comment->comment_content, '80' ); ?></span>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div class="tab-content" id="tab4">
				<?php echo hunterking_tags(); ?>


		</div>

		<?php
		// Close the theme's widget wrapper.
		echo $after_widget;

	}

	/**
	 * Updates the widget control options for the particular instance of the widget.
	 *
	 * @since 1.0.0
	 */
	function update( $new_instance, $old_instance ) {

		$instance = $new_instance;

		$instance['popular_num']  = absint( $new_instance['popular_num'] );
		$instance['recent_num']   = absint( $new_instance['recent_num'] );
		$instance['comments_num'] = absint( $new_instance['comments_num'] );

		return $instance;
	}

	/**
	 * Displays the widget control options in the Widgets admin screen.
	 *
	 * @since 1.0.0
	 */
	function form( $instance ) {

		// Default value.
		$defaults = array(
			'popular_num'  => 5,
			'recent_num'   => 5,
			'comments_num' => 5
		);

		$instance = wp_parse_args( (array) $instance, $defaults );
	?>

		<p>
			<label for="<?php echo $this->get_field_id( 'popular_num' ); ?>">
				<?php esc_html_e( 'Number of Popular Posts', 'hunterking' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'popular_num' ); ?>" name="<?php echo $this->get_field_name( 'popular_num' ); ?>" value="<?php echo absint( $instance['popular_num'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'recent_num' ); ?>">
				<?php esc_html_e( 'Number of Recent Posts', 'hunterking' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'recent_num' ); ?>" name="<?php echo $this->get_field_name( 'recent_num' ); ?>" value="<?php echo absint( $instance['recent_num'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'comments_num' ); ?>">
				<?php esc_html_e( 'Number of Recent Comments', 'hunterking' ); ?>
			</label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'comments_num' ); ?>" name="<?php echo $this->get_field_name( 'comments_num' ); ?>" value="<?php echo esc_attr( $instance['comments_num'] ); ?>" />
		</p>

	<?php

	}

}

/**
 * Popular Posts by comment
 *
 * @since  1.0.0
 */
function hunterking_popular_posts( $number = 5 ) {

	$categories = get_the_category();

	if( sizeof($categories) > 0 ) {		
		$category = '';
		foreach ($categories as $key => $val) {
			$category .= $val->cat_ID . ",";
		}
		$category = trim($category, ",");
	
		$category_info = get_category( $category );

	} else { // get post types
		$category_info = (object) array( 'name' => get_post_type($post->ID));

	}
	
	// by default, display latest first
	$sort_by = 'date';
	$sort_order = 'DESC';
		
	// Exclude current post
	$exclude_current_post =  get_the_ID(); 

	if(!empty($categories[0])) {
		$args = array(
			'cat' => $category,
			'post__not_in' => array( $exclude_current_post ),
			'showposts' => $number, // Number of same posts that will be shown
			'ignore_sticky_posts' => 1,
			'orderby' => $sort_by,
			'order' => $sort_order
			);
	}else{
		$args = array(
			'post_type' => $category_info,
			'showposts' => $number, // Number of same posts that will be shown
			'ignore_sticky_posts' => 1,
			'orderby' => $sort_by,
			'order' => $sort_order
			);		
	}


	// The post query
	$popular = new WP_Query( $args );

	global $post;

	if ( $popular->have_posts() ) {
		$html = '<ul>';

			while ( $popular->have_posts() ) :
				$popular->the_post();

				$html .= '<li class="clearfix">';
					$html .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
						$html .= get_the_post_thumbnail( get_the_ID(), 'thumbnail', array( 'class' => 'entry-thumbnail', 'alt' => esc_attr( get_the_title() ) ) );
						$html .= '<h2 class="entry-title">' . esc_attr( get_the_title() ) . '</h2>';
						$html .= '<p >' .   wp_trim_words( get_the_content(), 15 ). '</p>';
						$html .= '<div class="entry-meta"><time class="entry-date" datetime="' . esc_html( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date('d-m-Y') ) . '</time></div>';
					$html .= '</a>';
				$html .= '</li>';

			endwhile;

		$html .= '</ul>';
	}

	// Reset the query.
	wp_reset_postdata();

	if ( isset( $html ) ) {
		return $html;
	}

}

/**
 * Recent Posts
 *
 * @since 1.0.0
 */
function hunterking_latest_posts( $number = 5 ) {

	// Posts query arguments.
	$args = array(
		'posts_per_page' => $number,
		'post_type'	  => 'post',
	);

	// The post query
	$recent = new WP_Query( $args );

	global $post;

	if ( $recent->have_posts() ) {
		$html = '<ul>';

			while ( $recent->have_posts() ) :
				$recent->the_post();

				$html .= '<li class="clearfix">';
					$html .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
						$html .= get_the_post_thumbnail( get_the_ID(), 'thumbnail', array( 'class' => 'entry-thumbnail', 'alt' => esc_attr( get_the_title() ) ) );
						$html .= '<h2 class="entry-title">' . esc_attr( get_the_title() ) . '</h2>';
						$html .= '<p >' .   wp_trim_words( get_the_content(), 15 ). '</p>';

						$html .= '<div class="entry-meta"><time class="entry-date" datetime="' . esc_html( get_the_date( 'c' ) ) . '">' . esc_html( get_the_date('d-m-Y') ) . '</time></div>';
					$html .= '</a>';
				$html .= '</li>';

			endwhile;

		$html .= '</ul>';
	}

	// Reset the query.
	wp_reset_postdata();

	if ( isset( $html ) ) {
		return $html;
	}

}


function hunterking_tags( $number = 5 ) {
	$tags_object= get_the_tags();

	$html = '';

	if ($tags_object) {
		foreach ($tags_object as $key => $value) {
			$html .=  '<a href="'.get_site_url().'/tag/'.$value->slug.'" class="tag-link-40 tag-link-position-1" title="1 topic" style="font-size: 8pt;">'.$value->name.'c</a>';
	}
	}
	

	wp_reset_postdata();

	if ( isset( $html ) ) {
		return $html;
	}

}

