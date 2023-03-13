<?php

// TODO: we need to disable the date filters after a moment to not affect everything (when?).
class WPBDP_Page_Meta {

    private $action = '';
    private $listing_id = 0;

    public function __construct( $action = '' ) {
        $this->action = $action;

        if ( 'showlisting' == $this->action ) {
            $this->listing_id = get_query_var( 'listing' ) ? wpbdp_get_post_by_slug( get_query_var( 'listing' ) )->ID : wpbdp_get_var( array( 'param' => 'id', 'default' => get_query_var( 'id' ) ) );

            add_filter( 'get_the_time', array( &$this, 'listing_page__get_the_time' ), 10, 2 );
            add_filter( 'get_the_date', array( &$this, 'listing_page__get_the_date' ), 10, 2 );
            add_filter( 'get_the_modified_time', array( &$this, 'listing_page__get_the_modified_time' ), 10, 2 );
            add_filter( 'get_the_modified_date', array( &$this, 'listing_page__get_the_modified_date' ), 10, 2 );
        }
    }

    // {{ Listing view.

    public function listing_page__get_the_time( $the_time, $d = '' ) {
        if ( ! $this->listing_id )
            return $the_time;

        if ( ! $d )
            $d = get_option( 'time_format' );

        //remove_filter( 'get_the_time', array( &$this, 'listing_page__get_the_time' ), 10, 2 );
        return get_post_time( $d, false, $this->listing_id, true );
    }

    public function listing_page__get_the_date( $the_date, $d = '' ) {
        if ( ! $this->listing_id )
            return $the_date;

        if ( ! $d )
            $d = get_option( 'date_format' );

        //remove_filter( 'get_the_date', array( &$this, 'listing_page__get_the_date' ), 10, 2 );
        return get_post_time( $d, $this->listing_id );
    }

    public function listing_page__get_the_modified_time( $the_time, $d = '' ) {
        if ( ! $this->listing_id )
            return $the_time;

        if ( ! $d )
            $d = get_option( 'time_format' );

        //remove_filter( 'get_the_modified_time', array( &$this, 'listing_page__get_the_modified_time' ), 10, 2 );
        return get_post_modified_time( $d, false, $this->listing_id, true );
    }

    public function listing_page__get_the_modified_date( $the_date, $d = '' ) {
        if ( ! $this->listing_id )
            return $the_date;

        if ( ! $d )
            $d = get_option( 'date_format' );

        //remove_filter( 'get_the_modified_date', array( &$this, 'listing_page__get_the_modified_date' ), 10, 2 );
        return get_post_modified_time( $d, false, $this->listing_id, true );
    }

    // }}

}
