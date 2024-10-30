<?php
/**
 * Adds Blogtopdf_Widget widget.
 */
class Blogtopdf_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'blogtopdf_widget', // Base ID
			'Blog To PDF Print', // Name
		array( 'description' => __( 'Displays a Download PDF button', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		global $post;

		if (!isset($post) || !isset($post->ID)) return;

		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ($instance['pages']) {
			$pages = explode(',',$instance['pages']);
			$temp=$post;
			$i=0;
			$found=in_array($post->ID,$pages) ? true : false;
			while ($temp->post_parent && !$found && ($i<100)) {
				if (in_array($temp->post_parent,$pages)) $found=true;
				$i++;
				$temp=get_post($temp->post_parent);
			}
			if (!$found) return;
		}
		echo $before_widget;
		//if ( ! empty( $title ) ) echo $before_title . $title . $after_title;
		$button='<form action="?blogtopdf='.$post->ID.'" method="POST">';
		$button.='<input type="hidden" name="blogtopdf" value="'.$post->ID.'" />';
		$button.='<input type="hidden" name="pdfimage" value="'.$instance['pdfimage'].'" />';
		$button.='<input type="hidden" name="pdftitle" value="'.$instance['pdftitle'].'" />';
		$button.='<input type="submit" value="'.__($title,'text_domain').'" />';
		$button.='</form>';
		echo $button;
		echo $after_widget;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		delete_option('blogtopdf_cache');

		$instance = array();
		$fields=$this->fields();
		foreach ($fields as $id => $field) {
			$instance[$id] = strip_tags( $new_instance[$id] );
		}

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$fields=$this->fields();
		foreach ($fields as $id => $field) {
			if ( isset( $instance[ $id ] ) ) {
				$title = $instance[ $id ];
			}
			else {
				$title = __( $field['default'], 'text_domain' );
			}
			?>
<p>
	<label for="<?php echo $this->get_field_id( $id ); ?>"><?php _e( $field['title'] ); ?>:</label>
	<input class="widefat" id="<?php echo $this->get_field_id( $id ); ?>"
		name="<?php echo $this->get_field_name( $id ); ?>" type="text"
		value="<?php echo esc_attr( $title ); ?>" />
</p>
			<?php
		}
	}

	function fields() {
		$fields=array();
		$fields['title']=array('title'=>'Button title','default'=>'');
		$fields['pages']=array('title'=>'Display on pages (leave blank to display on all pages and note that if a page ID is entered, it will also display on all sub-pages)','default'=>'');
		$fields=apply_filters('blogtopdf_widget_fields',$fields);
		return $fields;
	}

} // class Blogtopdf_Widget

// register Blogtopdf_Widget widget
add_action( 'widgets_init', create_function( '', 'register_widget( "blogtopdf_widget" );' ) );