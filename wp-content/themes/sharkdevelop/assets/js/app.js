(() => {
	document.documentElement.classList.add('js');

	const updateHeaderState = () => {
		document.body.classList.toggle('has-scrolled', window.scrollY > 0);
	};

	updateHeaderState();
	window.addEventListener('scroll', updateHeaderState, { passive: true });
})();
