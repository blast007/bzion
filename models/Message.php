<?php
/**
 * This file contains functionality relating to all of actual messages sent by players
 *
 * @package    BZiON\Models
 * @license    https://github.com/allejo/bzion/blob/master/LICENSE.md GNU General Public License Version 3
 */

/**
 * A message between players or teams
 * @package    BZiON\Models
 */
class Message extends AbstractMessage implements GroupEventInterface
{
    /**
     * The ID of the player who sent the message
     * @var int
     */
    protected $player_from;

    /**
     * The content of the message
     * @var string
     */
    protected $message;

    /**
     * {@inheritDoc}
     */
    protected function assignResult($message)
    {
        parent::assignResult($message);

        $this->player_from = $message['player_from'];
        $this->message = $message['message'];

        if (!$this->isMessage()) {
            throw new Exception("A group event cannot be represented by the Message class.");
        }
    }

    /**
     * Get the content of the message
     * @return string The message itself
     */
    public function getContent()
    {
        return $this->message;
    }

    /**
     * Gets the creator of the message
     * @return Player An object representing the message's author
     */
    public function getAuthor()
    {
        return Player::get($this->player_from);
    }

    /**
     * Create a new message
     *
     * This method only stores a message in the database (doesn't update the
     * unread count or push live notifications), prefer to use Group::sendMessage()
     * instead.
     *
     * @param  int     $to      The id of the group the message is sent to
     * @param  int     $from    The ID of the sender
     * @param  string  $message The body of the message
     * @param  string  $status  The status of the message - can be 'visible', 'hidden', 'deleted' or 'reported'
     * @return Message An object that represents the sent message
     */
    public static function sendMessage($to, $from, $message, $status = 'visible')
    {
        return self::create(array(
            'group_to'    => $to,
            'player_from' => $from,
            'message'     => $message,
            'status'      => $status,
        ), 'iiss', 'timestamp');
    }

    /**
     * Get a query builder for messages
     * @return QueryBuilder
     */
    public static function getQueryBuilder()
    {
        return parent::getQueryBuilder()->messagesOnly();
    }
}
