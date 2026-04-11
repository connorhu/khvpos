<?php declare(strict_types=1);

namespace KHTools\Tests\Normalizers;

use KHTools\VPos\Models\Enums\Currency;
use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\CartItemNormalizer;
use KHTools\VPos\Normalizers\EnumNormalizer;
use KHTools\VPos\Normalizers\RequestNormalizer;
use KHTools\VPos\Requests\ApplePayEchoRequest;
use KHTools\VPos\Requests\ApplePayInitRequest;
use KHTools\VPos\Requests\ApplePayProcessRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class ApplePayNormalizerTest extends TestCase
{
	private NormalizerInterface $normalizer;

	protected function setUp(): void
	{
		$classMetadataFactory = new ClassMetadataFactory(new AttributeLoader());
		$metadataAwareNameConverter = new MetadataAwareNameConverter($classMetadataFactory);
		$objectNormalizer = new ObjectNormalizer($classMetadataFactory, $metadataAwareNameConverter);

		$this->normalizer = new Serializer([
			new RequestNormalizer($objectNormalizer),
			new CartItemNormalizer($objectNormalizer),
			new EnumNormalizer(),
			new DateTimeNormalizer(),
			$objectNormalizer,
		]);
	}

	private function merchant(string $id = 'merch01'): Merchant
	{
		$m = new Merchant();
		$m->setMerchantId($id);
		return $m;
	}

	public function testApplePayEchoNormalizesMerchantId(): void
	{
		$request = new ApplePayEchoRequest();
		$request->setMerchant($this->merchant());

		$result = $this->normalizer->normalize($request);

		$this->assertSame('merch01', $result['merchantId']);
		$this->assertArrayHasKey('dttm', $result);
	}

	public function testApplePayInitNormalizesRequiredFields(): void
	{
		$request = new ApplePayInitRequest();
		$request->setMerchant($this->merchant());
		$request->setOrderNumber('order123');
		$request->setTotalAmount(5000);
		$request->setCurrency(Currency::HUF);
		$request->setPayload('base64payloadhere');
		$request->setReturnUrl('https://example.com/return');
		$request->setReturnMethod(\KHTools\VPos\Models\Enums\HttpMethod::Post);

		$result = $this->normalizer->normalize($request);

		$this->assertSame('merch01', $result['merchantId']);
		$this->assertSame('order123', $result['orderNo']);
		$this->assertSame(500000, $result['totalAmount']);
		$this->assertSame('HUF', $result['currency']);
		$this->assertSame('base64payloadhere', $result['payload']);
	}

	public function testApplePayProcessNormalizesPayId(): void
	{
		$request = new ApplePayProcessRequest();
		$request->setMerchant($this->merchant());
		$request->setPaymentId('pay001');

		$result = $this->normalizer->normalize($request);

		$this->assertSame('merch01', $result['merchantId']);
		$this->assertSame('pay001', $result['payId']);
	}
}
