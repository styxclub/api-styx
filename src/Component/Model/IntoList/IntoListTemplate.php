<?php
use Osumi\OsumiFramework\App\Component\Model\Into\IntoComponent;

foreach ($list as $i => $into) {
  $component = new IntoComponent([ 'into' => $into ]);
	echo strval($component);
	if ($i < count($list) - 1) {
		echo ",\n";
	}
}
