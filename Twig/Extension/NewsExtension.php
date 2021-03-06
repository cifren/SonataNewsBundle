<?php

/*
 * This file is part of sonata-project.
 *
 * (c) 2010 Thomas Rabaix
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\NewsBundle\Twig\Extension;

use Symfony\Component\Routing\Router;
use Sonata\NewsBundle\Model\TagManagerInterface;
use Sonata\NewsBundle\Model\BlogInterface;

class NewsExtension extends \Twig_Extension
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var CmsManagerSelectorInterface
     */
    private $tagManager;

    /**
     * @param \Symfony\Component\Routing\Router $router
     * @param \Sonata\NewsBundle\Entity\TagManagerInterface $tagManager
     */
    public function __construct(Router $router, TagManagerInterface $tagManager, BlogInterface $blog)
    {
        $this->router       = $router;
        $this->tagManager   = $tagManager;
        $this->blog         = $blog;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array(
            'sonata_news_link_tag_rss' => new \Twig_Function_Method($this, 'renderTagRss', array('is_safe' => array('html'))),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'sonata_news';
    }

    /**
     * @param null|\Sonata\PageBundle\Model\PageInterface $page
     * @param array $options
     * @return
     */
    public function renderTagRss()
    {
        $rss = array();
        foreach($this->tagManager->findBy(array('enabled' => true)) as $tag) {
            $rss[] = sprintf('<link href="%s" title="%s : %s" type="application/rss+xml" rel="alternate" />',
                $this->router->generate('sonata_news_tag', array('tag' => $tag->getSlug(), '_format' => 'rss'), true),
                $this->blog->getTitle(),
                $tag->getName()
            );
        }

        return implode("\n", $rss);
    }
}

