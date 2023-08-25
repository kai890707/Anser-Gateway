<?php

namespace App\Controllers\V2;

use App\Controllers\BaseController;
use App\Orchestrators\V2\CreateOrderOrchestrator;

class CreateOrder extends BaseController
{
    public function createOrder()
    {

        $jsonData  = json_decode($this->request->rawBody());

		$memberKey = $jsonData->memberKey;
		$products  = $jsonData->products;

		$createOrder = new CreateOrderOrchestrator();

		$data = $createOrder->build($products, $memberKey);
		$result = $data ?? ["order_key" => $createOrder->orderKey];

		return $this->response->withBody(json_encode($result));
    }
}