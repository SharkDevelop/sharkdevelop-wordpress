( function( blocks, element, blockEditor, components, serverSideRender, i18n ) {
	const el = element.createElement;
	const __ = i18n.__;
	const sprintf = i18n.sprintf;
	const InspectorControls = blockEditor.InspectorControls;
	const useBlockProps = blockEditor.useBlockProps;
	const PanelBody = components.PanelBody;
	const SelectControl = components.SelectControl;
	const TextareaControl = components.TextareaControl;
	const TextControl = components.TextControl;
	const ServerSideRender = serverSideRender;
	const iconOptions = [
		{ label: __( 'Target', 'sharkdevelop' ), value: 'target' },
		{ label: __( 'Route', 'sharkdevelop' ), value: 'route' },
		{ label: __( 'Layers', 'sharkdevelop' ), value: 'layers' },
		{ label: __( 'Globe', 'sharkdevelop' ), value: 'globe' },
		{ label: __( 'Spark', 'sharkdevelop' ), value: 'spark' },
		{ label: __( 'Sliders', 'sharkdevelop' ), value: 'sliders' },
	];

	blocks.registerBlockType( 'sharkdevelop/services-benefits', {
		edit: function( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const items = Array.isArray( attributes.items ) ? attributes.items : [];
			const blockProps = useBlockProps( {
				className: 'services-benefits__editor-preview',
			} );
			const updateItem = function( index, field, value ) {
				const nextItems = items.map( function( item, itemIndex ) {
					if ( itemIndex !== index ) {
						return item;
					}

					return Object.assign( {}, item, { [ field ]: value } );
				} );

				setAttributes( { items: nextItems } );
			};

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
							{ title: __( 'Benefits section', 'sharkdevelop' ), initialOpen: true },
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
						),
						items.map( function( item, index ) {
							return el(
								PanelBody,
								{
									title: sprintf( __( 'Benefit %d', 'sharkdevelop' ), index + 1 ),
									initialOpen: false,
									key: index,
								},
								el( SelectControl, {
									label: __( 'Icon', 'sharkdevelop' ),
									value: item.icon,
									options: iconOptions,
									onChange: function( icon ) {
										updateItem( index, 'icon', icon );
									},
								} ),
								el( TextControl, {
									label: __( 'Heading', 'sharkdevelop' ),
									value: item.title,
									onChange: function( title ) {
										updateItem( index, 'title', title );
									},
								} ),
								el( TextareaControl, {
									label: __( 'Description', 'sharkdevelop' ),
									value: item.description,
									onChange: function( description ) {
										updateItem( index, 'description', description );
									},
								} )
							);
						} )
					),
					el( ServerSideRender, {
						block: 'sharkdevelop/services-benefits',
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
