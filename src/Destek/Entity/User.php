<?php

namespace Destek\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{

    public function __construct()
    {
        $this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
    }
    /**
     * User id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Name.
     *
     * @var string
     */
    protected $name;

    /**
     * Surname.
     *
     * @var string
     */
    protected $surname;

    /**
     * Username.
     *
     * @var string
     */
    protected $username;
    /**
     * Salt.
     *
     * @var string
     */
    protected $salt;

    /**
     * Password.
     *
     * @var integer
     */
    protected $password;

    /**
     * Email.
     *
     * @var string
     */
    protected $email;

    /**
     * Role.
     *
     * ROLE_USER or ROLE_ADMIN.
     *
     * @var string
     */
    protected $role;

    /**
     * enabled
     *
     * @var boolean
     */
    protected $enabled;

    /**
     * When the artist entity was created.
     *
     * @var DateTime
     */
    protected $createdAt;

    /**
     * When the artist entity was updated.
     *
     * @var DateTime
     */
    protected $updatedAt;

    /**
     * When the artist entity was deleted.
     *
     * @var DateTime
     */
    protected $deletedAt;

    /**
     * When the artist entity was deleted.
     *
     * @var boolean
     */
    protected $deleted;

    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function getSurname()
    {
        return $this->surname;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;
    }

    public function getDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return array($this->getRole());
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
    }
    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
    }
}