<input
type="text"
class="text text-full fl-suggest-field<# if ( data.field.className ) { #> {{data.field.className}}<# } #>"
name="{{data.name}}"
data-value='{{data.value}}'
data-action="<# if ( data.field.action ) { #>{{data.field.action}}<# } #>"
data-action-data="<# if ( data.field.data ) { #>{{data.field.data}}<# } #>"
data-limit="<# if ( data.field.limit ) { #>{{data.field.limit}}<# } else { #>false<# } #>"
data-args='<# if ( data.field.args ) { args = JSON.stringify( args ); #>{{{args}}}<# } #>'
placeholder="<# if ( data.field.placeholder ) { #>{{data.field.placeholder}}<# } else { #><?php esc_attr__( 'Start typing...', 'fl-builder' ); ?><# } #>"
/>
