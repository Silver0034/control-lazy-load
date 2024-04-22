// Immediately Invoked Function Expression (IIFE) to avoid polluting the global namespace
;(function (wp) {
	// Importing necessary functions and components from WordPress packages
	var el = React.createElement
	var registerPlugin = wp.plugins.registerPlugin
	var PluginDocumentSettingPanel = wp.editPost.PluginDocumentSettingPanel
	var ToggleControl = wp.components.ToggleControl
	var useSelect = wp.data.useSelect
	var useDispatch = wp.data.useDispatch

	// This component represents the field in the document settings panel
	var MetaBlockField = function (props) {
		// Using the useSelect hook to get the current value of the meta field
		var metaFieldValue = useSelect(function (select) {
			// Getting the value of the meta field from the post attributes
			var value =
				select('core/editor').getEditedPostAttribute('meta')[
					jlcllData.postEditorKey
				]
			// Converting the string value to a boolean
			return value === 'true' ? true : false
		}, [])

		// Using the useDispatch hook to get the editPost action
		var editPost = useDispatch('core/editor').editPost

		// Checking if lazy loading is disabled site-wide
		var isDisabled = jlcllData.disabledSiteWide === '1'

		// Returning the elements to be rendered
		return [
			// If lazy loading is disabled site-wide, show a message with a link to the settings page
			isDisabled &&
				el(
					'p',
					{},
					'Lazy loading is currently disabled site-wide. To enable this option, please ',
					el(
						'a',
						{
							href: '/wp-admin/options-general.php?page=control-lazy-load'
						},
						'turn off the site-wide disable.'
					)
				),
			// If lazy loading is not disabled site-wide, show a message about the setting
			!isDisabled &&
				el(
					'p',
					{},
					'This setting disables lazy loading for the entire post. To disable lazy loading for specific images, use the "Disable lazy loading" option in the image block\'s settings.'
				),
			// The toggle control for the meta field
			el(ToggleControl, {
				label: 'Disable lazy load for this post?',
				checked: metaFieldValue,
				onChange: function (content) {
					// When the toggle control is changed, update the meta field
					editPost({
						meta: {
							[jlcllData.postEditorKey]: content
								? 'true'
								: 'false' // Convert the boolean to a string
						}
					})
				},
				disabled: isDisabled
			})
		]
	}

	// Registering the plugin
	registerPlugin('jlcll-post-setting-panel', {
		render: function () {
			// Rendering the PluginDocumentSettingPanel with the MetaBlockField
			return el(
				PluginDocumentSettingPanel,
				{
					name: 'jlcll-post-setting-panel',
					title: 'Control Lazy Load'
				},
				el(MetaBlockField)
			)
		}
	})
})(window.wp)
