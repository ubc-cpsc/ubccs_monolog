<?php

namespace Drupal\ubccs_monolog\Logger\Handler;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\monolog\Logger\Handler\DrupalMailHandler as MonologDrupalMailHandler;

/**
 * DrupalMailHandler uses the Drupal's core mail manager to send Log emails.
 */
class DrupalMailHandler extends MonologDrupalMailHandler {

  /**
   * DrupalMailHandler constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   * @param int|string|\Monolog\Level $level
   *   The minimum logging level at which this handler will be triggered.
   * @param bool $bubble
   *   The bubbling behavior.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    $level,
    bool $bubble = TRUE
  ) {
    $to = $config_factory->get('ubccs_monolog.settings')->get('notification_email') ??
      $config_factory->get('system.site')->get('mail');

    parent::__construct($to, $level, $bubble);

  }

}
