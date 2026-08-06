( function( blocks, element, blockEditor, components, serverSideRender, i18n ) {
	const el = element.createElement;
	const __ = i18n.__;
	const InspectorControls = blockEditor.InspectorControls;
	const useBlockProps = blockEditor.useBlockProps;
	const PanelBody = components.PanelBody;
	const TextControl = components.TextControl;
	const ServerSideRender = serverSideRender;

	blocks.registerBlockType( 'sharkdevelop/services-list', {
		edit: function( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const blockProps = useBlockProps( {
				className: 'services-listing__editor-preview',
			} );

			return el(
				'div',
				blockProps,
				el(
					element.Fragment,
					null,
					el(
						InspectorControls,
						null,
						el(
							PanelBody,
							{ title: __( 'Services list settings', 'sharkdevelop' ), initialOpen: true },
							el( TextControl, {
								label: __( 'Eyebrow', 'sharkdevelop' ),
								value: attributes.eyebrow,
								onChange: function( eyebrow ) {
									setAttributes( { eyebrow: eyebrow } );
								},
							} ),
							el( TextControl, {
								label: __( 'Heading', 'sharkdevelop' ),
								value: attributes.title,
								onChange: function( title ) {
									setAttributes( { title: title } );
								},
							} )
						)
					),
					el( ServerSideRender, {
						block: 'sharkdevelop/services-list',
						attributes: attributes,
					} )
				)
			);
		},
		save: function() {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.serverSideRender, window.wp.i18n );
