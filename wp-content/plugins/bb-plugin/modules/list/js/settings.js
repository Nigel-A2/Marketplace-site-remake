(function($){
    
    /**
     * Determine whether or not to override the icon in the List Item.
     * Only Generic List type are allowed to override the icon. 
     * Ordered and unordered lists can only use the predefined icons available in the module settings.
     */
    FLBuilder.registerModuleHelper('list_item_form', {
        
        init: function () {
            var moduleSettingsForm = $('form.fl-builder-list-settings'),
                listType = moduleSettingsForm.find('select[name=list_type]').val();
                
            if ('ol' == listType || 'ul' == listType) {
                $('#fl-builder-settings-section-list_item_icon_section').hide();
            } else {
                $('#fl-builder-settings-section-list_item_icon_section').show();
            }
        }
    });

})(jQuery);