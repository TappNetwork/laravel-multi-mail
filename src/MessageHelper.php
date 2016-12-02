<?php

namespace ElfSundae\Multimail;

class MessageHelper
{
    /**
     * Get all recipients for the given message, including To, Reply-To,
     * Cc and Bcc addresses.
     *
     * When the `$associated` is true, this method will return an associative
     * array, whereby the keys provide the actual email addresses and the values
     * provide the display names.
     *
     * @param  \Swift_Mime_Message|Illuminate\Mail\Message  $message
     * @param  bool  $associated
     * @return string[]
     */
    public static function getRecipients($message, $associated = false)
    {
        $recipients = array_merge(
            (array) $message->getTo(),
            (array) $message->getReplyTo(),
            (array) $message->getCc(),
            (array) $message->getBcc()
        );

        if (! $associated) {
            $recipients = array_keys($recipients);
        }

        return $recipients;
    }

    /**
     * Get domains of the email addresses for the message recipients.
     *
     * @param  \Swift_Mime_Message|Illuminate\Mail\Message  $message
     * @return string[]
     */
    public static function getRecipientsDomains($message)
    {
        return array_values(array_unique(array_map(
            function ($address) {
                return strtolower(last(explode('@', $address)));
            },
            static::getRecipients($message, false)
        )));
    }
}
