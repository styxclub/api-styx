<?php declare(strict_types=1);

namespace Osumi\OsumiFramework\App\Component\Model\Request;

use Osumi\OsumiFramework\Core\OComponent;
use Osumi\OsumiFramework\App\Model\Request;

class RequestComponent extends OComponent {
  public ?Request $request = null;
}
