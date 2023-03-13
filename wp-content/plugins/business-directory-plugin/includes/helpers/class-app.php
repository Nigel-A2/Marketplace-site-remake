<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.9.2
 */
class WPBDP_App_Helper {

	public static function plugin_folder() {
		return basename( self::plugin_path() );
	}

	public static function plugin_path() {
		return dirname( dirname( dirname( __FILE__ ) ) );
	}

	/**
	 * Prevously WPBDP_PLUGIN_FILE constant.
	 */
	public static function plugin_file() {
		return self::plugin_path() . '/business-directory-plugin.php';
	}

	/**
	 * Prevously WPBDP_URL constant.
	 */
	public static function plugin_url() {
		return trailingslashit( plugins_url( '', self::plugin_file() ) );
	}

	public static function relative_plugin_url() {
		return str_replace( array( 'https:', 'http:' ), '', self::plugin_url() );
	}

	/**
	 * @return string Site URL
	 */
	public static function site_url() {
		return site_url();
	}

	/**
	 * Check for any words in the string that have been flagged for replacement.
	 *
	 * @since 5.13
	 */
	public static function replace_labels( $msg ) {
		$originals = self::default_strings();
		foreach ( $originals as $name => $default ) {
			$label = self::get_label( $name );
			if ( empty( $label ) ) {
				continue;
			}

			self::replace_single_label( $default, $label, $msg );
			self::replace_single_label( strtolower( $default ), strtolower( $label ), $msg );
		}
		return $msg;
	}

	/**
	 * @since 5.13
	 */
	private static function replace_single_label( $find, $replace, &$msg ) {
		if ( $msg === $find ) {
			$msg = $replace;
			return;
		}

		// Middle of string.
		$msg = str_replace( ' ' . $find . ' ', ' ' . $replace . ' ', $msg );
		$msg = str_replace( ' ' . $find . '.', ' ' . $replace . '.', $msg );

		// Beginning of string
		$msg = preg_replace( '/^' . $find . ' /', $replace . ' ', $msg );

		// End of string
		$msg = preg_replace( '/ ' . $find . '$/', ' ' . $replace, $msg );
	}

	/**
	 * All the strings that have a setting.
	 *
	 * @since 5.13
	 */
	public static function default_strings( $translate = true ) {
		if ( $translate ) {
			$strings = array(
				'listing'   => __( 'Listing', 'business-directory-plugin' ),
				'listings'  => __( 'Listings', 'business-directory-plugin' ),
				'directory' => __( 'Directory', 'business-directory-plugin' ),
			);
		} else {
			// Prevent an infinite loop.
			$strings = array(
				'listing'   => 'Listing',
				'listings'  => 'Listings',
				'directory' => 'Directory',
			);
		}

		/**
		 * Add extra strings with their replacements.
		 *
		 * @since 5.13
		 */
		return apply_filters( 'wpbdp_custom_strings', $strings );
	}

	/**
	 * Get the saved setting. These settings should include '-label' on the end.
	 *
	 * @since 5.13
	 */
	private static function get_label( $name ) {
		$defaults = self::default_strings();
		$default  = isset( $defaults[ $name ] ) ? $defaults[ $name ] : false;
		$label    = wpbdp_get_option( $name . '-label', $default );
		$label    = strip_tags( $label );
		return $label;
	}

	/**
	 * Check for certain page in settings
	 *
	 * @since 5.9.2
	 *
	 * @param string $page The name of the page to check
	 *
	 * @return boolean
	 */
	public static function is_admin_page( $page = 'wpbdp_settings' ) {
		global $pagenow;
		$get_page = wpbdp_get_var( array( 'param' => 'page' ) );
		if ( $pagenow ) {
			// allow this to be true during ajax load
			$is_page = ( $pagenow == 'admin.php' || $pagenow == 'admin-ajax.php' ) && $get_page == $page;
			if ( $is_page ) {
				return true;
			}
		}

		return is_admin() && $get_page == $page;
	}

	/**
	 * Is this a page we should be changing?
	 *
	 * @since 5.8.2
	 *
	 * @return bool
	 */
	public static function is_bd_page() {
		if ( self::is_bd_post_page() ) {
			return true;
		}

		$page    = wpbdp_get_var( array( 'param' => 'page' ) );
		$is_page = $page && strpos( $page, 'wpbdp' ) !== false;

		/**
		 * @since 5.12.1
		 */
		return apply_filters( 'wpbdp_is_bd_page', $is_page );
	}

	/**
	 * Check if current page is a Business Directory plugin page.
	 *
	 * @since 5.8.2
	 *
	 * @return bool
	 */
	public static function is_bd_post_page() {
		global $pagenow;

		$is_tax  = 'term.php' === $pagenow || 'edit-tags.php' === $pagenow;
		$is_post = 'post.php' === $pagenow || 'post-new.php' === $pagenow || 'edit.php' === $pagenow;
		if ( ! $is_post && ! $is_tax ) {
			return false;
		}

		$post_type = wpbdp_get_var( array( 'param' => 'post_type' ) );

		if ( empty( $post_type ) && ! $is_tax ) {
			$post_id   = wpbdp_get_var( array( 'param' => 'post', 'sanitize' => 'absint' ) );
			$post      = get_post( $post_id );
			$post_type = $post ? $post->post_type : '';
		}

		return WPBDP_POST_TYPE === $post_type;
	}

	/**
	 * Check if the user has permision for action.
	 * Return permission message and stop the action if no permission
	 *
	 * @since 5.11.2
	 *
	 * @param string $permission
	 */
	public static function permission_check( $permission, $atts = array() ) {
		$defaults = array(
			'show_message' => '',
			'nonce_name'   => '_wpnonce',
			'nonce'        => '',
		);
		$atts = array_merge( $defaults, $atts );

		$permission_error = self::permission_nonce_error( $permission, $atts );
		if ( $permission_error !== false ) {
			if ( 'hide' === $atts['show_message'] ) {
				$permission_error = '';
			}
			wp_die( esc_html( $permission_error ) );
		}
	}

	/**
	 * Check user permission and nonce
	 *
	 * @since 5.11.2
	 *
	 * @param string $permission
	 *
	 * @return false|string The permission message or false if allowed
	 */
	public static function permission_nonce_error( $permission, $atts = array() ) {
		if ( ! empty( $permission ) && ! current_user_can( $permission ) && ! current_user_can( 'administrator' ) ) {
			return esc_html__( 'You are not allowed to do that.', 'business-directory-plugin' );
		}

		$error = false;
		if ( empty( $atts['nonce_name'] ) || empty( $atts['nonce'] ) ) {
			return $error;
		}

		$nonce_name  = $atts['nonce_name'];
		$nonce_value = ( $_REQUEST && isset( $_REQUEST[ $nonce_name ] ) ) ? sanitize_text_field( wp_unslash( $_REQUEST[ $nonce_name ] ) ) : '';

		if ( $_REQUEST && ( ! isset( $_REQUEST[ $nonce_name ] ) || ! wp_verify_nonce( $nonce_value, $atts['nonce'] ) ) ) {
			$error = esc_html__( 'You are not allowed to do that.', 'business-directory-plugin' );
		}

		return $error;
	}

	/**
	 * Try to show the SVG if possible. Otherwise, use the font icon.
	 *
	 * @since 5.9.2
	 *
	 * @param string $class
	 * @param array  $atts
	 */
	public static function icon_by_class( $class, $atts = array() ) {
		$echo = ! isset( $atts['echo'] ) || $atts['echo'];
		if ( isset( $atts['echo'] ) ) {
			unset( $atts['echo'] );
		}

		$html_atts = self::array_to_html_params( $atts );

		$icon = trim( str_replace( array( 'wpbdpfont ' ), '', $class ) );
		if ( $icon === $class ) {
			$icon = '<i class="' . esc_attr( $class ) . '"' . $html_atts . '></i>';
		} else {
			$class = strpos( $icon, ' ' ) === false ? '' : ' ' . $icon;
			if ( strpos( $icon, ' ' ) ) {
				$icon = explode( ' ', $icon );
				$icon = reset( $icon );
			}
			$icon  = '<svg class="wpbdpsvg' . esc_attr( $class ) . '"' . $html_atts . '>
				<use xlink:href="#' . esc_attr( $icon ) . '" />
			</svg>';
		}

		if ( $echo ) {
			echo $icon; // WPCS: XSS ok.
		} else {
			return $icon;
		}
	}

	/**
	 * Include svg images.
	 *
	 * @since 5.9.2
	 */
	public static function include_svg() {
		include_once self::plugin_path() . '/assets/images/icons.svg';
	}

	/**
	 * Convert an associative array to HTML values.
	 *
	 * @since 5.9.2
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function array_to_html_params( $atts ) {
		$html = '';
		if ( ! empty( $atts ) ) {
			foreach ( $atts as $key => $value ) {
				$html .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
			}
		}
		return $html;
	}

	/**
	 * @param array|int $atts
	 *
	 * @since 5.9.2
	 */
	public static function show_logo( $atts ) {
		echo self::kses( self::svg_logo( $atts ), 'all' ); // WPCS: XSS ok.
	}

	/**
	 * @since 5.9.2
	 */
	public static function svg_logo( $atts = array() ) {
		$atts = self::prep_logo_atts( $atts );

		return '<img src="' . esc_url( self::plugin_url() . '/assets/images/percie' . ( $atts['round'] ? '-round' : '' ) . '.svg' ) . '" width="' . esc_attr( $atts['size'] ) . '" height="' . esc_attr( $atts['size'] ) . '" class="' . esc_attr( $atts['class'] ) . '" alt="BD Plugin" />';
	}

	/**
	 * @param int|array $atts
	 *
	 * @since 6.0
	 */
	private static function prep_logo_atts( $atts ) {
		if ( ! is_array( $atts ) ) {
			// For reverse compatibility, changing param from int to array.
			$atts = array(
				'size' => $atts,
			);
		}

		$defaults   = array(
			'class' => false,
			'round' => false,
			'size'  => 18,
		);

		return wp_parse_args( $atts, $defaults );
	}

	/**
	 * Sanitize the value, and allow some HTML
	 *
	 * @since 5.9.2
	 *
	 * @param string $value
	 * @param array|string $allowed 'all' for everything included as defaults
	 *
	 * @return string
	 */
	public static function kses( $value, $allowed = array() ) {
		$allowed_html = self::allowed_html( $allowed );

		return wp_kses( $value, $allowed_html );
	}

	/**
	 * @since 5.9.2
	 */
	private static function allowed_html( $allowed ) {
		$html         = self::safe_html();
		$allowed_html = array();
		if ( $allowed == 'all' ) {
			$allowed_html = $html;
		} elseif ( ! empty( $allowed ) ) {
			foreach ( (array) $allowed as $a ) {
				$allowed_html[ $a ] = isset( $html[ $a ] ) ? $html[ $a ] : array();
			}
		}

		return apply_filters( 'wpbdp_striphtml_allowed_tags', $allowed_html );
	}

	/**
	 * @since 5.9.2
	 */
	private static function safe_html() {
		$allow_class = array(
			'class' => true,
			'id'    => true,
		);

		return array(
			'a'          => array(
				'class'  => true,
				'href'   => true,
				'id'     => true,
				'rel'    => true,
				'target' => true,
				'title'  => true,
			),
			'abbr'       => array(
				'title' => true,
			),
			'aside'      => $allow_class,
			'b'          => array(),
			'blockquote' => array(
				'cite' => true,
			),
			'br'         => array(),
			'cite'       => array(
				'title' => true,
			),
			'code'       => array(),
			'defs'       => array(),
			'del'        => array(
				'datetime' => true,
				'title'    => true,
			),
			'dd'         => array(),
			'div'        => array(
				'class' => true,
				'id'    => true,
				'title' => true,
				'style' => true,
			),
			'dl'         => array(),
			'dt'         => array(),
			'em'         => array(),
			'h1'         => $allow_class,
			'h2'         => $allow_class,
			'h3'         => $allow_class,
			'h4'         => $allow_class,
			'h5'         => $allow_class,
			'h6'         => $allow_class,
			'i'          => array(
				'class' => true,
				'id'    => true,
				'icon'  => true,
				'style' => true,
			),
			'img'        => array(
				'alt'    => true,
				'class'  => true,
				'height' => true,
				'id'     => true,
				'src'    => true,
				'width'  => true,
			),
			'li'         => $allow_class,
			'ol'         => $allow_class,
			'p'          => $allow_class,
			'path'       => array(
				'd'    => true,
				'fill' => true,
			),
			'pre'        => array(),
			'q'          => array(
				'cite'  => true,
				'title' => true,
			),
			'rect'       => array(
				'class'  => true,
				'fill'   => true,
				'height' => true,
				'width'  => true,
				'x'      => true,
				'y'      => true,
				'rx'     => true,
				'stroke' => true,
				'stroke-opacity' => true,
				'stroke-width'   => true,
			),
			'section'    => $allow_class,
			'span'       => array(
				'class' => true,
				'id'    => true,
				'title' => true,
				'style' => true,
			),
			'strike'     => array(),
			'strong'     => array(),
			'symbol'     => array(
				'class'   => true,
				'id'      => true,
				'viewbox' => true,
			),
			'svg'        => array(
				'class'   => true,
				'id'      => true,
				'xmlns'   => true,
				'viewbox' => true,
				'width'   => true,
				'height'  => true,
				'style'   => true,
				'fill'    => true,
			),
			'use'        => array(
				'href'   => true,
				'xlink:href' => true,
			),
			'ul'         => $allow_class,
		);
	}

	/**
	 * @since 5.16
	 * @return string
	 */
	public static function minimize_code( $html ) {
		return str_replace( array( "\r\n", "\n", "\t" ), '', $html );
	}
}
