<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Models/Enums/Currency.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Models/Enums/CustomerLoginAuth.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Models/Enums/DeliveryMode.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Models/Enums/HttpMethod.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Models/Enums/Language.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Models/Enums/OrderAvailability.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Models/Enums/OrderDelivery.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Models/Enums/OrderType.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Models/Enums/PaymentMethod.php',
];
$ignoreErrors[] = [
	// identifier: match.unhandled
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Models/Enums/PaymentOperation.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
