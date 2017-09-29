<?php

/*
 * This file is part of the SeoOverride project.
 *
 * (c) JoliCode <coucou@jolicode.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Joli\SeoOverride\Bridge\Symfony\Subscriber;

use Joli\SeoOverride\Bridge\Symfony\Blacklister;
use Joli\SeoOverride\SeoManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SeoSubscriber implements EventSubscriberInterface
{
    /**
     * @var SeoManager
     */
    private $seoManager;

    /**
     * @var Blacklister
     */
    private $blacklister;

    public function __construct(SeoManager $seoManager, Blacklister $blacklister)
    {
        $this->seoManager = $seoManager;
        $this->blacklister = $blacklister;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST === $event->getRequestType()) {
            $path = $event->getRequest()->getPathInfo();
            $domain = $event->getRequest()->getHost();

            $responseContent = $event->getResponse()->getContent();

            if (!$responseContent) {
                return;
            }

            if ($this->blacklister->isBlacklisted($event->getRequest(), $event->getResponse())) {
                // Overrides HTML to remove custom comment without running fetchers
                $newResponseContent = $this->seoManager->overrideHtml($responseContent);
            } else {
                $newResponseContent = $this->seoManager->updateAndOverride($responseContent, $path, $domain);
            }

            $event->getResponse()->setContent($newResponseContent);
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => [
                ['onKernelResponse', 0],
            ],
        ];
    }
}
