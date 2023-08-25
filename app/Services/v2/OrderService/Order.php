<?php

namespace App\Services\V2\OrderService;

use SDPMlab\Anser\Service\SimpleService;
use SDPMlab\Anser\Service\Action;
use SDPMlab\Anser\Exception\ActionException;
use Psr\Http\Message\ResponseInterface;
use SDPMlab\Anser\Service\ActionInterface;


class Order extends SimpleService
{

    protected $serviceName = "order_service";
    protected $retry      = 0;
    protected $retryDelay = 0.2;
    protected $timeout    = 6000.0;

    /**
     * 取得商品清單 Action
     *
     * @param integer|null $limit
     * @param integer|null $offset
     * @param string|null $isDesc
     * @return ActionInterface $action
     */
    public function getOrderList(
        ?int $limit,
        ?int $offset,
        ?string $isDesc
    ): ActionInterface {

        $payload = [];

        if (!is_null($limit))     $payload["limit"]  = $limit;
        if (!is_null($offset))    $payload["offset"] = $offset;
        if (!is_null($isDesc))    $payload["isDesc"] = $isDesc;

        $action = $this->getAction("GET", "/api/v1/order");
        if (!empty($payload)) $action->addOption("query", $payload);

        $action->addOption("headers", [
            "X-User-key" => 1
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
     * 取得訂單 Action
     *
     * @param string $orderKey
     * @return ActionInterface $action
     */
    public function getOrder(string $orderKey): ActionInterface
    {
        $action = $this->getAction("GET","/api/v1/order/{$orderKey}")
            ->addOption("headers", [
                "X-User-key" => 1
            ])
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

    /**
     * 取得新增訂單 Action
     *
     * @param string $orderKey
     * @param integer $discount
     * @param array $productDetailArr
     * @return ActionInterface $action
     */
    public function createOrder(
        string $orderKey,
        int $discount,
        array $productDetailArr,
        int $userKey
    ): ActionInterface {
        $action = $this->getAction("POST", "/api/v1/order")
            ->addOption("json",[
                "o_key"            => $orderKey,
                "discount"         => $discount,
                "productDetailArr" => $productDetailArr
            ])
            ->addOption("headers", [
                "X-User-key" => $userKey
            ])
            ->doneHandler(function (
                ResponseInterface $response,
                Action $action
            ){
                $resBody = $response->getBody()->getContents();
                $data    = json_decode($resBody, true);
                $action->setMeaningData($data);
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
     * 取得更新訂單 Action
     *
     * @param string $orderKey
     * @param integer|null $discount
     * @param array|null $productDetailArr
     * @return ActionInterface $action
     */
    public function updateOrder(
        string $orderKey,
        ?int $discount,
        ?array $productDetailArr
    ): ActionInterface
    {
        $payload = [];

        $payload["o_key"] = $orderKey;
        if (!is_null($discount))         $payload["discount"] = $discount;
        if (!is_null($productDetailArr)) $payload["productDetailArr"] = $productDetailArr;

        $action = $this->getAction("PUT", "/api/v1/order")
            ->addOption("json", $payload)
            ->addOption("headers", [
                "X-User-key" => 1
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
     * 取得刪除訂單 Action
     *
     * @param string $orderKey
     * @return ActionInterface $action
     */
    public function deleteOrder(string $orderKey, int $userKey): ActionInterface
    {
        $action = $this->getAction("DELETE","/api/v1/order/{$orderKey}")
            ->addOption("headers", [
                "X-User-key" => $userKey
            ])
            ->doneHandler(function(
                ResponseInterface $response,
                Action $action
            ){
                $resBody = $response->getBody()->getContents();
                $data = json_decode($resBody,true);
                $action->setMeaningData($data);
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
