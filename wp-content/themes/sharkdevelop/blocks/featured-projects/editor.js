(function (blocks, blockEditor, element, i18n) {
	const { registerBlockType } = blocks;
	const { useBlockProps } = blockEditor;
	const { createElement } = element;
	const { __ } = i18n;

	registerBlockType('sharkdevelop/featured-projects', {
		edit() {
			return createElement(
				'div',
				useBlockProps({ className: 'sd-featured-projects-placeholder' }),
				__('Projects marked "Show on homepage" appear here.', 'sharkdevelop')
			);
		},
		save() {
			return null;
		},
	});
})(window.wp.blocks, window.wp.blockEditor, window.wp.element, window.wp.i18n);
