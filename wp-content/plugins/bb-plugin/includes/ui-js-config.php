<script>
<?php
/**
 * Filter main UI JS config.
 * @see fl_builder_ui_js_config
 */
echo 'FLBuilderConfig              = ' . FLBuilderUtils::json_encode( apply_filters('fl_builder_ui_js_config', array(
	'adminUrl'                   => admin_url(),
	'ajaxNonce'                  => wp_create_nonce( 'fl_ajax_update' ),
	'builderEnabled'             => get_post_meta( $post_id, '_fl_builder_enabled', true ) ? true : false,
	'colorPresets'               => FLBuilderModel::get_color_presets(),
	'customImageSizeTitles'      => apply_filters( 'image_size_names_choose', array() ),
	'debug'                      => FLBuilder::is_debug(),
	'enabledTemplates'           => 'core',
	'global'                     => $global_settings,
	'help'                       => FLBuilderModel::get_help_button_settings(),
	'homeUrl'                    => home_url(),
	'enqueueMethod'              => FLBuilderModel::get_asset_enqueue_method(),
	'isRtl'                      => is_rtl(),
	'isUserTemplate'             => false,
	'lite'                       => true === FL_BUILDER_LITE,
	'modSecFix'                  => ( defined( 'FL_BUILDER_MODSEC_FIX' ) && FL_BUILDER_MODSEC_FIX ),
	'MaxInputVars'               => FL_Debug::safe_ini_get( 'max_input_vars' ),
	'moduleGroups'               => FLBuilderModel::get_module_groups(),
	'nestedColumns'              => ( ! defined( 'FL_BUILDER_NESTED_COLUMNS' ) || FL_BUILDER_NESTED_COLUMNS ),
	'newUser'                    => FLBuilderModel::is_new_user(),
	'pluginUrl'                  => FL_BUILDER_URL,
	'relativePluginUrl'          => FLBuilderModel::get_relative_plugin_url(),
	'postId'                     => $post_id,
	'postStatus'                 => get_post_status(),
	'postType'                   => get_post_type(),
	'services'                   => FLBuilderServices::get_services_data(),
	'safemode'                   => isset( $_GET['safemode'] ) ? true : false,
	'simpleUi'                   => $simple_ui ? true : false,
	'upgradeUrl'                 => FLBuilderModel::get_upgrade_url( array(
		'utm_medium'   => ( true === FL_BUILDER_LITE ? 'bb-lite' : 'bb-demo' ),
		'utm_source'   => 'builder-ui',
		'utm_campaign' => ( true === FL_BUILDER_LITE ? 'top-panel-cta' : 'demo-cta' ),
	) ),
	'userCanEditGlobalTemplates' => FLBuilderUserAccess::current_user_can( 'global_node_editing' ),
	'userCanPublish'             => current_user_can( 'publish_posts' ),
	'userSettings'               => FLBuilderUserSettings::get(),
	'userTemplateType'           => FLBuilderModel::get_user_template_type(),
	'brandingIcon'               => FLBuilderModel::get_branding_icon(),
	'url'                        => get_permalink(),
	'editUrl'                    => add_query_arg( 'fl_builder', '', get_permalink() ),
	'shortlink'                  => add_query_arg( 'fl_builder', '', FLBuilderUtils::get_safe_url( $post_id ) ),
	'previewUrl'                 => add_query_arg( 'fl_builder_preview', '', get_permalink() ),
	'layoutHasDraftedChanges'    => FLBuilderModel::layout_has_drafted_changes(),
	'panelData'                  => FLBuilderUIContentPanel::get_panel_data(),
	'contentItems'               => FLBuilderUIContentPanel::get_content_elements(),
	'mainMenu'                   => FLBuilder::get_main_menu_data(),
	'keyboardShortcuts'          => FLBuilder::get_keyboard_shortcuts(),
	'isCustomizer'               => is_customize_preview(),
	'showToolbar'                => is_customize_preview() ? false : true,
	/**
	 * Disable outline panel
	 * @since 2.5
	 * @see fl_builder_outline_panel_enabled
	 */
	'showOutlinePanel'           => apply_filters( 'fl_builder_outline_panel_enabled', true ),
	'shouldRefreshOnPublish'     => FLBuilder::should_refresh_on_publish(),
	'googleFontsUrl'             => apply_filters( 'fl_builder_google_fonts_domain', '//fonts.googleapis.com/' ) . 'css?family=',
	'wp_editor'                  => FLBuilder::get_wp_editor(),
	'rowResize'                  => FLBuilderModel::get_row_resize_settings(),
	'notifications'              => FLBuilderNotifications::get_notifications(),
	'isWhiteLabeled'             => FLBuilderModel::is_white_labeled(),
	'inlineEnabled'              => FLBuilderModel::is_inline_enabled(),
	'CheckCodeErrors'            => FLBuilderModel::is_codechecking_enabled(),
	'AceEditorSettings'          => FLBuilderModel::ace_editor_settings(),
	'optionSets'                 => apply_filters( 'fl_builder_shared_option_sets', array() ),
	'presets'                    => FLBuilderSettingsPresets::get_presets(),
	'FontWeights'                => FLBuilderFonts::get_font_weight_strings(),
	/**
	 * Enable/disable usage stats collection
	 * @see fl_builder_usage_enabled
	 */
	'statsEnabled'               => get_site_option( 'fl_builder_usage_enabled', false ),
	/**
	 * @see fl_remember_settings_tabs_enabled
	 */
	'rememberTab'                => apply_filters( 'fl_remember_settings_tabs_enabled', true ),
	/**
	 * @see fl_select2_enabled
	 */
	'select2Enabled'             => apply_filters( 'fl_select2_enabled', true ),
	/**
	 * @see fl_media_modal_types
	 */
	'uploadTypes'                => apply_filters( 'fl_media_modal_types', array(
		'image'      => 'image',
		'video'      => 'video',
		'videoTypes' => 'mp4,m4v,webm',
	) ),
	/**
	 * @see fl_builder_recent_icons
	 */
	'recentIcons'                => apply_filters( 'fl_builder_recent_icons', get_option( 'fl_plugin_recent_icons', array() ) ),
	'themerLayoutsUrl'           => admin_url( '/edit.php?post_type=fl-theme-layout' ),
	'userCaps'                   => array(
		'unfiltered_html'        => current_user_can( 'unfiltered_html' ),
		'global_unfiltered_html' => defined( 'DISALLOW_UNFILTERED_HTML' ) && DISALLOW_UNFILTERED_HTML ? true : false,
	),
	/**
	 * CSS to ignore during responsive preview
	 * @see fl_builder_responsive_ignore
	 */
	'responsiveIgnore'           => apply_filters( 'fl_builder_responsive_ignore', array(
		'fl-builder-preview',
		'fl-theme-builder',
		'/wp-includes/',
		'/wp-admin/',
		'admin-bar-inline-css',
		'ace-tm',
		'ace_editor.css',
	)),
	'wooActive'                  => class_exists( 'WooCommerce' ) ? true : false,
	'uploadPath'                 => ( get_option( 'upload_path' ) && get_option( 'upload_path' ) != 'wp-content/uploads' ) ? true : false,
	'uploadUrl'                  => admin_url( 'options-media.php' ),
	/**
	 * @see fl_builder_default_image_select_size
	 */
	'defaultImageSize'           => apply_filters( 'fl_builder_default_image_select_size', 'full' ),
) ) ) . ';';

/**
 * Filter UI JS Strings.
 * @see fl_builder_ui_js_strings
 */
echo 'FLBuilderStrings             = ' . FLBuilderUtils::json_encode( apply_filters('fl_builder_ui_js_strings', array(
	'actionsLightboxTitle'           => esc_attr__( 'What would you like to do?', 'fl-builder' ),
	/* translators: %s: field name */
	'addField'                       => esc_attr_x( 'Add %s', 'Field name to add.', 'fl-builder' ),
	/* translators: %s: preset color code */
	'alreadySaved'                   => esc_attr_x( '%s is already a saved preset.', '%s is the preset hex color code.', 'fl-builder' ),
	'audioSelected'                  => esc_attr__( 'Audio File Selected', 'fl-builder' ),
	/* translators: %d: number of files selected */
	'audioSelectedNum'               => esc_attr__( '%d Audio File Selected', 'fl-builder' ),
	'audiosSelected'                 => esc_attr__( 'Audio Files Selected', 'fl-builder' ),
	/* translators: %d: number of files selected (plural) */
	'audiosSelectedNum'              => esc_attr__( '%d Audio Files Selected', 'fl-builder' ),
	'blank'                          => esc_attr__( 'Blank', 'fl-builder' ),
	'cancel'                         => esc_attr__( 'Cancel', 'fl-builder' ),
	'changeTemplate'                 => esc_attr__( 'Change Template', 'fl-builder' ),
	'changeTemplateMessage'          => esc_attr__( 'Warning! Changing the template will replace your existing layout. Do you really want to do this?', 'fl-builder' ),
	'colorPresets'                   => esc_attr__( 'Color Presets', 'fl-builder' ),
	'colorPicker'                    => esc_attr__( 'Color Picker', 'fl-builder' ),
	'codeError'                      => esc_attr__( 'This code has errors. We recommend you fix them before saving.', 'fl-builder' ),
	'codeerrorhtml'                  => esc_attr__( 'You cannot add <script> or <iframe> tag here.', 'fl-builder' ),
	'codeErrorFix'                   => esc_attr__( 'Fix Errors', 'fl-builder' ),
	'codeErrorIgnore'                => esc_attr__( 'Save With Errors', 'fl-builder' ),
	'codeErrorDetected'              => esc_html__( 'We detected a possible issue here:', 'fl-builder' ),
	'childColumn'                    => esc_attr__( 'Child Column', 'fl-builder' ),
	'column'                         => esc_attr__( 'Column', 'fl-builder' ),
	'contentSliderSelectLayout'      => esc_attr__( 'Please select either a background layout or content layout before submitting.', 'fl-builder' ),
	'contentSliderTransitionWarn'    => esc_attr__( 'Transition value should be lower than Delay value.', 'fl-builder' ),
	'countdownDateisInThePast'       => esc_attr__( 'Error! Please enter a date that is in the future.', 'fl-builder' ),
	'deleteAccount'                  => esc_attr__( 'Remove Account', 'fl-builder' ),
	'deleteAccountWarning'           => esc_attr__( 'Are you sure you want to remove this account? Other modules that are connected to it will be affected.', 'fl-builder' ),
	'deleteColumnMessage'            => esc_attr__( 'Do you really want to delete this column?', 'fl-builder' ),
	'deleteFieldMessage'             => esc_attr__( 'Do you really want to delete this item?', 'fl-builder' ),
	'deleteModuleMessage'            => esc_attr__( 'Do you really want to delete this module?', 'fl-builder' ),
	'deleteRowMessage'               => esc_attr__( 'Do you really want to delete this row?', 'fl-builder' ),
	'deleteTemplate'                 => esc_attr__( 'Do you really want to delete this template?', 'fl-builder' ),
	'deleteGlobalTemplate'           => esc_attr__( 'WARNING! You are about to delete a global template that may be linked to other pages. Do you really want to delete this template and unlink it?', 'fl-builder' ),
	'discard'                        => esc_attr__( 'Discard Changes and Exit', 'fl-builder' ),
	'discardMessage'                 => esc_attr__( 'Do you really want to discard these changes? All of your changes that are not published will be lost.', 'fl-builder' ),
	'done'                           => esc_attr__( 'Done', 'fl-builder' ),
	'draft'                          => esc_attr__( 'Save Changes and Exit', 'fl-builder' ),
	'duplicate'                      => esc_attr__( 'Duplicate', 'fl-builder' ),
	'duplicateLayout'                => esc_attr_x( 'Duplicate Layout', 'Duplicate page/post action label.', 'fl-builder' ),
	/* translators: %s: form field label */
	'editFormField'                  => esc_attr_x( 'Edit %s', '%s stands for form field label.', 'fl-builder' ),
	'editGlobalSettings'             => esc_attr__( 'Global Settings', 'fl-builder' ),
	'editLayoutSettings'             => esc_attr__( 'Layout CSS / Javascript', 'fl-builder' ),
	'emptyMessage'                   => esc_attr__( 'Drop a row layout or module to get started!', 'fl-builder' ),
	'enterValidDay'                  => esc_attr__( 'Error! Please enter a valid day.', 'fl-builder' ),
	'enterValidMonth'                => esc_attr__( 'Error! Please enter a valid month.', 'fl-builder' ),
	'enterValidYear'                 => esc_attr__( 'Error! Please enter a valid year.', 'fl-builder' ),
	'errorMessage'                   => esc_attr__( 'Beaver Builder caught the following JavaScript error. If Beaver Builder is not functioning as expected the cause is most likely this error. Please help us by disabling all plugins and testing Beaver Builder while reactivating each to determine if the issue is related to a third party plugin.', 'fl-builder' ),
	'fieldLoading'                   => esc_attr__( 'Field Loading...', 'fl-builder' ),
	'fontAwesome'                    => esc_attr( FLBuilderFontAwesome::error_text() ),
	'fullSize'                       => esc_attr__( 'Full Size', 'fl-builder' ),
	'getHelp'                        => esc_attr__( 'Get Help', 'fl-builder' ),
	'global'                         => esc_attr_x( 'Global', 'Indicator for global node templates.', 'fl-builder' ),
	'globalErrorMessage'             => __( '"{message}" on line {line} of {file}.', 'fl-builder' ),
	'insert'                         => esc_attr__( 'Insert', 'fl-builder' ),
	'large'                          => esc_attr__( 'Large', 'fl-builder' ),
	'manageTemplates'                => esc_attr__( 'Manage Templates', 'fl-builder' ),
	'medium'                         => esc_attr__( 'Medium', 'fl-builder' ),
	'mobile'                         => esc_attr__( 'Small', 'fl-builder' ),
	'module'                         => esc_attr__( 'Module', 'fl-builder' ),
	'moduleTemplateSaved'            => esc_attr__( 'Module Saved!', 'fl-builder' ),
	'move'                           => esc_attr__( 'Move', 'fl-builder' ),
	'newColumn'                      => esc_attr__( 'New Column', 'fl-builder' ),
	'newRow'                         => esc_attr__( 'New Row', 'fl-builder' ),
	'noneColorSelected'              => esc_attr__( 'Please enter a color first.', 'fl-builder' ),
	'noPresets'                      => esc_attr__( 'Add a color preset first.', 'fl-builder' ),
	'noResultsFound'                 => esc_attr__( 'No results found.', 'fl-builder' ),
	'noSavedRows'                    => esc_attr__( 'No saved rows found.', 'fl-builder' ),
	'noSavedModules'                 => esc_attr__( 'No saved modules found.', 'fl-builder' ),
	'ok'                             => esc_attr__( 'OK', 'fl-builder' ),
	'photoPage'                      => esc_attr__( 'Photo Page', 'fl-builder' ),
	'photoSelected'                  => esc_attr__( 'Photo Selected', 'fl-builder' ),
	/* translators: %d: number of selected */
	'photoSelectedNum'               => esc_attr__( '%d Photo Selected', 'fl-builder' ),
	'photosSelected'                 => esc_attr__( 'Photos Selected', 'fl-builder' ),
	/* translators: %d: number of selected (plural) */
	'photosSelectedNum'              => esc_attr__( '%d Photos Selected', 'fl-builder' ),
	'placeholder'                    => esc_attr__( 'Paste color here...', 'fl-builder' ),
	'placeholderSelect2'             => esc_attr__( 'Pick a font...', 'fl-builder' ),
	'pleaseWait'                     => esc_attr__( 'Please Wait...', 'fl-builder' ),
	/* translators: %s: preset color code */
	'presetAdded'                    => esc_attr_x( '%s added to presets!', '%s is the preset hex color code.', 'fl-builder' ),
	'publish'                        => esc_attr__( 'Publish Changes', 'fl-builder' ),
	'remove'                         => esc_attr__( 'Remove', 'fl-builder' ),
	'removePresetConfirm'            => esc_attr__( 'Are you sure?', 'fl-builder' ),
	/* translators: %s: time diff - 1 day/2 weeks */
	'revisionDate'                   => esc_attr_x( '%s ago', '%s is a time diff such as 1 day or 2 weeks.', 'fl-builder' ),
	/* translators: %s: author name */
	'revisionAuthor'                 => esc_attr_x( 'By %s', '%s is the author name.', 'fl-builder' ),
	'row'                            => esc_attr__( 'Row', 'fl-builder' ),
	'rowSettings'                    => esc_attr__( 'Row Settings', 'fl-builder' ),
	'rowTemplateSaved'               => esc_attr__( 'Row Saved!', 'fl-builder' ),
	'saveCoreTemplate'               => esc_attr__( 'Save Core Template', 'fl-builder' ),
	'save'                           => esc_attr__( 'Save', 'fl-builder' ),
	'saveAs'                         => esc_attr__( 'Save As...', 'fl-builder' ),
	'saveColumn'                     => esc_attr__( 'Save Column', 'fl-builder' ),
	'saveModule'                     => esc_attr__( 'Save Module', 'fl-builder' ),
	'saveRow'                        => esc_attr__( 'Save Row', 'fl-builder' ),
	'saveTemplate'                   => esc_attr__( 'Save Template', 'fl-builder' ),
	'selectAudio'                    => esc_attr__( 'Select Audio', 'fl-builder' ),
	'selectPhoto'                    => esc_attr__( 'Select Photo', 'fl-builder' ),
	'selectPhotos'                   => esc_attr__( 'Select Photos', 'fl-builder' ),
	'selectVideo'                    => esc_attr__( 'Select Video', 'fl-builder' ),
	'settingsHaveErrors'             => esc_attr__( 'These settings have errors. Please correct them before continuing.', 'fl-builder' ),
	'submitForReview'                => esc_attr__( 'Submit for Review', 'fl-builder' ),
	'subscriptionModuleAccountError' => esc_attr__( 'Please select an account before saving.', 'fl-builder' ),
	'subscriptionModuleConnectError' => esc_attr__( 'Please connect an account before saving.', 'fl-builder' ),
	'subscriptionModuleListError'    => esc_attr__( 'Please select a list before saving.', 'fl-builder' ),
	'subscriptionModuleTagsError'    => esc_attr__( 'Please enter at least one tag before saving.', 'fl-builder' ),
	'takeHelpTour'                   => esc_attr__( 'Take a Tour', 'fl-builder' ),
	'templateAppend'                 => esc_attr__( 'Append New Layout', 'fl-builder' ),
	'templateReplace'                => esc_attr__( 'Replace Existing Layout', 'fl-builder' ),
	'templateSaved'                  => esc_attr__( 'Template Saved!', 'fl-builder' ),
	'thumbnail'                      => esc_attr__( 'Thumbnail', 'fl-builder' ),
	'tourNext'                       => esc_attr__( 'Next', 'fl-builder' ),
	'tourEnd'                        => esc_attr__( 'Get Started', 'fl-builder' ),
	'tourTemplatesTitle'             => esc_attr__( 'Choose a Template', 'fl-builder' ),
	'tourTemplates'                  => esc_attr__( 'Get started by choosing a layout template to customize, or build a page from scratch by selecting the blank layout template.', 'fl-builder' ),
	'tourAddRowsTitle'               => esc_attr__( 'Add Rows', 'fl-builder' ),
	'tourAddRows'                    => esc_attr__( 'Add multi-column rows, adjust spacing, add backgrounds and more by dragging and dropping row layouts onto the page.', 'fl-builder' ),
	'tourAddContentTitle'            => esc_attr__( 'Add Content', 'fl-builder' ),
	'tourAddContent'                 => esc_attr__( 'Add new content by dragging and dropping modules or widgets into your row layouts or to create a new row layout.', 'fl-builder' ),
	'tourEditContentTitle'           => esc_attr__( 'Edit Content', 'fl-builder' ),
	'tourEditContent'                => esc_attr__( 'Move your mouse over rows, columns or modules to edit and interact with them.', 'fl-builder' ),
	'tourEditContent2'               => esc_attr__( 'Use the action buttons to perform actions such as moving, editing, duplicating or deleting rows, columns and modules.', 'fl-builder' ),
	'tourAddContentButtonTitle'      => esc_attr__( 'Add More Content', 'fl-builder' ),
	'tourAddContentButton'           => esc_attr__( 'Use the Add Content button to open the content panel and add new row layouts, modules or widgets.', 'fl-builder' ),
	'tourTemplatesButtonTitle'       => esc_attr__( 'Change Templates', 'fl-builder' ),
	'tourTemplatesButton'            => esc_attr__( 'Use the Templates button to pick a new template or append one to your layout. Appending will insert a new template at the end of your existing page content.', 'fl-builder' ),
	'tourToolsButtonTitle'           => esc_attr__( 'Helpful Tools', 'fl-builder' ),
	'tourToolsButton'                => esc_attr__( 'The Tools button lets you save a template, duplicate a layout, edit the settings for a layout or edit the global settings.', 'fl-builder' ),
	'tourDoneButtonTitle'            => esc_attr__( 'Publish Your Changes', 'fl-builder' ),
	'tourDoneButton'                 => esc_attr__( "Once you're finished, click the Done button to publish your changes, save a draft or revert back to the last published state.", 'fl-builder' ),
	'tourFinishedTitle'              => esc_attr__( "Let's Get Building!", 'fl-builder' ),
	'tourFinished'                   => esc_attr__( "Now that you know the basics, you're ready to start building! If at any time you need help, click the help icon in the upper right corner to access the help menu. Happy building!", 'fl-builder' ),
	'unloadWarning'                  => esc_attr__( 'The settings you are currently editing will not be saved if you navigate away from this page.', 'fl-builder' ),
	'viewKnowledgeBase'              => esc_attr__( 'View the Knowledge Base', 'fl-builder' ),
	'validateRequiredMessage'        => esc_attr__( 'This field is required.', 'fl-builder' ),
	'schemaAllRequiredMessage'       => esc_attr__( 'All Structured Data fields are required.', 'fl-builder' ),
	'visitForums'                    => esc_attr__( 'Contact Support', 'fl-builder' ),
	'watchHelpVideo'                 => esc_attr__( 'Watch the Video', 'fl-builder' ),
	'welcomeMessage'                 => esc_attr__( 'Welcome! It looks like this might be your first time using the builder. Would you like to take a tour?', 'fl-builder' ),
	'widget'                         => esc_attr__( 'Widget', 'fl-builder' ),
	'widgetsCategoryTitle'           => esc_attr__( 'WordPress Widgets', 'fl-builder' ),
	'uncategorized'                  => esc_attr__( 'Uncategorized', 'fl-builder' ),
	'yesPlease'                      => esc_attr__( 'Yes Please!', 'fl-builder' ),
	'noScriptWarn'                   => array(
		'heading' => esc_attr__( 'Settings could not be saved.', 'fl-builder' ),
		// translators: %s : User Role
		'message' => sprintf( esc_attr__( 'These settings contain sensitive code that is not allowed for your user role (%s).', 'fl-builder' ), FLBuilderUtils::get_current_user_role() ),
		'global'  => esc_attr__( 'These settings contain sensitive code that is not allowed as DISALLOW_UNFILTERED_HTML has been set globally via wp-config.', 'fl-builder' ),
		// translators: %s : Link to Docs
		'footer'  => sprintf( esc_attr__( 'See the %s for more information.', 'fl-builder' ), sprintf( '<a style="color:#00A0D2" target="_blank" href="https://docs.wpbeaverbuilder.com/beaver-builder/troubleshooting/common-issues/error-settings-not-saved">%s</a>', __( 'Knowledge Base', 'fl-builder' ), 'fl-builder' ) ),
	),
	'savedStatus'                    => array(
		'saving'               => esc_attr__( 'Saving...', 'fl-builder' ),
		'savingTooltip'        => esc_attr__( 'The layout is currently being saved', 'fl-builder' ),
		'saved'                => esc_attr__( 'Saved', 'fl-builder' ),
		'savedTooltip'         => esc_attr__( 'The layout is saved', 'fl-builder' ),
		'edited'               => esc_attr__( 'Edited', 'fl-builder' ),
		'editedTooltip'        => esc_attr__( 'This layout has unpublished changes', 'fl-builder' ),
		'editedWarning'        => esc_attr__( 'This layout has unpublished changes. If you discard this draft all of your previously unpublished changes will be lost.', 'fl-builder' ),
		'editedWarningDismiss' => esc_attr__( 'Ok, got it!', 'fl-builder' ),
		'noChanges'            => esc_attr__( 'Nothing new to publish', 'fl-builder' ),
		'publishing'           => esc_attr__( 'Publishing Changes', 'fl-builder' ),
		'publishingTooltip'    => esc_attr__( 'Changes being published', 'fl-builder' ),
		'nothingToSave'        => esc_attr__( 'No new changes to save', 'fl-builder' ),
		'hasAlreadySaved'      => esc_attr__( 'Your changes are saved', 'fl-builder' ),
	),
	'typeLabels'                     => array(
		'template' => esc_attr__( 'Template', 'fl-builder' ),
		'module'   => esc_attr__( 'Module', 'fl-builder' ),
		'row'      => esc_attr__( 'Row', 'fl-builder' ),
		'colGroup' => esc_attr__( 'Column Group', 'fl-builder' ),
		'widget'   => esc_attr__( 'Widget', 'fl-builder' ),
	),
	'categoryMeta'                   => array(
		'landing' => array(
			'name' => esc_attr__( 'Landing Pages', 'fl-builder' ),
		),
		'company' => array(
			'name' => esc_attr__( 'Content Pages', 'fl-builder' ),
		),
	),
	'notifications'                  => array(
		'title'   => esc_attr__( 'Notifications', 'fl-builder' ),
		'loading' => esc_attr__( 'Loading...', 'fl-builder' ),
		'none'    => esc_attr__( 'No Notifications.', 'fl-builder' ),
	),
	'module_import'                  => array(
		'copied' => esc_attr__( 'Copied!', 'fl-builder' ),
		'error'  => esc_attr__( 'Import Error!', 'fl-builder' ),
		'type'   => esc_attr__( 'Missing header or wrong module type!', 'fl-builder' ),
	),
) ) ) . ';';

FLBuilderFonts::js();

?>
</script>
