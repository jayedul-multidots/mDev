/**
 * "Latest request wins": starting a request aborts the previous one, and a
 * response that finishes after a newer request started is reported as STALE
 * so it can never overwrite newer results (e.g. fast typing in search).
 */

export const STALE = Symbol( 'stale' );

/**
 * @typedef {Object} LatestRequest
 * @property {(task: (signal: AbortSignal) => Promise<unknown>) => Promise<unknown>} run       Run a task, aborting the previous one.
 * @property {() => void}                                                            abort     Abort the in-flight task.
 * @property {() => boolean}                                                         isPending Whether a task is in flight.
 */

/**
 * @param {() => AbortController} [createController] AbortController factory (for tests).
 * @return {LatestRequest} Runner.
 */
export function createLatestRequest(
	createController = () => new AbortController()
) {
	let current = null;

	return {
		/**
		 * Run a task, aborting any in-flight one.
		 *
		 * @param {(signal: AbortSignal) => Promise<unknown>} task Task.
		 * @return {Promise<unknown>} Task result, or STALE when superseded.
		 */
		async run( task ) {
			current?.abort();
			const controller = createController();
			current = controller;

			try {
				const result = await task( controller.signal );
				return controller === current ? result : STALE;
			} catch ( error ) {
				if ( controller !== current || error?.name === 'AbortError' ) {
					return STALE;
				}
				throw error;
			} finally {
				if ( controller === current ) {
					current = null;
				}
			}
		},

		/** Abort the in-flight request, if any. */
		abort() {
			current?.abort();
			current = null;
		},

		/** @return {boolean} Whether a request is in flight. */
		isPending() {
			return current !== null;
		},
	};
}
