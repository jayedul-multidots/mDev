/**
 * Front-end store for mdev-gb/helpful-poll (Interactivity API).
 *
 * Thin glue: state transitions live in lib/poll-controller.js.
 */
import { store, getContext, getElement } from '@wordpress/interactivity';

import { castVote, restoreVote } from './lib/poll-controller';
import { getStorage } from './lib/vote-storage';

export const STORE = 'mdev-gb/helpful-poll';

/**
 * Vote action for one answer.
 *
 * @param {string} vote "yes" or "no".
 * @return {() => Iterator<Promise<string>>} Generator action.
 */
function voteAction( vote ) {
	return function* () {
		const ctx = getContext();
		// Read the element while in scope; the buttons are hidden once the
		// vote is recorded, so focus moves to the thanks message.
		const root = getElement().ref?.closest( `[data-wp-interactive]` );

		yield castVote( ctx, vote, {
			fetchImpl: ( ...args ) => window.fetch( ...args ),
			storage: getStorage( window ),
			onRecorded: () =>
				root
					?.querySelector( '.wp-block-mdev-gb-helpful-poll__thanks' )
					?.focus(),
		} );
	};
}

const { actions } = store( STORE, {
	actions: {
		voteYes: voteAction( 'yes' ),
		voteNo: voteAction( 'no' ),
	},
	callbacks: {
		/** Show the thanks state if this browser already voted. */
		restore() {
			restoreVote( getContext(), getStorage( window ) );
		},
	},
} );

export { actions };
