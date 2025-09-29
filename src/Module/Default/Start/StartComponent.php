<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Module\Default\Start;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\Web\ORequest;
use Osumi\OsumiFramework\Routing\OUrl;

class StartComponent extends OComponent {
	/**
	 * Start page
	 *
	 * @param ORequest $req Request object with method, headers, parameters and filters used
	 * @return void
	 */
	public function run(ORequest $req): void {
		OUrl::goToUrl('https://styx.osumi.es');
	}
}
