<?php if (is_null($into)): ?>
null
<?php else: ?>
{
	"id": {{ into.id }},
	"name": {{ into.name | string }}
}
<?php endif ?>
