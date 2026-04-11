<?php declare(strict_types=1);

namespace KHTools\Tests\Normalizers;

use KHTools\VPos\Models\Merchant;
use KHTools\VPos\Normalizers\CartItemNormalizer;
use KHTools\VPos\Normalizers\EnumNormalizer;
use KHTools\VPos\Normalizers\RequestNormalizer;
use KHTools\VPos\Requests\OneClickEchoRequest;
use KHTools\VPos\Requests\OneClickInitRequest;
use KHTools\VPos\Requests\OneClickProcessRequest;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\NameConverter\MetadataAwareNameConverter;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class OneClickNormalizerTest extends TestCase
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

	public function testOneClickEchoNormalizesOrigPayId(): void
	{
		$request = new OneClickEchoRequest();
		$request->setMerchant($this->merchant());
		$request->setOriginalPaymentId('pay999');

		$result = $this->normalizer->normalize($request);

		$this->assertSame('merch01', $result['merchantId']);
		$this->assertSame('pay999', $result['origPayId']);
		$this->assertArrayHasKey('dttm', $result);
	}

	public function testOneClickInitNormalizesRequiredFields(): void
	{
		$request = new OneClickInitRequest();
		$request->setMerchant($this->merchant());
		$request->setOriginalPaymentId('pay999');
		$request->setOrderNumber('order123');
		$request->setReturnUrl('https://example.com/return');
		$request->setReturnMethod(\KHTools\VPos\Models\Enums\HttpMethod::Post);

		$result = $this->normalizer->normalize($request);

		$this->assertSame('merch01', $result['merchantId']);
		$this->assertSame('pay999', $result['origPayId']);
		$this->assertSame('order123', $result['orderNo']);
		$this->assertSame('https://example.com/return', $result['returnUrl']);
		$this->assertArrayHasKey('dttm', $result);
	}

	public function testOneClickProcessNormalizesPayId(): void
	{
		$request = new OneClickProcessRequest();
		$request->setMerchant($this->merchant());
		$request->setPaymentId('pay123');

		$result = $this->normalizer->normalize($request);

		$this->assertSame('merch01', $result['merchantId']);
		$this->assertSame('pay123', $result['payId']);
		$this->assertArrayHasKey('dttm', $result);
	}
}
