jQuery(document).ready(function () {
	let ids = [];
	let urls = [];
	const addButton = document.getElementById('video-upload-btn');
	const { __ } = wp.i18n;
	let customUploader = null;

	const hidden = document.getElementById('video-hidden-id-field').value;
	const hiddenUrls = document.getElementById('video-hidden-url-field').value;
	const videobox = document.getElementById('video-box-wrapper');

	const displayVideos = (videoID, videoUrl) => {
		if (videoID && videoUrl) {
			const div = document.createElement('div');
			const video = document.createElement('video');
			video.controls = true;
			video.autoplay = true;
			video.setAttribute('type', 'video/' + videoUrl.split('.').at(-1));
			const source = document.createElement('source');
			const deleteBtn = document.createElement('button');
			video.setAttribute('id', videoID);
			source.setAttribute('src', videoUrl);
			deleteBtn.setAttribute('id', videoID);
			deleteBtn.textContent = 'Delete';

			div.appendChild(video);
			video.appendChild(source);
			div.appendChild(deleteBtn);

			deleteBtn.addEventListener('click', () => {
				video.remove();
				ids = ids.filter((id) => id !== video.id);
				urls = urls.filter((url) => url !== video.src);
				document.getElementById('video-hidden-id-field').value =
					ids.join(',');
				document.getElementById('video-hidden-url-field').value =
					urls.join(',');
				deleteBtn.remove();
			});
			videobox.appendChild(div);
		}
	};

	if (hidden.length > 0 && hiddenUrls.length > 0) {
		ids = hidden.split(',');
		urls = hiddenUrls.split(',');
		for (let i = 0; i < ids.length; i++) {
			displayVideos(ids[i], urls[i]);
		}
	}

	addButton.addEventListener('click', function () {
		if (!customUploader) {
			customUploader = wp.media({
				title: __('Select a Video', 'movie-library'),
				button: {
					text: __('Use this video', 'movie-library'),
				},
				multiple: true,
				library: {
					type: ['video'],
				},
			});
			customUploader.open();
			customUploader.on('select', function () {
				const attachment = customUploader
					.state()
					.get('selection')
					.toJSON();
				attachment.forEach((att) => {
					if (0 !== att.id) {
						ids.push(att.id);
						urls.push(att.url);
						document.getElementById('video-hidden-id-field').value =
							ids;
						document.getElementById(
							'video-hidden-url-field'
						).value = urls;
						displayVideos(att.id, att.url);
					}
				});
			});
		} else {
			customUploader.open();
		}
	});
});
