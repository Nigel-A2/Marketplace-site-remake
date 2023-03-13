<?php
/**
 * @deprecated - Use {@link WPBDP__DB__Model} instead.
 */
class WPBDP_DB_Model2 {

    public $errors = array();

    protected $table = '';
    protected $serialized = array();

    private $attrs = array();


    public function __construct( $data = array() ) {
		_deprecated_function( __METHOD__, 'Unknown', 'WPBDP__DB__Model' );
        $this->fill( $data );
    }

    public function fill( $data = array() ) {
        foreach ( $data as $k => $v ) {
			$this->attrs[ $k ] = ( in_array( $k, $this->serialized, true ) && $v ) ? maybe_unserialize( $v ) : $v;
        }
    }

    private function validate() {
        $this->errors = $this->_validate();
        return empty( $this->errors ) ? true : false;
    }

    public function sanitize() {
    }

    protected function _validate() {
        return array();
    }

    public function is_valid() {
        return $this->validate();
    }

    public function is_invalid() {
        return ! $this->is_valid();
    }

    protected function update_timestamps( $row ) {
        global $wpdb;
        $table = $wpdb->prefix . 'wpbdp_' . $this->table;

        if ( isset( $this->attrs['id'] ) && $this->attrs['id'] ) {
        } else {
            if ( ! isset( $row['created_at'] ) && $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'created_at' ) ) ) {
                $row['created_at'] = current_time( 'mysql' );
            }
        }

        if ( $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$table} LIKE %s", 'updated_at' ) ) ) {
            $row['updated_at'] = current_time( 'mysql' );
        }

        return $row;
    }

    public function save( $validate = true ) {
        global $wpdb;

        if ( $validate )
            $this->sanitize();

		if ( isset( $this->attrs['id'] ) && $this->attrs['id'] ) {
            return $this->update( $validate );
		}
		return $this->insert( $validate );
    }

    public function delete() {
        if ( ! isset( $this->attrs['id'] ) || ! $this->attrs['id'] )
            return false;

        global $wpdb;
        $table = $wpdb->prefix . 'wpbdp_' . $this->table;
        return ( false !== $wpdb->delete( $table, array( 'id' => $this->attrs['id'] ) ) );
    }

    private function insert( $validate = true ) {
        global $wpdb;
        $table = $wpdb->prefix . 'wpbdp_' . $this->table;

        if ( $validate && ! $this->validate() )
            return false;

        $row = array();
        foreach ( $this->attrs as $k => $v ) {
            if ( is_null( $v ) )
                continue;

			$row[ $k ] = in_array( $k, $this->serialized, true ) ? ( $v ? serialize( $v ) : '' ) : $v;
        }

        $row = $this->update_timestamps( $row );

        if ( false !== $wpdb->insert( $table, $row ) ) {
            $this->attrs['id'] = intval( $wpdb->insert_id );
            return true;
        }

        return false;
    }

    private function update( $validate = true ) {
        global $wpdb;
        $table = $wpdb->prefix . 'wpbdp_' . $this->table;

        $row = array();
        foreach ( $this->attrs as $k => $v ) {
			if ( ! is_null( $v ) ) {
				$row[ $k ] = in_array( $k, $this->serialized, true ) ? ( $v ? serialize( $v ) : '' ) : $v;
			}
        }

        $row = $this->update_timestamps( $row );

        if ( $validate && ! $this->validate() )
            return false;

        return false !== $wpdb->update( $table, $row, array( 'id' => $this->id ) );
    }

    public function &get_attr( $k ) {
        if ( array_key_exists( $k, $this->attrs ) ) {
            $value = $this->attrs[ $k ];
        } else {
            $value = null;
        }

        return $value;
    }

    public function set_attr( $k, $v ) {
        $this->attrs[ $k ] = $v;
    }

    public function &__get( $k ) {
        if ( method_exists( $this, "get_$k" ) ) {
            $v = call_user_func( array( &$this, "get_$k" ) );
            return $v;
        }

        return $this->get_attr( $k );
    }

    public function __set( $k, $v ) {
        if ( method_exists( $this, "set_$k" ) ) {
            return call_user_func( array( &$this, "set_$k" ), $v );
        }

        // if ( array_key_exists( $k, $this->attrs ) )
        return $this->set_attr( $k, $v );

        // throw new Exception( 'Undefined Property: ' . $k );
    }

    /**
     * Convenience method to search records in a database table.
     * Subclasses should override this method because we have to support PHP 5.2 where late static binding is not available.
	 *
     * @return array
     */
    public static function find( $id, $args = array() ) {
		throw new Exception( 'find() method not implemented.' );
    }

    protected static function _find( $id, $args = array(), $table = '', $classname = '' ) {
        if ( ! $table || ! $classname || ! class_exists( $classname ) )
            throw new Exception( 'Please provide a table and class name.' );

        global $wpdb;

        $single = false;

        switch ( $id ) {
            case 'first':
                $args['_limit'] = 1;
                $args['_order'] = 'id';
                $single = true;

                break;
            case 'last':
                $args['_limit'] = 1;
                $args['_order'] = '-id';
                $single = true;

                break;
            case 'all':
                break;
            default:
                $args['id'] = intval( $id );
                $args['_limit'] = 1;
                $single = true;

                break;
        }

		$single = ( ! $single && isset( $args['_single'] ) && true == $args['_single'] ) ? true : $single;
        $order = isset( $args['_order'] ) ? $args['_order'] : '';
        $limit = isset( $args['_limit'] ) ? $args['_limit'] : '';
        $extra = isset( $args['_query_extra'] ) ? $args['_query_extra'] : array();

		$query = 'SELECT t.*';

        if ( isset( $extra['fields'] ) )
            $query .= ', ' . $extra['fields'];

        $query .= " FROM {$table} t";
        if ( isset( $extra['table'] ) )
            $query .= ', ' . $extra['table'] . ' ';

		$query .= ' WHERE 1=1';
        if ( isset( $extra['where'] ) )
            $query .= ' ' . $extra['where'] . ' ';

        foreach ( $args as $arg => $value ) {
            if ( is_null( $value ) || in_array( $arg, array( '_single', '_order', '_limit', '_query_extra' ), true ) )
                continue;

            if ( is_array( $value ) ) {
                $value_str = implode( ',', $value );
                $query .= " AND t.{$arg} IN ({$value_str})";
            } elseif ( $value[0] == '>' ) {
                $query .= " AND t.{$arg} {$value}";
            } else {
                $query .= $wpdb->prepare( " AND t.{$arg}=" . ( is_int( $value ) ? '%d' : '%s' ), $value );
            }
        }

        if ( isset( $extra['groupby'] ) )
            $query .= ' GROUP BY ' . $extra['groupby'];

        if ( $order ) {
            $order_field = wpbdp_starts_with( $order, '-' ) ? substr( $order, 1 ) : $order;
            $order_dir = wpbdp_starts_with( $order, '-' ) ? 'DESC' : 'ASC';

			if ( isset( $extra['orderby'] ) ) {
                $query .= ' ORDER BY ' . $extra['orderby'];
			} else {
                $query .= " ORDER BY t.{$order_field} {$order_dir}";
			}
        }

        if ( $limit > 0 )
            $query .= " LIMIT {$limit}";

        if ( $single ) {
			$row = $wpdb->get_row( $query, ARRAY_A );
			if ( $row ) {
                return new $classname( $row );
			} else {
                return null;
			}
        }

		return array_map(
            function( $x ) use ( $classname ) {
				return new $classname( $x );
            },
			$wpdb->get_results( $query, ARRAY_A )
        );
    }


}
