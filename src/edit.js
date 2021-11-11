/**
 * External dependencies
 */
//import { boolean, object, select, text } from '@storybook/addon-knobs';

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */

import { Component, useState } from '@wordpress/element';

import apiFetch from '@wordpress/api-fetch';

/**
 * Internal dependencies
 */
import SelectControl from '..';
import { PanelBody } from '@wordpress/components';

class TopicEdit extends Component {
	constructor(props) {
		super(props);
		this.state = {
			error: null,
			isLoaded: false,
			stones: [],
			topic: 'Default Topic',
		};
	}

	componentDidMount() {
		try {
			apiFetch({
				path: '/categorized-data-block/v1/stones',
			}).then((jsonObj) => {
				this.stones.push({ value: 0, label: __('Select a Topic') });
				for (var stone in jsonObj.jsonData) {
					this.stones.push({ value: stone, label: stone });
				}
				this.setState({ stones, isLoaded: true });
			});
		} catch (error) {
			console.log(error);
			this.setState({
				error: error,
				isLoaded: true,
			});
		}
	}

	render() {
		const { error, isLoaded, stones } = this.state;
		if (error) {
			return <div>Error: {error.message}</div>;
		} else if (!isLoaded) {
			return <div>Loading...</div>;
		} else {
			return (
				<div>
					Test
					<InspectorControls>
						<PanelBody title="Topic Selection" initialOpen={true}>
							<PanelRow>
								<SelectControl label="Topic" options={stones} />
							</PanelRow>
						</PanelBody>
					</InspectorControls>
				</div>
			);
		}
	}
}

export default TopicEdit;
