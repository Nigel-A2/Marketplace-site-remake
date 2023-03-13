<?php
$name  = empty( $value['name'] ) ? '' : $value['name'];
$email = empty( $value['email'] ) ? '' : $value['email'];

if ( ! $name && ! $email && 0 !== $value['user_id'] ) :
	$user  = get_user_by( 'ID', $value['user_id'] );
    $name = $user->data->user_login;
    $email = $user->data->user_email;
endif;
?>
<tr data-id="<?php echo $key; ?>">
    <td class="authoring-info">
        <?php echo $name ? $name : 'Visitor'; ?>
        <br/>
        <?php echo $email ? $email : ''; ?>
        <div class="row-actions">
            <span class="trash">
                <a href="<?php echo esc_url( add_query_arg( array( 'wpbdmaction' => 'delete-flagging', 'listing_id' => $listing->get_id(), 'meta_pos' => $key ) ) ); ?>" class="delete">
                    <?php esc_html_e( 'Delete', 'business-directory-plugin' ); ?>
                </a>
            </span>
        </div>
    </td>
    <td class="report">
        <div class="submitted-on">
            <?php echo date_i18n( get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ), $value['date'] ); ?>
        </div>
        <div class="report-reasons">
			<?php echo _x( 'Selected Option: ', 'admin listings', 'business-directory-plugin' ) . esc_html( $value['reason'] ); ?>
            <br/>
            <?php
			if ( ! empty( $value['comments'] ) ) :
                echo _x( 'Aditional Info: ', 'admin listings', 'business-directory-plugin' ) . esc_html( $value['comments'] );
            endif;
            ?>
        </div>
    </td>
</tr>


