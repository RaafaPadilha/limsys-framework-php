<?php

namespace Limsys\Tests\Http;

use Limsys\Http\HttpRequest;
use PHPUnit\Framework\TestCase;

/**
 * HttpRequest test class.
 *
 * @package Limsys\Tests\Http
 */
class HttpRequestTest extends TestCase
{
	/**
	 * Tests the path method.
	 */
	public function testPath()
	{
		$_SERVER['REQUEST_URI'] = '/customers/delete/1';

		$request = new HttpRequest();

		$this->assertEquals('/customers/delete/1', $request->path());
	}

	/**
	 * Tests the path method with an invalid path.
	 */
	public function testInvalidPath()
	{
		$_SERVER['REQUEST_URI'] = '//';

		$request = new HttpRequest();

		$this->assertEquals('/', $request->path());
	}

	/**
	 * Tests the HTTP method configuration.
	 */
	public function testMethod()
	{
		$_SERVER['REQUEST_METHOD'] = 'GET';

		$request = new HttpRequest();

		$this->assertEquals('GET', $request->method());
	}

	/**
	 * Tests the HTTP method configuration with a disallowed method.
	 */
	public function testNotAllowedMethod()
	{
		$this->expectException(\RuntimeException::class);
		$this->expectErrorMessage('Method Not Allowed');

		$_SERVER['REQUEST_METHOD'] = 'OPTIONS';

		$request = new HttpRequest();
		$request->method();
	}
}
