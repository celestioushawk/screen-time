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
		attributes: { personSearch },
		setAttributes,
	} = props;

	// Set the person search element.
	const setPersonSearch = (newPersonSearch) => {
		setAttributes({ personSearch: newPersonSearch });
		for (let i = 0; i < personSearchList?.length; i++) {
			if (personSearchList[i].title.rendered === newPersonSearch) {
				setAttributes({
					personId: personSearchList[i].id,
				});
				break;
			}
		}
	};

	// Fetch people based on person search field value.
	const { posts: personSearchList } = useSelect(
		(select) => {
			return {
				posts: select('core').getEntityRecords(
					'postType',
					'rt-person',
					{
						search: personSearch,
					}
				),
			};
		},
		[personSearch]
	);

	const [personSearchOptions, setPersonSearchOptions] = useState([]);

	// Update the person search options once the person search list updates
	useEffect(() => {
		setPersonSearchOptions(
			personSearchList?.map((post) => ({
				label: post.title.rendered,
				value: post.title.rendered,
			}))
		);
	}, [personSearchList]);

	return (
		<div {...useBlockProps()}>
			<InspectorControls key="setting">
				<PanelBody>
					<div className="ml-person-fields">
						<fieldset>
							<TextControl
								label={__('Person Search', 'movie-library')}
								value={personSearch}
								onChange={setPersonSearch}
								list={'person-data-list'}
							/>
							<datalist id="person-data-list">
								{personSearchOptions?.map((searchOption) => {
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
