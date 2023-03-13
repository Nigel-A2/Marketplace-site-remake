<#

var names = data.names;

if ( ! names ) {
	if ( data.isMultiple ) {
		names = {
			description: data.name + '[' + data.index + '][description]',
			icon: data.name + '[' + data.index + '][icon]',
			tooltip: data.name + '[' + data.index + '][tooltip]',
		};
	} else {
		names = {
			description: data.name + '[][description]',
			icon: data.name + '[][icon]',
			tooltip: data.name + '[][tooltip]',
		};
	}
}

var description_placeholder = '';
if ( data.field.description_placeholder ) {
	description_placeholder = data.field.description_placeholder;
}

var tooltip_placeholder = '';
if ( data.field.tooltip_placeholder ) {
	tooltip_placeholder = data.field.tooltip_placeholder;
}

var featureIcon = wp.template( 'fl-builder-field-icon')({
	name: names.icon,
	value: ( ( 'undefined' != typeof data.value.icon ) ? data.value.icon : '' ),
	field: {
		show_remove: true,
	},
});

#>
<div class="fl-price-feature-field">
	<div class="fl-price-feature-row fl-price-feature-description-row">
		<div class="fl-price-feature-description-wrapper">
			<label for="{{names.description}}"><?php _e( 'Description', 'fl-builder' ); ?></label>
			<input type="text" name="{{names.description}}" id="{{names.description}}" class="text fl-price-feature-field-input" value="{{data.value.description}}" placeholder="{{description_placeholder}}" />
		</div>
	</div>
	<div class="fl-price-feature-row fl-price-feature-icon-row">
		<div class="fl-price-feature-icon-wrapper">
			<label class="fl-price-feature-field-icon-label"><?php _e( 'Feature Icon', 'fl-builder' ); ?></label>
			{{{featureIcon}}}
		</div>
	</div>
	<div class="fl-price-feature-row fl-price-feature-tooltip-row">
		<div class="fl-price-feature-tooltip-wrapper">
			<label for="{{names.tooltip}}"><?php _e( 'Tooltip', 'fl-builder' ); ?></label>
			<input type="text" name="{{names.tooltip}}" class="text fl-price-feature-field-input" id="{{names.tooltip}}" value="{{data.value.tooltip}}" placeholder="{{tooltip_placeholder}}" />
		</div>
	</div>
	<div class="fl-price-feature-row fl-price-feature-row-toggle">
		<button class="fl-builder-button fl-builder-button-silent fl-builder-price-feature-toggle-button down" title="Slide Up/Down">
			<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" height="30px" width="30px">
				<path d="M5 6l5 5 5-5 2 1-7 7-7-7z"></path>
			</svg>
		</button>
	</div>
</div>
