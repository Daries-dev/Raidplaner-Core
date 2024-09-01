<?php

namespace rp\system\user\notification\event;

use rp\system\cache\runtime\ViewableEventRuntimeCache;
use wcf\system\user\notification\event\AbstractCommentUserNotificationEvent;
use wcf\system\user\notification\event\ITestableUserNotificationEvent;
use wcf\system\user\notification\event\TTestableCommentUserNotificationEvent;

/**
 * User notification event for event comments.
 * 
 * @author  Marco Daries
 * @copyright   2023-2024 Daries.dev
 * @license Raidplaner is licensed under Creative Commons Attribution-ShareAlike 4.0 International 
 */
final class EventCommentUserNotificationEvent  extends AbstractCommentUserNotificationEvent implements
    ITestableUserNotificationEvent
{
    use TTestableCommentUserNotificationEvent;
    use TTestableEventCommentUserNotificationEvent;

    /**
     * @inheritDoc
     */
    public function getEmailMessage($notificationType = 'instant'): array
    {
        return [
            'message-id' => \sprintf(
                'dev.daries.rp.eventComment.notification/%d',
                $this->getUserNotificationObject()->commentID
            ),
            'template' => 'email_notification_comment',
            'application' => 'wcf',
            'variables' => [
                'commentID' => $this->getUserNotificationObject()->commentID,
                'eventObj' => ViewableEventRuntimeCache::getInstance()
                    ->getObject($this->getUserNotificationObject()->objectID),
                'languageVariablePrefix' => 'rp.user.notification.eventComment',
            ],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getLink(): string
    {
        return ViewableEventRuntimeCache::getInstance()->getObject($this->getUserNotificationObject()->objectID)->getLink() . '#comment' . $this->getUserNotificationObject()->commentID;
    }

    /**
     * @inheritDoc
     */
    public function getMessage(): string
    {
        $authors = $this->getAuthors();
        if (\count($authors) > 1) {
            if (isset($authors[0])) {
                unset($authors[0]);
            }
            $count = \count($authors);

            return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.message.stacked', [
                'author' => $this->author,
                'authors' => \array_values($authors),
                'commentID' => $this->getUserNotificationObject()->commentID,
                'count' => $count,
                'event' => ViewableEventRuntimeCache::getInstance()
                    ->getObject($this->getUserNotificationObject()->objectID),
                'guestTimesTriggered' => $this->notification->guestTimesTriggered,
                'others' => $count - 1,
            ]);
        }

        return $this->getLanguage()->getDynamicVariable('rp.user.notification.eventComment.message', [
            'author' => $this->author,
            'commentID' => $this->getUserNotificationObject()->commentID,
            'event' => ViewableEventRuntimeCache::getInstance()
                ->getObject($this->getUserNotificationObject()->objectID),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function getObjectTitle(): string
    {
        return ViewableEventRuntimeCache::getInstance()
            ->getObject($this->getUserNotificationObject()->objectID)->getTitle();
    }

    /**
     * @inheritDoc
     */
    protected function getTypeName(): string
    {
        return $this->getLanguage()->get('wcf.user.recentActivity.dev.daries.rp.event.recentActivityEvent');
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        ViewableEventRuntimeCache::getInstance()->cacheObjectID($this->getUserNotificationObject()->objectID);
    }
}