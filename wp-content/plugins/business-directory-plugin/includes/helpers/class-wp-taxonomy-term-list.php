<?php
/**
 * @since 5.0
 */
class WPBDP__WP_Taxonomy_Term_List {

    static $_n = 0;

    protected $args = array();
    protected $output = null;


    public function __construct( $args = array() ) {
        self::$_n++;

        $defaults = array(
            'taxonomy' => WPBDP_CATEGORY_TAX,
            'input' => '',
            'input_name' => '',
            'selected' => array(),
            'anidate' => false,
            'indent' => true,
            'indent_character' => '&mdash;&nbsp;',
            'hide_empty' => false,
            'before' => '',
            'after' => ''
        );

        $this->args = wp_parse_args( $args, $defaults );
    }

    public function output() {
        if ( ! is_null( $this->output ) )
            return $this->output;

        $this->walk();
        return $this->output;
    }

    public function display() {
        echo $this->output();
    }

    protected function walk( $parent_id = 0, $depth = 0 ) {
		$terms = get_terms(
			array(
				'taxonomy'   => $this->args['taxonomy'],
				'parent'     => $parent_id,
				'hide_empty' => $this->args['hide_empty'],
			)
		);

        if ( $terms && $this->args['anidate'] )
            $this->output .= '<ul>';

        foreach ( $terms as $t ) {
            $this->output .= $this->element( $t, $depth );
            $this->output .= $this->walk( $t->term_id, $depth + 1 );
        }

        if ( $terms && $this->args['anidate']  )
            $this->output .= '</ul>';
    }

    protected function id_for( $term ) {
        return 'wpbdp-wp-tt-list-' . self::$_n . '-item-' . $term->term_id;
    }

    protected function element( $term, $depth ) {
        $res  = '';
        $res .= $this->args['anidate'] ? '<li>' : '';
		$res .= $this->element_before( $term, $depth );

        switch ( $this->args['input'] ) {
        case 'checkbox':
            $res .= '<input type="checkbox" class="term-cb" name="' . $this->args['input_name'] . '[]" value="' . $term->term_id . '" id="' . $this->id_for( $term ) . '" ' . checked( in_array( $term->term_id, $this->args['selected'], true ), true, false ) . '/>';
            break;
        case 'radio':
            $res .= '<input type="radio" class="term-cb" name="' . $this->args['input_name'] . '" value="' . $term->term_id . '" id="' . $this->id_for( $term ) . '" />';
            break;
        default:
            break;
        }

        if ( $this->args['input'] )
            $res .= '<label for="' . $this->id_for( $term ) . '">';

        if ( $this->args['indent'] )
            $res .= str_repeat( $this->args['indent_character'], $depth );

        $res .= $term->name;

        if ( $this->args['input'] )
            $res .= '</label>';

		$res .= $this->element_after( $term, $depth );

        $res .= $this->args['anidate'] ? '</li>' : '<br />';

        return $res;
    }

    protected function element_before( $term, $depth ) {
        return $this->args['before'];
    }

    protected function element_after( $term, $depth ) {
        return $this->args['after'];
    }

}
