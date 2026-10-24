<?php

namespace VG_Mailer\src\models;

use VG_Mailer\src\interfaces\SenderWorkerModelInterface;

class EmailModel implements SenderWorkerModelInterface
{
    private static EmailModel $model;
    protected bool $use = false;
    protected ?string $toMail = null;
    protected ?EmailSMTPModel $SMTPModel = null;
    protected bool $html = false;
    protected ?string $fromMail = null;
    protected ?string $subject = null;
    protected ?string $name = null;

    public static function make()
    {
        if (!isset(self::$model)) {
            self::$model = new EmailModel();
        }
        return self::$model;
    }

    /**
     * @return string|null
     */
    public function getSubject(): ?string
    {
        return $this->subject;
    }

    /**
     * @param string|null $subject
     * @return EmailModel
     */
    public function setSubject(?string $subject): EmailModel
    {
        $this->subject = $subject;
        return $this;
    }


    /**
     * @return string|null
     */
    public function getFromMail(): ?string
    {
        return $this->fromMail;
    }

    /**
     * @param string|null $fromMail
     * @return EmailModel
     */
    public function setFromMail(?string $fromMail): EmailModel
    {
        $this->fromMail = $fromMail;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHtml(): bool
    {
        return $this->html;
    }

    /**
     * @param bool $html
     * @return EmailModel
     */
    public function setHtml(bool $html): EmailModel
    {
        $this->html = $html;
        return $this;
    }

    /**
     * @return bool
     */
    public function isUse(): bool
    {
        return $this->use;
    }

    /**
     * @param bool $use
     * @return EmailModel
     */
    public function setUse(bool $use): EmailModel
    {
        $this->use = $use;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getToMail(): ?string
    {
        return $this->toMail;
    }

    /**
     * @param string|null $toMail
     * @return EmailModel
     */
    public function setToMail(?string $toMail): EmailModel
    {
        $this->toMail = $toMail;
        return $this;
    }

    /**
     * @return EmailSMTPModel|null
     */
    public function getSMTPModel(): ?EmailSMTPModel
    {
        return $this->SMTPModel;
    }

    /**
     * @param EmailSMTPModel|null $SMTPModel
     * @return EmailModel
     */
    public function setSMTPModel(?EmailSMTPModel $SMTPModel): EmailModel
    {
        $this->SMTPModel = $SMTPModel;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return EmailModel
     */
    public function setName(?string $name): EmailModel
    {
        $this->name = $name;
        return $this;
    }
}