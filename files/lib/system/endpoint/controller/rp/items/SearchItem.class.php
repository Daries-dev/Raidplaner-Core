<?php

namespace rp\system\endpoint\controller\rp\items;

use Laminas\Diactoros\Response\JsonResponse;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use rp\system\item\ItemHandler;
use wcf\http\Helper;
use wcf\system\endpoint\GetRequest;
use wcf\system\endpoint\IController;

/**
 * API endpoint for search items for item.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
#[GetRequest('/rp/items/search')]
final class SearchItem implements IController
{
    #[\Override]
    public function __invoke(ServerRequestInterface $request, array $variables): ResponseInterface
    {
        $parameters = Helper::mapApiParameters($request, SearchItemParameters::class);

        $item = ItemHandler::getInstance()->getSearchItem($parameters->itemName);

        return new JsonResponse([
            'itemID' => $item->getObjectID(),
            'itemName' => $item->getTitle(),
        ]);
    }
}

/** @internal */
final class SearchItemParameters
{
    public function __construct(
        /** @var non-empty-string **/
        public readonly string $itemName,
    ) {}
}
