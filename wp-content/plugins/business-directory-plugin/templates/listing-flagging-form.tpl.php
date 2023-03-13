<?php
$user_flagged = WPBDP__Listing_Flagging::user_has_flagged( $listing->get_id(), get_current_user_id() );
$flagging_text = __( 'Report Listing', 'business-directory-plugin' );
$flagging_options = WPBDP__Listing_Flagging::get_flagging_options();
?>

<div id="wpbdp-listing-flagging-page">
    <h3><?php echo esc_html( $flagging_text ); ?></h3>

    <form class="confirm-form" action="" method="post">
        <?php wp_nonce_field( 'flag listing report ' . $listing->get_id() ); ?>

        <?php if ( false === $user_flagged ) : ?>
			<?php if ( $current_user ) : ?>
                <p>
                    <?php
                    printf(
                        /* translators: %s: listing title */
                        esc_html__( 'You are about to report the listing "%s" as inappropriate. ', 'business-directory-plugin' ),
                        '<b>' . esc_html( $listing->get_title() ) . '</b>'
                    );
                    ?>
                </p>
                <p>
                    <?php
                    printf(
                        /* translators: %s: user name */
                        esc_html__( 'You are currently logged in as %s. Listing report will be sent using your logged in contact email.', 'business-directory-plugin' ),
                        esc_html( $current_user->user_login )
                    );
                    ?>
                </p>
			<?php else : ?>
                <p>
                    <label><?php esc_html_e( 'Name', 'business-directory-plugin' ); ?></label>
                    <input type="text" class="intextbox" name="reportauthorname" value="<?php echo esc_attr( wpbdp_get_var( array( 'param' => 'commentauthorname' ), 'post' ) ); ?>" />
                </p>
                <p>
                    <label><?php esc_html_e( 'Email', 'business-directory-plugin' ); ?></label>
                    <input type="text" class="intextbox" name="reportauthoremail" value="<?php echo esc_attr( wpbdp_get_var( array( 'param' => 'commentauthoremail', 'sanitize' => 'sanitize_email' ), 'post' ) ); ?>" />
                </p>
            <?php endif; ?>

			<?php if ( $flagging_options ) : ?>
                <p><?php esc_html_e( 'Please select the reason to report this listing:', 'business-directory-plugin' ); ?></p>

                <div class="wpbdp-listing-flagging-options">
                    <?php foreach ( $flagging_options as $option ) : ?>
                        <p><label><input type="radio" name="flagging_option" value="<?php echo esc_attr( $option ); ?>" required> <span><?php echo esc_html( $option ); ?></span></label></p>
                    <?php endforeach; ?>
                </div>
			<?php else : ?>
                <p><?php esc_html_e( 'Please enter the reasons to report this listing:', 'business-directory-plugin' ); ?></p>
            <?php endif; ?>

            <textarea name="flagging_more_info" value="" placeholder="<?php esc_attr_e( 'Additional info.', 'business-directory-plugin' ); ?>" <?php echo esc_attr( $flagging_options ? '' : 'required' ); ?>></textarea>
            
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo $recaptcha;
            ?>

            <p>
                <input class="wpbdp-submit button wpbdp-button" type="submit" value="<?php echo esc_attr( $flagging_text ); ?>" />
				<a href="<?php echo esc_url( wpbdp_url( 'main' ) ); ?>"><?php esc_html_e( 'Cancel', 'business-directory-plugin' ); ?></a>
            </p>
		<?php else : ?>
            <?php
            printf(
                /* translators: %s: listing title */
                esc_html__( 'You already reported the listing "%s" as inappropriate.', 'business-directory-plugin' ),
                '<b>' . esc_html( $listing->get_title() ) . '</b>'
            );
            ?>
            <p>
                <?php printf(
                    /* translators: %1$s: open link html, %2$s close link html */
                    esc_html__( 'Return to %1$slisting%2$s.', 'business-directory-plugin' ),
                    '<a href="' . esc_url( $listing->get_permalink() ) . '">',
                    '</a>'
                );
                ?>
            </p>
        <?php endif; ?>

    </form>
</div>
