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

use Joli\SeoOverride\SeoManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class SeoSubscriber implements EventSubscriberInterface
{
    /**
     * @var SeoManagerInterface
     */
    private $seoManager;

    public function __construct(SeoManagerInterface $seoManager)
    {
        $this->seoManager = $seoManager;
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        if ($event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) {
            $path = $event->getRequest()->getPathInfo();
            $domain = $event->getRequest()->getHost();

            $responseContent = $event->getResponse()->getContent();

            if (!$responseContent) {
                return;
            }

            if ($event->getResponse()->getStatusCode() >= 300) {
                // We do not want to trigger fetchers on non 2XX response
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
