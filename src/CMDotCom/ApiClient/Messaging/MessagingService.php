<?php

namespace tvdijen\CMDotCom\ApiClient\Messaging;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\TransferException;
use RuntimeException;
use tvdijen\CMDotCom\ApiClient\Exception\ApiRuntimeException;
use tvdijen\CMDotCom\ApiClient\Helper\JsonHelper;

use function in_array;
use function intval;
use function sprintf;
use function strval;

class MessagingService
{
    /**
     * A Guzzle client, configured with CM.com's API base url and a valid Authorization header.
     *
     * @var \GuzzleHttp\Client
     */
    private Client $guzzleClient;

    /**
     * @param \GuzzleHttp\Client $guzzleClient
     */
    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param \tvdijen\CMDotCom\ApiClient\Messaging\Message $message
     * @return \tvdijen\CMDotCom\ApiClient\Messaging\SendMessageResult
     *
     * @throws \tvdijen\CMDotCom\ApiClient\Exception\ApiRuntimeException
     * @throws \GuzzleHttp\Exception\TransferException Thrown by Guzzle during communication failure or unexpected server behaviour.
     */
    public function send(Message $message): SendMessageResult
    {
        $response = $this->guzzleClient->post('/messages', [
            'json' => [
                'originator' => $message->getOriginator(),
                'recipients' => $message->getRecipient(),
                'body'       => $message->getBody(),
            ],
            'http_errors' => false,
        ]);

        try {
            $document = JsonHelper::decode(strval($response->getBody()));
        } catch (RuntimeException $e) {
            throw new ApiRuntimeException('The CM.com server did not return valid JSON.', [], $e);
        }

        if (isset($document['errors'])) {
            $errors = $document['errors'];
        } else {
            $errors = [];
        }

        $statusCode = intval($response->getStatusCode());

        if (!in_array($statusCode, [200, 201, 204, 401, 404, 405, 422]) && !($statusCode >= 500 && $statusCode < 600)) {
            throw new ApiRuntimeException(sprintf('Unexpected CM.com server behaviour (HTTP %d)', $statusCode), $errors);
        }

        if (!isset($document['recipients']['items'][0]['status'])) {
            $deliveryStatus = SendMessageResult::STATUS_NOT_SENT;
        } else {
            $deliveryStatus = $document['recipients']['items'][0]['status'];
        }

        return new SendMessageResult($deliveryStatus, $errors);
    }
}
