<?php

namespace Drupal\ubccs_monolog\Logger\Handler;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\monolog\Logger\ConditionResolver\ConditionResolverInterface;
use Drupal\monolog\Logger\Handler\DrupalMailHandler as MonologDrupalMailHandler;
use Monolog\LogRecord;

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
   * @param \Drupal\monolog\Logger\ConditionResolver\ConditionResolverInterface $conditionResolver
   *   The condition resolver.
   * @param bool $bubble
   *   The bubbling behavior.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    $level,
    private readonly ConditionResolverInterface $conditionResolver,
    bool $bubble = TRUE
  ) {
    $to = $config_factory->get('ubccs_monolog.settings')->get('notification_email') ??
      $config_factory->get('system.site')->get('mail');

    parent::__construct($to, $level, $bubble);

  }

  /**
   * {@inheritdoc}
   */
  protected function write(LogRecord $record): void {
    // Do not send emails when it is a CLI request.
    if (!$this->conditionResolver->resolve()) {
      $this->send((string) $record->formatted, [$record]);
    }
  }

}
