( function( blocks, blockEditor, components, data, element, serverSideRender, i18n ) {
	const el = element.createElement;
	const __ = i18n.__;
	const InspectorControls = blockEditor.InspectorControls;
	const useBlockProps = blockEditor.useBlockProps;
	const PanelBody = components.PanelBody;
	const TextControl = components.TextControl;
	const CheckboxControl = components.CheckboxControl;
	const Spinner = components.Spinner;
	const useSelect = data.useSelect;
	const ServerSideRender = serverSideRender;

	blocks.registerBlockType( 'sharkdevelop/selected-projects', {
		edit: function( props ) {
			const attributes = props.attributes;
			const setAttributes = props.setAttributes;
			const selectedIds = Array.isArray( attributes.projectIds ) ? attributes.projectIds.map( Number ) : [];
			const projects = useSelect( function( select ) {
				return select( 'core' ).getEntityRecords( 'postType', 'sd_project', {
					per_page: 100,
					orderby: 'title',
					order: 'asc',
					status: 'publish',
				} );
			}, [] );
			const toggleProject = function( projectId, checked ) {
				const nextIds = checked
					? selectedIds.concat( projectId )
					: selectedIds.filter( function( id ) { return id !== projectId; } );

				setAttributes( { projectIds: nextIds } );
			};

			return el(
				'div',
				useBlockProps( { className: 'selected-projects__editor-preview' } ),
				el(
					element.Fragment,
					null,
					el(
						InspectorControls,
						null,
						el(
							PanelBody,
							{ title: __( 'Section heading', 'sharkdevelop' ), initialOpen: true },
							el( TextControl, {
								label: __( 'Eyebrow', 'sharkdevelop' ),
								value: attributes.eyebrow,
								onChange: function( eyebrow ) { setAttributes( { eyebrow: eyebrow } ); },
							} ),
							el( TextControl, {
								label: __( 'Heading', 'sharkdevelop' ),
								value: attributes.title,
								onChange: function( title ) { setAttributes( { title: title } ); },
							} )
						),
						el(
							PanelBody,
							{ title: __( 'Projects', 'sharkdevelop' ), initialOpen: true },
							! projects && el( Spinner ),
							projects && projects.map( function( project ) {
								const projectId = Number( project.id );
								return el( CheckboxControl, {
									key: projectId,
									label: project.title.rendered || __( '(Untitled project)', 'sharkdevelop' ),
									checked: selectedIds.indexOf( projectId ) !== -1,
									onChange: function( checked ) { toggleProject( projectId, checked ); },
								} );
							} )
						)
					),
					el( ServerSideRender, {
						block: 'sharkdevelop/selected-projects',
						attributes: attributes,
					} )
				)
			);
		},
		save: function() {
			return null;
		},
	} );
} )( window.wp.blocks, window.wp.blockEditor, window.wp.components, window.wp.data, window.wp.element, window.wp.serverSideRender, window.wp.i18n );
