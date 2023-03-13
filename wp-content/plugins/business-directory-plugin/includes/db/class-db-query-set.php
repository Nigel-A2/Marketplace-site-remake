<?php
/**
 * @since 5.0
 */
class WPBDP__DB__Query_Set implements IteratorAggregate {

    private $db;

    private $model;

    private $query = array();
    private $executed = false;

    private $rows = array();

    public function __construct( $model, $query = null ) {
        global $wpdb;

        $this->db = $wpdb;
        $this->model = is_array( $model ) ? $model : WPBDP__DB__Model::get_model_info( $model );

        if ( $query ) {
            $this->query = $query;
        } else {
            $this->query = array( 'where' => '',
                                  'join' => '',
                                  'groupby' => '',
                                  'orderby' => '',
                                  'distinct' => '',
                                  'limits' => '',
                                  'fields' => ''
			);
		}
    }

    public function get( $args = array() ) {
        if ( is_scalar( $args ) )
            $args = array( 'pk' => $args );

        $q = $this->query;

        if ( $args ) {
            $where = implode( ' AND ', $this->filter_args( $args ) );
            $q['where'] = ! empty( $q['where'] ) ? $q['where'] . " AND ($where)" : $where;
        }

        $q['limit'] = 'LIMIT 1';

        $qs = new self( $this->model, $q );
        $qs->maybe_execute_query();

        $res = $qs->to_array();

        if ( ! $res )
            return false;

        return $res[0];
    }

    public function filter( $args = array(), $negate = false, $operator = 'AND' ) {
        if ( ! $args )
            return $this;

        $where = $this->filter_args( $args );
        $where = implode( ' ' . $operator . ' ', $where );

        if ( $negate )
            $where = " NOT ($where) ";

        $q = $this->query;
        $q['where'] = ! empty( $q['where'] ) ? $q['where'] . " AND ($where)" : $where;

        return new self( $this->model, $q );
    }

    public function exclude( $args = array() ) {
        if ( ! $args )
            return $this;

        return $this->filter( $args, true );
    }

    public function all() {
        return new self( $this->model, $this->query );
    }

    public function order_by( $args ) {
        if ( is_string( $args ) )
            $args = array( $args );

        $order = array();

        foreach ( $args as $o ) {
			if ( '-' == $o[0] ) {
				$order[] = substr( $o, 1 ) . ' DESC';
			} else {
				$order[] = $o . ' ASC';
			}
        }

        $order = implode( ',', $order );

        $q = $this->query;
        $q['orderby'] = ! empty( $q['orderby'] ) ? $q['orderby'] . ', ' . $order : $order;

        return new self( $this->model, $q );
    }

    public function limit( $limit ) {
        $limit = absint( $limit );

        if ( ! $limit )
            return $this;

        $q = $this->query;

		if ( ! empty( $q['limits'] ) ) {
			throw new Exception( 'Query already has a limit.' );
		}

        $q['limits'] = 'LIMIT ' . $limit;
        return new self( $this->model, $q );
    }

    public function count() {
        $sql = $this->build_sql_query();
        $sql = str_replace( '*', 'COUNT(*)', $sql );

        return absint( $this->db->get_var( $sql ) );
    }

    public function exists() {
        return $this->count() > 0;
    }

	#[\ReturnTypeWillChange]
    public function getIterator() {
        return new ArrayIterator( $this->to_array() );
    }

    public function to_array() {
        $this->maybe_execute_query();

        $res = array();

        foreach ( $this->rows as $r ) {
            $res[] = WPBDP__DB__Model::from_db( $r, $this->model['class'] );
        }

        return $res;
    }

    private function maybe_execute_query() {
        if ( $this->executed )
            return;

        $sql = $this->build_sql_query();
        $this->rows = $this->db->get_results( $sql, ARRAY_A );
    }

    private function filter_args( $args ) {
        $args = wp_parse_args( $args );
        // null is NULL
        // _exact
        // _iexact ILIKE
        // __in
        // >
        // <
        // <=
        // >=
        // startswith
        // istartswith
        // endswith
        // iendswith
        // range BETWEEN x AND y
        // __isnull
        $filters = array();

        foreach ( $args as $f => $v ) {
            if ( 'pk' == $f )
                $f = $this->model['primary_key'];

            $op = '=';

            if ( false !== strpos( $f, '__' ) ) {
                $parts = explode( '__', $f );
                $f = 'LOWER(' . $parts[0] . ')';
                $qop = $parts[1];

                switch ( $qop ) {
                case 'contains':
                case 'icontains':
                    $op = 'LIKE';
                    $v = '%' . strtolower( $v ) . '%';
                    break;
                }
            }

			if ( is_array( $v ) ) {
				$filters[] = "$f IN ('" . implode( '\',\'', $v ) . "')";
			} else {
				$filters[] = $this->db->prepare( "$f $op %s", $v );
			}
        }

        return $filters;
    }

    public function build_sql_query() {
        extract( $this->query );

        $table = $this->model['table']['name'];

        if ( ! $fields )
            $fields = '*';

        if ( ! empty( $groupby ) )
            $groupby = 'GROUP BY ' . $groupby;

        if ( ! empty( $orderby ) )
            $orderby = 'ORDER BY ' . $orderby;

        if ( ! empty( $where ) )
            $where = "WHERE $where";

        $query = "SELECT $distinct $fields FROM $table $join $where $groupby $orderby $limits";
        return $query;
    }

}
