/**
 * Playwright end-to-end tests: `npm run test:e2e` (headless),
 * `npm run test:e2e:gui` (visible browser) or `npm run test:e2e:ui`
 * (Playwright UI mode); from the repository root: `make e2e`, `make e2e-gui`
 * or `make e2e-ui`.
 *
 * Extends the `@wordpress/scripts` config, but the tests only browse the
 * already running site as a visitor: no wp-env, no admin login.
 */
const fs = require( 'node:fs' );
const path = require( 'node:path' );
const { parseEnv } = require( 'node:util' );

/**
 * WP_HOME from the repository's .env, parsed as data (nothing else is read
 * into the environment); empty when the file or key is missing.
 *
 * @return {string} Site URL.
 */
function siteUrlFromDotEnv() {
	try {
		const file = path.resolve( __dirname, '../../../.env' );
		return parseEnv( fs.readFileSync( file, 'utf8' ) ).WP_HOME ?? '';
	} catch {
		return '';
	}
}

// Both are read by the base config when it loads, so set them first.
// WP_BASE_URL in the environment wins over .env.
process.env.WP_BASE_URL ||= siteUrlFromDotEnv();

// Traces and failure screenshots go to tests/e2e/artifacts/ (ignored by Git;
// plugin tests/ folders are left out of Docker images by .dockerignore).
process.env.WP_ARTIFACTS_PATH ??= path.join( __dirname, 'tests/e2e/artifacts' );

const { defineConfig } = require( '@playwright/test' );
const baseConfig = require( '@wordpress/scripts/config/playwright.config.js' );

module.exports = defineConfig( {
	...baseConfig,
	testDir: './tests/e2e',
	globalSetup: undefined,
	webServer: undefined,
	use: {
		...baseConfig.use,
		storageState: undefined,
	},
} );
