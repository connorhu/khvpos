<?php declare(strict_types=1);

namespace KHTools\Tests\Models;

use KHTools\VPos\Models\Fingerprint;
use PHPUnit\Framework\TestCase;

class FingerprintTest extends TestCase
{
	public function testBrowserDataGetterSetter(): void
	{
		$fingerprint = new Fingerprint();
		$this->assertNull($fingerprint->getBrowserData());

		$fingerprint->setBrowserData(['colorDepth' => 24, 'javaEnabled' => false]);
		$this->assertSame(['colorDepth' => 24, 'javaEnabled' => false], $fingerprint->getBrowserData());
	}

	public function testSdkDataGetterSetter(): void
	{
		$fingerprint = new Fingerprint();
		$this->assertNull($fingerprint->getSdkData());

		$fingerprint->setSdkData(['sdkAppID' => 'abc', 'sdkMaxTimeout' => 5]);
		$this->assertSame(['sdkAppID' => 'abc', 'sdkMaxTimeout' => 5], $fingerprint->getSdkData());
	}
}
