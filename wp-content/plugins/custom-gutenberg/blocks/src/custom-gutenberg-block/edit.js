import {
	useBlockProps,
	InspectorControls,
	RichText,
	MediaUpload,
} from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import {
	PanelBody,
	Button,
	SearchControl,
	ToggleControl,
	SelectControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import './style.scss';
/**
 * Edit function to render the block on the editor along with its controls.
 *
 * @param {Object} props Props object containing the attributes of the block.
 * @return {WPElement} Element to render.
 */
const Edit = (props) => {
	const {
		attributes: {
			postSearch,
			postDate,
			postExcerpt,
			postImage,
			imageUrl,
			postTitle,
			postID,
			postShowExcerpt,
			postShowDate,
			postContentOrientation,
		},
		setAttributes,
	} = props;

	// Get posts from database.
	const { posts: postSearchList } = useSelect(
		(select) => {
			return {
				posts: select('core').getEntityRecords('postType', 'post', {
					search: postSearch,
					_embed: true,
				}),
			};
		},
		[postSearch]
	);

	/**
	 * Set post attributes after comparing all the options and finding one that matches.
	 *
	 * @param {string} newPostSearch The new post search input value
	 */
	const setPostSearch = (newPostSearch) => {
		setAttributes({ postSearch: newPostSearch });
		for (let i = 0; i < postSearchList?.length; i++) {
			if (postSearchList[i].title.rendered === newPostSearch) {
				setAttributes({
					postID: postSearchList[i].id,
					postSearch: newPostSearch,
					postTitle: postSearchList[i].title.rendered,
					postDate: new Date(
						postSearchList[i].date
					).toLocaleDateString(),
					postExcerpt: postSearchList[i].excerpt.raw,
					postImage: postSearchList[i].featured_media,
				});
				setAttributes({
					imageUrl:
						postSearchList[i]._embedded['wp:featuredmedia'][0]
							.source_url,
				});
				break;
			}
		}
	};

	// Update the post title.
	const onChangeSetPostTitle = (newPostTitle) => {
		setAttributes({ postTitle: newPostTitle });
	};
	// Update the post date.
	const onChangeSetPostDate = (newPostDate) => {
		setAttributes({ postDate: newPostDate });
	};
	// Update the post excerpt.
	const onChangeSetPostExcerpt = (newPostExcerpt) => {
		setAttributes({ postExcerpt: newPostExcerpt });
	};

	// Set show excerpt flag.
	const togglePostExceprt = (value) => {
		setAttributes({ postShowExcerpt: value });
	};
	// Set publish date flag.
	const togglePublishDate = (value) => {
		setAttributes({ postShowDate: value });
	};
	// Set post orientation flag.
	const togglePostOrientation = (value) => {
		setAttributes({ postContentOrientation: value });
	};
	// Get post image url
	const postImageUrl = useSelect(
		(select) => {
			return select('core').getMedia(postImage);
		},
		[postImage]
	);
	// Set post image id.
	const onChangeImage = (value) => {
		setAttributes({ postImage: value.id });
		setAttributes({ imageUrl: value.url });
	};

	const blockProps = useBlockProps({ className: 'reverse-content' });

	return (
		<div {...blockProps}>
			<InspectorControls key="setting">
				<PanelBody>
					<div className="wp-block-create-block-custom-gutenberg">
						<fieldset>
							<SearchControl
								label={__('Post Search', 'custom-gutenberg')}
								value={postSearch}
								onChange={setPostSearch}
								list={'post-data-list'}
							/>
							<datalist id="post-data-list">
								{postSearchList
									?.map((post) => ({
										label: post.title.rendered,
										value: post.title.rendered,
									}))
									?.map((searchOption) => {
										return (
											<option
												key={
													Date.now().toString(36) +
													Math.random()
														.toString(36)
														.substring(2)
												}
												value={searchOption?.value}
											>
												{searchOption?.label}
											</option>
										);
									})}
							</datalist>
						</fieldset>
						<fieldset>
							<ToggleControl
								label={__('Show Excerpt', 'custom-gutenberg')}
								checked={postShowExcerpt}
								onChange={(value) => togglePostExceprt(value)}
							/>
						</fieldset>
						<fieldset>
							<ToggleControl
								label={__(
									'Show Publish Date',
									'custom-gutenberg'
								)}
								checked={postShowDate}
								onChange={(value) => togglePublishDate(value)}
							/>
						</fieldset>
						<fieldset>
							<SelectControl
								label={__(
									'Display image and content',
									'custom-gutenberg'
								)}
								value={postContentOrientation}
								options={[
									{
										label: __(
											'Left to Right',
											'custom-gutenberg'
										),
										value: 'leftright',
									},
									{
										label: __(
											'Right to Left',
											'custom-gutenberg'
										),
										value: 'rightleft',
									},
								]}
								onChange={(value) =>
									togglePostOrientation(value)
								}
							/>
						</fieldset>
					</div>
				</PanelBody>
			</InspectorControls>
			{postID ? (
				<div
					className={
						postContentOrientation === 'leftright'
							? 'post-container'
							: 'post-container reverse-content'
					}
				>
					<div className="post-thumbnail">
						{imageUrl && (
							<img src={imageUrl} alt="Featured Thumbnail" />
						)}
						<MediaUpload
							type="image"
							onSelect={onChangeImage}
							value={postImageUrl}
							render={({ open }) => (
								<Button className={postImage} onClick={open}>
									{__('Upload Image', 'custom-gutenberg')}
								</Button>
							)}
						/>
					</div>
					<div className="post-details">
						<div className="post-title">
							<RichText
								value={postTitle}
								onChange={onChangeSetPostTitle}
								placeholder={__(
									'Enter title here',
									'custom-gutenberg'
								)}
								tagName="h3"
							/>
						</div>
						<div className="post-date">
							{postShowDate && (
								<RichText
									value={postDate}
									onChange={onChangeSetPostDate}
									placeholder={__(
										'Enter date here',
										'custom-gutenberg'
									)}
									tagName="h5"
								/>
							)}
						</div>
						<div className="post-excerpt">
							{postShowExcerpt && (
								<RichText
									value={postExcerpt}
									onChange={onChangeSetPostExcerpt}
									placeholder={__(
										'Enter excerpt here',
										'custom-gutenberg'
									)}
								/>
							)}
						</div>
					</div>
				</div>
			) : (
				<p>{__('No Posts to Show', 'custom-gutenberg')}</p>
			)}
		</div>
	);
};
export default Edit;
