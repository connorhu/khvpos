<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Property KHTools\\\\VPos\\\\Entities\\\\Browser\\:\\:\\$vars type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Browser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Entities\\\\CartItem\\:\\:getRawAmount\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/CartItem.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Enums/Currency.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Entities\\\\Enums\\\\Currency\\:\\:stringValues\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Enums/Currency.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Enums/CustomerLoginAuth.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Enums/DeliveryMode.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Enums/HttpMethod.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Enums/Language.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Enums/OrderAvailability.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Enums/OrderDelivery.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Enums/OrderType.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Enums/PaymentMethod.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Enums/PaymentOperation.php',
];
$ignoreErrors[] = [
	'message' => '#^Property KHTools\\\\VPos\\\\Entities\\\\Order\\:\\:\\$giftcards type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Entities/Order.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset non\\-falsy\\-string on array\\|ArrayObject\\|bool\\|float\\|int\\|string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/AddressNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\AddressNormalizer\\:\\:normalize\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/AddressNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\AddressNormalizer\\:\\:normalize\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/AddressNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\AddressNormalizer\\:\\:supportsNormalization\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/AddressNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$arrayToOrder of static method KHTools\\\\VPos\\\\Normalizers\\\\NormalizerResultOrderingHelper\\:\\:orderArray\\(\\) expects array, array\\|ArrayObject\\|bool\\|float\\|int\\|string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/AddressNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\CartItemNormalizer\\:\\:normalize\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/CartItemNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\CartItemNormalizer\\:\\:normalize\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/CartItemNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\CartItemNormalizer\\:\\:supportsNormalization\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/CartItemNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$arrayToOrder of static method KHTools\\\\VPos\\\\Normalizers\\\\NormalizerResultOrderingHelper\\:\\:orderArray\\(\\) expects array, array\\|ArrayObject\\|bool\\|float\\|int\\|string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/CartItemNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\EnumNormalizer\\:\\:denormalize\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/EnumNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\EnumNormalizer\\:\\:normalize\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/EnumNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\EnumNormalizer\\:\\:supportsDenormalization\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/EnumNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\EnumNormalizer\\:\\:supportsNormalization\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/EnumNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\HttpErrorNormalizer\\:\\:denormalize\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/HttpErrorNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\HttpErrorNormalizer\\:\\:supportsDenormalization\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/HttpErrorNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\NormalizerResultOrderingHelper\\:\\:orderArray\\(\\) has parameter \\$arrayToOrder with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/NormalizerResultOrderingHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\NormalizerResultOrderingHelper\\:\\:orderArray\\(\\) has parameter \\$keyOrder with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/NormalizerResultOrderingHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\NormalizerResultOrderingHelper\\:\\:orderArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/NormalizerResultOrderingHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'dttm\' on array\\|ArrayObject\\|bool\\|float\\|int\\|string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/RequestNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\RequestNormalizer\\:\\:getContextWithObject\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/RequestNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\RequestNormalizer\\:\\:normalize\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/RequestNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\RequestNormalizer\\:\\:normalize\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/RequestNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\RequestNormalizer\\:\\:normalize\\(\\) should return array but returns array\\|ArrayObject\\|ArrayObject\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/RequestNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\RequestNormalizer\\:\\:supportsNormalization\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/RequestNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$arrayToOrder of static method KHTools\\\\VPos\\\\Normalizers\\\\NormalizerResultOrderingHelper\\:\\:orderArray\\(\\) expects array, array\\|ArrayObject given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/RequestNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'KHTools\\\\\\\\VPos\\\\\\\\Responses\\\\\\\\ResponseInterface\' on array\\<string, class\\-string\\>\\|false\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/ResponseNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\ResponseNormalizer\\:\\:denormalize\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/ResponseNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\ResponseNormalizer\\:\\:responseKeyOrderWithClass\\(\\) return type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/ResponseNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Normalizers\\\\ResponseNormalizer\\:\\:supportsDenormalization\\(\\) has parameter \\$context with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Normalizers/ResponseNormalizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Property KHTools\\\\VPos\\\\Requests\\\\ApplePayEchoRequest\\:\\:\\$clientIp is never read, only written\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/ApplePayEchoRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Property KHTools\\\\VPos\\\\Requests\\\\ApplePayEchoRequest\\:\\:\\$payload is never read, only written\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/ApplePayEchoRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Property KHTools\\\\VPos\\\\Requests\\\\ApplePayEchoRequest\\:\\:\\$sdkUsed is never read, only written\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/ApplePayEchoRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/ApplePayInitRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/ApplePayProcessRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/GooglePayInitRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/GooglePayProcessRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/OneClickInitRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Unreachable statement \\- code above always terminates\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Requests/OneClickProcessRequest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\EchoResponse\\:\\:getResultCode\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/EchoResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\EchoResponse\\:\\:getResultMessage\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/EchoResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentCloseResponse\\:\\:getResultCode\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentCloseResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentCloseResponse\\:\\:getResultMessage\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentCloseResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentInitResponse\\:\\:getResultCode\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentInitResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentInitResponse\\:\\:getResultMessage\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentInitResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentProcessResponse\\:\\:getResultCode\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentProcessResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentProcessResponse\\:\\:getResultMessage\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentProcessResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentRefundResponse\\:\\:getResultCode\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentRefundResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentRefundResponse\\:\\:getResultMessage\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentRefundResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentReverseResponse\\:\\:getResultCode\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentReverseResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentReverseResponse\\:\\:getResultMessage\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentReverseResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentStatusResponse\\:\\:getResultCode\\(\\) should return int but returns int\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentStatusResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\Responses\\\\PaymentStatusResponse\\:\\:getResultMessage\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/Responses/PaymentStatusResponse.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\SignatureProvider\\:\\:buildStringContentToSign\\(\\) has parameter \\$contentToSign with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/SignatureProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\SignatureProvider\\:\\:sign\\(\\) has parameter \\$contentToSign with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/SignatureProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\SignatureProvider\\:\\:verify\\(\\) has parameter \\$signedContent with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/SignatureProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Property KHTools\\\\VPos\\\\SignatureProvider\\:\\:\\$privateKeys type has no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/SignatureProvider.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\SignatureProviderInterface\\:\\:sign\\(\\) has parameter \\$contentToSign with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/SignatureProviderInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\SignatureProviderInterface\\:\\:verify\\(\\) has parameter \\$signedContent with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/SignatureProviderInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'dttm\' on array\\|ArrayObject\\|bool\\|float\\|int\\|string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'signature\' on array\\|ArrayObject\\|bool\\|float\\|int\\|string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Comparison operation "\\>" between int\\<1, max\\> and 0 is always true\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Match expression does not handle remaining value\\: string$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\VPosClient\\:\\:initResponseWithArray\\(\\) has parameter \\$responseArray with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\VPosClient\\:\\:prepareEndpointPath\\(\\) has parameter \\$requestParameters with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\VPosClient\\:\\:prepareEndpointPath\\(\\) should return string but returns string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\VPos\\\\VPosClient\\:\\:send\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^PHPDoc tag @return with type TResponse is not subtype of native type KHTools\\\\VPos\\\\Responses\\\\ResponseInterface\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$content of method Psr\\\\Http\\\\Message\\\\StreamFactoryInterface\\:\\:createStream\\(\\) expects string, string\\|false given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$code of class KHTools\\\\VPos\\\\Exceptions\\\\InvalidArgumentException constructor expects int, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$contentToSign of method KHTools\\\\VPos\\\\SignatureProviderInterface\\:\\:sign\\(\\) expects array, array\\|ArrayObject given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$contentToSign of method KHTools\\\\VPos\\\\SignatureProviderInterface\\:\\:sign\\(\\) expects array, array\\|ArrayObject\\|bool\\|float\\|int\\|string\\|null given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$requestParameters of method KHTools\\\\VPos\\\\VPosClient\\:\\:prepareEndpointPath\\(\\) expects array, array\\|ArrayObject given\\.$#',
	'count' => 2,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$previous of class KHTools\\\\VPos\\\\Exceptions\\\\InvalidArgumentException constructor expects Throwable\\|null, string given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Property KHTools\\\\VPos\\\\VPosClient\\:\\:\\$httpClient is unused\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../../src/VPosClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\Tests\\\\Normalizers\\\\AddressNormalizerTest\\:\\:testPaymentInit\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/AddressNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\Tests\\\\Normalizers\\\\AddressNormalizerTest\\:\\:testPaymentInit\\(\\) has parameter \\$expected with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/AddressNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$loader of class Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Factory\\\\ClassMetadataFactory constructor expects Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Loader\\\\LoaderInterface, Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Loader\\\\AnnotationLoader\\|Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Loader\\\\AttributeLoader given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/AddressNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Property KHTools\\\\Tests\\\\Normalizers\\\\EnumNormalizerTest\\:\\:\\$normalizer has no type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/EnumNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'dttm\' on array\\|ArrayObject\\|bool\\|float\\|int\\|string\\|null\\.$#',
	'count' => 3,
	'path' => __DIR__ . '/../Tests/Normalizers/RequestNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \\(int\\|string\\) on array\\|ArrayObject\\|bool\\|float\\|int\\|string\\|null\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/RequestNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\Tests\\\\Normalizers\\\\RequestNormalizerTest\\:\\:testPaymentCloseRequest\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/RequestNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\Tests\\\\Normalizers\\\\RequestNormalizerTest\\:\\:testPaymentCloseRequest\\(\\) has parameter \\$expected with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/RequestNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\Tests\\\\Normalizers\\\\RequestNormalizerTest\\:\\:testPaymentInit\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/RequestNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\Tests\\\\Normalizers\\\\RequestNormalizerTest\\:\\:testPaymentInit\\(\\) has parameter \\$expected with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/RequestNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\Tests\\\\Normalizers\\\\RequestNormalizerTest\\:\\:testPaymentRefundRequest\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/RequestNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\Tests\\\\Normalizers\\\\RequestNormalizerTest\\:\\:testPaymentRefundRequest\\(\\) has parameter \\$expected with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/RequestNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\Tests\\\\Normalizers\\\\RequestNormalizerTest\\:\\:testSimplePaymentRequests\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/RequestNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\Tests\\\\Normalizers\\\\RequestNormalizerTest\\:\\:testSimplePaymentRequests\\(\\) has parameter \\$expected with no value type specified in iterable type array\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/RequestNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$loader of class Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Factory\\\\ClassMetadataFactory constructor expects Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Loader\\\\LoaderInterface, Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Loader\\\\AnnotationLoader\\|Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Loader\\\\AttributeLoader given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/RequestNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method Symfony\\\\Component\\\\Serializer\\\\Normalizer\\\\NormalizerInterface\\:\\:denormalize\\(\\)\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/ResponseNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Method KHTools\\\\Tests\\\\Normalizers\\\\ResponseNormalizerTest\\:\\:testPaymentStatusResponse\\(\\) has no return type specified\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/ResponseNormalizerTest.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$loader of class Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Factory\\\\ClassMetadataFactory constructor expects Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Loader\\\\LoaderInterface, Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Loader\\\\AnnotationLoader\\|Symfony\\\\Component\\\\Serializer\\\\Mapping\\\\Loader\\\\AttributeLoader given\\.$#',
	'count' => 1,
	'path' => __DIR__ . '/../Tests/Normalizers/ResponseNormalizerTest.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];