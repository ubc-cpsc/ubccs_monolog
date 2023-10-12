<?php

namespace Drupal\ubccs_monolog;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;
use Symfony\Component\DependencyInjection\Reference;

/**
 *
 */
class UbccsMonologServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $modules = $container->getParameter('container.modules');
    if (!isset($modules['symfony_mailer'])) {
      return;
    }

    $definition = $container->getDefinition('monolog.handler.mail');
    $args = $definition->getArguments();
    $definition->setClass('Drupal\ubccs_monolog\Logger\Handler\DrupalSymfonyMailHandler')
      ->setArguments(array_merge([
        new Reference('symfony_mailer'),
        new Reference('email_factory'),
      ], $args));

    $definition = $container->getDefinition('monolog.handler.php_mail');
    $args = $definition->getArguments();
    $definition->setClass('Drupal\ubccs_monolog\Logger\Handler\DrupalSymfonyMailHandler')
      ->setArguments(array_merge([
        new Reference('symfony_mailer'),
        new Reference('email_factory'),
      ], $args));
  }

}
