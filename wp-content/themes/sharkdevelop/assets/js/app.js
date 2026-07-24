(() => {
	document.documentElement.classList.add('js');

	const updateHeaderState = () => {
		document.body.classList.toggle('has-scrolled', window.scrollY > 0);
	};

	const waveSections = [...document.querySelectorAll('.services-section')];
	let waveArtFrame = 0;

	const clamp = (value, min, max) => Math.min(Math.max(value, min), max);
	const wavePath = [
		[
			{ x: -60, y: 132 },
			{ x: 114, y: 38 },
			{ x: 270, y: 7 },
			{ x: 456, y: 56 },
		],
		[
			{ x: 456, y: 56 },
			{ x: 607, y: 96 },
			{ x: 676, y: 191 },
			{ x: 832, y: 204 },
		],
		[
			{ x: 832, y: 204 },
			{ x: 1050, y: 223 },
			{ x: 1145, y: 74 },
			{ x: 1344, y: 96 },
		],
		[
			{ x: 1344, y: 96 },
			{ x: 1473, y: 110 },
			{ x: 1553, y: 169 },
			{ x: 1674, y: 133 },
		],
	];

	const sampleCubic = ([start, controlA, controlB, end], position) => {
		const inverse = 1 - position;
		const inverse2 = inverse * inverse;
		const position2 = position * position;

		return {
			x:
				inverse2 * inverse * start.x +
				3 * inverse2 * position * controlA.x +
				3 * inverse * position2 * controlB.x +
				position2 * position * end.x,
			y:
				inverse2 * inverse * start.y +
				3 * inverse2 * position * controlA.y +
				3 * inverse * position2 * controlB.y +
				position2 * position * end.y,
		};
	};

	const sampleWavePath = (progress) => {
		const pathProgress = clamp(progress, 0, 1) * wavePath.length;
		const segmentIndex = Math.min(Math.floor(pathProgress), wavePath.length - 1);
		const segmentProgress = pathProgress - segmentIndex;
		const point = sampleCubic(wavePath[segmentIndex], segmentProgress);
		const nextPoint = sampleCubic(wavePath[segmentIndex], clamp(segmentProgress + 0.02, 0, 1));
		const angle = (Math.atan2(nextPoint.y - point.y, nextPoint.x - point.x) * 180) / Math.PI;

		return {
			x: (point.x / 1600) * 100,
			y: 310 - point.y,
			rotate: angle,
		};
	};

	const updateWaveArt = () => {
		waveArtFrame = 0;

		if (!waveSections.length) {
			return;
		}

		waveSections.forEach((section) => {
			const rect = section.getBoundingClientRect();
			const waveTop = rect.bottom - 120;
			const waveFinishTop = -130;
			const travel = window.innerHeight - waveFinishTop;
			const progress = clamp((window.innerHeight - waveTop) / travel, 0, 1);
			const eased = 0.5 - Math.cos(progress * Math.PI) / 2;
			const point = sampleWavePath(eased);
			const verticalScale = clamp(rect.width / 1600, 0.62, 1);
			const waveMiddle = 155;
			const startDrop = Math.max(0, 1 - eased / 0.42) * 48;
			const firstArcDrop = Math.sin(clamp((eased - 0.06) / 0.46, 0, 1) * Math.PI) * 28;
			const x = point.x;
			const y = waveMiddle + (point.y - waveMiddle - startDrop - firstArcDrop) * verticalScale;
			const rotate = point.rotate;

			section.style.setProperty('--sd-wave-progress', eased.toFixed(3));
			section.style.setProperty('--sd-shark-fin-x', `${x.toFixed(2)}%`);
			section.style.setProperty('--sd-shark-fin-y', `${y.toFixed(2)}px`);
			section.style.setProperty('--sd-shark-fin-rotate', `${rotate.toFixed(2)}deg`);
		});
	};

	const requestWaveArtUpdate = () => {
		if (waveArtFrame) {
			return;
		}

		waveArtFrame = window.requestAnimationFrame(updateWaveArt);
	};

	updateHeaderState();
	updateWaveArt();
	window.addEventListener('scroll', updateHeaderState, { passive: true });
	window.addEventListener('scroll', requestWaveArtUpdate, { passive: true });
	window.addEventListener('resize', requestWaveArtUpdate);
})();
