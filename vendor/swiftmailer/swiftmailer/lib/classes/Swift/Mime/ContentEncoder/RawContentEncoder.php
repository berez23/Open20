<?php

/*
 * (c) 2004-2009 Chris Corbyn
 *
 */

/**
 * Handles raw Transfer Encoding in Swift Mailer.
 *
 * When sending 8-bit content over SMTP, you should use
 * Swift_Transport_Esmtp_EightBitMimeHandler to enable the 8BITMIME SMTP
 * extension.
 *
 */
class Swift_Mime_ContentEncoder_RawContentEncoder implements Swift_Mime_ContentEncoder
{
    /**
     * Encode a given string to produce an encoded string.
     *
     * @param string $string
     * @param int    $firstLineOffset ignored
     * @param int    $maxLineLength   ignored
     *
     * @return string
     */
    public function encodeString($string, $firstLineOffset = 0, $maxLineLength = 0)
    {
        return $string;
    }

    /**
     * Encode stream $in to stream $out.
     *
     * @param int $firstLineOffset ignored
     * @param int $maxLineLength   ignored
     */
    public function encodeByteStream(Swift_OutputByteStream $os, Swift_InputByteStream $is, $firstLineOffset = 0, $maxLineLength = 0)
    {
        while (false !== ($bytes = $os->read(8192))) {
            $is->write($bytes);
        }
    }

    /**
     * Get the name of this encoding scheme.
     *
     * @return string
     */
    public function getName()
    {
        return 'raw';
    }

    /**
     * Not used.
     */
    public function charsetChanged($charset)
    {
    }
}
