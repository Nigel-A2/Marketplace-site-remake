<?php
require_once WPBDP_PATH . 'includes/db/class-db-query-set.php';

class WPBDP__DB__Model {

    protected $_adding = true;
    protected $_attrs = array();
    protected $_dirty = array();
    protected $_saving = false;


    public function __construct( $fields, $from_db = false ) {
        $model_info = self::get_model_info( $this );

        foreach ( $fields as $f => $v ) {
            if ( $from_db && in_array( $f, $model_info['serialized'], true ) )
                $v = maybe_unserialize( $v );

            $this->set_attr( $f, $v );
        }

        if ( $from_db )
            $this->_adding = false;

        $this->init();
    }

    protected function init() {
        // Init all model cols.
        $model_info = self::get_model_info( $this );
        $defaults = (array) $this->get_defaults();

        foreach ( array_keys( $model_info['table']['columns'] ) as $col ) {
            if ( in_array( $col, array( 'created_at', 'updated_at' ), true ) )
                continue;

            if ( isset( $this->_attrs[ $col ] ) )
                continue;

            $value = null;
            if ( array_key_exists( $col, $defaults ) )
                $value = $defaults[ $col ];

            $this->set_attr( $col, $value );
        }
    }

    protected function get_defaults() {
        return array();
    }

    protected function is_valid_attr( $name ) {
        $info = self::get_model_info( $this );
        $db_columns = array_keys( $info['table']['columns'] );

        if ( in_array( $name, $db_columns, true ) )
            return true;

        if ( method_exists( $this, 'get_' . $name ) || method_exists( $this, 'set_' . $name ) )
            return true;

        return false;
    }

    protected function get_attr( $name ) {
        if ( ! $this->is_valid_attr( $name ) )
            return false;

        return $this->_attrs[ $name ];
    }

    protected function set_attr( $name, $value ) {
        if ( ! $this->is_valid_attr( $name ) )
            return false;

        if ( isset( $this->_attrs[ $name ] ) && $value == $this->_attrs[ $name ] )
            return;

        $this->_attrs[ $name ] = $value;

        if ( ! in_array( $name, $this->_dirty, true ) )
            $this->_dirty[] = $name;
    }

    protected function prepare_row() {
        $row = array();

        $model = self::get_model_info( $this );
        $cols = $model['table']['columns'];
        $pk = $model['primary_key'];
        $dirty = $this->_dirty;

        // Assume everything's dirty for now, since we can't track arrays or objects..
        $dirty = $cols;
        // ... but handle these two columns with care.
        unset( $dirty['created_at'] );
        unset( $dirty['updated_at'] );

        $dirty = array_keys( $dirty );

        if ( ! $this->_adding )
            $row[ $pk ] = $this->_attrs[ $pk ];

        foreach ( $dirty as $col_name ) {
            if ( ! isset( $cols[ $col_name ] ) )
                continue;

            $col_value = $this->_attrs[ $col_name ];

            if ( $cols[ $col_name ]['serialized'] )
                $col_value = maybe_serialize( $col_value );

            $row[ $col_name ] = $col_value;
        }

        // Update timestamps.
        $time = current_time( 'mysql' );

        if ( isset( $cols['updated_at'] ) )
            $row['updated_at'] = $time;

        if ( $this->_adding && isset( $cols['created_at'] ) )
            $row['created_at'] = $time;

        return $row;
    }

    public function clean( &$errors ) {
    }

    public function &__get( $name ) {
        if ( ! $this->is_valid_attr( $name ) )
            throw new Exception( 'Invalid attribute: ' . $name );

        if ( method_exists( $this, 'get_' . $name ) ) {
            $v = call_user_func( array( $this, 'get_' . $name ) );
            return $v;
        }

        if ( ! isset( $this->_attrs[ $name ] ) ) {
            $v = null;
            return $v;
        }

        $value = &$this->_attrs[ $name ];
        return $value;
    }

    public function __set( $name, $value ) {
        if ( ! $this->is_valid_attr( $name ) )
            throw new Exception( 'Invalid attribute: ' . $name );

        if ( method_exists( $this, 'set_' . $name ) )
            return call_user_func( array( $this, 'set_' . $name ) );

        $this->set_attr( $name, $value );
    }

    public function update( $fields = array() ) {
        foreach ( $fields as $f => $v ) {
            $this->set_attr( $f, $v );
        }
    }

    protected function before_delete() {}
    protected function after_delete() {}
    protected function before_save( $new = false ) {}
    protected function after_save( $new = false ) {}

    public function save( $validate = true, $fire_hooks = true ) {
        global $wpdb;

        $adding = $this->_adding;
        $errors = array();

        if ( $validate )
            $this->clean( $errors );

        if ( $errors )
			throw new Exception( 'Invalid model instance!' );

        $this->_saving = true;

        $model = self::get_model_info( $this );
        $pk = $model['primary_key'];

        if ( $fire_hooks )
            $this->before_save( $adding );

        $row = $this->prepare_row();

        if ( $this->_adding ) {
            $res = $wpdb->insert( $model['table']['name'], $row );
        } else {
            $res = $wpdb->update( $model['table']['name'], $row, array( $pk => $this->_attrs[ $pk ] ) );
        }

        if ( $this->_adding && $res ) {
            $this->_attrs[ $pk ] = $wpdb->insert_id;
            $this->_adding = false;
        }

        $res = false !== $res;

        if ( $res && $fire_hooks )
            $this->after_save( $adding );

        $this->_saving = false;

        return $res;
    }

    public function delete() {
        global $wpdb;

        if ( $this->_adding )
            return true;

        $this->before_delete();

        $pk = self::get_model_info( $this, 'primary_key' );
        $where = array( $pk => $this->_attrs[ $pk ] );

        $res = ( false !== $wpdb->delete( self::get_model_info( $this, 'table_name' ), $where ) );

        if ( $res )
            $this->after_delete();

        return $res;
    }

    public function refresh() {
        if ( $this->_adding )
            return;

        $model = self::get_model_info( $this );
        $pk = $model['primary_key'];
        $obj = self::_objects( get_class( $this ) )->get( $this->{$pk} );

        foreach ( $obj->_attrs as $k => $v ) {
            $this->_attrs[ $k ] = $v;
        }
    }

    public static function objects() {
		throw new Exception( 'Method not overridden in subclass!' );
    }

    public static function _objects( $classname ) {
        static $managers_per_class = array();

		if ( ! isset( $managers_per_class[ $classname ] ) ) {
			$managers_per_class[ $classname ] = new WPBDP__DB__Query_Set( $classname, false );
		}

        return $managers_per_class[ $classname ];
    }

    public static function from_db( $fields, $classname ) {
        $obj = new $classname( $fields, true );
        return $obj;
    }

    public static function get_model_info( $classname, $key = '' ) {
        global $wpdb;
        static $cache = array();

        if ( is_object( $classname ) )
            $classname = get_class( $classname );

        if ( isset( $cache[ $classname ] ) )
            return $key ? $cache[ $classname ][ $key ] : $cache[ $classname ];

        $cls_vars = get_class_vars( $classname );

        $info                = array();
        $info['class']       = $classname;
		$info['table']       = array(
			'name'    => isset( $cls_vars['table'] ) ? $wpdb->prefix . $cls_vars['table'] : $wpdb->prefix . strtolower( $classname ) . 's',
			'columns' => array(),
		);
        $info['table_name']  = $info['table']['name'];
        $info['primary_key'] = isset( $cls_vars['primary_key'] ) ? $cls_vars['primary_key'] : 'id';
        $info['serialized']  = isset( $cls_vars['serialized'] ) ? $cls_vars['serialized'] : array();

		foreach ( $wpdb->get_results( 'SHOW COLUMNS FROM ' . $info['table']['name'], ARRAY_A ) as $col ) {
            $info['table']['columns'][ $col['Field'] ] = array( 'type'       => $col['Type'],
                                                                'nullable'   => ( 'yes' == strtolower( $col['Null'] ) ),
                                                                'default'    => $col['Default'],
                                                                'serialized' => in_array( $col['Field'], $info['serialized'], true ) );
        }

        $cache[ $classname ] = $info;
        return $key ? $info[ $key ] : $info;
    }

}

// For backwards-compat.
require_once WPBDP_PATH . 'includes/compatibility/deprecated/class-db-model2.php';


