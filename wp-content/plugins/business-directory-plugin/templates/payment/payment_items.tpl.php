<table class="wpbdp-payment-items-table" id="wpbdp-payment-items-<?php echo esc_attr( $payment->id ); ?>">
    <thead>
        <tr>
            <th><?php esc_html_e( 'Item', 'business-directory-plugin' ); ?></th>
            <th><?php esc_html_e( 'Amount', 'business-directory-plugin' ); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ( $payment->payment_items as $item ) : ?>
        <tr class="item <?php echo esc_attr( $item['type'] ); ?>">
            <td>
                <?php print esc_html( $item['description'] ); ?>
                <?php if ( ! empty( $item['fee_id'] ) && wpbdp_get_option( 'include-fee-description' ) ) : ?>
                    <div  class="item-fee-description" class="fee-description"><?php print esc_html( wpbdp_get_fee_plan( $item['fee_id'] )->description ); ?></div>
                <?php endif; ?>
            </td>
            <td><?php echo esc_html( wpbdp_currency_format( $item['amount'] ) ); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <th><?php esc_html_e( 'Total', 'business-directory-plugin' ); ?></th>
            <td class="total"><?php echo esc_html( wpbdp_currency_format( $payment->amount ) ); ?>
        </tr>
    </tfoot>
</table>
