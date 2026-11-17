/**
 * REST client for POST mdev-gb/v1/helpful-poll/<id>.
 */

/** Allowed votes. Keep in sync with src/HelpfulPoll/Vote.php. */
export const VOTES = Object.freeze( [ 'yes', 'no' ] );

/**
 * Whether a value is an allowed vote.
 *
 * @param {unknown} value Value.
 * @return {boolean} True for "yes" or "no".
 */
export function isVote( value ) {
	return VOTES.includes( value );
}

/**
 * Error for failed vote requests.
 */
export class VoteRequestError extends Error {
	/**
	 * @param {string} message Message (for logs; never shown to visitors).
	 * @param {number} status  HTTP status, 0 for network errors.
	 */
	constructor( message, status = 0 ) {
		super( message );
		this.name = 'VoteRequestError';
		this.status = status;
	}
}

/**
 * Send one vote.
 *
 * Sent without cookies: the endpoint is anonymous, and a logged-in visitor's
 * cookies without a REST nonce would only make WordPress treat them as
 * logged out anyway.
 *
 * @param {string}       endpoint  Vote endpoint URL from the server.
 * @param {string}       vote      "yes" or "no".
 * @param {typeof fetch} fetchImpl fetch implementation.
 * @return {Promise<void>} Resolves once the vote is recorded.
 * @throws {VoteRequestError} On invalid input, network failure or a non-2xx response.
 */
export async function submitVote( endpoint, vote, fetchImpl ) {
	if ( ! isVote( vote ) ) {
		throw new VoteRequestError( `Invalid vote "${ String( vote ) }".` );
	}

	let response;
	try {
		response = await fetchImpl( endpoint, {
			method: 'POST',
			headers: {
				Accept: 'application/json',
				'Content-Type': 'application/json',
			},
			body: JSON.stringify( { vote } ),
			credentials: 'omit',
		} );
	} catch ( error ) {
		throw new VoteRequestError(
			`Vote request failed: ${ error?.message ?? error }`
		);
	}

	if ( ! response.ok ) {
		throw new VoteRequestError(
			`Vote request failed with HTTP ${ response.status }.`,
			response.status
		);
	}
}
