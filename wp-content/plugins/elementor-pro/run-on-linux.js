const { spawn, exec } = require( 'child_process' );
const packageJson = require( './package.json' );

function isDockerExist() {
	return new Promise( ( resolve ) => {
		exec( 'docker -v', ( error ) => {
			resolve( ! error );
		} );
	} );
}

async function run( tag ) {
	const playwrightVersion = packageJson.devDependencies[ '@playwright/test' ];
	const workingDir = process.cwd();

	const image = `mcr.microsoft.com/playwright:v${ playwrightVersion.replace( '^', '' ) }-jammy`;
	const tagArgs = ( Array.isArray( tag ) ? tag : [ tag ] ).filter( Boolean );
	const args = [
		'run',
		'--rm',
		'--network', 'host',
		'--volume', `${ workingDir }:/work`,
		'--workdir', '/work/',
		'--interactive',
		...( process.env.CI ? [] : [ '--tty' ] ),
		image,
		'npm', 'run', 'test:playwright', '--',
		...( tagArgs.length ? [ '--grep', ...tagArgs ] : [] ),
	];

	spawn( 'docker', args, {
		stdio: 'inherit',
		stderr: 'inherit',
		shell: false,
	} );
}

( async () => {
	if ( ! await isDockerExist() ) {
		// eslint-disable-next-line no-console
		console.error( 'Docker is not installed, please install it first.' );

		process.exit( 1 );
	}

	await run( process.argv.slice( 2 ) );
} )();
