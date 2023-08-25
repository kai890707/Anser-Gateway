<?php

namespace App\Services\V2\PaymentService;

use SDPMlab\Anser\Service\SimpleService;
use SDPMlab\Anser\Service\Action;
use SDPMlab\Anser\Exception\ActionException;
use Psr\Http\Message\ResponseInterface;
use SDPMlab\Anser\Service\ActionInterface;

class Payment extends SimpleService
{

    protected $serviceName = "payment_service";
    protected $retry      = 0;
    protected $retryDelay = 0.2;
    protected $timeout    = 6000.0;

    /**
     * 取得使用者付款清單 Action
     *
     * @param integer $userKey
     * @param integer|null $limit
     * @param integer|null $offset
     * @param string|null $search
     * @param string|null $isDesc
     * @return ActionInterface
     */
    public function getPaymentList(
        int $userKey,
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

        $action = $this->getAction("GET", "/api/v1/payments");
        if(!empty($payload)) $action->addOption("query", $payload);
        $action->doneHandler(function (
            ResponseInterface $response,
            Action $action
        ){
            $resBody = $response->getBody()->getContents();
            $data    = json_decode($resBody, true);
            $action->setMeaningData($data["data"]);
        })
        ->addOption("headers", [
            "X-User-key" => $userKey
        ])
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
     * 取得單筆付款 Action
     *
     * @param integer $paymentKey
     * @return ActionInterface $action
     */
    public function getPayment(int $paymentKey): ActionInterface
    {
        $action = $this->getAction("GET","/api/v1/payments/{$paymentKey}")
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
     * 取得新增付款 Action
     *
     * @param string $orderKey
     * @param integer $total
     * @return ActionInterface $action
     */
    public function createPayment(string $orderKey,int $total, int $userKey): ActionInterface
    {
        $action = $this->getAction("POST", "/api/v1/payments")
            ->addOption("form_params",[
                "o_key" => $orderKey,
                "total" => $total,
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
                if ($e->isClientError()) {
                    $errorResult = $e->getResponse()->getBody()->getContents();
                    $data = json_decode($errorResult, true);
                    $e->getAction()->setMeaningData($data);
                }

                if ($e->isServerError()) {
                    log_message("error", $e->getMessage());
                    $e->getAction()->setMeaningData(['error' => 500]);
                }

                if ($e->isConnectError()) {
                    log_message("critical", $e->getMessage());
                    $e->getAction()->setMeaningData(['error' => 000]);
                }
            });
        return $action;
    }

    /**
     * 取得更新付款 Action
     *
     * @param integer $paymentKey
     * @param integer $total
     * @return ActionInterface $action
     */
    public function updatePayment(int $paymentKey, int $total): ActionInterface
    {
        $action = $this->getAction("PUT", "/api/v1/payments")
            ->addOption("headers", [
                "X-User-key" => 1
            ])
            ->addOption("json",[
                "p_key" => $paymentKey,
                "total" => $total
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
     * 取得刪除付款 Action
     *
     * @param integer $paymentKey
     * @return ActionInterface
     */
    public function deletePayment(int $paymentKey): ActionInterface
    {
        $action = $this->getAction("DELETE","/api/v1/payments/{$paymentKey}")
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
     * 透過訂單主鍵刪除訂單付款
     *
     * @param integer $orderKey
     * @param integer $userKey
     * @param integer $total
     * @return ActionInterface
     */
    public function deletePaymentByOrderKey(int $orderKey, int $userKey, int $total): ActionInterface
    {
        $action = $this->getAction("POST", "/api/vDtm/payments/createOrderCompensate")
            ->addOption("json", [
                "o_key" => $orderKey,
                "u_key" => $userKey,
                "total" => $total
            ])
            ->doneHandler(function (
                ResponseInterface $response,
                Action $action
            ) {
                $resBody = $response->getBody()->getContents();
                $data = json_decode($resBody, true);
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