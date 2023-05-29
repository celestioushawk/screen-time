import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
	SelectControl,
	PanelBody,
	TextControl,
	Spinner,
} from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import ServerSideRender from '@wordpress/server-side-render';
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
		attributes: {
			movieDirector,
			movieLabel,
			movieGenre,
			movieLanguage,
			movieCount,
		},
		setAttributes,
	} = props;

	// Set the movie director attribute.
	const onSelectMovieDirector = (newMovieDirector) => {
		setAttributes({ movieDirector: newMovieDirector });
	};
	// Set movie label attribute.
	const onSelectMovieLabel = (newMovieLabel) => {
		setAttributes({ movieLabel: newMovieLabel });
	};
	// Set movie genre attribute.
	const onSelectMovieGenre = (newMovieGenre) => {
		setAttributes({ movieGenre: newMovieGenre });
	};
	// Set movie language attribute.
	const onSelectMovieLanguage = (newMovieLanguage) => {
		setAttributes({ movieLanguage: newMovieLanguage });
	};
	// Set movie count attribute.
	const setMovieCount = (newMovieCount) => {
		setAttributes({ movieCount: newMovieCount });
	};

	const { posts: movieDirectors } = useSelect((select) => {
		return {
			posts: select('core').getEntityRecords(
				'taxonomy',
				'rt-person-career',
				{
					slug: 'director',
					_fields: 'id',
				}
			),
		};
	}, []);

	const { posts: movieDirectorsList } = useSelect(
		(select) => {
			return {
				posts: select('core').getEntityRecords(
					'postType',
					'rt-person',
					{
						'rt-person-career':
							movieDirectors && movieDirectors[0].id,
					}
				),
			};
		},
		[movieDirectors]
	);

	const { posts: movieLabels } = useSelect((select) => {
		return {
			posts: select('core').getEntityRecords(
				'taxonomy',
				'rt-movie-label'
			),
		};
	}, []);

	const { posts: movieGenres } = useSelect((select) => {
		return {
			posts: select('core').getEntityRecords(
				'taxonomy',
				'rt-movie-genre'
			),
		};
	}, []);

	const { posts: movieLanguages } = useSelect((select) => {
		return {
			posts: select('core').getEntityRecords(
				'taxonomy',
				'rt-movie-language'
			),
		};
	}, []);

	const defaultOption = {
		label: 'All',
		value: '',
	};

	let movieDirectorOptions = [];
	let movieGenreOptions = [];
	let movieLabelOptions = [];
	let movieLanguageOptions = [];

	movieDirectorOptions = movieDirectorsList?.map((post) => ({
		label: post.title.rendered,
		value: post.id,
	}));

	movieGenreOptions = movieGenres?.map((post) => ({
		label: post.name,
		value: post.id,
	}));

	movieLabelOptions = movieLabels?.map((post) => ({
		label: post.name,
		value: post.id,
	}));

	movieLanguageOptions = movieLanguages?.map((post) => ({
		label: post.name,
		value: post.id,
	}));

	const allOptions = [
		movieGenreOptions,
		movieDirectorOptions,
		movieLabelOptions,
		movieLanguageOptions,
	];

	allOptions.map((options) => options?.unshift(defaultOption));

	return (
		<div {...useBlockProps()}>
			<InspectorControls key="setting">
				<PanelBody>
					<div className="ml-movie-fields">
						<fieldset>
							<TextControl
								label={__('Movie Count', 'movie-library')}
								value={movieCount}
								__nextHasNoMarginBottom
								onChange={(mCount) => setMovieCount(mCount)}
							/>
						</fieldset>
						<fieldset>
							{movieDirectorOptions ? (
								<SelectControl
									label={__(
										'Movie Directors',
										'movie-library'
									)}
									value={movieDirector}
									options={movieDirectorOptions}
									onChange={(mDirector) => {
										onSelectMovieDirector(mDirector);
									}}
								/>
							) : (
								<Spinner />
							)}
						</fieldset>
						<fieldset>
							{movieGenreOptions ? (
								<SelectControl
									label={__('Movie Genres', 'movie-library')}
									value={movieGenre}
									options={movieGenreOptions}
									onChange={(mGenre) => {
										onSelectMovieGenre(mGenre);
									}}
								/>
							) : (
								<Spinner />
							)}
						</fieldset>
						<fieldset>
							{movieLabelOptions ? (
								<SelectControl
									label={__('Movie Labels', 'movie-library')}
									value={movieLabel}
									options={movieLabelOptions}
									onChange={(mLabel) => {
										onSelectMovieLabel(mLabel);
									}}
								/>
							) : (
								<Spinner />
							)}
						</fieldset>
						<fieldset>
							{movieLanguageOptions ? (
								<SelectControl
									label={__(
										'Movie Languages',
										'movie-library'
									)}
									value={movieLanguage}
									options={movieLanguageOptions}
									onChange={(mLanguage) => {
										onSelectMovieLanguage(mLanguage);
									}}
								/>
							) : (
								<Spinner />
							)}
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
