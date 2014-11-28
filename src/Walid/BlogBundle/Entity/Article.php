<?php

namespace Walid\BlogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Article
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Walid\BlogBundle\Entity\ArticleRepository")
 */
class Article
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    private $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity="Walid\BlogBundle\Entity\Image", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\ManyToMany(targetEntity="Walid\BlogBundle\Entity\Category", cascade={"persist"})
     */
    private $categories = null;

      public function __construct()
      {
        // Default date is today.
        $this->date = new \Datetime();
        $this->categories   = new ArrayCollection();
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
     * Set title
     *
     * @param string $title
     * @return Article
     */
    public function setTitle($title)
    {
      $this->title = $title;

      return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
      return $this->title;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Article
     */
    public function setContent($content)
    {
      $this->content = $content;

      return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
      return $this->content;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Article
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
     * Set image
     *
     * @param \Walid\BlogBundle\Entity\Image $image
     * @return Article
     */
    public function setImage(\Walid\BlogBundle\Entity\Image $image = null)
    {
      $this->image = $image;

      return $this;
    }

    /**
     * Get image
     *
     * @return \Walid\BlogBundle\Entity\Image
     */
    public function getImage()
    {
      return $this->image;
    }

    /**
     * Add categories
     *
     * @param \OC\PlatformBundle\Entity\Category $categories
     * @return Article
     */
    public function addCategory(\OC\PlatformBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \OC\PlatformBundle\Entity\Category $categories
     */
    public function removeCategory(\OC\PlatformBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }
}
