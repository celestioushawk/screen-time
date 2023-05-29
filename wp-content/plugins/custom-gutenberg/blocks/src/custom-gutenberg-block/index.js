import { registerBlockType } from '@wordpress/blocks';
import './style.scss';
import { registerPlugin } from '@wordpress/plugins';
import { useState, useEffect } from '@wordpress/element';
import { useSelect, useDispatch } from '@wordpress/data';
import { Notice } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { PluginPostStatusInfo } from '@wordpress/edit-post';

import Edit from './edit';
import save from './save';
import metadata from './block.json';

/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(metadata.name, {
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	/**
	 * @see ./save.js
	 */
	save,
});

const CheckForCustomBlock = () => {
	const [hasBlock, setHasBlock] = useState(false);
	const { lockPostSaving, unlockPostSaving } = useDispatch('core/editor');
	// Fetch all the registered blocks.
	const { blocks } = useSelect((select) => {
		return {
			blocks: select('core/block-editor').getBlocks(),
		};
	});
	// Lock or unlock saving the post depending whether custom block is present in post or not.
	useEffect(() => {
		let lockSaving = true;
		blocks?.forEach((block) => {
			if (block.name === metadata.name) {
				lockSaving = false;
				setHasBlock(true);
			}
		});
		if (lockSaving) {
			setHasBlock(false);
			lockPostSaving();
		} else {
			unlockPostSaving();
		}
	}, [blocks]);
	return (
		<>
			<PluginPostStatusInfo>
				{hasBlock ? (
					<Notice status="success" isDismissible={true}>
						{__(
							'You are allowed to save your post.',
							'custom-gutenberg'
						)}
					</Notice>
				) : (
					<Notice status="error" isDismissible={false}>
						{__(
							'You are not allowed to save your post since you have not used the custom block.',
							'custom-gutenberg'
						)}
					</Notice>
				)}
			</PluginPostStatusInfo>
		</>
	);
};
registerPlugin('check-for-custom-block', { render: CheckForCustomBlock });
