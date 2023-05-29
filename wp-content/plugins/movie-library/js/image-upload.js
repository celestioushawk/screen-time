jQuery(document).ready(function () {
	let ids = [];
	let urls = [];
	const addButton = document.getElementById('image-upload-btn');
	const { __ } = wp.i18n;
	let customUploader = null;

	const hidden = document.getElementById('img-hidden-id-field').value;
	const hiddenUrls = document.getElementById('img-hidden-url-field').value;
	const imagebox = document.getElementById('meta-box-wrapper');

	const displayImages = (imageId, imageUrl) => {
		if (imageId && imageUrl) {
			const div = document.createElement('div');
			const img = document.createElement('img');
			const deleteBtn = document.createElement('button');
			img.setAttribute('src', imageUrl);
			img.setAttribute('id', imageId);
			deleteBtn.setAttribute('id', imageId);
			deleteBtn.textContent = __('Delete Image', 'movie-library');

			div.appendChild(img);
			div.appendChild(deleteBtn);

			deleteBtn.addEventListener('click', () => {
				ids = ids.filter((id) => id !== img.id);
				urls = urls.filter((url) => url !== img.src);
				document.getElementById('img-hidden-id-field').value =
					ids.join(',');
				document.getElementById('img-hidden-url-field').value =
					urls.join(',');
				img.remove();
				deleteBtn.remove();
			});
			imagebox.appendChild(div);
		}
	};
	if (hidden.length > 0 && hiddenUrls.length > 0) {
		ids = hidden.split(',');
		urls = hiddenUrls.split(',');
		for (let i = 0; i < ids.length; i++) {
			displayImages(ids[i], urls[i]);
		}
	}

	addButton.addEventListener('click', function () {
		if (!customUploader) {
			customUploader = wp.media({
				title: __('Select an Image', 'movie-library'),
				button: {
					text: __('Use this image', 'movie-library'),
				},
				multiple: true,
				library: {
					type: ['image'],
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
						document.getElementById('img-hidden-id-field').value =
							ids;
						document.getElementById('img-hidden-url-field').value =
							urls;
						displayImages(att.id, att.url);
					}
				});
			});
		} else {
			customUploader.open();
		}
	});
});
