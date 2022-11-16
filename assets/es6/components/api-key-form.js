import { useState } from "@wordpress/element";
import { __ } from '@wordpress/i18n';
import { Notice, Spinner, Button, TextControl, Flex, FlexBlock, FlexItem } from "@wordpress/components";

/**
 * Displays a form to set the API key.
 *
 * @param {*} props
 * @returns {JSX.Element} The template selector.
 */
 export default function ApiKeyForm( { onChange } ) {

	const [ apiKey, setApiKey ] = useState( '' );
	const [ loading, setLoading ] = useState( false );
	const [ error, setError ] = useState( null );
	const [ planSupported, setPlanSupported ] = useState( true );

	/**
	 * Checks if the plan is supported.
	 *
	 * @param {function} callback The callback function.
	 * @returns {Promise<void>}
	 */
	const checkPlanSupported = ( callback ) => {

		setLoading( true );
		setError( null );
		setPlanSupported( true );

		return window

			.fetch( 'https://api.bannerbear.com/v2/account/', {
				headers: {
					'Authorization': 'Bearer ' + apiKey,
				},
			})

			// Parse the response.
			.then( ( res ) => res.json() )

			// Throw an error if the response is not successful.
			.then( ( res ) => {
				if ( res.message ) {
					throw new Error( res.message );
				}
				return res;
			} )

			// Response is an array of templates.
			.then( ( res ) => {

				// Check if res.paid_plan_name contains the word Automate
				if ( ! res.paid_plan_name || res.paid_plan_name.includes( 'Automate' ) ) {
					setPlanSupported( false );
				} else {
					setPlanSupported( true );
					callback();
				}

				setLoading( false );
			} )

			// Display an error on failure.
			.catch( ( err ) => {

				if ( err.message ) {
					setError( err.message );
				} else {
					setError( __( 'Something went wrong.', 'wp-bannerbear' ) );
				}

				setLoading( false );
			} );

	};

	/**
	 * Fetches the templates from the API.
	 *
	 * @returns {Promise<void>}
	 */
	const fetchTemplates = () => {

		setLoading( true );
		setError( null );

		window
		
			.fetch( 'https://api.bannerbear.com/v2/templates/', {
				headers: {
					'Authorization': 'Bearer ' + apiKey,
				},
			})

			// Parse the response.
			.then( ( res ) => res.json() )

			// Throw an error if the response is not successful.
			.then( ( res ) => {
				if ( res.message ) {
					throw new Error( res.message );
				}
				return res;
			} )

			// Response is an array of templates.
			.then( ( res ) => {
				onChange( apiKey, res );
				setLoading( false );
			} )

			// Display an error on failure.
			.catch( ( err ) => {
				console.log( err );

				if ( err.message ) {
					setError( err.message );
				} else {
					setError( __( 'Something went wrong.', 'wp-bannerbear' ) );
				}

				setLoading( false );
			} );

	};

	/**
	 * Handles the form submission.
	 *
	 * @param {Event} e
	 */
	const handleSubmit = ( e ) => {
		e.preventDefault();

		if ( apiKey ) {
			checkPlanSupported( fetchTemplates );
		}
	};

	return (
		<>

			<form onSubmit={ handleSubmit } className="bannerbear-api-key-form">

				{error && (
					<Notice
						status="error"
						isDismissible={true}
						onRemove={() => setError(null)}
					>{error}</Notice>
				)}

				{ ! planSupported && (
					<Notice status="warning" isDismissible={false}>
						<p>Your Bannerbear plan is <b>Automate</b> which does not include the Signed URLs feature.</p>
						<p>
							<a
								href="https://www.bannerbear.com/pricing/"
								target="_blank"
							>{__( 'View Bannerbear Plans', 'bannerbear' )}</a>
						</p>
						<p>
							<a
								variant="secondary"
								href="https://app.bannerbear.com/account"
								target="_blank"
							>{__( 'Upgrade Bannerbear Plan', 'bannerbear' )}</a>
						</p>
					</Notice>
				) }

				<Flex wrap align="top">

					<FlexBlock>
						<TextControl
							label={__( 'API Key', 'bannerbear' )}
							hideLabelFromVision
							value={ apiKey }
							onChange={ setApiKey }
							onFocus={ () => { setPlanSupported( true ) } }
							placeholder={__( 'Enter your API key', 'bannerbear' )}
							disabled={ loading }
						/>
					</FlexBlock>

					<FlexItem>
						<Button
							variant="primary"
							onClick={ handleSubmit }
							disabled={ ! apiKey || loading }
							isPressed={ loading }
						>
							{ ! loading && __( 'Continue', 'bannerbear' ) }
							{ loading && __( 'Saving...', 'bannerbear' ) }
							{ loading && <Spinner /> }
						</Button>
					</FlexItem>

				</Flex>

			</form>

			<p className="description">{__( 'Enter your Bannerbear API key to get started.', 'bannerbear' )}</p>

		</>
	);
}
