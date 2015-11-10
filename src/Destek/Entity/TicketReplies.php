<?php

namespace Destek\Entity;

/**
 * TicketReplies
 */
class TicketReplies
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var integer
     */
    private $ticket_id;

    /**
     * @var integer
     */
    private $reply_user_id;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var \DateTime
     */
    private $created_at;

    /**
     * @var \DateTime
     */
    private $updated_at;

    /**
     * @var boolean
     */
    private $deleted;

    /**
     * @var \DateTime
     */
    private $deleted_at;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set ticketId
     *
     * @param integer $ticketId
     *
     * @return TicketReplies
     */
    public function setTicketId($ticketId)
    {
        $this->ticket_id = $ticketId;

        return $this;
    }

    /**
     * Get ticketId
     *
     * @return integer
     */
    public function getTicketId()
    {
        return $this->ticket_id;
    }

    /**
     * Set replyUserId
     *
     * @param integer $replyUserId
     *
     * @return TicketReplies
     */
    public function setReplyUserId($replyUserId)
    {
        $this->reply_user_id = $replyUserId;

        return $this;
    }

    /**
     * Get replyUserId
     *
     * @return integer
     */
    public function getReplyUserId()
    {
        return $this->reply_user_id;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return TicketReplies
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return TicketReplies
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return TicketReplies
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return TicketReplies
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     *
     * @return TicketReplies
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     *
     * @return TicketReplies
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deleted_at = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }
}

