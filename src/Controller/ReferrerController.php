<?php

namespace Drupal\only_one_referrer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\onlyone\OnlyOne;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Language\LanguageManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ReferrerController
 *
 * @package Drupal\only_one_referrer\Controller
 */
class ReferrerController extends ControllerBase
{

    /**
     * The only one service.
     *
     * @var \Drupal\onlyone\OnlyOne
     */
    protected $onlyOne;

    /**
     * The language manager
     *
     * @var \Drupal\Core\Language\LanguageManagerInterface
     */
    protected $languageManager;

    /**
     * ReferrerController constructor.
     *
     * @param \Drupal\onlyone\OnlyOne $onlyOne
     *   The only one service.
     * @param \Drupal\Core\Language\LanguageManagerInterface $languageManager
     *   The language manager.
     */
    public function __construct(OnlyOne $onlyOne, LanguageManagerInterface $languageManager)
    {
        $this->onlyOne = $onlyOne;
        $this->languageManager = $languageManager;
    }

    /**
     * Create.
     *
     * @param ContainerInterface $container
     *
     * @return ControllerBase|ReferrerController
     */
    public static function create(ContainerInterface $container)
    {

        /** @var \Drupal\onlyone\OnlyOne $onlyOne */
        $onlyOne = $container->get('onlyone');

        /** @var \Drupal\Core\Language\LanguageManagerInterface $languageManager */
        $languageManager = $container->get('language_manager');

        return new static(
            $onlyOne,
            $languageManager
        );
    }

    /**
     * Redirect to the correct page.
     *
     * @param string $only_one
     *   The identifier for the page to redirect to.
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     *   The redirect.
     */
    public function referrer($only_one) {
      $match = $this->onlyOne->existsNodesContentType($only_one, $this->languageManager->getCurrentLanguage()->getId());
      if (!$match) {
        throw new NotFoundHttpException();
      }

      return $this->redirect('entity.node.canonical', ['node' => $match]);
    }

}
