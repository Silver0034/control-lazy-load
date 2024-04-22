// Immediately Invoked Function Expression (IIFE) to avoid polluting the global namespace
;(function (wp) {
	// Importing necessary functions and components from WordPress packages
	var el = wp.element.createElement
	var ToggleControl = wp.components.ToggleControl
	var PanelBody = wp.components.PanelBody

	// Function to add a new attribute to the image block
	var addAttribute = function (settings, name) {
		// If the block is not an image block, return the original settings
		if (name !== 'core/image') {
			return settings
		}

		// Add the 'disableLazyLoad' attribute to the image block
		settings.attributes = Object.assign(settings.attributes, {
			disableLazyLoad: {
				type: 'boolean',
				default: false
			}
		})

		return settings
	}

	// Subscribe to changes in the post meta
	var unsubscribe = wp.data.subscribe(function () {
		// Get the current value of the post meta
		var meta = wp.data.select('core/editor').getEditedPostAttribute('meta')
		var newMetaValue

		if (meta) {
			newMetaValue = meta[jlcllBlockData.postEditorKey]
		}

		// If the value of the post meta has changed
		if (newMetaValue !== jlcllBlockData.disabledPerPost) {
			// Update the value of the post meta
			jlcllBlockData.disabledPerPost = newMetaValue

			// Get the currently selected block
			var selectedBlock = wp.data
				.select('core/block-editor')
				.getSelectedBlock()

			if (selectedBlock) {
				// Update a block attribute to force a re-render
				wp.data
					.dispatch('core/block-editor')
					.updateBlockAttributes(selectedBlock.clientId, {
						updated: Date.now()
					})
			}
		}
	})

	// Add the 'disableLazyLoad' attribute to the image block
	wp.hooks.addFilter(
		'blocks.registerBlockType',
		'control-lazy-load/add-attribute',
		addAttribute
	)

	// Higher-order component to add a new control to the image block's settings
	var withInspectorControls = wp.compose.createHigherOrderComponent(function (
		BlockEdit
	) {
		return function (props) {
			var isSelected = props.isSelected
			var attributes = props.attributes
			var setAttributes = props.setAttributes

			var isDisabledSiteWide = jlcllBlockData.disabledSiteWide === '1'
			var isDisabledPost = jlcllBlockData.disabledPerPost === 'true'

			// If the block is not an image block, return the original BlockEdit component
			if (props.name !== 'core/image') {
				return el(BlockEdit, props)
			}

			// Return the original BlockEdit component and the new control
			return el(
				wp.element.Fragment,
				{},
				el(BlockEdit, props),
				isSelected &&
					el(
						wp.blockEditor.InspectorControls,
						{},
						el(
							PanelBody,
							{
								title: 'Control Lazy Loading',
								initialOpen: true
							},
							isDisabledSiteWide &&
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
							isDisabledPost &&
								el(
									'p',
									{},
									'Lazy loading is currently disabled across the entire post. To enable this option, please use the checkbox in the post settings.'
								),
							!isDisabledSiteWide &&
								!isDisabledPost &&
								el(
									'p',
									{},
									'This setting disables lazy loading for this image. To disable lazy loading for the entire post, use the "Disable lazy loading" option in the post settings.'
								),
							el(ToggleControl, {
								label: 'Disable lazy load for this image?',
								checked: attributes.disableLazyLoad,
								onChange: function (content) {
									setAttributes({ disableLazyLoad: content })
								},
								disabled: isDisabledSiteWide || isDisabledPost
							})
						)
					)
			)
		}
	},
	'withInspectorControls')

	// Add the new control to the image block's settings
	wp.hooks.addFilter(
		'editor.BlockEdit',
		'control-lazy-load/with-inspector-controls',
		withInspectorControls
	)

	// Add the 'loading="eager"' attribute to the image block's save function
	wp.hooks.addFilter(
		'blocks.getSaveContent.extraProps',
		'control-lazy-load/add-data-attribute',
		function (extraProps, blockType, attributes) {
			if (blockType.name !== 'core/image') {
				return extraProps
			}

			if (attributes.disableLazyLoad) {
				extraProps['loading'] = 'eager'
			}

			return extraProps
		}
	)
})(window.wp)
