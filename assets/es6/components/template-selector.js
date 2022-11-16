import { __ } from '@wordpress/i18n';
import { Flex, FlexItem, Card, CardMedia, Button, CardFooter } from "@wordpress/components";

/**
 * Displays an interface to select a template.
 *
 * @param {*} props
 * @returns {JSX.Element} The template selector.
 */
export default function TemplateSelector( { templates, onSelect } ) {

	return (
		<>

			{ templates && (
				<Flex wrap align="top" id="bannerbear-create-signed-url__select-template">
					{templates.map((template) => (
						<FlexItem className="bannerbear-template-wrapper" key={template.uid}>
							<Card elevation={1}>

								<CardMedia>
									<img
										src={template.preview_url ? template.preview_url : bannerbearCreateSignedUrl.placeholder}
										alt={template.name}
										className="bannerbear-template-preview"
									/>
								</CardMedia>

								<CardFooter>
									<h4>{template.name}</h4>
									<Button variant="secondary" onClick={() => onSelect(template.uid, template.name)}>
										{__( 'Use Template', 'bannerbear' )}
									</Button>
								</CardFooter>

							</Card>
						</FlexItem>
					))}
				</Flex>
			)}

			{ ( !templates || 0 === templates.length ) && (
				<p>{__('No templates found.', 'bannerbear')}</p>
			)}
		</>
	);
}
