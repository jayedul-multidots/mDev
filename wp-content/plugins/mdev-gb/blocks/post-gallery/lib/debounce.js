/**
 * @typedef {Object} Timers
 * @property {typeof setTimeout}   setTimeout   Schedules a callback.
 * @property {typeof clearTimeout} clearTimeout Cancels a scheduled callback.
 */

/**
 * @typedef {((...args: unknown[]) => void) & {cancel: () => void, flush: () => unknown, isPending: () => boolean}} Debounced
 */

/**
 * Debounce with cancel/flush, used for search-as-you-type.
 *
 * @param {(...args: unknown[]) => unknown} fn     Function to debounce.
 * @param {number}                          wait   Delay in ms.
 * @param {Timers}                          timers Timer functions (injectable for tests).
 * @return {Debounced} Debounced function.
 */
export function debounce(
	fn,
	wait,
	timers = {
		setTimeout: globalThis.setTimeout,
		clearTimeout: globalThis.clearTimeout,
	}
) {
	let timer = null;
	let lastArgs = [];

	const invoke = () => {
		timer = null;
		return fn( ...lastArgs );
	};

	function debounced( ...args ) {
		lastArgs = args;
		if ( timer !== null ) {
			timers.clearTimeout( timer );
		}
		timer = timers.setTimeout( invoke, wait );
	}

	debounced.cancel = () => {
		if ( timer !== null ) {
			timers.clearTimeout( timer );
			timer = null;
		}
	};

	debounced.flush = () => {
		if ( timer === null ) {
			return undefined;
		}
		timers.clearTimeout( timer );
		return invoke();
	};

	debounced.isPending = () => timer !== null;

	return debounced;
}
