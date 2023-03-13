<?php
/**
 * Search widget.
 *
 * @since 2.1.6
 */
class WPBDP_SearchWidget extends WP_Widget {

    public function __construct() {
		parent::__construct(
			'',
			_x( 'Business Directory - Search', 'widgets', 'business-directory-plugin' ),
			array(
				'description' => _x( 'Displays a search form to look for Business Directory listings.', 'widgets', 'business-directory-plugin' )
			)
		);
    }

	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = _x( 'Search the Business Directory', 'widgets', 'business-directory-plugin' );
		}

		echo sprintf(
			'<p><label for="%s">%s</label> <input class="widefat" id="%s" name="%s" type="text" value="%s" /></p>',
			esc_attr( $this->get_field_id( 'title' ) ),
			esc_html_x( 'Title:', 'widgets', 'business-directory-plugin' ),
			esc_attr( $this->get_field_id( 'title' ) ),
			esc_attr( $this->get_field_name( 'title' ) ),
			esc_attr( $title )
		);
        echo '<p>';

		echo _x( 'Form Style:', 'widgets', 'business-directory-plugin' );
        echo '<br/>';
		echo sprintf(
			'<input id="%s" name="%s" type="radio" value="%s" %s/> <label for="%s">%s</label>',
			esc_attr( $this->get_field_id( 'use_basic_form' ) ),
			esc_attr( $this->get_field_name( 'form_mode' ) ),
			'basic',
			wpbdp_getv( $instance, 'form_mode', 'basic' ) === 'basic' ? 'checked="checked"' : '',
			esc_attr( $this->get_field_id( 'use_basic_form' ) ),
			esc_html_x( 'Basic', 'widgets', 'business-directory-plugin' )
		);
        echo '&nbsp;&nbsp;';
		echo sprintf(
			'<input id="%s" name="%s" type="radio" value="%s" %s/> <label for="%s">%s</label>',
			esc_attr( $this->get_field_id( 'use_advanced_form' ) ),
			esc_attr( $this->get_field_name( 'form_mode' ) ),
			'advanced',
			wpbdp_getv( $instance, 'form_mode', 'basic' ) === 'advanced' ? 'checked="checked"' : '',
			esc_attr( $this->get_field_id( 'use_advanced_form' ) ),
			esc_html_x( 'Advanced', 'widgets', 'business-directory-plugin' )
		);
        echo '</p>';

        echo '<p class="wpbdp-search-widget-advanced-settings">';
		echo esc_html_x( 'Search Fields (advanced mode):', 'widgets', 'business-directory-plugin' ) . '<br/>';
		echo ' <span class="description">' . esc_html_x( 'Display the following fields in the form.', 'widgets', 'business-directory-plugin' ) . '</span>';

        $instance_fields = wpbdp_getv( $instance, 'search_fields', array() );

        $api = wpbdp_formfields_api();

		printf( '<select name="%s[]" multiple="multiple">', esc_attr( $this->get_field_name( 'search_fields' ) ) );

        foreach ( $api->get_fields() as $field ) {
            if ( $field->display_in( 'search' ) ) {
				printf(
					'<option value="%s" %s>%s</option>',
					esc_attr( $field->get_id() ),
					( ! $instance_fields || in_array( $field->get_id(), $instance_fields ) ) ? 'selected="selected"' : '',
					esc_html( $field->get_label() )
				);
            }
        }

        echo '</select>';
        echo '</p>';
		return '';
    }

	public function update( $new, $old ) {
		$instance                  = $old;
		$instance['title']         = strip_tags( $new['title'] );
		$instance['form_mode']     = wpbdp_getv( $new, 'form_mode', 'basic' );
		$instance['search_fields'] = wpbdp_getv( $new, 'search_fields', array() );
		return $instance;
    }

	public function widget( $args, $instance ) {
		extract( $args );
        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $before_widget;
        if ( ! empty( $title ) ) echo $before_title . $title . $after_title;

		printf( '<form action="%s" method="get">', esc_attr( wpbdp_url( '/' ) ) );

		if ( ! wpbdp_rewrite_on() ) {
			printf( '<input type="hidden" name="page_id" value="%s" />', esc_attr( wpbdp_get_page_id( 'main' ) ) );
		}

        echo '<input type="hidden" name="wpbdp_view" value="search" />';
        echo '<input type="hidden" name="dosrch" value="1" />';

		if ( wpbdp_getv( $instance, 'form_mode', 'basic' ) === 'advanced' ) {
            $fields_api = wpbdp_formfields_api();

			foreach ( $fields_api->get_fields() as $field ) {
                if ( $field->display_in( 'search' ) && in_array( $field->get_id(), $instance['search_fields'] ) ) {
                    echo $field->render( null, 'search' );
                }
            }
        } else {
			?>
			<div class="wpbdp-form-field">
				<label for="wpbdp-keyword-field" style="display:none;">Keywords:</label>
				<input id="wpbdp-keyword-field" type="text" name="kw" value="" />
			</div>
			<?php
        }

		?>
		<p><input type="submit" value="<?php esc_attr_e( 'Search', 'business-directory-plugin' ); ?>" class="submit wpbdp-search-widget-submit" /></p>
		</form>
		<?php

        echo $after_widget;

		wp_enqueue_style( 'wpbdp-base-css' );
	}

}
