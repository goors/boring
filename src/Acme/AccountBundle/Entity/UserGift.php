<?php

namespace Acme\AccountBundle\Entity;
 
use Doctrine\ORM\Mapping as ORM;
 
/**
 * @ORM\Entity
 * @ORM\Table(name="sent_gifts")
 */
class UserGift
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;
 
    
    /** 
    * @ORM\ManyToOne(targetEntity="Gift")
    * @ORM\JoinColumn(name="gift", referencedColumnName="id")
    */
    private $gift;
    
    /** 
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="sent_by", referencedColumnName="id")
    */
    private $sent_by;

    
    /** 
    * @ORM\ManyToOne(targetEntity="User")
    * @ORM\JoinColumn(name="received_by", referencedColumnName="id")
    */
    private $received_by;
    
    
    /**
     * @ORM\Column(type="string", length=1)
     */
    private $new = 1;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $sent_date;
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $comment;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
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
     * Set gift
     *
     * @param string $gift
     * @return UserGift
     */
    public function setGift($gift)
    {
        $this->gift = $gift;
    
        return $this;
    }

    /**
     * Get gift
     *
     * @return string 
     */
    public function getGift()
    {
        return $this->gift;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return UserGift
     */
    public function setDate($date)
    {
        $this->date = $date;
    
        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set comment
     *
     * @param string $comment
     * @return UserGift
     */
    public function setComment($comment)
    {
        $this->comment = $comment;
    
        return $this;
    }

    /**
     * Get comment
     *
     * @return string 
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * Add user
     *
     * @param \Acme\AccountBundle\Entity\User $user
     * @return UserGift
     */
    public function addUser(\Acme\AccountBundle\Entity\User $user)
    {
        $this->user[] = $user;
    
        return $this;
    }

    /**
     * Remove user
     *
     * @param \Acme\AccountBundle\Entity\User $user
     */
    public function removeUser(\Acme\AccountBundle\Entity\User $user)
    {
        $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set sent_by
     *
     * @param \Acme\AccountBundle\Entity\User $sentBy
     * @return UserGift
     */
    public function setSentBy(\Acme\AccountBundle\Entity\User $sentBy = null)
    {
        $this->sent_by = $sentBy;
    
        return $this;
    }

    /**
     * Get sent_by
     *
     * @return \Acme\AccountBundle\Entity\User 
     */
    public function getSentBy()
    {
        return $this->sent_by;
    }

    /**
     * Set received_by
     *
     * @param \Acme\AccountBundle\Entity\User $receivedBy
     * @return UserGift
     */
    public function setReceivedBy(\Acme\AccountBundle\Entity\User $receivedBy = null)
    {
        $this->received_by = $receivedBy;
    
        return $this;
    }

    /**
     * Get received_by
     *
     * @return \Acme\AccountBundle\Entity\User 
     */
    public function getReceivedBy()
    {
        return $this->received_by;
    }

    /**
     * Set sent_date
     *
     * @param \DateTime $sentDate
     * @return UserGift
     */
    public function setSentDate($sentDate)
    {
        $this->sent_date = $sentDate;
    
        return $this;
    }

    /**
     * Get sent_date
     *
     * @return \DateTime 
     */
    public function getSentDate()
    {
        return $this->sent_date;
    }

    /**
     * Set new
     *
     * @param string $new
     * @return UserGift
     */
    public function setNew($new)
    {
        $this->new = $new;
    
        return $this;
    }

    /**
     * Get new
     *
     * @return string 
     */
    public function getNew()
    {
        return $this->new;
    }
}