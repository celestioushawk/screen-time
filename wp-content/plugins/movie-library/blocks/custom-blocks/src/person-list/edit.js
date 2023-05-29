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
		attributes: { personCareer, personCount },
		setAttributes,
	} = props;

	// Set the person career attribute.
	const onSelectPersonCareer = (newPersonCareer) => {
		setAttributes({ personCareer: newPersonCareer });
	};
	// Set the person count attribute.
	const setPersonCount = (newPersonCount) => {
		setAttributes({ personCount: newPersonCount });
	};

	const { posts: personCareers } = useSelect((select) => {
		return {
			posts: select('core').getEntityRecords(
				'taxonomy',
				'rt-person-career'
			),
		};
	}, []);

	const defaultOption = {
		label: 'All',
		value: '',
	};

	let personCareerOptions = [];
	personCareerOptions = personCareers?.map((post) => ({
		label: post.name,
		value: post.id,
	}));

	if (personCareerOptions) {
		personCareerOptions.unshift(defaultOption);
	}

	return (
		<div {...useBlockProps()}>
			<InspectorControls key="setting">
				<PanelBody>
					<div className="ml-movie-fields">
						<fieldset>
							<TextControl
								label={__('Person Count', 'movie-library')}
								value={personCount}
								__nextHasNoMarginBottom
								onChange={(pCount) => setPersonCount(pCount)}
							/>
						</fieldset>
						<fieldset>
							{personCareerOptions ? (
								<SelectControl
									label={__('Person Career', 'movie-library')}
									value={personCareer}
									options={personCareerOptions}
									onChange={(pCareer) => {
										onSelectPersonCareer(pCareer);
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
