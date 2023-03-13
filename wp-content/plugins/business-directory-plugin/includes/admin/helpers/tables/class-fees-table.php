<?php
/**
 * Class fees table
 *
 * @package Includes/Admin/Helpers
 */

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class WPBDP__Admin__Fees_Table
 */
class WPBDP__Admin__Fees_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct(
            array(
				'singular' => _x( 'fee', 'fees admin', 'business-directory-plugin' ),
				'plural'   => _x( 'fees', 'fees admin', 'business-directory-plugin' ),
				'ajax'     => false,
            )
        );
    }

    public function no_items() {
		printf(
			/* translators: %1$s: open link html, %2$s close link */
			esc_html__( 'There are no plans right now. %1$sCreate one%2$s.', 'business-directory-plugin' ),
			'<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp-admin-fees&wpbdp-view=add-fee' ) ) . '">',
			'</a>'
        );
    }

    public function get_current_view() {
		return 'all';
    }

    public function get_views() {
        global $wpdb;

        $admin_fees_url = admin_url( 'admin.php?page=wpbdp-admin-fees' );

        $views = array();

		$all = absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_plans" ) );

        $views['all'] = sprintf(
            '<a href="%s" class="current">%s</a> <span class="count">(%s)</span></a>',
			esc_url( $admin_fees_url ),
            _x( 'All', 'admin fees table', 'business-directory-plugin' ),
            number_format_i18n( $all )
        );

        return $views;
    }

    public function get_columns() {
        $cols = array(
			'order'      => __( 'Order', 'business-directory-plugin' ),
			'label'      => __( 'Plan Details', 'business-directory-plugin' ),
			'amount'     => __( 'Pricing', 'business-directory-plugin' ),
			'listings'   => __( 'Listings', 'business-directory-plugin' ),
			'images'     => __( 'Images', 'business-directory-plugin' ),
			'attributes' => _x( 'Attributes', 'fees admin', 'business-directory-plugin' ),
        );
		$current_order = wpbdp_get_option( 'fee-order' );
		if ( 'custom' !== $current_order['method'] ) {
			unset( $cols['order'] );
		}

        return $cols;
    }

    public function prepare_items() {
        $this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );

        $args = array(
			'admin_view'   => true, // Admin view shows all listings
			'enabled'      => 'all',
        );

        $this->items = wpbdp_get_fee_plans( $args );
    }

	/**
	 * @param object $item
	 */
    public function single_row( $item ) {
		$classes = 'fee';
		if ( ! $item->enabled ) {
			$classes .= ' disabled-fee';
		} elseif ( 'free' === $item->tag ) {
			$classes .= ' free-fee';
		}

        echo '<tr class="' . $classes . '">';
        $this->single_row_columns( $item );
        echo '</tr>';
    }

    public function column_order( $fee ) {
        return sprintf(
            '<span class="wpbdp-drag-handle" data-fee-id="%s"></span> <a href="%s"><strong>↑</strong></a> | <a href="%s"><strong>↓</strong></a>',
            $fee->id,
            esc_url(
                add_query_arg(
                    array(
						'action' => 'feeup',
						'id'     => $fee->id,
                    ),
                    admin_url( 'admin.php?page=wpbdp-admin-fees' )
                )
            ),
            esc_url(
                add_query_arg(
                    array(
						'action' => 'feedown',
						'id'     => $fee->id,
                    ),
                    admin_url( 'admin.php?page=wpbdp-admin-fees' )
                )
            )
        );
    }

    public function column_label( $fee ) {
        $admin_fees_url = admin_url( 'admin.php?page=wpbdp-admin-fees' );
        $actions         = array();
        $actions['edit'] = sprintf(
            '<a href="%s">%s</a>',
            esc_url(
                add_query_arg(
                    array(
						'wpbdp-view' => 'edit-fee',
						'id'         => $fee->id,
                    ),
                    $admin_fees_url
                )
            ),
            _x( 'Edit', 'fees admin', 'business-directory-plugin' )
        );

		$toggle_url = add_query_arg(
			array(
				'wpbdp-view' => 'toggle-fee',
				'id'         => $fee->id,
			),
			$admin_fees_url
		);

		if ( $fee->enabled ) {
			$actions['disable'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $toggle_url ),
				esc_html__( 'Disable', 'business-directory-plugin' )
			);
		} else {
			$actions['enable'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( $toggle_url ),
				esc_html__( 'Enable', 'business-directory-plugin' )
			);
		}
		if ( 'free' !== $fee->tag ) {
            $actions['delete'] = sprintf(
				'<a href="%1$s" data-bdconfirm="%2$s">%3$s</a>',
                esc_url(
					wp_nonce_url(
						add_query_arg(
							array(
								'wpbdp-view' => 'delete-fee',
								'id'         => $fee->id,
							),
							$admin_fees_url
						),
						'delete-fee'
					)
                ),
				esc_attr__( 'Are you sure you want to do this?', 'business-directory-plugin' ),
                esc_html__( 'Delete', 'business-directory-plugin' )
            );
        }

        $html  = '';

		$fee_id_string = sprintf(
			__( 'ID: %s', 'business-directory-plugin' ),
			$fee->id
		);
		$fee_id_string .= '<br/><strong>' . ( $fee->is_paid_plan() ? __( 'Paid Plan', 'business-directory-plugin' ) : __( 'Free Plan', 'business-directory-plugin' ) ) . '</strong>';

        $html .= sprintf(
            '<strong><a href="%s">%s</a></strong><br/>%s',
            esc_url(
                add_query_arg(
                    array(
						'wpbdp-view' => 'edit-fee',
						'id'         => $fee->id,
                    ),
                    $admin_fees_url
                )
            ),
            esc_attr( $fee->label ),
            wp_kses( $fee_id_string, array( 'br' => array(), 'strong' => array() ) )
        );
        $html .= $this->row_actions( $actions );

        return $html;
    }

    public function column_amount( $fee ) {
        if ( 'variable' === $fee->pricing_model ) {
            return _x( 'Variable', 'fees admin', 'business-directory-plugin' );
        } elseif ( 'extra' === $fee->pricing_model ) {
            $amount = wpbdp_currency_format( $fee->amount );
            $extra  = wpbdp_currency_format( $fee->pricing_details['extra'] );

            return sprintf( _x( '%1$s + %2$s per category', 'fees admin', 'business-directory-plugin' ), $amount, $extra );
        }

		$amount = $fee->amount ? wpbdp_currency_format( $fee->amount ) : '';
		$time   = $this->column_duration( $fee );
		if ( $amount ) {
			$amount = sprintf(
				__( '%1$s for %2$s', 'business-directory-plugin' ),
				$amount,
				$time
			);
		} else {
			$amount = $time;
		}
		return esc_html( $amount );
    }

    public function column_duration( $fee ) {
        if ( $fee->days === 0 ) {
            return _x( 'Forever', 'fees admin', 'business-directory-plugin' );
        }
        return sprintf( _nx( '%d day', '%d days', $fee->days, 'fees admin', 'business-directory-plugin' ), $fee->days );
    }

	/**
	 * Add listing count column.
	 *
	 * @param WPBDP__Fee_Plan $fee The current plan.
	 *
	 * @since 5.15.3
	 *
	 * @return string|int
	 */
	public function column_listings( $fee ) {
		$column = $fee->count_listings();

		if ( ! (float) $fee->amount ) {
			return $column;
		}

		$revenue = wpbdp_currency_format( $fee->total_revenue(), array( 'force_numeric' => true ) );
		$title   = __( 'Total revenue earned from listings', 'business-directory-plugin' );
		$column .= ' <br/><span class="wpbdp-tag wpbdp-tooltip" title="' . esc_attr( $title ) . '">' . esc_html( $revenue ) . '</span>';
		return $column;
	}

    public function column_images( $fee ) {
		return $fee->images;
    }

    public function column_categories( $fee ) {
        if ( $fee->categories['all'] ) {
            return _x( 'All categories', 'fees admin', 'business-directory-plugin' );
        }

        $names = array();

        foreach ( $fee->categories['categories'] as $category_id ) {
            $category = get_term( $category_id, wpbdp()->get_post_type_category() );
            if ( $category ) {
                $names[] = $category->name;
            }
        }

		return $names ? join( ', ', $names ) : '--';
    }

    public function column_attributes( $fee ) {
		$tags = array();

		if ( ! $fee->enabled ) {
			$tags[] = esc_html__( 'Disabled', 'business-directory-plugin' );
		} else {
			$tags[] = esc_html__( 'Active', 'business-directory-plugin' );
		}

		if ( 'free' === $fee->tag ) {
			$tags[] = esc_html__( 'Default', 'business-directory-plugin' ) . '</span>';
		}

		if ( $fee->sticky ) {
			$tags[] = _x( 'Sticky', 'fees admin', 'business-directory-plugin' );
		}

		if ( $fee->recurring ) {
			$tags[] = _x( 'Recurring', 'fees admin', 'business-directory-plugin' );
		}

		if ( ! empty( $fee->extra_data['private'] ) ) {
			$tags[] = _x( 'Private', 'fees admin', 'business-directory-plugin' );
		}

		$html = '<span class="wpbdp-tag">' . implode( '</span><span class="wpbdp-tag">', $tags ) . '</span>';
        return $html;
    }

}
