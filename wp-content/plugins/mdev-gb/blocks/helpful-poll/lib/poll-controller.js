/**
 * State transitions for mdev-gb/helpful-poll, free of the Interactivity API
 * so they can be unit tested with a plain context object.
 *
 * Context shape (from PollView::build()): postId, endpoint, hasVoted,
 * isSubmitting, message, error, thanks, errorMessage.
 */
import { submitVote } from './vote-client';
import { readVote, saveVote } from './vote-storage';

/**
 * Show the "thanks" state.
 *
 * @param {Object} ctx Poll context.
 */
function markVoted( ctx ) {
	ctx.hasVoted = true;
	ctx.isSubmitting = false;
	ctx.error = '';
	ctx.message = ctx.thanks;
}

/**
 * On load: if this browser already voted on the post, show the thanks state.
 *
 * @param {Object}       ctx     Poll context.
 * @param {Storage|null} storage Storage.
 * @return {boolean} Whether a previous vote was found.
 */
export function restoreVote( ctx, storage ) {
	if ( readVote( storage, ctx.postId ) === null ) {
		return false;
	}
	markVoted( ctx );
	return true;
}

/**
 * Record a vote: once per browser and post, ignoring clicks while a request
 * is in flight. The vote is remembered only after the server accepted it,
 * so a failed request can be retried.
 *
 * @param {Object}       ctx               Poll context.
 * @param {string}       vote              "yes" or "no".
 * @param {Object}       deps              Browser dependencies.
 * @param {typeof fetch} deps.fetchImpl    fetch implementation.
 * @param {Storage|null} deps.storage      Storage.
 * @param {() => void}   [deps.onRecorded] Called after a recorded vote (e.g. move focus).
 * @return {Promise<'ok'|'skipped'|'error'>} Outcome.
 */
export async function castVote(
	ctx,
	vote,
	{ fetchImpl, storage, onRecorded = () => {} }
) {
	if ( ctx.hasVoted || ctx.isSubmitting ) {
		return 'skipped';
	}
	// Voted in another tab since this page loaded.
	if ( restoreVote( ctx, storage ) ) {
		return 'skipped';
	}

	ctx.isSubmitting = true;
	ctx.error = '';

	try {
		await submitVote( ctx.endpoint, vote, fetchImpl );
	} catch {
		ctx.isSubmitting = false;
		ctx.error = ctx.errorMessage;
		return 'error';
	}

	saveVote( storage, ctx.postId, vote );
	markVoted( ctx );
	onRecorded();
	return 'ok';
}
