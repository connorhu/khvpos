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
$ignoreErrors[] = [
	// identifier: property.onlyWritten
	'message' => '#^Property KHTools\\\\VPos\\\\Requests\\\\ApplePayEchoRequest\\:\\:\\$clientIp is never read, only written\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/ApplePayEchoRequest.php',
];
$ignoreErrors[] = [
	// identifier: property.unusedType
	// ApplePayEchoRequest properties are written by the Symfony Serializer (external deserialization),
	// never assigned from PHP code directly — PHPStan cannot see the string assignment path.
	'message' => '#^Property KHTools\\\\VPos\\\\Requests\\\\ApplePayEchoRequest\\:\\:\\$clientIp \\(string\\|null\\) is never assigned string so it can be removed from the property type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/ApplePayEchoRequest.php',
];
$ignoreErrors[] = [
	// identifier: property.unusedType
	'message' => '#^Property KHTools\\\\VPos\\\\Requests\\\\ApplePayEchoRequest\\:\\:\\$payload \\(string\\|null\\) is never assigned string so it can be removed from the property type\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/ApplePayEchoRequest.php',
];
$ignoreErrors[] = [
	// identifier: property.onlyWritten
	'message' => '#^Property KHTools\\\\VPos\\\\Requests\\\\ApplePayEchoRequest\\:\\:\\$payload is never read, only written\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/ApplePayEchoRequest.php',
];
$ignoreErrors[] = [
	// identifier: property.onlyWritten
	'message' => '#^Property KHTools\\\\VPos\\\\Requests\\\\ApplePayEchoRequest\\:\\:\\$sdkUsed is never read, only written\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/ApplePayEchoRequest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
