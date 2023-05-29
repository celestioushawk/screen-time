import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { PanelBody, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useState, useEffect } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
/**
 * Edit function to render the block on the editor along with its controls.
 *
 * @param {Object} props Props object containing the attributes of the block.
 * @return {WPElement} Element to render.
 */
const Edit = (props) => {
	const {
		attributes: { movieSearch },
		setAttributes,
	} = props;

	// Fetch movies based on movie search field value.
	const { posts: movieSearchList } = useSelect(
		(select) => {
			return {
				posts: select('core').getEntityRecords('postType', 'rt-movie', {
					search: movieSearch,
				}),
			};
		},
		[movieSearch]
	);

	// Set the movie search element.
	const setMovieSearch = (newMovieSearch) => {
		setAttributes({ movieSearch: newMovieSearch });

		for (let i = 0; i < movieSearchList?.length; i++) {
			if (movieSearchList[i].title.rendered === newMovieSearch) {
				setAttributes({
					movieId: movieSearchList[i].id,
				});
				break;
			}
		}
	};

	const [movieSearchOptions, setMovieSearchOptions] = useState([]);

	// Update the movie search options once the movie search list updates
	useEffect(() => {
		setMovieSearchOptions(
			movieSearchList?.map((post) => ({
				label: post.title.rendered,
				value: post.title.rendered,
			}))
		);
	}, [movieSearchList]);

	return (
		<div {...useBlockProps()}>
			<InspectorControls key="setting">
				<PanelBody>
					<div className="ml-movie-fields">
						<fieldset>
							<TextControl
								label={__('Movie Search', 'movie-library')}
								value={movieSearch}
								__nextHasNoMarginBottom
								onChange={setMovieSearch}
								list={'movie-data-list'}
							/>
							<datalist id="movie-data-list">
								{movieSearchOptions?.map((searchOption) => {
									return (
										<option
											value={searchOption?.value}
											key={
												Date.now().toString(36) +
												Math.random()
													.toString(36)
													.substring(2)
											}
										>
											{searchOption?.label}
										</option>
									);
								})}
							</datalist>
						</fieldset>
					</div>
				</PanelBody>
			</InspectorControls>
			<ServerSideRender
				block={metadata.name}
				attributes={props.attributes}
			/>
		</div>
	);
};
export default Edit;
