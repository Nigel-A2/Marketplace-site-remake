<?php

/**
 * @since 3.7
 */
class WPBDP_DB_Entity_Error_List {

    private $errors;


    public function __construct() {
        $this->errors = array();
    }

    public function clear() {
        $this->errors = array();
    }

    public function is_empty() {
        return empty( $this->errors );
    }

    public function add( $attr, $error ) {
        if ( ! isset( $this->errors[ $attr ] ) )
            $this->errors[ $attr ] = array();

        $this->errors[ $attr ][] = $error;
    }

    public function is_invalid( $attr ) {
        return isset( $this->errors[ $attr ] );
    }

    public function get_errors() {
        return $this->errors;
    }

    public function messages( $callback = null ) {
        $res = array();

        foreach ( $this->errors as $attr => $attr_errors ) {
            foreach ( $attr_errors as $e ) {
                if ( ! $e )
                    continue;

                if ( $callback ) {
                    $res[] = call_user_func( $callback, $attr, $e );
                } else {
                    $res[] = $e;
                }
            }
        }

        return $res;
    }

    public function html( $before = '', $after = '', $before_item = '&#149; ', $after_item = '<br />' ) {
        $html  = '';
        $html .= $before;

        foreach ( $this->messages() as $msg )
            $html .= $before_item . $msg . $after_item;

        $html .= $after;

        return $html;
    }

    public function __toString() {
        return implode( "\n", $this->messages() );
    }


}
