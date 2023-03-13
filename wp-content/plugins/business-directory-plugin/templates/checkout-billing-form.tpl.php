<?php
$show_personal_info_section = ! isset( $show_personal_info_section );
$show_cc_section = ! isset( $show_cc_section );
$show_details_section = ! isset( $show_details_section );
?>
<?php if ( $show_personal_info_section ) : ?>
<div class="wpbdp-checkout-personal-info-fields wpbdp-grid">
    <h3><?php esc_html_e( 'Personal Info', 'business-directory-plugin' ); ?></h3>

    <div class="wpbdp-billing-detail-field wpbdp-required wpbdp-form-field">
        <label><?php esc_html_e( 'Email Address', 'business-directory-plugin' ); ?></label>
        <span class="wpbdp-description  wpbdp-form-field-description"><?php esc_html_e( 'We will send a receipt to this e-mail address.', 'business-directory-plugin' ); ?></span>
        <input type="text" name="payer_email" value="<?php echo ! empty( $data['payer_email'] ) ? esc_attr( $data['payer_email'] ) : ''; ?>" />
    </div>

    <div class="wpbdp-billing-detail-field wpbdp-required wpbdp-form-field wpbdp-half">
        <label><?php esc_html_e( 'First Name', 'business-directory-plugin' ); ?></label>
        <input type="text" name="payer_first_name" value="<?php echo ! empty( $data['payer_first_name'] ) ? esc_attr( $data['payer_first_name'] ) : ''; ?>" />
    </div>

    <div class="wpbdp-billing-detail-field wpbdp-required wpbdp-form-field wpbdp-half">
        <label><?php esc_html_e( 'Last Name', 'business-directory-plugin' ); ?></label>
        <input type="text" name="payer_last_name" value="<?php echo ! empty( $data['payer_last_name'] ) ? esc_attr( $data['payer_last_name'] ) : ''; ?>" />
    </div>
</div>
<?php endif; ?>

<?php if ( $show_cc_section ) : ?>
<div class="wpbdp-checkout-cc-fields wpbdp-checkout-section">
    <h3><?php esc_html_e( 'Credit Card Info', 'business-directory-plugin' ); ?></h3>

    <div class="wpbdp-billing-detail-field wpbdp-required wpbdp-form-field">
        <label><?php esc_html_e( 'Card Number', 'business-directory-plugin' ); ?></label>
        <span class="wpbdp-description  wpbdp-form-field-description"><?php esc_html_e( 'The digits on the front of your credit card.', 'business-directory-plugin' ); ?></span>
        <input type="text" name="card_number" value="" placeholder="<?php esc_attr_e( 'Card Number', 'business-directory-plugin' ); ?>" />
    </div>

    <div class="wpbdp-billing-detail-field wpbdp-required wpbdp-form-field">
        <label><?php esc_html_e( 'CVC', 'business-directory-plugin' ); ?></label>
        <span class="wpbdp-description  wpbdp-form-field-description"><?php esc_html_e( 'The 3 digit (back) or 4 digit (front) security code on your credit card.', 'business-directory-plugin' ); ?></span>
        <input type="text" name="cvc" value="" placeholder="<?php esc_attr_e( 'Security Code', 'business-directory-plugin' ); ?>" />
    </div>

    <div class="wpbdp-billing-detail-field wpbdp-required wpbdp-form-field">
        <label><?php esc_html_e( 'Name on the Card', 'business-directory-plugin' ); ?></label>
        <span class="wpbdp-description  wpbdp-form-field-description"><?php esc_html_e( 'The name as it appears printed on the front of your credit card.', 'business-directory-plugin' ); ?></span>
        <input type="text" name="card_name" value="" placeholder="<?php esc_attr_e( 'Name on the Card', 'business-directory-plugin' ); ?>" />
    </div>

    <div class="wpbdp-billing-detail-field wpbdp-required wpbdp-form-field wpbdp-exp-field">
        <label><?php esc_html_e( 'Expiration Date', 'business-directory-plugin' ); ?></label>
        <span class="wpbdp-description  wpbdp-form-field-description"><?php esc_html_e( 'Format: MM/YY', 'business-directory-plugin' ); ?></span>
        <select name="exp_month">
			<?php for ( $i = 1; $i <= 12; $i++ ) : ?>
            <option value="<?php echo esc_attr( sprintf( '%02d', $i ) ); ?>">
                <?php echo esc_attr( sprintf( '%02d', $i ) ); ?>
            </option>
            <?php endfor; ?>
        </select>
        <span class="wpbdp-exp-slash">/</span>
        <select name="exp_year">
            <?php for ( $i = date( 'Y' ); $i < date( 'Y' ) + 30; $i++ ) : ?>
            <option value="<?php echo esc_attr( $i ); ?>"><?php echo esc_html( substr( $i, 2 ) ); ?></option>
            <?php endfor; ?>
        </select>
    </div>
</div>
<?php endif; ?>

<?php if ( $show_details_section ) : ?>
<div class="wpbdp-checkout-billing-details wpbdp-grid">
    <h3><?php esc_html_e( 'Billing Details', 'business-directory-plugin' ); ?></h3>

    <div class="wpbdp-billing-detail-field wpbdp-required wpbdp-form-field">
        <label><?php esc_html_e( 'Address', 'business-directory-plugin' ); ?></label>
        <span class="wpbdp-description  wpbdp-form-field-description"><?php esc_html_e( 'Please enter the address where you receive your billing statement.', 'business-directory-plugin' ); ?></span>
        <input type="text" name="payer_address" value="<?php echo ! empty( $data['payer_address'] ) ? esc_attr( $data['payer_address'] ) : ''; ?>" />
    </div>

    <div class="wpbdp-billing-detail-field wpbdp-form-field">
        <label><?php esc_html_e( 'Address Line 2', 'business-directory-plugin' ); ?></label>
        <span class="wpbdp-description  wpbdp-form-field-description"><?php esc_html_e( 'Additional details (suite, apt no, etc.) associated with your billing address.', 'business-directory-plugin' ); ?></span>
        <input type="text" name="payer_address_2" value="<?php echo ! empty( $data['payer_address_2'] ) ? esc_attr( $data['payer_address_2'] ) : ''; ?>" />
    </div>

    <div class="wpbdp-billing-detail-field wpbdp-required wpbdp-form-field wpbdp4">
        <label><?php esc_html_e( 'City', 'business-directory-plugin' ); ?></label>
        <input type="text" name="payer_city" value="<?php echo ! empty( $data['payer_city'] ) ? esc_attr( $data['payer_city'] ) : ''; ?>" />
    </div>

    <div class="wpbdp-billing-detail-field wpbdp-form-field wpbdp4">
        <label><?php esc_html_e( 'State / Province', 'business-directory-plugin' ); ?></label>
        <input type="text" name="payer_state" value="<?php echo ! empty( $data['payer_state'] ) ? esc_attr( $data['payer_state'] ) : ''; ?>" />
    </div>

    <div class="wpbdp-billing-detail-field wpbdp-required wpbdp-form-field wpbdp4">
        <label><?php esc_html_e( 'Postal Code', 'business-directory-plugin' ); ?></label>
        <input type="text" name="payer_zip" value="<?php echo ! empty( $data['payer_zip'] ) ? esc_attr( $data['payer_zip'] ) : ''; ?>" />
    </div>

    <div class="wpbdp-billing-detail-field wpbdp-required wpbdp-form-field">
        <label><?php esc_html_e( 'Country', 'business-directory-plugin' ); ?></label>
        <input type="text" name="payer_country" value="<?php echo ! empty( $data['payer_country'] ) ? esc_attr( $data['payer_country'] ) : ''; ?>" />
    </div>

</div>
<?php endif; ?>
