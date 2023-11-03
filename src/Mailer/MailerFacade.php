<?php

namespace App\Facades;


use App\Helpers\Validators;
use App\Libs\FileManager\FileManager;
use App\Store\OpeningHours\OpeningHoursFacadeFactory;
use Kdyby\Monolog\Logger;
use Kdyby\Translation\Latte\TranslateMacros;
use Kdyby\Translation\TemplateHelpers;
use Kdyby\Translation\Translator;
use Nette\Application\Application;
use Nette\Application\LinkGenerator;
use Nette\Bridges\ApplicationLatte\UIMacros;
use Nette\Database\Context;
use Nette\DI\Container;
use Nette\Mail\IMailer;
use Nette\Mail\Message;
use Nette\Mail\SendException;
use Nette\Utils\DateTime;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class MailerFacade
{


    /** @var bool */
    protected static $testEnvironment = false;

    /** @var array|Message[] for save emails in test environment */
    protected static $emails = [];

    /** @var IMailer */
    protected $mailer;

    /** @var Application */
    protected $application;

    /** @var Container */
    protected $container;

    /** @var FileManager */
    protected $fileManager;

    /** @var Translator */
    protected $translator;

    /** @var LinkGenerator */
    protected $linkGenerator;

    /** @var Logger */
    protected $logger;

    /** @var array */
    protected $to = [];

    /** @var array */
    protected $bcc = [];

		/** @var array */
    protected $attachedFiles = [];
		
    /** @var string|null */
    protected $subject;

    /** @var string|null */
    protected $template;

    /** @var string|null */
    protected $fromEmail;

    /** @var string|null */
    protected $fromName;

    /** @var OpeningHoursFacadeFactory */
    protected $openingHoursFacadeFactory;

    /** @var Context */
    protected $database;

    const MAX_TIRES = 20;

    public function __construct(
        Application $application,
        Container $container,
        IMailer $mailer,
        FileManager $fileManager,
        Translator $translator,
        LinkGenerator $linkGenerator,
        Logger $logger,
        OpeningHoursFacadeFactory $openingHoursFacadeFactory,
        Context $database
    ) {
        $this->application = $application;
        $this->mailer = $mailer;
        $this->fileManager = $fileManager;
        $this->translator = $translator;
        $this->linkGenerator = $linkGenerator;
        $this->logger = $logger;
        $this->container = $container;
        $parameters = $container->getParameters();
        $this->fromEmail = $parameters["project"]["email"];
        $this->fromName = $parameters["project"]["name"];
        $this->openingHoursFacadeFactory = $openingHoursFacadeFactory;
        $this->database = $database;
    }


    /**
     * Set test environment
     * @param $param bool
     * @return void
     */
    public static function setTestEnvironment(bool $param)
    {
        self::$testEnvironment = $param;
    }


    /**
     * Get emails if is set test environment
     * @param $clear bool clear stack with sent emails
     * @return array|Message[]
     */
    public static function getEmails(bool $clear = false)
    {
        $emails = self::$emails;
        if ($clear === true) {
            self::$emails = [];
        }
        return $emails;
    }


    /**
     * Add reciever
     * @param $email mixed
     * @return self
     */
    public function addTo($email)
    {
        foreach (is_array($email) ? $email : [$email] as $e) {
            $this->to[] = $e;
        }
        return $this;
    }


    /**
     * Add bcc reciever
     * @param $email mixed
     * @return self
     */
    public function addBcc($email)
    {
        foreach (is_array($email) ? $email : [$email] as $e) {
            $this->bcc[] = $e;
        }
        return $this;
    }


    /**
     * Add attached files
     * @param $files mixed
     * @return self
     */
    public function addAttachedFiles($files)
    {
        foreach (is_array($files) ? $files : [$files] as $e) {
            $this->attachedFiles[] = $e;
        }
        return $this;
    }
		
		
    /**
     * Set subject
     * @param $subject string
     * @return self
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }


    /**
     * @deprecated
     */
    public function addTemplate($name, $params)
    {
        $this->setTemplate($name, $params);
        return $this;
    }


    /**
     * Set template
     * @param $name string
     * @param $params array
     * @return self
     */
    public function setTemplate($name, $params = [])
    {
        $latte = new \Latte\Engine;
        (new TemplateHelpers($this->translator))->register($latte);
        \App\Extensions\Latte\MacroSet::install($latte->getCompiler(), $this->translator);
        UIMacros::install($latte->getCompiler());
        TranslateMacros::install($latte->getCompiler());
        $path = is_file($name) ? $name : __DIR__ . '/../../src/Model/Emails/' . $name . '.latte';
        $configParameters = $this->container->getParameters();
        $this->template = $latte
            ->addProvider("uiControl", $this->linkGenerator)
            ->renderToString($path, array_merge([
                "fromName" => $this->fromName,
                "fromEmail" => $this->fromEmail,
                'configParameters' => $configParameters,
                'actualYear' => (new \DateTime())->format('Y'),
                'openingHours' => $this->openingHoursFacadeFactory->create()->getWeekList(),
                'title' => $this->subject,
                'fileManager' => $this->fileManager,
            ], $params));
        return $this;
    }


    /**
     * store email into queue
     * @return void
     */
    public function send()
    {
        $this->storeEmail();
        $this->clear();
    }


    /**
     * Create email form db record
     * @return \Nette\Mail\Message
     */
    protected function createEmailFromActiveRow($emailRow)
    {
        $email = new \Nette\Mail\Message();
        foreach (explode(',', $emailRow->to) as $e) {
            if (Validators::isEmail($e)) {
                $email->addTo($e);
            }
        }
        foreach (explode(',', $emailRow->bcc) as $e) {
            if (Validators::isEmail($e)) {
                $email->addBcc($e);
            }
        }
				if (strlen($emailRow->attachedFiles)) {
						foreach (explode(',', $emailRow->attachedFiles) as $e) {
								$email->addAttachment( __DIR__.'/../..'.$e);
						}
				}
        $email->setFrom($emailRow->fromMail, $emailRow->fromName);
        $email->setHtmlBody($emailRow->htmlbody);
        $email->setSubject($emailRow->subject);
        return $email;
    }

    /**
     * Create email
     * @return \Nette\Mail\Message
     */
    protected function createEmail()
    {
        $this->checkData();
        $email = new \Nette\Mail\Message();
        foreach ($this->to as $e) {
            $email->addTo($e);
        }
        foreach ($this->bcc ? $this->bcc : [] as $e) {
            $email->addBcc($e);
        }
        foreach ($this->attachedFiles ? $this->attachedFiles : [] as $e) {
            $email->addAttachment($e);
        }
        $email->setFrom($this->fromEmail, $this->fromName);
        $email->setHtmlBody($this->template);
        $email->setSubject($this->subject);
        return $email;
    }

    public function storeEmail()
    {
        $this->checkData();

        $store = [
            'created' => new DateTime(),
            'subject' => $this->subject,
            'htmlbody' => $this->template,
            'fromMail' => $this->fromEmail,
            'fromName' => $this->fromName,
            'to' => implode(',', $this->to),
            'cc' => '', //not used yet
            'bcc' => implode(',', $this->bcc),
            'attachedFiles' => implode(',', $this->attachedFiles),

        ];
        $this->database->table('mail_queue')->insert($store);
    }

    public function getQueuedEmails()
    {
        return $this->database->table('mail_queue')
            ->where('send IS NULL')
            ->where('failed < ?', self::MAX_TIRES)
            ->where('DATE_ADD(created, INTERVAL 2 DAY) > NOW() AND (lastTry IS NULL OR DATE_ADD(lastTry, INTERVAL failed * failed MINUTE) < NOW())');
    }

    public function sendQueueEmails()
    {
        //limit to prevent memmory useage problem
        for ($i = 0; $i < 100; $i++) {
            foreach ($this->getQueuedEmails()->limit(10) as $email) {
                $this->sendEmail($email);
            }
        }
    }

    public function sendEmail($emailRow)
    {
        try {
            $email = $this->createEmailFromActiveRow($emailRow);
						$this->mailer->send($email);
            $emailRow->update(['send' => new DateTime()]);
        } catch (SendException $ex) {
					var_dump($ex);
					exit;
            $this->mailFail($emailRow, $email);
        } catch (\ErrorException $ex) {
					var_dump($ex);
					exit;
            $this->mailFail($emailRow, $email);
        }
    }

    protected function mailFail($emailRow, $email)
    {
        $emailRow->update([
            'failed' => $emailRow->failed + 1,
            'lastTry' => new DateTime(),
        ]);
        if ($emailRow->failed >= self::MAX_TIRES) {
            $this->logger->addInfo('Failed to send email.', [
                'subject' => $email->getSubject(),
                'To' => (array)$email->getHeader('To'),
                'Cc' => (array)$email->getHeader('Cc'),
                'Bcc' => (array)$email->getHeader('Bcc'),
                'headers' => $email->getHeaders(),
            ]);
        }
    }

    /**
     * Check data for create email
     * @throws \Exception
     */
    protected function checkData()
    {
        if (!$this->to) {
            throw new \Exception("Missing receiver for email.");
        } elseif (!$this->subject) {
            throw new \Exception("Missing subject for e-mail");
        }
        return $this;
    }


    /**
     * Clear self
     * @return void
     */
    protected function clear()
    {
        $this->to = [];
        $this->bcc = [];
        $this->attachedFiles = [];
        $this->subject = null;
        $this->template = null;
    }
}