<?php

namespace Destek\Entity;

/**
 * Tickets
 */
class Tickets
{
    /**
     * @var integer
     */
    private $id;


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
     * @var integer
     */
    private $recipient_id;

    /**
     * @var integer
     */
    private $owner_user_id;

    /**
     * @var integer
     */
    private $priority_id;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $message;

    /**
     * @var integer
     */
    private $status_id;

    /**
     * @var integer
     */
    private $replied;

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
     * Set recipientId
     *
     * @param integer $recipientId
     *
     * @return Tickets
     */
    public function setRecipientId($recipientId)
    {
        $this->recipient_id = $recipientId;

        return $this;
    }

    /**
     * Get recipientId
     *
     * @return integer
     */
    public function getRecipientId()
    {
        return $this->recipient_id;
    }

    /**
     * Set ownerUserId
     *
     * @param integer $ownerUserId
     *
     * @return Tickets
     */
    public function setOwnerUserId($ownerUserId)
    {
        $this->owner_user_id = $ownerUserId;

        return $this;
    }

    /**
     * Get ownerUserId
     *
     * @return integer
     */
    public function getOwnerUserId()
    {
        return $this->owner_user_id;
    }

    /**
     * Set priorityId
     *
     * @param integer $priorityId
     *
     * @return Tickets
     */
    public function setPriorityId($priorityId)
    {
        $this->priority_id = $priorityId;

        return $this;
    }

    /**
     * Get priorityId
     *
     * @return integer
     */
    public function getPriorityId()
    {
        return $this->priority_id;
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Tickets
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Tickets
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
     * Set statusId
     *
     * @param integer $statusId
     *
     * @return Tickets
     */
    public function setStatusId($statusId)
    {
        $this->status_id = $statusId;

        return $this;
    }

    /**
     * Get statusId
     *
     * @return integer
     */
    public function getStatusId()
    {
        return $this->status_id;
    }

    /**
     * Set replied
     *
     * @param integer $replied
     *
     * @return Tickets
     */
    public function setReplied($replied)
    {
        $this->replied = $replied;

        return $this;
    }

    /**
     * Get replied
     *
     * @return integer
     */
    public function getReplied()
    {
        return $this->replied;
    }

    /**
     * Set ip
     *
     * @param string $ip
     *
     * @return Tickets
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
     * @return Tickets
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
     * @return Tickets
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
     * @return Tickets
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
     * @return Tickets
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
