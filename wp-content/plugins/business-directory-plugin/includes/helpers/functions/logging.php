<?php

/**
 * @since 5.0
 */
function wpbdp_insert_log( $args = array() ) {
    $defaults = array(
        'object_id' => 0,
        'rel_object_id' => 0,
        'object_type' => '',
        'created_at' => current_time( 'mysql' ),
        'log_type' => '',
        'actor' => 'system',
        'message' => '',
        'data' => null
    );
	$row = wp_parse_args( $args, $defaults );

	if ( ! $row['object_type'] && false !== strstr( $row['log_type'], '.' ) ) {
		$parts = explode( '.', $row['log_type'] );
		$row['object_type'] = $parts[0];
	}

	$row['object_id'] = absint( $row['object_id'] );
	$row['message']   = trim( $row['message'] );
	$row['data']      = $row['data'] ? serialize( $row['data'] ) : null;

	if ( ! $row['data'] ) {
		unset( $row['data'] );
	}

    global $wpdb;
    if ( ! $wpdb->insert( $wpdb->prefix . 'wpbdp_logs', $row ) )
        return false;

    $row['id'] = absint( $wpdb->insert_id );

    return (object) $row;
}

/**
 * @since 5.0
 */
function wpbdp_delete_log( $log_id ) {
    global $wpdb;
    return $wpdb->delete( $wpdb->prefix . 'wpbdp_logs', array( 'id' => $log_id ) );
}

/**
 * @since 5.0
 */
function wpbdp_get_log( $id ) {
    $results = wpbdp_get_logs( array( 'id' => $id ) );

    if ( ! $results )
        return false;

    return $results[0];
}

/**
 * @since 5.0
 * @return array|null
 */
function wpbdp_get_logs( $args = array() ) {
    $defaults = array(
        'limit' => 0,
        'orderby' => 'created_at',
        'order' => 'DESC'
    );
    $args = wp_parse_args( $args, $defaults );

    global $wpdb;

    $query  = '';
    $query .= "SELECT * FROM {$wpdb->prefix}wpbdp_logs WHERE 1=1";

    foreach ( $args as $arg_k => $arg_v ) {
        if ( in_array( $arg_k, array( 'id', 'object_id', 'object_type', 'created_at', 'log_type', 'actor' ) ) )
            $query .= $wpdb->prepare( " AND {$arg_k} = %s", $arg_v );
    }

    $query .= " ORDER BY {$args['orderby']} {$args['order']}, id {$args['order']}";

    return $wpdb->get_results( $query );
}
