<?php

/**
 * A class for working with auto suggest AJAX requests.
 *
 * @since 1.2.3
 */
final class FLBuilderAutoSuggest {

	/**
	 * Checks for an auto suggest request. If one is found
	 * the data will be echoed as a JSON response.
	 *
	 * @since 1.2.3
	 * @return array
	 */
	static public function init() {
		if ( isset( $_REQUEST['fl_as_action'] ) && isset( $_REQUEST['fl_as_query'] ) ) {

			switch ( $_REQUEST['fl_as_action'] ) {

				case 'fl_as_posts':
					$data = self::posts();
					break;

				case 'fl_as_terms':
					$data = self::terms();
					break;

				case 'fl_as_users':
					$data = self::users();
					break;

				case 'fl_as_links':
					$data = self::links();
					break;
			}

			if ( isset( $data ) ) {
				$data = apply_filters( 'fl_builder_auto_suggest_lookup', $data, $_REQUEST['fl_as_action'] );
				return $data;
			}
		}
	}

	/**
	 * Returns a JSON encoded value for a suggest field.
	 *
	 * @since 1.2.3
	 * @param string $action The type of auto suggest action.
	 * @param string $value The current value.
	 * @param string $data Additional auto suggest data.
	 * @return string The JSON encoded value.
	 */
	static public function get_value( $action = '', $value = '', $data = '' ) {
		switch ( $action ) {

			case 'fl_as_posts':
				$data = self::posts_value( $value );
				break;

			case 'fl_as_terms':
				$data = self::terms_value( $value, $data );
				break;

			case 'fl_as_users':
				$data = self::users_value( $value );
				break;

			default:
				if ( function_exists( $action . '_value' ) ) {
					$data = call_user_func_array( $action . '_value', array( $value, $data ) );
				}

				break;
		}

		return isset( $data ) ? str_replace( "'", '&#39;', json_encode( $data ) ) : '';
	}

	/**
	 * Returns the values for all suggest fields in a settings form.
	 *
	 * @since 2.0
	 * @param array $fields
	 * @return array
	 */
	static public function get_values( $fields ) {
		$values = array();

		foreach ( $fields as $field ) {
			$values[ $field['name'] ] = self::get_value( $field['action'], $field['value'], $field['data'] );
		}

		return $values;
	}

	/**
	 * Returns the SQL escaped like value for auto suggest queries.
	 *
	 * @since 1.2.3
	 * @return string
	 */
	static public function get_like() {
		global $wpdb;

		$like = stripslashes( urldecode( $_REQUEST['fl_as_query'] ) );

		$like = esc_sql( $wpdb->esc_like( $like ) );

		return $like;
	}

	/**
	 * Returns data for post auto suggest queries.
	 *
	 * @since 1.2.3
	 * @return array
	 */
	static public function posts() {
		global $wpdb;

		$data     = array();
		$like     = self::get_like();
		$types    = explode( ',', esc_sql( $_REQUEST['fl_as_action_data'] ) );
		$types_in = join( "', '", array_map( 'esc_sql', $types ) );

		// @codingStandardsIgnoreStart
		$posts	= $wpdb->get_results( $wpdb->prepare( "
			SELECT ID, post_title FROM {$wpdb->posts}
			WHERE post_title LIKE %s
			AND post_type IN ('{$types_in}')
			AND post_status = 'publish'
		", '%' . $like . '%' ) );
		// @codingStandardsIgnoreEnd

		foreach ( $posts as $post ) {
			$data[] = array(
				'name'  => $post->post_title,
				'value' => $post->ID,
			);
		}

		return apply_filters( 'fl_builder_auto_suggest_posts_lookup', $data );
	}

	/**
	 * Returns data for selected posts.
	 *
	 * @since 1.2.3
	 * @param string $ids The selected post ids.
	 * @return array An array of post data.
	 */
	static public function posts_value( $ids ) {
		global $wpdb;

		$data = array();

		if ( ! empty( $ids ) ) {

			$order        = implode( ',', array_filter( explode( ',', $ids ), 'intval' ) );
			$list         = explode( ',', $ids );
			$how_many     = count( $list );
			$placeholders = array_fill( 0, $how_many, '%d' );
			$format       = implode( ', ', $placeholders );

			$query = "SELECT ID, post_title FROM {$wpdb->posts} WHERE ID IN ($format) ORDER BY FIELD(ID, $order)";

			// @codingStandardsIgnoreStart
			$posts = $wpdb->get_results( $wpdb->prepare( $query, $list ) );
			// @codingStandardsIgnoreEnd

			foreach ( $posts as $post ) {
				$data[] = array(
					'name'  => $post->post_title,
					'value' => $post->ID,
				);
			}
		}

		return $data;
	}

	/**
	 * Returns data for term auto suggest queries.
	 *
	 * @since 1.2.3
	 * @return array
	 */
	static public function terms() {
		$data = array();
		$cats = get_categories(array(
			'hide_empty' => 0,
			'taxonomy'   => $_REQUEST['fl_as_action_data'],
		));

		foreach ( $cats as $cat ) {
			$data[] = array(
				'name'  => htmlspecialchars_decode( $cat->name ),
				'value' => $cat->term_id,
			);
		}

		return $data;
	}

	/**
	 * Returns data for selected terms.
	 *
	 * @since 1.2.3
	 * @param string $ids The selected term ids.
	 * @param string $taxonomy The taxonomy to look in.
	 * @return array An array of term data.
	 */
	static public function terms_value( $ids, $taxonomy ) {
		$data = array();

		if ( ! empty( $ids ) ) {

			$cats = get_categories(array(
				'hide_empty' => 0,
				'taxonomy'   => $taxonomy,
				'include'    => $ids,
			));

			foreach ( $cats as $cat ) {
				$data[] = array(
					'name'  => htmlspecialchars_decode( $cat->name ),
					'value' => $cat->term_id,
				);
			}
		}

		return $data;
	}

	/**
	 * Returns data for user auto suggest queries.
	 *
	 * @since 1.2.3
	 * @return array
	 */
	static public function users() {
		global $wpdb;

		$data  = array();
		$like  = self::get_like();
		$users = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->users} WHERE user_login LIKE %s", '%' . $like . '%' ) );

		foreach ( $users as $user ) {
			$data[] = array(
				'name'  => $user->user_login,
				'value' => $user->ID,
			);
		}

		return $data;
	}

	/**
	 * Returns data for selected users.
	 *
	 * @since 1.2.3
	 * @param string $ids The selected user ids.
	 * @return array An array of user data.
	 */
	static public function users_value( $ids ) {
		global $wpdb;

		$data = array();

		if ( ! empty( $ids ) ) {

			$list         = explode( ',', $ids );
			$how_many     = count( $list );
			$placeholders = array_fill( 0, $how_many, '%d' );
			$format       = implode( ', ', $placeholders );

			$query = "SELECT * FROM {$wpdb->users} WHERE ID IN ($format)";

			// @codingStandardsIgnoreStart
			$users = $wpdb->get_results( $wpdb->prepare( $query, $list ) );
			// @codingStandardsIgnoreEnd

			foreach ( $users as $user ) {
				$data[] = array(
					'name'  => $user->user_login,
					'value' => $user->ID,
				);
			}
		}
		return $data;
	}

	/**
	 * Returns data for link auto suggest queries.
	 *
	 * @since 1.3.9
	 * @return array
	 */
	static public function links() {
		global $wpdb;

		$data  = array();
		$like  = self::get_like();
		$types = FLBuilderLoop::post_types();
		$slugs = array( 'attachment' );

		foreach ( $types as $slug => $type ) {
			$slugs[] = esc_sql( $slug );
		}

		// we can't use an array of arrays for prepare() so use sprintf 1st.
		$query = sprintf( "SELECT ID, post_title, post_type FROM {$wpdb->posts}
			WHERE post_title LIKE %%s
			AND post_type IN ('%s')
			AND post_status IN ('publish', 'inherit')",
			implode( "', '", $slugs )
		);

		// @codingStandardsIgnoreStart
		$posts = $wpdb->get_results( $wpdb->prepare( $query, '%' . esc_sql( $like ) . '%' ) );
		// @codingStandardsIgnoreEnd

		foreach ( $posts as $post ) {

			$data[] = array(
				'name'  => ( 'attachment' === $post->post_type ) ? basename( wp_get_attachment_url( $post->ID ) ) : esc_html( $post->post_title ),
				'value' => ( 'attachment' === $post->post_type ) ? wp_get_attachment_url( $post->ID ) : get_permalink( $post->ID ),
				'type'  => ucfirst( $post->post_type ),
			);
		}

		return $data;
	}
}
