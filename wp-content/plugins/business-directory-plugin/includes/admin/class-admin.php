<?php
/**
 * Class Admin
 *
 * @package BDP/Includes/Admin/Class Admin
 */

require_once WPBDP_PATH . 'includes/admin/admin-pages.php';
require_once WPBDP_PATH . 'includes/admin/controllers/class-admin-listings.php';
require_once WPBDP_PATH . 'includes/admin/controllers/class-form-fields-admin.php';
require_once WPBDP_PATH . 'includes/admin/helpers/tables/class-form-fields-table.php';
require_once WPBDP_PATH . 'includes/admin/csv-import.php';
require_once WPBDP_PATH . 'includes/admin/csv-export.php';
require_once WPBDP_PATH . 'includes/admin/class-listing-owner.php';
require_once WPBDP_PATH . 'includes/admin/class-listing-fields-metabox.php';
require_once WPBDP_PATH . 'includes/admin/page-debug.php';
require_once WPBDP_PATH . 'includes/admin/controllers/class-admin-controller.php';
require_once WPBDP_PATH . 'includes/admin/tracking.php';
require_once WPBDP_PATH . 'includes/admin/class-listings-with-no-fee-plan-view.php';
require_once WPBDP_PATH . 'includes/admin/helpers/class-modules-list.php';
require_once WPBDP_PATH . 'includes/models/class-reviews.php';

if ( ! class_exists( 'WPBDP_Admin' ) ) {

    /**
     * Class WPBDP_Admin
     */
    class WPBDP_Admin {

        private $menu                      = array();
		private $menu_id                   = 'wpbdp_admin';
        private $current_controller        = null;
        private $current_controller_output = '';

        private $dropdown_users_args_stack = array();

        public $messages = array();


        public function __construct() {
            add_action( 'admin_init', array( $this, 'handle_actions' ) );

            add_action( 'admin_init', array( $this, 'check_for_required_pages' ) );

            add_action( 'admin_init', array( &$this, 'process_admin_action' ), 999 );
            add_action( 'admin_init', array( $this, 'register_listings_views' ) );

            add_action( 'admin_notices', array( $this, 'prepare_admin_notices' ) );
            add_action( 'wp_ajax_wpbdp_dismiss_review', array( &$this, 'maybe_dismiss_review' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'init_scripts' ) );

            // Adds admin menus.
            add_action( 'admin_menu', array( &$this, 'admin_menu' ) );
            add_action( 'admin_head', array( &$this, 'hide_menu' ) );

            // Enables reordering of admin menus.
            add_filter( 'custom_menu_order', '__return_true' );

            add_filter( 'manage_edit-' . WPBDP_CATEGORY_TAX . '_columns', array( &$this, 'add_custom_taxonomy_columns' ) );
            add_filter( 'manage_edit-' . WPBDP_TAGS_TAX . '_columns', array( &$this, 'tag_taxonomy_columns' ) );
            add_action( 'manage_' . WPBDP_CATEGORY_TAX . '_custom_column', array( &$this, 'custom_taxonomy_columns' ), 10, 3 );

            add_filter( 'wp_terms_checklist_args', array( $this, '_checklist_args' ) ); // fix issue #152

            add_action( 'wp_ajax_wpbdp-formfields-reorder', array( &$this, 'ajax_formfields_reorder' ) );

            add_action( 'wp_ajax_wpbdp-admin-fees-set-order', array( &$this, 'ajax_fees_set_order' ) );
            add_action( 'wp_ajax_wpbdp-admin-fees-reorder', array( &$this, 'ajax_fees_reorder' ) );

            add_action( 'wp_ajax_wpbdp-renderfieldsettings', array( 'WPBDP_FormFieldsAdmin', '_render_field_settings' ) );

            add_action( 'wp_ajax_wpbdp-create-main-page', array( &$this, 'ajax_create_main_page' ) );
            add_action( 'wp_ajax_wpbdp-drip_subscribe', array( &$this, 'ajax_drip_subscribe' ) );
            add_action( 'wp_ajax_wpbdp-set_site_tracking', 'WPBDP_SiteTracking::handle_ajax_response' );
            add_action( 'wp_ajax_wpbdp_dismiss_notification', array( &$this, 'ajax_dismiss_notification' ) );

            add_action( 'wpbdp_admin_ajax_dismiss_notification_server_requirements', array( $this, 'ajax_dismiss_notification_server_requirements' ) );

            add_action( 'current_screen', array( $this, 'admin_view_dispatch' ), 9999 );
            add_action( 'wp_ajax_wpbdp_admin_ajax', array( $this, 'admin_ajax_dispatch' ), 9999 );

			add_filter( 'admin_head-post.php', array( $this, 'maybe_highlight_menu' ) );
			add_filter( 'admin_head-post-new.php', array( $this, 'maybe_highlight_menu' ) );
			add_filter( 'admin_head-post.php', array( $this, 'maybe_highlight_menu' ) );
			add_filter( 'admin_head-edit.php', array( $this, 'maybe_highlight_menu' ) );
			add_filter( 'admin_head-edit-tags.php', array( $this, 'maybe_highlight_menu' ) );
			add_filter( 'admin_head-term.php', array( $this, 'maybe_highlight_menu' ) );

			// Clear listing page cache.
			add_filter( 'pre_delete_post', array( $this, 'before_delete_post' ), 10, 2 );

			require_once WPBDP_PATH . 'includes/controllers/class-addons.php';
			WPBDP_Addons_Controller::load_hooks();

			require_once WPBDP_INC . 'controllers/class-smtp.php';
			WPBDP_SMTP_Controller::load_hooks();

			require_once WPBDP_PATH . 'includes/admin/helpers/class-notices.php';
			WPBDP_Admin_Notices::load_hooks();

            $this->listings   = new WPBDP_Admin_Listings();
            $this->csv_import = new WPBDP_CSVImportAdmin();
            $this->csv_export = new WPBDP_Admin_CSVExport();
            $this->debug_page = new WPBDP_Admin_Debug_Page();

            // Post-install migrations.
            if ( get_option( 'wpbdp-migrate-18_0-featured-pending', false ) ) {
                require_once WPBDP_PATH . 'includes/admin/upgrades/migrations/manual-upgrade-18_0-featured-levels.php';
                $this->post_install_migration = new WPBDP__Manual_Upgrade__18_0__Featured_Levels();
            }

            require_once WPBDP_INC . 'admin/controllers/class-settings-admin.php';
            $this->settings_admin = new WPBDP__Settings_Admin();

			add_action( 'wpbdp_settings_subtab_uninstall', array( $this, 'uninstall_plugin' ) );

            if ( wpbdp_get_option( 'tracking-on' ) ) {
                $this->site_tracking = new WPBDP_SiteTracking();
            }
        }

		public function init_scripts( $force = false ) {
			global $wpbdp;

			if ( ! $force && ! $wpbdp->is_bd_page() ) {
				return;
			}

			$this->add_pointers();
		}

		/**
		 * Load the pointer box if it hasn't yet been dismissed.
		 */
		private function add_pointers() {
			if ( ! current_user_can( 'administrator' ) ) {
				return;
			}

			$callback = $this->pointer_callback();

			if ( $callback ) {
				wp_enqueue_style( 'wp-pointer' );
				wp_enqueue_script( 'wp-pointer' );
				add_action( 'admin_print_footer_scripts', $callback );
			}
		}

		/**
		 * Which pointer message should show?
		 *
		 * @return mixed
		 */
		private function pointer_callback() {
			$callback = false;
			if ( $this->should_show_pointer( 'drip' ) ) {
				$callback = array( $this, 'drip_pointer' );
			} elseif ( ! wpbdp_get_option( 'tracking-on', false ) && $this->should_show_pointer( 'tracking' ) ) {
				// Ask for site tracking if needed.
				$callback = 'WPBDP_SiteTracking::request_js';
			}

			return $callback;
		}

		/**
		 * Limit the number of times the email/tracking requests will show.
		 *
		 * @return bool
		 */
		private function should_show_pointer( $name = 'drip' ) {
			$tries  = 3;
			$option = 'wpbdp-show-' . $name . '-pointer';
			$request_it = (int) get_option( $option, 0 );
			if ( $request_it && $request_it < $tries ) {
				update_option( $option, $request_it + 1 );
				return true;
			}
			return false;
		}

        /**
         * @since 3.4.1
         */
        public function drip_pointer() {
            $current_user = wp_get_current_user();

            $js = '$.post( ajaxurl, { action: "wpbdp-drip_subscribe",
                                    email: $( "#wpbdp-drip-pointer-email" ).val(),
                                    nonce: "' . wp_create_nonce( 'drip pointer subscribe' ) . '",
                                    subscribe: "%d" } );';

            $content  = '';
            $content .= __( 'Find out how to create a compelling, thriving business directory from scratch in this ridiculously actionable (and FREE) 5-part email course.', 'business-directory-plugin' ) . '<br /><br />';
            $content .= '<label>';
            $content .= '<b>' . _x( 'Email Address:', 'drip pointer', 'business-directory-plugin' ) . '</b>';
            $content .= '<br />';
            $content .= '<input type="text" id="wpbdp-drip-pointer-email" value="' . esc_attr( $current_user->user_email ) . '" />';
            $content .= '</label>';

            wpbdp_admin_pointer(
                '#wpadminbar',
                __( 'Want to know the Secrets of Building an Awesome Business Directory?', 'business-directory-plugin' ),
                $content,
                __( 'Yes, please!', 'business-directory-plugin' ),
                sprintf( $js, 1 ),
                __( 'No, thanks', 'business-directory-plugin' ),
                sprintf( $js, 0 )
            );
        }

        /**
         * @since 3.5.3
         */
        public function ajax_create_main_page() {
            $nonce = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'request' );
			$res = new WPBDP_AJAX_Response();
            if ( ! current_user_can( 'administrator' ) || ! $nonce || ! wp_verify_nonce( $nonce, 'create main page' ) ) {
				$res->send_error();
            }

            if ( wpbdp_get_page_id( 'main' ) ) {
				$res->send_error();
            }

            $page    = array(
                'post_status'  => 'publish',
                'post_title'   => _x( 'Business Directory', 'admin', 'business-directory-plugin' ),
                'post_type'    => 'page',
                'post_content' => '[businessdirectory]',
            );
            $page_id = wp_insert_post( $page );

            if ( ! $page_id ) {
				$res->send_error();
            }

            $res->set_message(
				sprintf(
					__( 'You\'re all set. Visit your new %1$sBusiness Directory%2$s page.', 'business-directory-plugin' ),
					'<a href="' . get_permalink( $page_id ) . '" target="_blank" rel="noopener">',
					'</a>'
				)
            );
            $res->send();
        }

        /**
         * @since 3.4.1
         */
        public function ajax_drip_subscribe() {
            $res   = new WPBDP_AJAX_Response();
            $nonce = wpbdp_get_var( array( 'param' => 'nonce' ), 'post' );

            if ( ! get_option( 'wpbdp-show-drip-pointer' ) || ! wp_verify_nonce( $nonce, 'drip pointer subscribe' ) ) {
                $res->send_error();
            }

            $subscribe = ( '1' === wpbdp_get_var( array( 'param' => 'subscribe' ), 'post' ) );

			if ( ! $subscribe ) {
				delete_option( 'wpbdp-show-drip-pointer' );
				$res->send();
			}

			$email = wpbdp_get_var( array( 'param' => 'email' ), 'post' );
			if ( ! filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				return $res->send_error( __( 'Invalid email address.', 'business-directory-plugin' ) );
			}

			delete_option( 'wpbdp-show-drip-pointer' );

			$current_user = wp_get_current_user();

			$response = wp_remote_post(
				'https://strategy1137274.activehosted.com/proc.php?jsonp=true',
				array(
					'body' => array(
						'firstname' => $current_user->first_name,
						'email'     => $email,
						'u'         => '15',
						'f'         => '15',
						'act'       => 'sub',
						'c'         => 0,
						'm'         => 0,
						'v'         => '2',
					),
				)
			);

			$res->send();
		}

        function admin_menu() {
            add_action( 'admin_menu', array( &$this, 'maybe_add_themes_update_count' ), 20 );

            if ( ! current_user_can( 'manage_categories' ) ) {
                return;
            }

            $menu_id = $this->menu_id;

            add_menu_page(
                __( 'Business Directory Admin', 'business-directory-plugin' ),
                __( 'Directory', 'business-directory-plugin' ),
                'manage_categories',
                $menu_id,
                current_user_can( 'administrator' ) ? array( &$this, 'main_menu' ) : '',
                WPBDP__CPT_Integration::menu_icon(),
                20
            );

            $menu = array();

            $menu['wpbdp-admin-fees']       = array(
                'title' => __( 'Plans', 'business-directory-plugin' ),
            );
            $menu['wpbdp_admin_formfields'] = array(
                'title'    => __( 'Form Fields', 'business-directory-plugin' ),
                'callback' => array( 'WPBDP_FormFieldsAdmin', 'admin_menu_cb' ),
            );
            $menu['wpbdp_admin_payments']   = array(
                'title' => _x( 'Payment History', 'admin menu', 'business-directory-plugin' ),
            );
            $menu['wpbdp_admin_csv']        = array(
                'title' => _x( 'Import & Export', 'admin menu', 'business-directory-plugin' ),
            );
            $menu['wpbdp-debug-info']       = array(
                'title'    => _x( 'Debug', 'admin menu', 'business-directory-plugin' ),
                'callback' => array( &$this->debug_page, 'dispatch' ),
            );

            $this->menu = apply_filters( 'wpbdp_admin_menu_items', $menu );
            $this->prepare_menu( $this->menu );

            // Register menu items.
            foreach ( $this->menu as $item_slug => &$item_data ) {
                $item_data['hook'] = add_submenu_page(
                    $menu_id,
                    $item_data['title'],
                    $item_data['label'],
                    ( empty( $item_data['capability'] ) ? 'administrator' : $item_data['capability'] ),
                    $item_slug,
                    array( $this, 'menu_dispatch' )
                );
            }

			$label = '<span style="color:#1da867">' . __( 'Modules', 'business-directory-plugin' ) . '</span>';
			add_submenu_page( $menu_id, __( 'Business Directory', 'business-directory-plugin' ) . ' | ' . __( 'Modules', 'business-directory-plugin' ), $label, 'install_plugins', 'wpbdp-addons', 'WPBDP_Show_Modules::list_addons' );

            do_action( 'wpbdp_admin_menu', $menu_id );

			$this->admin_menu_combine();

			if ( empty( $GLOBALS['submenu'] ) || empty( $GLOBALS['submenu'][ $menu_id ] ) ) {
				return;
			}

            // Handle some special menu items.
			$prepend = array();

			foreach ( $GLOBALS['submenu'][ $menu_id ] as &$menu_item ) {
                if ( ! isset( $this->menu[ $menu_item[2] ] ) ) {
					$new = array(
						$menu_item[2] => array(
							'title' => $menu_item[0],
						),
					);
					// Add in front of the existing nav items.
					if ( strpos( $menu_item[2], 'post_type' ) ) {
						$prepend = $prepend + $new;
					} else {
						$this->menu = $this->menu + $new;
					}

                    continue;
                }

                $menu_item_data = $this->menu[ $menu_item[2] ];

                if ( ! empty( $menu_item_data['url'] ) ) {
                    $menu_item[2] = $menu_item_data['url'];
                }
            }

			if ( ! empty( $prepend ) ) {
				$this->menu = $prepend + $this->menu;
			}
        }

		/**
		 * Get the menu to piece together tabs.
		 *
		 * @since 6.0
		 */
		public function get_menu() {
			return $this->menu;
		}

        /**
         * Removed the dashboard wpbdp_admin submenu.
         *
         * This means the submenu is still available to us, but hidden.
         *
         * @since 5.7.3
         */
        public function hide_menu() {
			global $submenu;

			$menu_id = $this->menu_id;
			if ( empty( $submenu[ $menu_id ] ) ) {
				return;
			}

			$top_level   = $this->top_level_nav();
			$top_level[] = 'edit.php?post_type=' . WPBDP_POST_TYPE;

			foreach ( $submenu[ $menu_id ] as $menu ) {
				$key = $menu[2];
				if ( ! in_array( $key, $top_level, true ) ) {
					// Remove all the menu items that are included in the combined page.
					remove_submenu_page( $menu_id, $key );
				}
			}

			remove_submenu_page( $menu_id, 'wpbdp-debug-info' ); // This page isn't used anymore.

            if ( current_user_can( 'administrator' ) ) {
                remove_menu_page( 'edit.php?post_type=' . WPBDP_POST_TYPE );
				remove_submenu_page( $menu_id, 'post-new.php?post_type=wpbdp_listing' );
            } else {
                $this->maybe_restore_regions_submenu();
                remove_menu_page( $menu_id );
            }

            remove_submenu_page( $menu_id, $menu_id );
			$this->add_upgrade_menu();
        }

		/**
		 * These are the pages that will be hidden from the combined tabs page.
		 *
		 * @since 6.0
		 */
		public function top_level_nav() {
			$top = array(
				'wpbdp_settings',
				'wpbdp-smtp',
				$this->menu_id,
				'wpbdp_admin_payments',
				'post-new.php?post_type=' . WPBDP_POST_TYPE,
				'wpbdp-addons',
				'wpbdp-themes',
				'wpbdp-debug-info', // Exclude from the tabs.
			);

			/**
			 * @since 6.0.1
			 */
			return apply_filters( 'wpbdp_top_level_nav', $top );
		}

		/**
		 * We use the global submenu, because we are adding an external link here.
		 *
		 * @since 6.0.1
		 */
		private function add_upgrade_menu() {
			if ( WPBDP_Admin_Education::is_installed( 'premium' ) || ! current_user_can( 'administrator' ) ) {
				return;
			}

			global $submenu;
			$submenu[ $this->menu_id ][] = array(
				'<span class="wpbdp-upgrade-submenu">' . esc_html__( 'Upgrade to Premium', 'business-directory-plugin' ) . '</span>',
				'administrator',
				wpbdp_admin_upgrade_link( 'admin-menu' )
			);
			add_action( 'admin_footer', array( &$this, 'highlight_menu' ) );
		}

		/**
		 * Add class to parent container so we can style it.
		 *
		 * @since 6.0.1
		 */
		public function highlight_menu() {
			?>
<style>
.wpbdp-submenu-highlight{background: #1da867;}
.wpbdp-submenu-highlight a span{color: #fff;font-weight: 600;font-size:12px;}
</style>
<script>
	submenuItem = document.querySelector( '.wpbdp-upgrade-submenu' );
	if ( null !== submenuItem ) {
		li = submenuItem.parentNode.parentNode;
		if ( li ) {
			li.classList.add( 'wpbdp-submenu-highlight' );
		}
	}
</script>
			<?php
		}

        /**
         * Combine submenus from post type and wpbdp_admin
         * together and asign it to wpbdp_admin
         *
         * @since 5.7.3
         */
        public function admin_menu_combine() {
            global $submenu;

            $cpt_menu   = 'edit.php?post_type=' . WPBDP_POST_TYPE;
            $admin_menu = $this->menu_id;

			if ( isset( $submenu[ $cpt_menu ] ) && isset( $submenu[ $admin_menu ] ) ) {
				$this->change_menu_name( $submenu[ $cpt_menu ] );
				$submenu[ $admin_menu ] = array_merge( $submenu[ $cpt_menu ], $submenu[ $admin_menu ] );
            }
        }

		/**
		 * Since the top link points to the listings page, the menu name needs to change.
		 * If we add a dashboard, this can be removed.
		 *
		 * @since 6.0
		 */
		private function change_menu_name( &$submenu ) {
			foreach ( $submenu as $k => $menu ) {
				if ( $menu[0] === __( 'Directory Listings', 'business-directory-plugin' ) ) {
					$submenu[ $k ][0] = __( 'Directory Content', 'business-directory-plugin' );
				}
			}
		}

        /**
         * @since 5.0
         */
        private function prepare_menu( &$menu ) {
            $n = 1;

			foreach ( $menu as &$item ) {
				if ( ! isset( $item['priority'] ) ) {
					$item['priority'] = $n++;
				}

				if ( ! isset( $item['title'] ) ) {
					$item['title'] = _x( 'Untitled Menu', 'admin', 'business-directory-plugin' );
				}

				if ( ! isset( $item['label'] ) ) {
					$item['label'] = $item['title'];
				}

				if ( ! isset( $item['file'] ) ) {
					$item['file'] = '';
				}

				if ( ! isset( $item['callback'] ) ) {
					$item['callback'] = '';
				}

				if ( ! isset( $item['url'] ) ) {
					$item['url'] = '';
				}
			}

			WPBDP_Utils::sort_by_property( $menu, 'priority' );
        }

        /**
         * @since 5.0
         */
        function admin_view_dispatch() {
            global $plugin_page;

            if ( ! isset( $plugin_page ) || ! isset( $this->menu[ $plugin_page ] ) ) {
                return;
            }

            $item     = $this->menu[ $plugin_page ];
            $slug     = $plugin_page;
			$callback = isset( $item['callback'] ) ? $item['callback'] : '';

            // Simple callback view are not processed here.
            if ( $callback && is_callable( $callback ) ) {
                return;
            }

            $id = str_replace( array( 'wpbdp-admin-', 'wpbdp_admin_' ), '', $slug );

			$candidates = array(
				isset( $item['file'] ) ? $item['file'] : '',
				WPBDP_INC . 'admin/controllers/class-admin-' . $id . '.php',
				WPBDP_INC . 'admin/class-admin-' . $id . '.php',
				WPBDP_INC . 'admin/' . $id . '.php',
			);
			$candidates = array_filter( $candidates );

			foreach ( $candidates as $c ) {
				if ( $c && file_exists( $c ) ) {
					require_once $c;
					break; // Prevent loading deprecated files and looping for the same file once its found.
				}
			}

            // Maybe loading one of the candidate files made the callback available.
			/** @phpstan-ignore-next-line */
            if ( $callback && is_callable( $callback ) ) {
                ob_start();
                call_user_func( $callback );
                $this->current_controller_output = ob_get_contents();
                ob_end_clean();
                return;
            }

            $classname = 'WPBDP__Admin__' . ucfirst( $id );

            if ( ! class_exists( $classname ) ) {
                return;
            }

			$this->current_controller = new $classname();

            ob_start();
            $this->current_controller->_dispatch();
            $this->current_controller_output = ob_get_contents();
            ob_end_clean();

            add_action( 'admin_enqueue_scripts', array( $this->current_controller, '_enqueue_scripts' ) );
        }

        /**
         * @since 5.0
         */
        function admin_ajax_dispatch() {
            if ( empty( $_REQUEST['handler'] ) ) {
                return;
            }

            $handler = trim( wpbdp_get_var( array( 'param' => 'handler' ), 'request' ) );
			$handler = WPBDP_Utils::normalize( $handler );

            $parts         = explode( '__', $handler );
            $controller_id = $parts[0];
            $function      = isset( $parts[1] ) ? $parts[1] : '';

            $candidates = array(
				WPBDP_INC . 'admin/controllers/class-admin-' . $controller_id . '.php',
				WPBDP_INC . 'admin/class-admin-' . $controller_id . '.php',
				WPBDP_INC . 'admin/' . $controller_id . '.php',
            );
            foreach ( $candidates as $c ) {
                if ( ! file_exists( $c ) ) {
                    continue;
                }

                require_once $c;
                $classname = 'WPBDP__Admin__' . ucfirst( $controller_id );

                if ( ! class_exists( $classname ) ) {
                    continue;
                }

                $controller = new $classname();
                return $controller->_ajax_dispatch();
            }

            exit;
        }

        /**
         * @since 5.0
         */
        function menu_dispatch() {
            $output = $this->current_controller_output;

            if ( $output ) {
                return print( $output );
            }

            global $plugin_page;
            if ( ! isset( $plugin_page ) || ! isset( $this->menu[ $plugin_page ] ) ) {
                return;
            }

            $item     = $this->menu[ $plugin_page ];
            $slug     = $plugin_page;
            $callback = $item['callback'];

            if ( $callback ) {
                call_user_func( $callback );
            }
        }

        /**
         * Add BD themes available updates count to Menu title.
         *
         * @since 5.8
         */
        public function maybe_add_themes_update_count() {
            $badge_number = absint( apply_filters( 'wpbdp_admin_menu_badge_number', 0 ) );

            if ( ! $badge_number ) {
                return;
            }

            global $menu;

            $menu_item = wp_list_filter(
                $menu,
                array( 2 => $this->menu_id ) // 2 is the position of an array item which contains URL, it will always be 2!
            );

            if ( ! empty( $menu_item ) ) {
                $menu_item_position              = key( $menu_item ); // get the array key (position) of the element
                $menu[ $menu_item_position ][0] .= ' <span class="update-plugins"><span class="plugin-count">' . $badge_number . '</span></span>';
            }
        }

        public function _checklist_args( $args ) {
            $args['checked_ontop'] = false;
            return $args;
        }

        public function ajax_formfields_reorder() {
            $response = new WPBDP_AJAX_Response();

            if ( ! current_user_can( 'administrator' ) ) {
                $response->send_error();
            }

            $order = array_map( 'intval', isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : array() );

            if ( ! $order ) {
                $response->send_error();
            }

            global $wpbdp;

            if ( ! $wpbdp->formfields->set_fields_order( $order ) ) {
                $response->send_error();
            }

            $response->send();
        }

        public function ajax_fees_set_order() {
            $nonce = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'post' );
            $order = wpbdp_get_var( array( 'param' => 'fee_order' ), 'post' );

            if ( ! wp_verify_nonce( $nonce, 'change fees order' ) || ! $order ) {
                exit();
            }

            $res = new WPBDP_AJAX_Response();
            wpbdp_set_option( 'fee-order', $order );
            $res->send();
        }

        public function ajax_fees_reorder() {
            global $wpdb;

            $response = new WPBDP_AJAX_Response();

            if ( ! current_user_can( 'administrator' ) ) {
                $response->send_error();
            }

            $order = array_map( 'intval', isset( $_REQUEST['order'] ) ? $_REQUEST['order'] : array() );

            if ( ! $order ) {
                $response->send_error();
            }

            $wpdb->query( "UPDATE {$wpdb->prefix}wpbdp_plans SET weight = 0" );

            $weight = count( $order ) - 1;
            foreach ( $order as $fee_id ) {
                $wpdb->update( $wpdb->prefix . 'wpbdp_plans', array( 'weight' => $weight ), array( 'id' => $fee_id ) );
                $weight--;
            }

            $response->send();
        }

        /*
        * AJAX listing actions.
        */
        function ajax_dismiss_notification() {
            $id      = wpbdp_get_var( array( 'param' => 'id' ), 'post' );
            $nonce   = wpbdp_get_var( array( 'param' => 'nonce' ), 'post' );
            $user_id = get_current_user_id();

            $res = new WPBDP_AJAX_Response();

            if ( ! $id || ! $nonce || ! $user_id || ! wp_verify_nonce( $nonce, 'dismiss notice ' . $id ) ) {
                $res->send_error();
            }

            if ( has_action( 'wpbdp_admin_ajax_dismiss_notification_' . $id ) ) {
                do_action( 'wpbdp_admin_ajax_dismiss_notification_' . $id, $user_id );
                return;
            }

			$dismissed = get_user_meta( $user_id, 'wpbdp_notice_dismissed', true );
			if ( ! $dismissed || ! is_array( $dismissed ) ) {
				$dismissed = array();
			}
			$dismissed[] = $id;
			update_user_meta( $user_id, 'wpbdp_notice_dismissed', $dismissed );

            $res->send();
        }

		/**
		 * Get all dismissals from the same cell for better db performance.
		 *
		 * @since 6.0
		 */
		private function is_notice_dismissed( $id, $user_id = 0 ) {
			$user_id = $user_id ? $user_id : get_current_user_id();
			$dismissed = get_user_meta( $user_id, 'wpbdp_notice_dismissed', true );
			return in_array( $id, (array) $dismissed );
		}

		/**
		 * Prepare admin notices that should only be checked once.
		 *
		 * @since 6.0
		 */
		public function prepare_admin_notices() {
			if ( ! current_user_can( 'administrator' ) ) {
				return;
			}

			$this->upgrade_bar();

			$this->check_server_requirements();
			$this->check_setup();
			$this->check_deprecation_warnings();

			$this->maybe_request_review();

			do_action( 'wpbdp_admin_notices' );

			$this->admin_notices();
		}

		function admin_notices() {
			if ( ! current_user_can( 'administrator' ) ) {
				return;
			}

			if ( ! isset( $this->displayed_warnings ) ) {
				$this->displayed_warnings = array();
			}

            foreach ( $this->messages as $msg ) {
                $msg_sha1 = sha1( is_array( $msg ) ? $msg[0] : $msg );

                if ( in_array( $msg_sha1, $this->displayed_warnings, true ) ) {
                    continue;
                }

                $this->displayed_warnings[] = $msg_sha1;

				$class = 'updated';
				$extra = array();
                if ( is_array( $msg ) ) {
                    $class = isset( $msg[1] ) ? $msg[1] : $class;
                    $text  = isset( $msg[0] ) ? $msg[0] : '';
                    $extra = isset( $msg[2] ) && is_array( $msg[2] ) ? $msg[2] : $extra;
                } else {
                    $text  = $msg;
                }

				// Check if dismissed.
				if ( ! empty( $extra['dismissible-id'] ) && $this->is_notice_dismissed( $extra['dismissible-id'] ) ) {
					continue;
				}

				$this->maybe_update_notice_classes( $class );

				echo '<div class="wpbdp-notice notice ' . esc_attr( $class ) . '">';
                echo '<p>' . $text . '</p>';

                if ( ! empty( $extra['dismissible-id'] ) ) {
                    printf(
                        '<button type="button" class="notice-dismiss" data-dismissible-id="%s" data-nonce="%s"><span class="screen-reader-text">%s</span></button>',
                        $extra['dismissible-id'],
                        wp_create_nonce( 'dismiss notice ' . $extra['dismissible-id'] ),
                        _x( 'Dismiss this notice.', 'admin', 'business-directory-plugin' )
                    );
                }

                echo '</div>';
            }

            $this->messages = array();
        }

		/**
		 * This is for reverse compatibility. Switches old classes to new ones like:
		 * notice, notice-{type} and is-dismissible
		 *
		 * @since 5.15.3
		 */
		private function maybe_update_notice_classes( &$class ) {
			$classes = explode( ' ', $class );
			$find    = array(
				'error',
				'dismissible',
			);
			$replace = array(
				'notice-error',
				'is-dismissible',
			);

			if ( empty( array_intersect( $classes, $find ) ) ) {
				return;
			}

			//_deprecated_function( __METHOD__, '5.15.3', 'The classes in an admin notice are outdated: ' . $class );
			$classes = str_replace( $find, $replace, $classes );
			$class   = implode( ' ', $classes );
		}

		/**
		 * @since 5.9.1
		 */
		private function upgrade_bar() {
			global $wpbdp;
			if ( ! $wpbdp->is_bd_page() ) {
				return;
			}

			$modules = wpbdp()->modules;
			$module_count = $this->get_installed_premium_module_count( $modules );
			if ( $module_count > 0 ) {
				return;
			}
			?>
			<div class="wpbdp-notice wpbdp-upgrade-bar wpbdp-inline-notice">
				You're using Business Directory Plugin Lite. To unlock more features consider
				<a href="<?php echo esc_url( wpbdp_admin_upgrade_link( 'upgrade-bar' ) ); ?>">
					upgrading to premium.
				</a>
			</div>
			<?php
		}

		/**
		 * Get the installed premium modules count.
		 *
		 * @since 5.16.1
		 *
		 * @return int
		 */
		private function get_installed_premium_module_count( $modules ) {
			$module_list = $modules->get_modules();
			if ( isset( $module_list['categories'] ) ) {
				unset( $module_list['categories'] );
			}
			return count( array_keys( $module_list ) );
		}

        function handle_actions() {
            if ( ! isset( $_REQUEST['wpbdmaction'] ) || ! isset( $_REQUEST['post'] ) ) {
                return;
            }

			$action = wpbdp_get_var( array( 'param' => 'wpbdmaction' ), 'request' );
			$posts  = wpbdp_get_var( array( 'param' => 'post' ), 'request' );
			$posts  = is_array( $posts ) ? $posts : array( $posts );

            $listings_api = wpbdp_listings_api();

            if ( ! current_user_can( 'administrator' ) ) {
                exit;
            }

            switch ( $action ) {
                case 'change-to-publish':
                case 'change-to-pending':
                case 'change-to-draft':
                    $new_status = str_replace( 'change-to-', '', $action );

                    foreach ( $posts as $post_id ) {
                        wp_update_post(
                            array(
                                'ID'          => $post_id,
                                'post_status' => $new_status,
                            )
                        );
                    }

                    $this->messages[] = _nx( 'The listing has been updated.', 'The listings have been updated.', count( $posts ), 'admin', 'business-directory-plugin' );
                    break;

                case 'change-to-expired':
                    foreach ( $posts as $post_id ) {
                        $listing = wpbdp_get_listing( $post_id );
                        $listing->update_plan( array( 'expiration_date' => current_time( 'mysql' ) ) );
                        $listing->set_status( 'expired' );
                    }

                    $this->messages[] = _nx( 'The listing has been updated.', 'The listings have been updated.', count( $posts ), 'admin', 'business-directory-plugin' );
                    break;

                case 'change-to-complete':
                case 'approve-payments':
                    foreach ( $posts as $post_id ) {
                        $pending_payments = WPBDP_Payment::objects()->filter(
                            array(
                                'listing_id' => $post_id,
                                'status'     => 'pending',
                            )
                        );

                        foreach ( $pending_payments as $p ) {
                            $p->status = 'completed';
                            $p->save();
                        }
                    }

                    break;

                case 'assignfee':
                    $listing = WPBDP_Listing::get( $posts[0] );
                    $fee_id  = (int) $_GET['fee_id'];
                    $listing->set_fee_plan( $fee_id );

                    $this->messages[] = _x( 'The plan was successfully assigned.', 'admin', 'business-directory-plugin' );

                    break;

                case 'renewlisting':
                    foreach ( $posts as $post_id ) :
                        $listing = WPBDP_Listing::get( $post_id );
                        $listing->renew();
                    endforeach;

                    $this->messages[] = _nx( 'Listing was renewed.', 'Listings were renewed.', count( $posts ), 'admin', 'business-directory-plugin' );
                    break;

                case 'send-renewal-email':
                    $listing_id = intval( $_GET['listing_id'] );
                    $listing    = WPBDP_Listing::get( $listing_id );

                    if ( ! $listing ) {
                        break;
                    }

                    if ( wpbdp()->listing_email_notification->send_notices( 'expiration', '0 days', $listing_id, true ) ) {
                        $this->messages[] = _x( 'Renewal email sent.', 'admin', 'business-directory-plugin' );
                        break;
                    }

                    $this->messages[] = array( __( 'Could not send renewal email.', 'business-directory-plugin' ), 'error' );

                    break;

                case 'delete-flagging':
					$meta_pos   = wpbdp_get_var( array( 'param' => 'meta_pos' ) );
					$listing_id = wpbdp_get_var( array( 'param' => 'listing_id' ) );
					WPBDP__Listing_Flagging::remove_flagging( $listing_id, $meta_pos );

					$this->messages[] = _nx( 'Listing report deleted.', 'Listing reports deleted.', $meta_pos == 'all' ? 2 : 1, 'admin', 'business-directory-plugin' );
                    break;

                case 'send-access-keys':
                    $this->send_access_keys( $posts );
                    break;

                default:
                    do_action( 'wpbdp_admin_directory_handle_action', $action );
                    break;
            }

            $_SERVER['REQUEST_URI'] = remove_query_arg( array( 'wpbdmaction', 'wpbdmfilter', 'transaction_id', 'category_id', 'fee_id', 'u', 'renewal_id', 'flagging_user' ), wpbdp_get_server_value( 'REQUEST_URI' ) );
        }

        private function send_access_keys( $posts ) {
            $listings_by_email_address = array();

            foreach ( $posts as $post_id ) {
                $listing = wpbdp_get_listing( $post_id );

                if ( ! $listing ) {
                    continue;
                }

                $email_address = wpbusdirman_get_the_business_email( $post_id );

                if ( ! $email_address ) {
                    continue;
                }

                $listings_by_email_address[ $email_address ][] = $listing;
            }

            $sender       = $this->get_access_keys_sender();
            $message_sent = false;

            foreach ( $listings_by_email_address as $email_address => $listings ) {
                try {
                    $message_sent = $message_sent || $sender->send_access_keys_for_listings( $listings, $email_address );
                } catch ( Exception $e ) {
                    // pass
					continue;
                }
            }

            // TODO: Add more descriptive messages to indicate how many listings were
            // processed successfully, how many failed and why.
            if ( $message_sent ) {
                $this->messages[] = _x( 'Access keys sent.', 'admin', 'business-directory-plugin' );
            } else {
                $this->messages[] = _x( "The access keys couldn't be sent.", 'admin', 'business-directory-plugin' );
            }

            // TODO: Redirect and show messages on page load.
            // if ( wp_redirect( remove_query_arg( array( 'action', 'post', 'wpbdmaction' ) ) ) ) {
            //     exit();
            // }
        }

        public function get_access_keys_sender() {
            return new WPBDP__Access_Keys_Sender();
        }

        /**
         * @deprecated since 5.6.3
         * @see WPBDP__Admin__Listing_Owner::_dropdown_users_args
         */
        public function _dropdown_users_args( $query_args, $r ) {
			_deprecated_function( __METHOD__, '5.6.3', 'WPBDP__Admin__Listing_Owner::_dropdown_users' );

            global $post;

            if ( isset( $r['wpbdp_skip_dropdown_users_args'] ) ) {
                return $query_args;
            }

            if ( is_admin() && get_post_type( $post ) == WPBDP_POST_TYPE ) {
                add_filter( 'wp_dropdown_users', array( $this, '_dropdown_users' ) );
                array_push( $this->dropdown_users_args_stack, $r );
            }

            return $query_args;
        }

        /**
         * @deprecated since 5.6.3
         * @see WPBDP__Admin__Listing_Owner::_dropdown_users
         */
        public function _dropdown_users( $output ) {
			_deprecated_function( __METHOD__, '5.6.3', 'WPBDP__Admin__Listing_Owner::_dropdown_users' );

            global $post;

            remove_filter( 'wp_dropdown_users', array( $this, '_dropdown_users' ) );

            if ( ! $this->dropdown_users_args_stack ) {
                return $output;
            }

            $args = array_pop( $this->dropdown_users_args_stack );

            if ( $args['show_option_none'] ) {
                $selected = $args['option_none_value'];
            } else {
                $selected = ! empty( $post->ID ) ? $post->post_author : wp_get_current_user()->ID;
            }

            return wp_dropdown_users(
                array_merge(
                    $args,
                    array(
                        'echo'                           => false,
                        'selected'                       => $selected,
                        'include_selected'               => true,
                        'who'                            => 'all',
                        'wpbdp_skip_dropdown_users_args' => true,
                    )
                )
            );
        }

        public function add_custom_taxonomy_columns( $cols ) {
            $newcols = array_merge(
                array_slice( $cols, 0, 1 ),
                array( 'id' => __( 'ID', 'business-directory-plugin' ) ),
                array_slice( $cols, 1, -1 ),
                array( 'posts' => __( 'Listing Count', 'business-directory-plugin' ) )
            );
            return $newcols;
        }

        public function tag_taxonomy_columns( $cols ) {
            $newcols = array_merge(
                array_slice( $cols, 0, -1 ),
                array( 'posts' => __( 'Listing Count', 'business-directory-plugin' ) )
            );
            return $newcols;
        }

        public function custom_taxonomy_columns( $value, $column_name, $id ) {
            if ( $column_name == 'id' ) {
                return $id;
            }

            return $value;
        }

        /* Uninstall. */
        public function uninstall_plugin() {
            global $wpdb;

            $nonce = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'post' );

            if ( $nonce && wp_verify_nonce( $nonce, 'uninstall bd' ) ) {
                $installer = new WPBDP_Installer( 0 );

                // Delete listings.
                $post_ids = $wpdb->get_col( $wpdb->prepare( "SELECT * FROM {$wpdb->posts} WHERE post_type = %s", WPBDP_POST_TYPE ) );

                foreach ( $post_ids as $post_id ) {
                    wp_delete_post( $post_id, true );
                }

                // Drop tables.
                $tables = array_keys( $installer->get_database_schema() );
                foreach ( $tables as &$table ) {
                    $wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}wpbdp_{$table}" );
                }

                // Delete options.
                delete_option( 'wpbdp-db-version' );
                delete_option( 'wpbusdirman_db_version' );
                $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s", 'wpbdp%' ) );

                // Clear scheduled hooks.
                wp_clear_scheduled_hook( 'wpbdp_hourly_events' );
                wp_clear_scheduled_hook( 'wpbdp_daily_events' );

                $tracking = new WPBDP_SiteTracking();
                $tracking->track_uninstall( wpbdp_get_var( array( 'param' => 'uninstall', 'default' => null ), 'post' ) );

                // Deactivate plugin.
                $real_path = WPBDP_PATH . 'business-directory-plugin.php';
                // if the plugin directory is a symlink, plugin_basename will return
                // the real path, which may not be the same path WP associated to
                // the plugin. Plugin paths must be of the form:
                // wp-content/plugins/plugin-directory/plugin-file.php
                $fixed_path = WP_CONTENT_DIR . '/plugins/' . basename( dirname( $real_path ) ) . '/' . basename( $real_path );
                deactivate_plugins( $fixed_path, true );

				$template = 'complete';
            } else {
				$template = 'confirm';
            }

			wpbdp_render_page( WPBDP_PATH . 'templates/admin/uninstall-' . $template . '.tpl.php', array(), true );
        }

        /* Required pages check. */
        public function check_for_required_pages() {
			if ( ! WPBDP_App_Helper::is_bd_page() || wpbdp_get_page_id( 'main' ) || ! current_user_can( 'administrator' ) ) {
				return;
			}

			$wpbdp = wpbdp();
			if ( empty( $wpbdp->assets ) ) {
				$wpbdp->assets = new WPBDP__Assets();
			}
			$wpbdp->assets->register_installation_resources();

			$message  = _x( '<b>Business Directory Plugin</b> requires a page with the <tt>[businessdirectory]</tt> shortcode to function properly.', 'admin', 'business-directory-plugin' );
			$message .= '<br />';
			$message .= _x( 'You can create this page by yourself or let Business Directory do this for you automatically.', 'admin', 'business-directory-plugin' );
			$message .= '<p>';
			$message .= sprintf(
				'<a href="#" class="button wpbdp-create-main-page-button" data-nonce="%s">%s</a>',
				wp_create_nonce( 'create main page' ),
				esc_html( _x( 'Create required pages for me', 'admin', 'business-directory-plugin' ) )
			);
			$message .= '</p>';

			$this->messages[] = array(
				$message,
				'notice-error is-dismissible',
				array( 'dismissible-id' => 'server_requirements' ),
			);
        }

        /**
         * Request review.
         */
        private function maybe_request_review() {
            WPBDP_Reviews::instance()->review_request();
        }

        /**
         * Dismiss review.
         * Action is only valid for an admin.
         */
        public function maybe_dismiss_review() {
            check_ajax_referer( 'wpbdp_dismiss_review', 'nonce' );
            if ( ! is_admin() ) {
                wp_die();
            }

            WPBDP_Reviews::instance()->dismiss_review();
        }

        /**
         * @since 3.6.10
         */
        function process_admin_action() {
            if ( isset( $_REQUEST['wpbdp-action'] ) ) {
				do_action( 'wpbdp_action_' . wpbdp_get_var( array( 'param' => 'wpbdp-action' ), 'request' ) );
                // do_action( 'wpbdp_dispatch_' . $_REQUEST['wpbdp-action'] );
            }
        }

        private function check_server_requirements() {
            $php_version       = explode( '.', phpversion() );
            $installed_version = $php_version[0] . '.' . $php_version[1];

            // PHP 5.6 is required.
            if ( version_compare( $installed_version, '5.6', '>=' ) ) {
                return;
            }

            $dismissed = get_transient( 'wpbdp_server_requirements_warning_dismissed' );
            if ( $dismissed ) {
                return;
            }

			$this->messages[] = array(
				sprintf(
					_x( '<strong>Business Directory Plugin</strong> requires <strong>PHP 5.6</strong> or later, but your server is running version <strong>%s</strong>. Please ask your provider to upgrade in order to prevent any issues with the plugin.', 'admin', 'business-directory-plugin' ),
					$installed_version
				),
				'notice-error is-dismissible',
				array( 'dismissible-id' => 'server_requirements' ),
			);
        }

        public function ajax_dismiss_notification_server_requirements() {
            set_transient( 'wpbdp_server_requirements_warning_dismissed', true, WEEK_IN_SECONDS );
        }

        public function check_setup() {
            global $pagenow;

            if ( in_array( $pagenow, array( 'admin.php', 'edit.php' ) ) || ! WPBDP_App_Helper::is_admin_page( 'wpbdp_settings' ) ) {
                return;
            }

            // Registration disabled message.
			if ( wpbdp_get_option( 'require-login' ) && ! get_option( 'users_can_register' ) ) {
				$this->messages[] = array(
					sprintf(
						__( 'We noticed you want your Business Directory users to register before posting listings, but Registration for your site is currently disabled. Go %1$shere%2$s and check "Anyone can register".', 'business-directory-plugin' ),
						'<a href="' . esc_url( admin_url( 'options-general.php' ) ) . '">',
						'</a>'
					),
					'notice-error is-dismissible',
					array( 'dismissible-id' => 'registration_disabled' ),
				);
			}
        }

        private function check_deprecation_warnings() {
            global $wpbdp_deprecation_warnings;

            if ( ! empty( $wpbdp_deprecation_warnings ) ) {
                foreach ( $wpbdp_deprecation_warnings as $warning ) {
                    $this->messages[] = $warning;
                }
            }
        }

        public function main_menu() {
            // TODO: This will be the new dashboard page.
        }

        public function register_listings_views() {
            $view = new WPBDP__ListingsWithNoFeePlanView();

            add_filter( 'wpbdp_admin_directory_views', array( $view, 'filter_views' ), 10, 2 );
            add_filter( 'wpbdp_admin_directory_filter', array( $view, 'filter_query_pieces' ), 10, 2 );
        }

        public function maybe_highlight_menu() {
			if ( ! WPBDP_App_Helper::is_bd_post_page() ) {
				return;
			}

            echo '<script>var wpbdpSelectNav = 1;</script>';
        }

		/**
		 * Action called before post is deleted.
		 * Delete cached directory ids if a page is deleted.
		 *
		 * @since 5.16.1
		 */
		public function before_delete_post( $check, $post ) {
			if ( 'page' === $post->post_type ) {
				wpbdp_delete_page_ids_cache();
			}
			return $check;
		}

        /**
         * This function restores Manage Regions menu for Editors,
         * it won't be necessary after fixing the editors
         * issue in regions module.
         */
        private function maybe_restore_regions_submenu() {
            if ( class_exists( 'WPBDP_RegionsAdmin' ) ) {
                global $submenu;

                $parent_file  = 'wpbdp_admin';
                $submenu_file = 'edit-tags.php?taxonomy=%s&amp;post_type=%s';
                $submenu_file = sprintf( $submenu_file, wpbdp_regions_taxonomy(), WPBDP_POST_TYPE );

                $directory_regions = null;
                foreach ( wpbdp_getv( $submenu, $parent_file, array() ) as $k => $item ) {
                    if ( strcmp( $item[2], $submenu_file ) === 0 ) {
                        $directory_regions = $k;
                        break;
                    }
                }

                if ( is_null( $directory_regions ) ) {
                    return;
                }

				array_splice(
					$submenu[ 'edit.php?post_type=' . WPBDP_POST_TYPE ],
					count( $submenu[ 'edit.php?post_type=' . WPBDP_POST_TYPE ] ),
					0,
					array( $submenu[ $parent_file ][ $directory_regions ] )
				);
            }
        }

		/**
		 * @deprecated 5.13.2
		 */
		public function check_ajax_compat_mode() {
			_deprecated_function( __METHOD__, '5.13.2' );
		}

		/**
		 * @deprecated 5.9.2
		 */
		public function enqueue_scripts( $force = false ) {
			_deprecated_function( __METHOD__, '5.9.2', 'WPBDP__Assets::enqueue_admin_scripts' );

			global $wpbdp;

			if ( ! $force && ! $wpbdp->is_bd_page() ) {
				return;
			}

			$wpbdp->assets->enqueue_admin_scripts();
		}
    }

    function wpbdp_admin_message( $msg, $kind = '', $extra = array() ) {
        global $wpbdp;
        $wpbdp->admin->messages[] = ( $kind || $extra ) ? array( $msg, $kind, $extra ) : $msg;
    }
}
