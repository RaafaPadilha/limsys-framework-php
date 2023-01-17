<?php

namespace Limsys\Http;

/**
 * HttpRequest class.
 *
 * @package Limsys\Http
 */
class HttpRequest
{
	/**
	 * The request path.
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * The request method.
	 *
	 * @var string
	 */
	protected $method;

	/**
	 * Array of allowed HTTP methods.
	 *
	 * @var array
	 */
	public static $allowedMethods = ['GET', 'HEAD', 'POST', 'PUT', 'DELETE'];

	/**
	 * Get the current request path.
	 *
	 * @return string
	 */
	public function path(): string
	{
		return $this->path ??= $this->preparePath();
	}

	/**
	 * Get the current request method.
	 *
	 * @return string
	 */
	public function method(): string
	{
		return $this->method ??= $this->prepareMethod();
	}

	/**
	 * Filter and clear the request path.
	 *
	 * @return string
	 */
	private function preparePath(): string
	{
		$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

		if ($path === false) {
			return '/';
		}

		return $path;
	}

	/**
	 * Checks if the request method is valid and returns it.
	 *
	 * @throws \RuntimeException Method Not Allowed.
	 * @return string
	 */
	public function prepareMethod(): string
	{
		$method = $_SERVER['REQUEST_METHOD'];

		if (in_array($method, self::$allowedMethods)) {
			return $method;
		}

		throw new \RuntimeException('Method Not Allowed');
	}
}
