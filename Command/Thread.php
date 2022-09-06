<?php

namespace Truonglv\Telegram\Command;

use XF;

class Thread extends AbstractHandler
{
    public function handle(): void
    {
        /** @var \XF\Finder\Thread|null $finder */
        $finder = null;
        switch ($this->command) {
            case 'most_viewed_threads':
                /** @var \XF\Finder\Thread $finder */
                $finder = $this->app->finder('XF:Thread');
                $finder->where('post_date', '>=', XF::$time - 86400);
                $finder->order('view_count', 'desc');

                break;
            case 'most_replied_threads':
                /** @var \XF\Finder\Thread $finder */
                $finder = $this->app->finder('XF:Thread');
                $finder->where('post_date', '>=', XF::$time - 86400);
                $finder->order('reply_count', 'desc');

                break;
            case 'recent_threads':
                /** @var \XF\Finder\Thread $finder */
                $finder = $this->app->finder('XF:Thread');
                $finder->where('last_post_date', '>=', XF::$time - 86400);
                $finder->order('last_post_date', 'desc');

                break;
        }

        if ($finder === null) {
            return;
        }

        // TODO: support command argument limit
        $limit = 10;

        $finder->where('discussion_state', 'visible');
        $finder->with('User');
        $finder->limit($limit * 2);

        $threads = $finder->fetch()->filterViewable();
        if ($threads->count() === 0) {
            $this->telegram->sendMessage('There are no threads to display.');

            return;
        }

        if ($threads->count() > $limit) {
            $threads = $threads->slice(0, $limit);
        }

        $messages = [];
        $router = $this->app->router('public');
        /** @var \XF\Entity\Thread $thread */
        foreach ($threads as $thread) {
            $messages[] = sprintf(
                '<a href="%s">%s</a> - %s',
                htmlspecialchars($router->buildLink('canonical:threads', $thread)),
                htmlspecialchars($thread->title),
                htmlspecialchars($thread->User !== null ? $thread->User->username : $thread->username)
            );
        }

        $this->telegram->sendMessage(implode("\n", $messages), [
            'parse_mode' => 'HTML'
        ]);
    }

    public function description(): string
    {
        switch ($this->command) {
            case 'most_viewed_threads':
                return 'Top 10 viewed threads in 24 hours';
            case 'most_replied_threads':
                return 'Top 10 replied threads in 24 hours';
            case 'recent_threads':
                return 'Get 10 recent threads in 24 hours';
        }

        return '';
    }
}
