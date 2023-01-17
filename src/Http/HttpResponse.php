<?php

namespace Limsys\Http;

use InvalidArgumentException;

/**
 * HttpResponse class.
 *
 * @package Limsys\Http
 */
class HttpResponse
{
	/**
	 * The HTTP response code.
	 *
	 * @var int
	 */
	protected $statusCode;

	/**
	 * The text of the HTTP response code.
	 *
	 * @var string
	 */
	protected $statusText;

	/**
	 * The response body.
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * The header of the response.
	 *
	 * @var array
	 */
	protected $headers = [];

	/**
	 * The HTTP protocol version.
	 *
	 * @var string
	 */
	protected $protocol;

	/**
	 * Status codes translation table.
	 *
	 * @var array
	 * @link https://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
	 */
	public static $statusTexts = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',
		103 => 'Early Hints',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No Content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		207 => 'Multi-Status',
		208 => 'Already Reported',
		226 => 'IM Used',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',
		305 => 'Use Proxy',
		307 => 'Temporary Redirect',
		308 => 'Permanent Redirect',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length Required',
		412 => 'Precondition Failed',
		413 => 'Content Too Large',
		414 => 'URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Range Not Satisfiable',
		417 => 'Expectation Failed',
		418 => 'I\'m a teapot',
		421 => 'Misdirected Request',
		422 => 'Unprocessable Content',
		423 => 'Locked',
		424 => 'Failed Dependency',
		425 => 'Too Early',
		426 => 'Upgrade Required',
		428 => 'Precondition Required',
		429 => 'Too Many Requests',
		431 => 'Request Header Fields Too Large',
		451 => 'Unavailable For Legal Reasons',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
		506 => 'Variant Also Negotiates',
		507 => 'Insufficient Storage',
		508 => 'Loop Detected',
		510 => 'Not Extended',
		511 => 'Network Authentication Required',
	];

	/**
	 * Create a new response.
	 *
	 * By default the HTTP protocol version number is 1.1.
	 *
	 * @param string $content The response body.
	 * @param int $code The HTTP response code. Default is 200.
	 */
	public function __construct(string $content = '', int $code = 200)
	{
		$this->body($content);
		$this->status($code);
		$this->protocol('1.1');
	}

	/**
	 * Specify the HTTP response code and its text. If the code does not exists
	 * inform 'unknown status'.
	 *
	 * @param int $code The HTTP response code.
	 * @param string|null $text The custom text of the HTTP response code.
	 * @return HttpResponse
	 */
	public function status(int $code, string $text = null): self
	{
		$this->statusCode = $code;

		if ($text === null) {
			$this->statusText = self::$statusTexts[$code] ?? 'unknown status';
		} else {
			$this->statusText = $text;
		}

		return $this;
	}

	/**
	 * Gets the response status code.
	 *
	 * @return int
	 */
	public function getStatusCode(): int
	{
		return $this->statusCode;
	}

	/**
	 * Gets the response status text.
	 *
	 * @return string
	 */
	public function getStatusText(): string
	{
		return $this->statusText;
	}

	/**
	 * Sets the body of the response to the specified content.
	 *
	 * @param string $content The content of the response.
	 * @return HttpResponse
	 */
	public function body(string $content): self
	{
		$this->content = $content;
		return $this;
	}

	/**
	 * Gets the response body.
	 *
	 * @return string
	 */
	public function getBody(): string
	{
		return $this->content;
	}

	/**
	 * Adds a header to the response.
	 *
	 * @param string $header The header name.
	 * @param string $value The header value.
	 * @return HttpResponse
	 */
	public function header(string $header, string $value): self
	{
		$this->headers[$header] = $value;
		return $this;
	}

	/**
	 * Gets the value of the specified header.
	 *
	 * @param string $header The header name.
	 * @throws \Exception Header not found.
	 * @return string
	 */
	public function getHeaderLine(string $header): string
	{
		if (isset($this->headers[$header])) {
			return $this->headers[$header];
		}

		throw new \Exception("{$header} header not found");
	}

	/**
	 * Sets the HTTP protocol version number (e.g., "1.0", "1.1").
	 *
	 * @param string $protocol The protocol version number.
	 * @return HttpResponse
	 */
	public function protocol(string $protocol): self
	{
		if (!preg_match('/^(1\.[01])$/', $protocol)) {
			throw new InvalidArgumentException("Unsuported protocol version {$protocol} provided");
		}
		$this->protocol = $protocol;

		return $this;
	}

	/**
	 * Gets the HTTP protocol version number.
	 *
	 * @return string
	 */
	public function getProtocol(): string
	{
		return $this->protocol;
	}

	/**
	 * Sends the response body and header to the client.
	 *
	 * @return HttpResponse
	 */
	public function send(): self
	{
		header(sprintf("HTTP/%s %s %s", $this->protocol, $this->statusCode, $this->statusText), true, $this->statusCode);

		foreach ($this->headers as $header => $value) {
			header("{$header}: {$value}", false, $this->statusCode);
		}

		echo $this->content;

		return $this;
	}
}
