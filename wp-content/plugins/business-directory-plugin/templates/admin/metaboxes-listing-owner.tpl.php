<?php
/**
 * Listing owner metabox template
 *
 * @package BDP/Templates/Admin/Metabox listing owner
 */

?>
<?php if ( isset( $wrapper_id ) || isset( $wrapper_class ) ) : ?>
    <div id="<?php echo esc_attr( ! empty( $wrapper_id ) ? $wrapper_id : '' ); ?>" class="<?php echo esc_attr( implode( ' ', $wrapper_class ? $wrapper_class : array() ) ); ?>" style="display: block;">
<?php endif; ?>
<?php if ( $label ) : ?>
<?php // phpcs:ignore Squiz.ControlStructures.ControlSignature.NewlineAfterOpenBrace ?>
<label <?php if ( $label_class ) : ?>class="<?php echo esc_attr( $label_class ); ?>" <?php endif; ?>for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $label ); ?></label>
<?php endif; ?>

<?php // TODO: Remove style attribute. ?>
<select id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" class="<?php echo esc_attr( implode( ' ', $class ) ); ?>" data-configuration="<?php echo esc_attr( wp_json_encode( $configuration ) ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>" style="width: 100%">
    <?php if ( $default ) : ?>
    <option value=""><?php echo esc_html( $default ); ?></option>
    <?php endif; ?>

    <?php foreach ( $users as $k => $user ) : ?>

        <option value="<?php echo esc_attr( $k ); ?>"<?php echo $selected && $selected === $k ? ' selected="selected"' : ''; ?>>
        <?php echo esc_html( $user ); ?>
    </option>

    <?php endforeach; ?>
</select>
<?php if ( isset( $wrapper_class ) ) : ?>
    </div>
<?php endif; ?>
