import { registerFormatType, toggleFormat } from '@wordpress/rich-text';
import { BlockControls } from '@wordpress/block-editor';
import { Toolbar, ToolbarButton } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

const dpTextHighlight = ( { isActive, onChange, value } ) => {
	return (
		<BlockControls>
			<Toolbar label="Options">
				<ToolbarButton
					icon="admin-customizer"
					title={ __( 'Highlight Text' ) }
					className="dp-text-highlight"
					onClick={ () => {
						onChange(
							toggleFormat( value, {
								type: 'prime-time-funding/dp-text-highlight',
							} )
						);
					} }
					isActive={ isActive }
				/>
			</Toolbar>
		</BlockControls>
	);
};

registerFormatType( 'prime-time-funding/dp-text-highlight', {
	title: __( 'HL' ),
	tagName: 'span',
	className: 'dp-text-highlight',
	edit: dpTextHighlight,
} );
