import apiFetch from '@wordpress/api-fetch';
import {__} from '@wordpress/i18n';
import {render, useState, useEffect} from "@wordpress/element";
import { Notice, Spinner, Modal } from "@wordpress/components";
import TemplateSelector from './components/template-selector';
import ApiKeyForm from './components/api-key-form';
import domReady from "@wordpress/dom-ready";

/**
 * Displays the main APP.
 */
function App() {
	const [ apiKey, setApiKey ] = useState( null );
	const [ templates, setTemplates ] = useState( null );
	const [ error, setError ] = useState( null );
	const [ loading, setLoading ] = useState( false );
	const [ isModalOpen, setIsModalOpen ] = useState( false );

	// Fetch the templates when the API key changes.
	useEffect( () => {
		jQuery( '.post-type-bannerbear_url #wpbody-content .page-title-action' ).on( 'click', function( e ) {
			e.preventDefault();
			setIsModalOpen( true );
		} );
	}, [] );

	/**
	 * Creates a signed URL for the API key and template.
	 *
	 * @returns {Promise<void>}
	 */
	const saveSignedURL = ( template_id, template_name ) => {

		setLoading( true );
		setError( null );

		// Create a signed URL.
		apiFetch( {
			path: '/bannerbear/v1/signed-url',
			method: 'POST',
			data: {
				api_key: apiKey,
				template_id,
				template_name,
			},
		} )

		// Update the state on success.
		.then( ( res ) => {

			// Redirect to the template editor.
			window.location.href = res.edit_url;

			return res;
		} )

		// Display an error on failure.
		.catch( ( err ) => {

			if ( err.message ) {
				setError( err.message );
			} else {
				setError( __( 'An error occurred while saving.', 'bannerbear' ) );
			}

			setLoading( false );
		} )
	};

	return (
		<>
			{ isModalOpen && (
				<Modal
					onRequestClose={ () => setIsModalOpen( false ) }
					title={ __( 'Create BannerBear Signed URL', 'bannerbear' ) }
					isFullScreen={ false }
				>

					{ loading && <Spinner /> }

					{ ! loading && (
						<>
							{error && (
								<Notice
									status="error"
									isDismissible={true}
									onRemove={() => setError(null)}
								>{error}</Notice>
							)}

							{!apiKey && (
								<ApiKeyForm
									onChange={(apiKey, templates) => {
										setApiKey(apiKey);
										setTemplates(templates);
									}}
								/>
							)}

							{apiKey && (
								<TemplateSelector
									templates={templates}
									onSelect={saveSignedURL}
								/>
							)}
						</>
					)}
				</Modal>
			) }
		</>
	);
}

domReady( () => {
	render(
		<App />,
		document.getElementById( 'bannerbear-create-signed-url__app' )
	);
} );
