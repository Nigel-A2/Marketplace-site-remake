<h3><?php echo $_state->step_number . ' - '; ?><?php _ex( 'Additional Information', 'templates', 'business-directory-plugin' ); ?></h3>

<form id="wpbdp-listing-form-extra" class="wpbdp-listing-form" method="POST" action="" enctype="multipart/form-data">
	<input type="hidden" name="_state" value="<?php echo $_state->id; ?>" />
    <?php echo $output; ?>
    <input type="submit" name="continue-with-save" value="<?php _ex( 'Continue', 'templates', 'business-directory-plugin' ); ?> " class="submit" />  
</form>
