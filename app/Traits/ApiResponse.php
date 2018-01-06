<?php

namespace App\Traits;
use Symfony\Component\HttpFoundation\Response as FoundationResponse;

trait ApiResponse
{
	protected $statusCode = FoundationResponse::HTTP_OK;

	public function getStatusCode()
	{
		return $this->statusCode;
	}

	public function setStatusCode($statusCode)
	{
		$this->statusCode = $statusCode;
		return $this;
	}

	public function respond($data, $header = [])
	{
		return response()->json($data, $this->getStatusCode(), $header);
	}

	public function status($status, array $data, $code = null) {
		if ($code) {
			$this->setStatusCode($code);
		}

		$status = [
			'status' => $status,
			'code' => $this->getStatusCode()
		];

		$data = array_merge($status, $data);

		return $this->respond($data);
	}

	public function message($message, $status = "success"){

		return $this->status($status,[
			'message' => $message
		]);
	}

	public function success($data, $status = "success"){

		return $this->status($status, compact('data'));
	}

	public function failed($message, $code = FoundationResponse::HTTP_BAD_REQUEST, $status = 'error'){

		return $this->setStatusCode($code)->message($message,$status);
	}

	public function created($message = "Created.")
	{
		return $this->setStatusCode(FoundationResponse::HTTP_CREATED)
			->message($message);
	}

	public function deleted($message = "Deleted.")
	{
		return $this->setStatusCode(FoundationResponse::HTTP_OK)
			->message($message);
	}

	public function notFound($message = 'Not found.')
	{
		return $this->failed($message,Foundationresponse::HTTP_NOT_FOUND);
	}
}