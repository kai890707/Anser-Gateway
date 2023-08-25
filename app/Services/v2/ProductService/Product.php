<?php

namespace App\Services\V2\ProductService;

use SDPMlab\Anser\Service\SimpleService;
use SDPMlab\Anser\Service\Action;
use Psr\Http\Message\ResponseInterface;
use SDPMlab\Anser\Exception\ActionException;
use SDPMlab\Anser\Service\ActionInterface;

class Product extends SimpleService
{

    protected $serviceName = "product_service";
    protected $filters = [
        "before" => [],
        "after"  => [],
    ];
    protected $retry      = 0;
    protected $retryDelay = 0.2;
    protected $timeout    = 6000.0;

    /**
     * 取得商品清單 Action
     *
     * @param integer|null $limit
     * @param integer|null $offset
     * @param string|null $search
     * @param string|null $isDesc
     * @return ActionInterface $action
     */
    public function getProductList(
        ?int $limit,
        ?int $offset,
        ?string $search, 
        ?string $isDesc
    ): ActionInterface {
        $payload = [];

        if (!is_null($limit))     $payload["limit"]  = $limit;
        if (!is_null($offset))    $payload["offset"] = $offset;
        if (!is_null($search))    $payload["search"] = $search;
        if (!is_null($isDesc))    $payload["isDesc"] = $isDesc;

        $action = $this->getAction("GET", "/api/v1/products");
        if(!empty($payload)) $action->addOption("query", $payload);
        $action->doneHandler(function (
            ResponseInterface $response,
            Action $action
        ){
            $resBody = $response->getBody()->getContents();
            $data    = json_decode($resBody, true);
            $action->setMeaningData($data["data"]);
        })
        ->failHandler(function (
            ActionException $e
        ){
            $errorResult = $e->getResponse()->getBody();
            $data = json_decode($errorResult, true);
            if ($e->isServerError()) {
                log_message("error", $e->getMessage());
                $e->getAction()->setMeaningData([]);
            }

            if ($e->isClientError()) {
                $errorResult = $errorResult->getContents();
                $data = json_decode($errorResult, true);
                log_message("notice", $e->getMessage());
                $e->getAction()->setMeaningData([]);
            }

            if ($e->isConnectError()) {
                log_message("critical", $e->getMessage());
                $e->getAction()->setMeaningData([]);
            }
        });
        return $action;
    }

    /**
     * 取得商品 Action
     *
     * @param integer $productKey
     * @return ActionInterface $action
     */
    public function getProduct(int $productKey): ActionInterface
    {
        $action = $this->getAction("GET","/api/v1/products/{$productKey}")
            ->doneHandler(function(
                ResponseInterface $response,
                Action $action
            ){
                $resBody = $response->getBody()->getContents();
                $data = json_decode($resBody,true);
                $action->setMeaningData($data["data"]);
            })
            ->failHandler(function (
                ActionException $e
            ) {
                if ($e->isServerError()) {
                    log_message("error", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isClientError()) {
                    $errorResult = $e->getResponse()->getBody();
                    $data = json_decode($errorResult, true);    
                    $errorResult = $errorResult->getContents();
                    $data = json_decode($errorResult, true);
                    log_message("notice", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isConnectError()) {
                    log_message("error", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }
            });
        return $action;
    }

    /**
     * 取得新增商品 Action
     *
     * @param string $name
     * @param string $description
     * @param integer $price
     * @param integer $amount
     * @return ActionInterface $action
     */
    public function createProduct(
        string $name,
        string $description,
        int $price,
        int $amount
    ): ActionInterface {
        $action = $this->getAction("POST", "/api/v1/products")
            ->addOption("form_params",[
                "name"        => $name,
                "description" => $description,
                "price"       => $price,
                "amount"      => $amount
            ])
            ->doneHandler(function (
                ResponseInterface $response,
                Action $action
            ){
                $resBody = $response->getBody()->getContents();
                $data    = json_decode($resBody, true);
                $action->setMeaningData($data["data"]);
            })
            ->failHandler(function (
                ActionException $e
            ){
                $errorResult = $e->getResponse()->getBody();
                $data = json_decode($errorResult, true);
                if ($e->isServerError()) {
                    log_message("error", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isClientError()) {
                    $errorResult = $errorResult->getContents();
                    $data = json_decode($errorResult, true);
                    log_message("notice", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isConnectError()) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }
            });
        return $action;
    }

    /**
     * 取得更新商品 Action
     *
     * @param string|null $name
     * @param string|null $description
     * @param integer|null $price
     * @param integer|null $amount     
     * @return ActionInterface $action
     */
    public function updateProduct(
        ?string $name,
        ?string $description,
        ?int $price,
        ?int $amount
    ): ActionInterface {
        $payload = [];

        if (!is_null($name))        $payload["name"] = $name;
        if (!is_null($description)) $payload["description"] = $description;
        if (!is_null($price))       $payload["price"] = $price;
        if (!is_null($amount))      $payload["amount"] = $amount;

        $action = $this->getAction("PUT", "/api/v1/products")
            ->addOption("json", $payload)
            ->doneHandler(function (
                ResponseInterface $response,
                Action $action
            ){
                $resBody = $response->getBody()->getContents();
                $data    = json_decode($resBody, true);
                $action->setMeaningData($data["data"]);
            })
            ->failHandler(function (
                ActionException $e
            ){
                $errorResult = $e->getResponse()->getBody();
                $data = json_decode($errorResult, true);
                if ($e->isServerError()) {
                    log_message("error", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isClientError()) {
                    $errorResult = $e->getResponse()->getBody()->getContents();
                    $data = json_decode($errorResult, true);
                    log_message("notice", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isConnectError()) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }
            });
        return $action;
    }

    /**
     * 取得刪除商品 Action
     *
     * @param integer $productKey
     * @return ActionInterface $action
     */
    public function deleteProduct(int $productKey): ActionInterface
    {
        $action = $this->getAction("DELETE","/api/v1/products/{$productKey}")
            ->doneHandler(function(
                ResponseInterface $response,
                Action $action
            ){
                $resBody = $response->getBody()->getContents();
                $data = json_decode($resBody,true);
                $action->setMeaningData($data["data"]);
            })
            ->failHandler(function (
                ActionException $e
            ) {
                $errorResult = $e->getResponse()->getBody();
                $data = json_decode($errorResult, true);
                if ($e->isServerError()) {
                    log_message("error", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isClientError()) {
                    $errorResult = $errorResult->getContents();
                    $data = json_decode($errorResult, true);
                    log_message("notice", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }

                if ($e->isConnectError()) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData([]);
                }
            });
        return $action;
    }
}
