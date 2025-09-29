<?php if (is_null($request)): ?>
null
<?php else: ?>
{
	"id": {{ request.id }},
	"idUser": {{ request.id_user }},
	"title": {{ request.title | string }},
	"body": {{ request.body | string }},
	"cost": {{ request.cost }},
	"reward": {{ request.reward }}
	"status": {{ request.status }}
	"completedAt": {{ request.completed_at | date }},
	"createdAt": {{ request.created_at | date }}
}
<?php endif ?>
