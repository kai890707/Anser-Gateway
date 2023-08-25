<?php

namespace App\Services\V2\PaymentService;

use SDPMlab\Anser\Service\SimpleService;
use SDPMlab\Anser\Service\Action;
use SDPMlab\Anser\Exception\ActionException;
use Psr\Http\Message\ResponseInterface;
use SDPMlab\Anser\Service\ActionInterface;

class Wallet extends SimpleService
{

    protected $serviceName = "payment_service";
    protected $retry      = 0;
    protected $retryDelay = 0.2;
    protected $timeout    = 6000.0;

    /**
     * 取得使用者錢包餘額 Action
     *
     * @return ActionInterface $action
     */
    public function getWallet(int $userKey): ActionInterface
    {
        $action = $this->getAction("GET", "/api/v1/wallet")
            ->addOption("headers", [
                "X-User-key" => $userKey
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
     * 取得使用者錢包儲值 Action
     *
     * @param integer $addAmount
     * @return ActionInterface $action
     */
    public function storeToWallet(int $addAmount): ActionInterface
    {
         $action = $this->getAction("POST", "/api/v1/wallet")
            ->addOption("headers", [
                "X-User-key" => 1
            ])
            ->addOption("form_params",[
                "addAmount" => $addAmount
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
     * 取得使用者錢包補償 Action
     *
     * @param integer $addAmount
     * @return ActionInterface $action
     */
    public function walletCompensate(int $addAmount): ActionInterface
    {
        $action = $this->getAction("POST", "/api/v1/wallet/compensate")
            ->addOption("headers", [
                "X-User-key" => 1
            ])
            ->addOption("form_params",[
                "addAmount" => $addAmount
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
}