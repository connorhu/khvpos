<?php declare(strict_types=1);

namespace KHTools\Tests\Models;

use KHTools\VPos\Models\InitParams;
use PHPUnit\Framework\TestCase;

class InitParamsTest extends TestCase
{
	public function testGettersSetters(): void
	{
		$params = new InitParams();
		$this->assertNull($params->getMerchantIdentifier());
		$this->assertNull($params->getMerchantName());
		$this->assertNull($params->getMerchantCountry());
		$this->assertNull($params->getSupportedNetworks());

		$params->setMerchantIdentifier('merchant.com.example');
		$params->setMerchantName('Example Store');
		$params->setMerchantCountry('CZE');
		$params->setSupportedNetworks(['VISA', 'MC']);

		$this->assertSame('merchant.com.example', $params->getMerchantIdentifier());
		$this->assertSame('Example Store', $params->getMerchantName());
		$this->assertSame('CZE', $params->getMerchantCountry());
		$this->assertSame(['VISA', 'MC'], $params->getSupportedNetworks());
	}
}
