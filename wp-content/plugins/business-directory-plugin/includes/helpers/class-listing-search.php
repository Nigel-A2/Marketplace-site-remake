<?php
/**
 * @package WPBDP/Includes/Helpers/Search
 */

/**
 * Class WPBDP__Listing_Search
 *
 * @since 5.0
 */
class WPBDP__Listing_Search {

    private $tree             = array();
    private $original_request = array();
    private $parts            = array();
    private $original_parts   = array();
    private $is_quick_search  = true;
    public $aliases           = array();
    private $query_template   = '';
    private $query            = '';
    private $results          = null;


    public function __construct( $tree, $original_request = array() ) {
        $this->tree             = $tree;
        $this->original_request = $original_request;
        $this->is_quick_search  = array_key_exists( 'kw', $original_request );

        // If the tree has no head, assume 'and'.
        if ( ! isset( $this->tree[0] ) || ! is_string( $this->tree[0] ) ) {
            array_unshift( $this->tree, 'and' );
        }

        if ( ! $this->original_parts ) {
            $this->_traverse_tree( $this->tree );
            $this->original_parts = $this->parts;
        }
    }

    /**
     * Returns current search quick_search status.
     *
     * @since 5.6.1
     */
    public function is_quick_search() {
        return $this->is_quick_search;
    }

    public function terms_for_field( $field ) {
        $field = is_object( $field ) ? $field->get_id() : absint( $field );

        $result = array();

        $parts = $this->is_quick_search ? $this->parts : $this->original_parts;

        foreach ( $parts as $p ) {
            if ( $field == $p[0] ) {
                $result[] = $p[1];
            }
        }

        return $result;
    }

    public function get_original_search_terms_for_field( $field ) {
        $quick_search_fields_ids = self::get_quick_search_fields_ids();

        if ( in_array( $field->get_id(), $quick_search_fields_ids, true ) && isset( $this->original_request['kw'] ) ) {
            return array( $this->original_request['kw'] );
        }

        return $this->terms_for_field( $field );
    }

    public function get_tree() {
        return $this->tree;
    }

    public function get_results() {
        if ( ! is_array( $this->results ) ) {
            $this->execute();
        }

        return $this->results;
    }

    public function execute() {
        global $wpdb;

        $this->tree = self::tree_simplify( $this->tree );

        // Prepare query template.
        $this->query_template = $this->_traverse_tree( $this->tree );

        // Build query.
        $query_pieces = array(
			'where'    => $this->query_template,
			'join'     => '',
			'orderby'  => '',
			'distinct' => '',
			'fields'   => "{$wpdb->posts}.ID",
			'limits'   => '',
            'posts_in' => '',
		);

        foreach ( $this->parts as $key => $data ) {
            $res   = null;
            $field = wpbdp_get_form_field( $data[0] );

            if ( $field ) {
                $res = $field->configure_search( $data[1], $this );
            }

            if ( ! empty( $res['where'] ) ) {
                $query_pieces['where'] = str_replace( '%' . $key . '%', $res['where'], $query_pieces['where'] );
            } else {
                // This prevents incorrect queries from being created.
                $query_pieces['where'] = str_replace( 'AND %' . $key . '%', '', $query_pieces['where'] );
                $query_pieces['where'] = str_replace( 'OR %' . $key . '%', '', $query_pieces['where'] );
            }

            if ( ! $res ) {
                continue;
            }

            foreach ( $res as $k => $v ) {
                if ( 'where' == $k ) {
                    continue;
                }

                $query_pieces[ $k ] .= ' ' . $v . ' ';
            }
        }

        $query_pieces['where'] = str_replace( 'AND  AND', 'AND', $query_pieces['where'] );
        $query_pieces['where'] = str_replace( 'OR  OR', 'OR', $query_pieces['where'] );
        $query_pieces['where'] = str_replace( 'AND )', ')', $query_pieces['where'] );
        $query_pieces['where'] = str_replace( 'OR )', ')', $query_pieces['where'] );

        $query_pieces = apply_filters_ref_array( 'wpbdp_search_query_pieces', array( $query_pieces, $this ) );

        $this->query = sprintf(
            "SELECT %s %s FROM {$wpdb->posts} %s WHERE ({$wpdb->posts}.post_type = '%s' AND {$wpdb->posts}.post_status = '%s') AND %s %s GROUP BY {$wpdb->posts}.ID %s %s",
            $query_pieces['distinct'],
            $query_pieces['fields'],
            $query_pieces['join'],
            WPBDP_POST_TYPE,
            'publish',
            $query_pieces['where'],
            $query_pieces['posts_in'],
            $query_pieces['orderby'],
            $query_pieces['limits']
        );
        // wpbdp_debug_e($this->query);

        $this->results = $wpdb->get_col( $this->query );

    }

    private function _traverse_tree( $tree ) {
        if ( is_array( $tree ) && 2 == count( $tree ) && is_numeric( $tree[0] ) ) {
            $key = md5( serialize( $tree ) );

            if ( ! isset( $this->parts[ $key ] ) ) {
                $this->parts[ $key ] = $tree;
            }

            return '%' . $key . '%';
        }

        $res  = '';
        $head = $tree[0];
        $args = array_slice( $tree, 1 );

        $res .= '(';
        $res .= ( 'and' == $head ? '1=1' : '1=0' );

        foreach ( $args as $x ) {
            $res .= ' ' . strtoupper( $head ) . ' ';
            $res .= $this->_traverse_tree( $x );
        }

        $res .= ')';

        return $res;
    }

    public function join_alias( $table, $reuse = false ) {
        if ( ! isset( $this->aliases[ $table ] ) ) {
            $this->aliases[ $table ] = array();
        }

        $i      = count( $this->aliases[ $table ] );
        $alias  = '';
        $reused = false;

        if ( $reuse && $i > 0 ) {
            $alias  = $this->aliases[ $table ][ $i - 1 ];
            $reused = true;
        } else {
            $alias = $i > 0 ? $table . '_t_' . $i : $table;
        }

        $this->aliases[ $table ][] = $alias;

        return array( $alias, $reused );
    }

    public static function from_request( $request = array() ) {
        return new self( self::parse_request( $request ), $request );
    }

    public static function parse_request( $request = array() ) {
        $res = array();

        // Quick search.
        if ( ! empty( $request['kw'] ) ) {
            if ( wpbdp_get_option( 'quick-search-enable-performance-tricks' ) ) {
                $request['kw'] = array( trim( $request['kw'] ) );
            } else {
                $request['kw'] = explode( ' ', trim( $request['kw'] ) );
            }

            $fields = array();

            foreach ( self::get_quick_search_fields_ids() as $field_id ) {
                $field = wpbdp_get_form_field( $field_id );

                if ( $field ) {
                    $fields[] = $field;
                }
            }

            $res[] = 'and';

            foreach ( $request['kw'] as $k ) {
                $subq = array( 'or' );

                foreach ( $fields as $field ) {
                    $subq[] = array( $field->get_id(), $k );
                }

                $res[] = $subq;
            }
        } elseif ( ! empty( $request['listingfields'] ) ) {
            // Regular search.
            $res[] = 'and';

            foreach ( $request['listingfields'] as $field_id => $term ) {
                if ( ! $term ) {
                    continue;
                }

                $search_terms = array();

                if ( is_string( $term ) ) {
                    $search_terms = array_filter(
                        explode( ' ', trim( $term ) ),
                        function ( $t ) {
                            return strlen( $t ) >= 2;
                        }
                    );
                }

                if ( count( $search_terms ) < 2 ) {
                    $res[] = array( $field_id, $term );
                    continue;
                }

                $subq   = array( 'or' );
                $termsq = array( 'and' );

                foreach ( $search_terms as $k ) {
                    $termsq[] = array( $field_id, $k );
                }

				$subq[] = $termsq;
                $subq[] = array( $field_id, $term );

                $res[] = $subq;
            }
        }

        $res = apply_filters( 'wpbdp_listing_search_parse_request', $res, $request );
        return $res;
    }

    /**
     * TODO: This method is similar to WPBDP_Listings_API::get_quick_search_fields().
     * TODO: Do we need to cache this?
     *
     * @since 4.1.13
     */
    private static function get_quick_search_fields_ids() {
        $fields_ids = wpbdp_get_option( 'quick-search-fields' );
        $fields_ids = $fields_ids ? $fields_ids : wpbdp_get_form_fields( 'association=title,excerpt,content&output=ids' );
        return array_map( 'intval', $fields_ids );
    }

    public static function tree_remove_field( $tree, $field, $term = null ) {
        $field  = is_object( $field ) ? $field->get_id() : absint( $field );
        $result = array();

        foreach ( $tree as $t ) {
            if ( self::is_field_node( $t, $field, $term ) ) {
                continue;
            } elseif ( is_array( $t ) ) {
                $t = self::tree_remove_field( $t, $field, $term );
            }

            $result[] = $t;
        }

        return $result;
    }

    /**
     * Checks whether the given node is a field node for the given Form Field ID
     * and search term.
     *
     * A field node is an indexed array with two elements:
     *
     * - The Form Field ID.
     * - A search term for that field.
     *
     * @since 4.0.12
     * @param array  $node     The node that will be checked.
     * @param int    $field_id The ID of the Form Field.
     * @param string $term     If provided and is not null, this function will return true
     *                  when both the Field ID and the search term match only.
     * @return boolean
     */
    private static function is_field_node( $node, $field_id, $term = null ) {
        if ( ! is_array( $node ) || 2 != count( $node ) || ! isset( $node[0] ) || ! isset( $node[1] ) ) {
            return false;
        }

        if ( $field_id != $node[0] ) {
            return false;
        }

        if ( ! is_null( $term ) && $term != $node[1] ) {
            return false;
        }

        return true;
    }

    public static function tree_simplify( $tree ) {
        return $tree;
    }

    public function get_query_template() {
        if ( $this->is_quick_search && empty( $this->original_request['kw'] ) ) {
            return '( 1=1 )';
        }

        return $this->_traverse_tree( $this->tree );
    }
}
