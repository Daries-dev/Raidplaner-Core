<?php

namespace wcf\system\endpoint\controller\rp\events;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\data\event\Event;
use wcf\http\Helper;
use wcf\system\endpoint\DeleteRequest;
use wcf\system\endpoint\IController;
use wcf\system\exception\PermissionDeniedException;
use wcf\system\WCF;

/**
 * API endpoint for the deletion of events.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[DeleteRequest('/rp/events/{id:\d+}')]
final class DeleteEvent implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $event = Helper::fetchObjectFromRequestParameter($variables['id'], Event::class);

        $this->assertEventIsDeletable($event);

        (new \rp\system\event\command\DeleteEvents([$event]))();

        return new JsonResponse([]);
    }

    private function assertEventIsDeletable(Event $event): void
    {
        if (!$event->canDelete()) {
            throw new PermissionDeniedException();
        }
    }
}
