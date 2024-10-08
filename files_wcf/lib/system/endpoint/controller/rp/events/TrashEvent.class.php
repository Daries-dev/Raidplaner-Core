<?php

namespace wcf\system\endpoint\controller\rp\events;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\event\Event;
use wcf\http\Helper;
use wcf\system\endpoint\IController;
use wcf\system\endpoint\PostRequest;
use wcf\system\exception\IllegalLinkException;
use wcf\system\exception\PermissionDeniedException;

/**
 * API endpoint for the trash a events.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[PostRequest('/rp/events/{id:\d+}/trash')]
final class TrashEvent implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $event = Helper::fetchObjectFromRequestParameter($variables['id'], Event::class);

        $this->assertEventIsEditable($event);

        (new \rp\system\event\command\TrashEvent($event))();

        return new JsonResponse([]);
    }

    private function assertEventIsEditable(Event $event): void
    {
        if ($event->isDeleted) {
            throw new IllegalLinkException();
        }

        if (!$event->canTrash()) {
            throw new PermissionDeniedException();
        }
    }
}
