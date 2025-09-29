<?php
use Osumi\OsumiFramework\App\Component\Model\Request\RequestComponent;

foreach ($list as $i => $request) {
  $component = new RequestComponent([ 'request' => $request ]);
	echo strval($component);
	if ($i < count($list) - 1) {
		echo ",\n";
	}
}
