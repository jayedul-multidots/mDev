/**
 * Remembers in localStorage which posts this browser has voted on, so each
 * visitor counts once per post. Storage can be missing or throw (privacy
 * modes, disabled storage, quota); voting then still works, it just is not
 * remembered across page loads.
 */
import { isVote } from './vote-client';

export const KEY_PREFIX = 'mdev-gb/helpful-poll:';

/**
 * Storage key for a post.
 *
 * @param {number} postId Post ID.
 * @return {string} Key.
 */
export function storageKey( postId ) {
	return `${ KEY_PREFIX }${ postId }`;
}

/**
 * The browser's localStorage, or null when it is unavailable (merely
 * reading `window.localStorage` throws when storage is blocked).
 *
 * @param {Window} win Window.
 * @return {Storage|null} Storage.
 */
export function getStorage( win ) {
	try {
		return win.localStorage ?? null;
	} catch {
		return null;
	}
}

/**
 * The vote stored for a post.
 *
 * @param {Storage|null} storage Storage.
 * @param {number}       postId  Post ID.
 * @return {string|null} "yes", "no", or null when not voted (or unreadable).
 */
export function readVote( storage, postId ) {
	try {
		const value = storage?.getItem( storageKey( postId ) ) ?? null;
		return isVote( value ) ? value : null;
	} catch {
		return null;
	}
}

/**
 * Remember a vote.
 *
 * @param {Storage|null} storage Storage.
 * @param {number}       postId  Post ID.
 * @param {string}       vote    "yes" or "no".
 * @return {boolean} Whether it was stored.
 */
export function saveVote( storage, postId, vote ) {
	if ( ! storage || ! isVote( vote ) ) {
		return false;
	}
	try {
		storage.setItem( storageKey( postId ), vote );
		return true;
	} catch {
		return false;
	}
}
