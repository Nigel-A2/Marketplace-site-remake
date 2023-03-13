<?php

/**
 * Custom Customizer controls for the Beaver Builder theme.
 *
 * @since 1.2.0
 */
final class FLCustomizerControl extends WP_Customize_Control {

	/**
	 * Used to connect controls to each other.
	 *
	 * @since 1.2.0
	 * @var bool $connect
	 */
	public $connect = false;

	/**
	 * Used to set the mode for code controls.
	 *
	 * @since 1.3.3
	 * @var bool $mode
	 */
	public $mode = 'html';

	/**
	 * Reference to the class `$args` parameter.
	 *
	 * @since 1.7
	 * @var array
	 */
	public $args = array();

	/**
	 * If true, the preview button for a control will be rendered.
	 *
	 * @since 1.3.3
	 * @var bool $preview_button
	 */
	public $preview_button = false;

	/**
	 * Constructor.
	 *
	 * @since 1.7
	 */
	public function __construct( $manager, $id, $args = array() ) {
		$this->args = $args;
		parent::__construct( $manager, $id, $args );
	}

	/**
	 * Renders the control wrapper and calls $this->render_content() for the internals.
	 *
	 * @since 1.7
	 * @return void
	 */
	protected function render() {
		$id    = 'customize-control-' . str_replace( array( '[', ']' ), array( '-', '' ), $this->id );
		$class = 'customize-control customize-control-' . $this->type;

		if ( isset( $this->args['classes'] ) ) {
			$class .= ' ' . implode( ' ', $this->args['classes'] );
		}

		printf( '<li id="%s" class="%s">', esc_attr( $id ), esc_attr( $class ) );
		$this->render_content();
		echo '</li>';
	}

	/**
	 * Renders the content for a control based on the type
	 * of control specified when this class is initialized.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @return void
	 */
	protected function render_content() {
		switch ( $this->type ) {

			case 'font':
				$this->render_font();
				break;

			case 'font-weight':
				$this->render_font_weight();
				break;

			case 'code':
				$this->render_code();
				break;

			case 'line':
				$this->render_line();
				break;

			case 'export-import':
				$this->render_export_import();
				break;

			case 'slider':
				$this->render_slider();
				break;

			case 'checkbox-multiple':
				$this->render_checkbox_multiple();
				break;

			case 'switch':
				$this->render_switch();
				break;
		}
	}

	/**
	 * Renders the title and description for a control.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @return void
	 */
	protected function render_content_title() {
		if ( ! empty( $this->label ) ) {
			echo '<span class="customize-control-title">' . esc_html( $this->label );

			if ( isset( $this->args['classes'] ) && in_array( 'fl-responsive-customize-control', $this->args['classes'], true ) ) {
				$icon = end( $this->args['classes'] );

				if ( 'medium' === $icon ) {
					$icon = 'tablet';
				} elseif ( 'mobile' === $icon ) {
					$icon = 'smartphone';
				}

				echo '<i class="fl-responsive-control-toggle dashicons dashicons-' . $icon . '"></i>';
			}

			echo '</span>';
		}
		if ( ! empty( $this->description ) ) {
			echo '<span class="description customize-control-description">' . $this->description . '</span>';
		}
	}

	/**
	 * Renders the connect attribute for a connected control.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @return void
	 */
	protected function render_connect_attribute() {
		if ( $this->connect ) {
			echo ' data-connected-control="' . $this->connect . '"';
		}
	}

	/**
	 * Renders a font control.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @return void
	 */
	protected function render_font() {
		echo '<label>';
		$this->render_content_title();
		echo '<select ';
		$this->link();
		$this->render_connect_attribute();
		echo '>';
		echo '<optgroup label="System">';

		foreach ( FLFontFamilies::get_system() as $name => $variants ) {
			echo '<option value="' . $name . '" ' . selected( $name, $this->value(), false ) . '>' . $name . '</option>';
		}

		echo '<optgroup label="Google">';

		foreach ( FLFontFamilies::get_google() as $name => $variants ) {
			echo '<option value="' . $name . '" ' . selected( $name, $this->value(), false ) . '>' . $name . '</option>';
		}

		echo '</select>';
		echo '</label>';
	}

	/**
	 * Renders a font weight control.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @return void
	 */
	protected function render_font_weight() {
		echo '<label>';
		$this->render_content_title();
		echo '<select ';
		$this->link();
		$this->render_connect_attribute();
		echo '>';
		echo '<option value="' . $this->value() . '" selected="selected">' . $this->value() . '</option>';
		echo '</select>';
		echo '</label>';
	}

	/**
	 * Renders a code control.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @return void
	 */
	protected function render_code() {
		$this->render_content_title();

		if ( $this->preview_button ) {
			echo '<input type="button" name="fl-preview-button" class="button fl-preview-button" value="Preview" />';
		}

		echo '<label>';
		echo '<textarea rows="15" style="width:100%" ';
		$this->link();
		echo '>' . $this->value() . '</textarea>';
		echo '<div class="fl-code-editor" data-mode="' . $this->mode . '"></div>';
		echo '</label>';
	}

	/**
	 * Renders a line break control.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @return void
	 */
	protected function render_line() {
		echo '<hr />';
	}

	/**
	 * Renders the export/import control.
	 *
	 * @since 1.2.0
	 * @access protected
	 * @return void
	 */
	protected function render_export_import() {
		$plugin = 'customizer-export-import';
		$nonce  = wp_create_nonce( 'install-plugin_' . $plugin );
		$url    = admin_url( 'update.php?action=install-plugin&plugin=' . $plugin . '&_wpnonce=' . $nonce );

		echo '<p>' . __( 'Please install and activate the "Customizer Export/Import" plugin to proceed.', 'fl-automator' ) . '</p>';
		echo '<a class="install-now button" href="' . $url . '">' . _x( 'Install &amp; Activate', '...a plugin.', 'fl-automator' ) . '</a>';
	}

	/**
	 * Renders the slider control.
	 *
	 * @since 1.5.0
	 * @access protected
	 * @return void
	 */
	protected function render_slider() {
		$this->choices['min']  = ( isset( $this->choices['min'] ) ) ? $this->choices['min'] : '0';
		$this->choices['max']  = ( isset( $this->choices['max'] ) ) ? $this->choices['max'] : '100';
		$this->choices['step'] = ( isset( $this->choices['step'] ) ) ? $this->choices['step'] : '1';

		echo '<label class="fl-range-label">';
		$this->render_content_title();
		echo '<div class="wrapper">';
		echo '<input type="range" class="fl-range-slider" min="' . $this->choices['min'] . '" max="' . $this->choices['max'] . '" step="' . $this->choices['step'] . '" value="' . $this->value() . '"';
		$this->link();
		echo 'data-reset_value="' . $this->settings['default']->default . '">';
		echo '<div class="fl-range-value">';
		echo '<input type="text" class="fl-range-value-input" value="' . $this->value() . '">';
		echo '</div>';
		echo '<div class="fl-slider-reset">';
		echo '<span class="dashicons dashicons-image-rotate"></span>';
		echo '</div>';
		echo '</div>';
		echo '</label>';
	}

	/**
	 * Renders multiple checkbox markup
	 *
	 * @since 1.5.3
	 * @access protected
	 * @return void
	 */
	protected function render_checkbox_multiple() {
		if ( empty( $this->choices ) ) {
			return;
		}

		$this->render_content_title();

		$multi_values = ! is_array( $this->value() ) ? explode( ',', $this->value() ) : $this->value();

		if ( isset( $this->choices['custom'] ) && 'post_types' === $this->choices['custom'] ) {
			$choices = $this->get_checkbox_choices_post_types();

			// Set all post types as default.
			if ( 'all' === $this->value() ) {
				$multi_values = array_keys( $choices );
			}
		} else {
			$choices = $this->choices;
		}

		if ( count( $choices ) > 0 ) {
			echo '<ul>';

			foreach ( $choices as $value => $label ) {
				echo '<li>';
					echo '<label>';
						echo '<input type="checkbox" value="' . esc_attr( $value ) . '" ';
								checked( in_array( $value, $multi_values, true ) );
						echo ' />';
						echo esc_html( $label );
					echo '</label>';
				echo '</li>';
			}

			echo '</ul>';
		}

		if ( is_array( $multi_values ) ) {
			echo '<input type="hidden" ' . $this->get_link() . ' value="' . esc_attr( implode( ',', $multi_values ) ) . '" />';
		}
	}

	/**
	 * Renders switch control markup
	 *
	 * @since 1.7.11
	 * @access protected
	 * @return void
	 */
	protected function render_switch() {
		echo '<div class="fl-control-switch-wrap">';
			echo $this->render_content_title();
			echo '<label class="fl-control-switch">
				<input type="checkbox" ' . $this->get_link() . ' ' . ( true === $this->value() ? 'checked' : '' ) . '>
				<span></span>
			</label>
		</div>';
	}

	/**
	 * Get post types for multiple checkbox choices
	 *
	 * @since 1.6.2
	 * @access protected
	 * @return array
	 */
	protected function get_checkbox_choices_post_types() {
		$ptypes     = array();
		$post_types = get_post_types(array(
			'public' => true,
		), 'objects');

		if ( $post_types ) {
			foreach ( $post_types as $key => $post_type ) {
				$ptypes[ $post_type->name ] = $post_type->label;
			}

			// Remove post_type `product` for woocommerce since we have separate sidebar control for WooCommerce
			unset( $ptypes['product'] );
			// Remove post type `page` since sidebar is set per page where default has no sidebar.
			unset( $ptypes['page'] );
			// Remove BB plugin templates
			unset( $ptypes['fl-builder-template'] );
		}

		return $ptypes;
	}
}
