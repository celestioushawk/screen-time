import { useBlockProps, RichText } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';

const Save = (props) => {
	const {
		attributes: {
			postDate,
			postExcerpt,
			imageUrl,
			postTitle,
			postShowExcerpt,
			postShowDate,
			postContentOrientation,
		},
	} = props;
	const blockProps = useBlockProps.save();
	return (
		<div {...blockProps}>
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
				</div>
				<div className="post-details">
					<div className="post-title">
						<h3>
							<RichText.Content
								value={postTitle}
								placeholder={__(
									'Enter title here',
									'custom-gutenberg'
								)}
							/>
						</h3>
					</div>
					<div className="post-date">
						{postShowDate && (
							<h5>
								<RichText.Content
									value={postDate}
									placeholder={__(
										'Enter date here',
										'custom-gutenberg'
									)}
								/>
							</h5>
						)}
					</div>
					<div className="post-excerpt">
						{postShowExcerpt && (
							<p>
								<RichText.Content
									value={postExcerpt}
									placeholder={__(
										'Enter excerpt here',
										'custom-gutenberg'
									)}
								/>
							</p>
						)}
					</div>
				</div>
			</div>
		</div>
	);
};

export default Save;
