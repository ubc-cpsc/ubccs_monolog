<?php

namespace Drupal\ubccs_monolog\Logger\Handler;

use Drupal\Component\Utility\Unicode;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Render\Markup;
use Drupal\symfony_mailer\EmailFactoryInterface;
use Drupal\symfony_mailer\EmailInterface;
use Drupal\symfony_mailer\MailerInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\MailHandler;

/**
 * DrupalMailHandler uses the Drupal's core mail manager to send Log emails.
 */
class DrupalSymfonyMailHandler extends MailHandler {

  /**
   * The mailer service.
   */
  private MailerInterface $mailer;

  private EmailFactoryInterface $emailFactory;

  /**
   * The mail address to send the log emails to.
   */
  private string $to;

  /**
   * DrupalSymfonyMailHandler constructor.
   *
   * @param int|string|Level|LogLevel::* $level
   *   The minimum logging level at which this handler will be triggered.
   * @param bool $bubble
   *   Whether the messages that are handled can bubble up the stack or not.
   */
  public function __construct(MailerInterface $mailer, EmailFactoryInterface $emailFactory, ConfigFactoryInterface $config_factory, $level, bool $bubble = TRUE) {
    parent::__construct($level, $bubble);

    $this->emailFactory = $emailFactory;
    $this->mailer = $mailer;
    $this->to = $config_factory->get('ubccs_monolog.settings')->get('notification_email') ??
      $config_factory->get('system.site')->get('mail');
  }

  /**
   * {@inheritdoc}
   */
  protected function send(string $content, array $records): void {
    $this->mailer->send($this->buildMessage($content, $records));
  }

  /**
   * Builds an instance of Email to be sent.
   *
   * @param string $content
   *   formatted email body to be sent.
   * @param array $records
   *   Log records that formed the content.
   */
  protected function buildMessage(string $content, array $records): EmailInterface {
    $message = $this->emailFactory->newTypedEmail('monolog', 'default');
    $message->setTo($this->to);
    $message->setBody(Markup::create($content));

    if ($records) {
      $subjectFormatter = new LineFormatter('%message%');
      $line = $subjectFormatter->format($this->getHighestRecord($records));
      $subject = '[' . \Drupal::config('system.site')->get('name') . '] ';
      $subject .= Unicode::truncate(strip_tags($line), 78, TRUE, TRUE);
      $message->setSubject($subject);
    }

    return $message;
  }

}
