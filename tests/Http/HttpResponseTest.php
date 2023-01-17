<?php

namespace Limsys\Tests\Http;

use Limsys\Http\HttpResponse;
use PHPUnit\Framework\TestCase;

/**
 * HttpResponse test class.
 *
 * @package Limsys\Tests\Http
 */
class HttpResponseTest extends TestCase
{
	/**
	 * Tests the default values of the constructor method.
	 */
	public function testDefaultConstruct()
	{
		$response = new HttpResponse();

		$this->assertSame('', $response->getBody());
		$this->assertSame(200, $response->getStatusCode());
		$this->assertSame('1.1', $response->getProtocol());
	}

	/**
	 * Tests the parameters of the constructor method.
	 */
	public function testParamsConstruct()
	{
		$response = new HttpResponse('The body response', 404);

		$this->assertSame('The body response', $response->getBody());
		$this->assertSame(404, $response->getStatusCode());
	}

	/**
	 * Tests the status method.
	 */
	public function testStatus()
	{
		$response = new HttpResponse();
		$response->status(404);

		$this->assertSame(404, $response->getStatusCode());
		$this->assertSame('Not Found', $response->getStatusText());
	}

	/**
	 * Tests the status method with text.
	 */
	public function testStatusWithText()
	{
		$response = new HttpResponse();
		$response->status(404, 'Custom Text');

		$this->assertSame(404, $response->getStatusCode());
		$this->assertSame('Custom Text', $response->getStatusText());
	}

	/**
	 * Tests the status method with a unknown status.
	 */
	public function testUnknownStatus()
	{
		$response = new HttpResponse();
		$response->status(1000);

		$this->assertSame(1000, $response->getStatusCode());
		$this->assertSame('unknown status', $response->getStatusText());
	}

	/**
	 * Tests the status method with a unknown status and a text.
	 */
	public function testUnknownStatusWithText()
	{
		$response = new HttpResponse();
		$response->status(1000, 'Custom Text');

		$this->assertSame(1000, $response->getStatusCode());
		$this->assertSame('Custom Text', $response->getStatusText());
	}

	/**
	 * Tests the body method.
	 */
	public function testBody()
	{
		$response = new HttpResponse();
		$response->body('The body response');

		$this->assertSame('The body response', $response->getBody());
	}

	/**
	 * Tests the header method.
	 */
	public function testHeader()
	{
		$response = new HttpResponse();
		$response->header('Content-Type', 'application/json');

		$this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
	}

	/**
	 * Tests headerLine method with missing header.
	 */
	public function testMissingHeaderLine()
	{
		$this->expectException(\Exception::class);
		$this->expectErrorMessage('Content-Type header not found');

		$response = new HttpResponse();
		$response->getHeaderLine('Content-Type');
	}

	/**
	 * Tests the protocol method.
	 */
	public function testProtocol()
	{
		$response = new HttpResponse();
		$response->protocol('1.0');

		$this->assertSame('1.0', $response->getProtocol());
	}

	/**
	 * Tests the protocol method with unsupported protocol version number.
	 */
	public function testUnsupportedProtocol()
	{
		$this->expectException(\InvalidArgumentException::class);
		$this->expectErrorMessage('Unsuported protocol version 3 provided');

		$response = new HttpResponse();
		$response->protocol('3');
	}

	/**
	 * Tests the send method.
	 */
	public function testSend()
	{
		$response = new HttpResponse();
		$response->protocol('1.0')
			->status(201, 'Custom text')
			->header('Content-Type', 'application/json')
			->body('The body response');

		ob_start();
		$response->send();
		$output = ob_get_clean();

		$this->assertSame('The body response', $output);
		$this->assertSame('1.0', $response->getProtocol());
		$this->assertSame(201, $response->getStatusCode());
		$this->assertSame('application/json', $response->getHeaderLine('Content-Type'));
		$this->assertSame('Custom text', $response->getStatusText());
	}
}
