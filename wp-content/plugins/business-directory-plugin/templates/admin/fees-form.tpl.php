<?php
/**
 * Fees Form Template
 *
 * @package WPBDP/Templates/Admin
 */

?>

<form id="wpbdp-fee-form" action="" method="post">
	<?php wp_nonce_field( 'wpbdp-fees' ); ?>

    <table class="form-table">
        <tbody>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="wpbdp-fee-form-fee-label"> <?php esc_html_e( 'Plan Label', 'business-directory-plugin' ); ?> <span class="description'">(<?php esc_html_e( 'required', 'business-directory-plugin' ); ?>)</span></label>
                </th>
                <td>
                    <input
                            id="wpbdp-fee-form-fee-label"
                            name="fee[label]"
                            type="text" aria-required="true"
                            value="<?php echo esc_attr( $fee->label ); ?>"
                    />
                </td>
            </tr>
            <tr class="form-field">
                <th scope="row">
                    <label for="wpbdp-fee-form-fee-description"> <?php esc_html_e( 'Plan Description', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <textarea id="wpbdp-fee-form-fee-description" name="fee[description]" rows="5" cols="50"><?php echo esc_textarea( $fee->description ); ?></textarea>
                </td>
            </tr>
            <tr class="form-required">
                <th scope="row">
                    <label for="wpbdp-fee-form-days"> <?php esc_html_e( 'How long should the listing run?', 'business-directory-plugin' ); ?> <span class="description">(<?php esc_html_e( 'required', 'business-directory-plugin' ); ?>)</span></label>
                </th>
                <td>
                    <input type="radio" id="wpbdp-fee-form-days" name="_days" value="1" <?php echo absint( $fee->days ) > 0 ? 'checked="checked"' : ''; ?>/> <label for="wpbdp-fee-form-days"><?php esc_html_e( 'run listing for', 'business-directory-plugin' ); ?></label>
                    <input
                            id="wpbdp-fee-form-days-n"
                            type="text"
                            aria-required="true"
                            value="<?php echo absint( $fee->days ); ?>"
                            style="width: 80px;"
                            name="fee[days]"
                            <?php echo ( absint( $fee->days ) === 0 ) ? 'disabled="disabled"' : ''; ?>
                    />
                    <?php esc_html_e( 'days', 'business-directory-plugin' ); ?>
                    <br />
                    <input type="radio" id="wpbdp-fee-form-days-0" name="_days" value="0" <?php echo ( absint( $fee->days ) === 0 ) ? 'checked="checked"' : ''; ?>/> <label for="wpbdp-fee-form-days-0"><?php esc_html_e( 'run listing forever', 'business-directory-plugin' ); ?>
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="wpbdp-fee-form-fee-images"> <?php esc_html_e( 'Number of images allowed', 'business-directory-plugin' ); ?> <span class="description">(<?php esc_html_e( 'required', 'business-directory-plugin' ); ?>)</span></label>
                </th>
                <td>
                    <input
                            id="wpbdp-fee-form-fee-images"
                            name="fee[images]"
                            type="text"
                            aria-required="true"
                            value="<?php echo absint( $fee->images ); ?>"
                            style="width: 80px;"
                    />
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="wpbdp-fee-form-fee-private"> <?php esc_html_e( 'Private Plan (visible to admins only)?', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <label>
                        <input
                                id="wpbdp-fee-form-fee-recurring"
                                name="fee[extra_data][private]"
                                type="checkbox"
                                value="1"
                                <?php echo ! empty( $fee->extra_data['private'] ) ? 'checked="checked"' : ''; ?>
                                <?php echo ( 'free' === $fee->tag ) ? 'disabled="disabled"' : ''; ?>
                        />
                    </label>
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
                    <label for="wpbdp-fee-form-fee-recurring"> <?php esc_html_e( 'Is recurring?', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <label>
                        <input
                                id="wpbdp-fee-form-fee-recurring"
                                name="fee[recurring]"
                                type="checkbox"
                                value="1"
                                <?php echo $fee->recurring ? 'checked="checked"' : ''; ?>
                                <?php echo ( 'free' === $fee->tag ) ? 'disabled="disabled"' : ''; ?>
                        />
                        <span class="description"><?php esc_html_e( 'Should the listing auto-renew at the end of the listing term?', 'business-directory-plugin' ); ?></span>
                    </label>
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
					<label for="wpbdp-fee-form-fee-sticky">
						<?php esc_html_e( 'Make listings on this plan featured (sticky)?', 'business-directory-plugin' ); ?>
					</label>
                </th>
                <td>
                    <input
                            id="wpbdp-fee-form-fee-sticky"
                            name="fee[sticky]"
                            type="checkbox"
                            value="1"
                            <?php echo $fee->sticky ? 'checked="checked"' : ''; ?>
                            <?php echo ( 'free' === $fee->tag ) ? 'disabled="disabled"' : ''; ?>
                    />
                    <label for="wpbdp-fee-form-fee-sticky"><span class="description"><?php esc_html_e( 'This floats the listing to the top of search results and browsing the directory when the user buys this plan.', 'business-directory-plugin' ); ?></span></label>
                </td>
            </tr>
            <tr class="form-field form-required">
                <th scope="row">
					<label for="fee-bgcolor-value">
						<?php esc_html_e( 'Listing background color:', 'business-directory-plugin' ); ?>
					</label>
                </th>
                <td>
                    <input type="text" class="cpa-color-picker" name="fee[extra_data][bgcolor]" id="fee-bgcolor-value" value="<?php echo isset( $fee->extra_data['bgcolor'] ) ? esc_attr( $fee->extra_data['bgcolor'] ) : ''; ?>" />

                    <span class="description"><?php esc_html_e( 'Used to differentiate listings inside this plan from others.', 'business-directory-plugin' ); ?></span>
                </td>
            </tr>
            <tr class="form-field limit-categories">
                <th scope="row">
                    <label for="wpbdp-fee-form-fee-category-policy"><?php _ex( 'Plan Category Policy:', 'fees admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <select id="wpbdp-fee-form-fee-category-policy"
                            name="limit_categories">
                        <option value="0"><?php _ex( 'Plan applies to all categories', 'fees admin', 'business-directory-plugin' ); ?></option>
                        <option value="1" <?php selected( is_array( $fee->supported_categories ), true ); ?> ><?php _ex( 'Plan applies to only certain categories', 'fees admin', 'business-directory-plugin' ); ?></option>
                    </select>

                    <div id="limit-categories-list" class="<?php echo is_array( $fee->supported_categories ) ? '' : 'hidden'; ?>">
                        <p><span class="description"><?php _ex( 'Limit plan to the following categories:', 'fees admin', 'business-directory-plugin' ); ?></span></p>
<?php
$all_categories       = get_terms(
    array(
		'taxonomy'     => WPBDP_CATEGORY_TAX,
		'hide_empty'   => false,
		'hierarchical' => true,
    )
);
$supported_categories = is_array( $fee->supported_categories ) ? array_map( 'absint', $fee->supported_categories ) : array();

if ( count( $all_categories ) <= 30 ) :
    foreach ( $all_categories as $category ) :
?>
    <div class="wpbdp-category-item">
        <label>
            <input type="checkbox" name="fee[supported_categories][]" value="<?php echo $category->term_id; ?>" <?php checked( in_array( (int) $category->term_id, $supported_categories, true ) ); ?>>
            <?php echo esc_html( $category->name ); ?>
        </label>
    </div>
<?php
    endforeach;
else :
?>
    <select name="fee[supported_categories][]" multiple="multiple" placeholder="<?php _ex( 'Click to add categories to the selection.', 'fees admin', 'business-directory-plugin' ); ?>">
    <?php foreach ( $all_categories as $category ) : ?>
    <option value="<?php echo $category->term_id; ?>" <?php selected( in_array( (int) $category->term_id, $supported_categories, true ) ); ?>><?php echo esc_html( $category->name ); ?></option>
    <?php endforeach; ?>
    </select>
<?php
endif;
?>
                        </div>
                </td>
            </tr>
        </tbody>
    </table>

    <h2><?php _ex( 'Pricing', 'fees admin', 'business-directory-plugin' ); ?></h2>

	<?php WPBDP_Admin_Education::show_tip( 'discounts' ); ?>

    <table class="form-table">
        <tbody>
            <tr class="form-field pricing-info">
                <th scope="row">
                    <label for="wpbdp-fee-form-pricing-model-flat"><?php _ex( 'Pricing model', 'fees admin', 'business-directory-plugin' ); ?>
                </th>
                <td>
                    <div class="pricing-options">
                        <label><input id="wpbdp-fee-form-pricing-model-flat" type="radio" name="fee[pricing_model]" value="flat" <?php checked( $fee->pricing_model, 'flat' ); ?> /> <?php _ex( 'Flat price', 'fees admin', 'business-directory-plugin' ); ?></label>
                        <label><input id="wpbdp-fee-form-pricing-model-variable" type="radio" name="fee[pricing_model]" value="variable" <?php checked( $fee->pricing_model, 'variable' ); ?> /> <?php _ex( 'Different price for different categories', 'fees admin', 'business-directory-plugin' ); ?></label>
                        <label><input id="wpbdp-fee-form-pricing-model-extra" type="radio" name="fee[pricing_model]" value="extra" <?php checked( $fee->pricing_model, 'extra' ); ?> /> <?php _ex( 'Base price plus an extra amount per category', 'fees admin', 'business-directory-plugin' ); ?></label>
                    </div>
                </td>
            </tr>
            <tr class="form-field fee-pricing-details pricing-details-flat pricing-details-extra <?php echo ( 'flat' === $fee->pricing_model || 'extra' === $fee->pricing_model ) ? '' : 'hidden'; ?>">
                <th scope="row">
                    <label for="wpbdp-fee-form-fee-price"><?php _ex( 'Plan Price', 'fees admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <input id="wpbdp-fee-form-fee-price" type="text" name="fee[amount]" value="<?php echo $fee->amount; ?>" />
                </td>
            </tr>
            <tr class="form-field fee-pricing-details pricing-details-variable <?php echo 'variable' === $fee->pricing_model ? '' : 'hidden'; ?>">
                <th scope="row">
                    <label><?php _ex( 'Prices per category', 'fees admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <table>
                        <thead>
                        <th><?php esc_html_e( 'Category', 'business-directory-plugin' ); ?></th>
                        <th><?php _ex( 'Price', 'fees admin', 'business-directory-plugin' ); ?></th>
                        </thead>
                        <tbody>
                            <?php
                            require_once WPBDP_INC . 'admin/helpers/class-variable-pricing-configurator.php';
                            $c = new WPBDP__Admin__Variable_Pricing_Configurator( array( 'fee' => $fee ) );
                            $c->display();
                            ?>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr class="form-field fee-pricing-details pricing-details-extra <?php echo 'extra' === $fee->pricing_model ? '' : 'hidden'; ?>">
                <th scope="row">
                    <label for="wpbdp-fee-form-fee-extra"><?php _ex( 'Extra amount (per category)', 'fees admin', 'business-directory-plugin' ); ?></label>
                </th>
                <td>
                    <input id="wpbdp-fee-form-fee-extra" type="text" name="fee[pricing_details][extra]" value="<?php echo isset( $fee->pricing_details['extra'] ) ? floatval( $fee->pricing_details['extra'] ) : 0; ?>" />
                </td>
            </tr>
        </tbody>
    </table>

    <?php do_action( 'wpbdp_after_admin_fee_form', $fee ); ?>

    <?php submit_button( $fee->id ? esc_html__( 'Save Changes', 'business-directory-plugin' ) : esc_html__( 'Add Plan', 'business-directory-plugin' ) ); ?>
</form>

