<?php if (is_null($parameter)): ?>
null
<?php else: ?>
{
	"id": {{ parameter.id }},
	"title": {{ parameter.title | string }},
	"body": {{ parameter.body | string }},
	"cost": {{ parameter.cost }},
	"type": {{ parameter.type }},
	"values": <?php echo json_encode($parameter->getValues()) ?>
}
<?php endif ?>
