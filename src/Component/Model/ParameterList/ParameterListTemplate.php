<?php
use Osumi\OsumiFramework\App\Component\Model\Parameter\ParameterComponent;

foreach ($list as $i => $parameter) {
  $component = new ParameterComponent([ 'parameter' => $parameter ]);
	echo strval($component);
	if ($i < count($list) - 1) {
		echo ",\n";
	}
}
