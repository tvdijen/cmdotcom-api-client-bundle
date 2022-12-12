<?php

namespace tvdijen\CMDotCom\ApiClientBundle\Service;

use Psr\Log\LoggerInterface;
use tvdijen\CMDotCom\ApiClient\Exception\ApiDomainException;
use tvdijen\CMDotCom\ApiClient\Exception\ApiException;
use tvdijen\CMDotCom\ApiClient\Exception\ApiRuntimeException;
use tvdijen\CMDotCom\ApiClient\Exception\InvalidAccessKeyException;
use tvdijen\CMDotCom\ApiClient\Exception\UnprocessableMessageException;
use tvdijen\CMDotCom\ApiClient\Messaging\Message;
use tvdijen\CMDotCom\ApiClient\Messaging\MessagingService as LibraryMessagingService;
use tvdijen\CMDotCom\ApiClient\Messaging\SendMessageResult;

use function sprintf;

class MessagingService
{
    /**
     * @var \tvdijen\CMDotCom\ApiClient\Messaging\MessagingService
     */
    private LibraryMessagingService $messagingService;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private LoggerInterface $logger;


    public function __construct(LibraryMessagingService $messagingService, LoggerInterface $logger)
    {
        $this->messagingService = $messagingService;
        $this->logger = $logger;
    }


    /**
     * @param \tvdijen\CMDotCom\ApiClient\Messaging\Message $message
     * @return \tvdijen\CMDotCom\ApiClient\Messaging\SendMessageResult
     */
    public function send(Message $message): SendMessageResult
    {
        try {
            $result = $this->messagingService->send($message);
        } catch (ApiRuntimeException $e) {
            $this->logger->error(
                sprintf('Unexpected communication failure with CM.com; %s', $e->getMessage()),
                $this->createMessageLogContext($message)
            );

            throw $e;
        }

        if ($result->isMessageInvalid()) {
            $this->logger->notice(
                sprintf('Invalid message sent to CM.com (%s)', $result->getErrorsAsString()),
                $this->createMessageLogContext($message)
            );
        }

        if ($result->isAccessKeyInvalid()) {
            $this->logger->critical(
                sprintf('Invalid access key used for CM.com (%s)', $result->getErrorsAsString()),
                $this->createMessageLogContext($message)
            );
        }

        return $result;
    }


    /**
     * @param \tvdijen\CMDotCom\ApiClient\Messaging\Message $message
     * @return array
     */
    private function createMessageLogContext(Message $message): array
    {
        return [
            'message' => ['recipient' => $message->getRecipient(), 'body' => $message->getBody()],
        ];
    }
}
