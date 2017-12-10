<?php

namespace App\Http\Controllers\Api;

use App\Nest;
use App\Repositories\NestRepository;
use App\Http\Controllers\Controller;

class NestsController extends Controller
{
	protected $repo;

	public function __construct(NestRepository $nest)
	{
		$this->repo = $nest;
	}

	public function nest()
	{

	}
}
