<#
data.value = '';
var select = wp.template( 'fl-builder-field-select' )( data );
#>
<div class="fl-preset-select-controls" data-presets="{{data.field.presets}}" data-prefix="{{data.field.prefix}}">{{{select}}}</div>
