<?php

namespace Eshop\ShopBundle\Entity;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Product
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Eshop\ShopBundle\Repository\ProductRepository")
 * @UniqueEntity("slug"),
 *     errorPath="slug",
 *     message="This slug is already in use."
 * @ORM\HasLifecycleCallbacks()
 */
class Product
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
     * @ORM\Column(name="slug", type="string", length=255, nullable=false, unique=true)
     */

    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="ProductType", inversedBy="products")
     * @ORM\JoinColumn(name="product_type_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $type;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float")
     */
    private $price;

    /**
     * @var float
     *
     * @ORM\Column(name="old_price", type="float")
     */
    private $oldPrice;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_keys", type="text", nullable=true)
     */
    private $metaKeys;

    /**
     * @var string
     *
     * @ORM\Column(name="meta_description", type="text", nullable=true)
     */
    private $metaDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="material", type="string", length=255)
     */
    private $material;

    /**
     * @var string
     *
     * @ORM\Column(name="male", type="string", length=255)
     */
    private $male;

    /**
     * @var integer
     *
     * @ORM\Column(name="age_from", type="integer")
     */
    private $ageFrom;

    /**
     * @var integer
     *
     * @ORM\Column(name="age_to", type="integer")
     */
    private $ageTo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_updated", type="datetime")
     */
    private $dateUpdated;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer", nullable=false)
     * @Assert\Range(
     *      min = 0,
     *      minMessage = "Must be at least {{ limit }}",
     * )
     */
    private $quantity;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean", nullable=false)
     */
    private $deleted;

    /**
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $category;

    /**
     * @ORM\ManyToOne(targetEntity="Manufacturer", inversedBy="products")
     * @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    private $manufacturer;

    /**
     * @ORM\OneToMany(targetEntity="Image", mappedBy="product", cascade={"remove"})
     **/
    private $images;

    /**
     * @ORM\OneToMany(targetEntity="OrderProduct", mappedBy="product")
     **/
    private $productOrders;

    /**
     * @ORM\OneToMany(targetEntity="Favourites", mappedBy="product")
     **/
    private $favourites;

    /**
     * @ORM\OneToOne(targetEntity="Featured", mappedBy="product")
     */
    private $featured;

    public function __construct()
    {
        $this->dateCreated = new \DateTime();
        $this->dateUpdated = $this->dateCreated;
        $this->productOrders = new ArrayCollection();
        $this->images = new ArrayCollection();
        $this->favourites = new ArrayCollection();
        $this->quantity = 1;
        $this->deleted = false;
    }

    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Called before saving the entity
     *
     * @ORM\PreUpdate()
     */
    public function preUpdate()
    {
        $this->dateUpdated = new \DateTime();
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
     * Set name
     *
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Product
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set price
     *
     * @param float $price
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Set oldPrice
     *
     * @param float $oldPrice
     * @return Product
     */
    public function setOldPrice($oldPrice)
    {
        $this->oldPrice = $oldPrice;

        return $this;
    }

    /**
     * Get oldPrice
     *
     * @return float
     */
    public function getOldPrice()
    {
        return $this->oldPrice;
    }

    /**
     * Set category
     *
     * @param \Eshop\ShopBundle\Entity\Category $category
     * @return Product
     */
    public function setCategory(\Eshop\ShopBundle\Entity\Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \Eshop\ShopBundle\Entity\Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set manufacturer
     *
     * @param \Eshop\ShopBundle\Entity\Manufacturer $manufacturer
     * @return Product
     */
    public function setManufacturer(\Eshop\ShopBundle\Entity\Manufacturer $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;

        return $this;
    }

    /**
     * Get manufacturer
     *
     * @return \Eshop\ShopBundle\Entity\Manufacturer
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * Add images
     *
     * @param \Eshop\ShopBundle\Entity\Image $images
     * @return Product
     */
    public function addImage(\Eshop\ShopBundle\Entity\Image $images)
    {
        $this->images[] = $images;

        return $this;
    }

    /**
     * Remove images
     *
     * @param \Eshop\ShopBundle\Entity\Image $images
     */
    public function removeImage(\Eshop\ShopBundle\Entity\Image $images)
    {
        $this->images->removeElement($images);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Add productOrders
     *
     * @param \Eshop\ShopBundle\Entity\OrderProduct $productOrders
     * @return Product
     */
    public function addProductOrder(\Eshop\ShopBundle\Entity\OrderProduct $productOrders)
    {
        $this->productOrders[] = $productOrders;

        return $this;
    }

    /**
     * Remove productOrders
     *
     * @param \Eshop\ShopBundle\Entity\OrderProduct $productOrders
     */
    public function removeProductOrder(\Eshop\ShopBundle\Entity\OrderProduct $productOrders)
    {
        $this->productOrders->removeElement($productOrders);
    }

    /**
     * Get productOrders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductOrders()
    {
        return $this->productOrders;
    }

    /**
     * Set metaKeys
     *
     * @param string $metaKeys
     * @return Product
     */
    public function setMetaKeys($metaKeys)
    {
        $this->metaKeys = $metaKeys;

        return $this;
    }

    /**
     * Get metaKeys
     *
     * @return string
     */
    public function getMetaKeys()
    {
        return $this->metaKeys;
    }

    /**
     * Set metaDescription
     *
     * @param string $metaDescription
     * @return Product
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    /**
     * Set material
     *
     * @param string $material
     * @return Product
     */
    public function setMaterial($material)
    {
        $this->material = $material;

        return $this;
    }

    /**
     * Get material
     *
     * @return string
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * Set male
     *
     * @param string $male
     * @return Product
     */
    public function setMale($male)
    {
        $this->male = $male;

        return $this;
    }

    /**
     * Get male
     *
     * @return string
     */
    public function getMale()
    {
        return $this->male;
    }

    /**
     * Set ageFrom
     *
     * @param string $ageFrom
     * @return Product
     */
    public function setAgeFrom($ageFrom)
    {
        $this->ageFrom = $ageFrom;

        return $this;
    }

    /**
     * Get ageFrom
     *
     * @return string
     */
    public function getAgeFrom()
    {
        return $this->ageFrom;
    }

    /**
     * Set ageTo
     *
     * @param string $ageTo
     * @return Product
     */
    public function setAgeTo($ageTo)
    {
        $this->ageTo = $ageTo;

        return $this;
    }

    /**
     * Get ageTo
     *
     * @return string
     */
    public function getAgeTo()
    {
        return $this->ageTo;
    }

    /**
     * Get metaDescription
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return Product
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Product
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set dateCreated
     *
     * @param \DateTime $dateCreated
     * @return Product
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime 
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateUpdated
     *
     * @param \DateTime $dateUpdated
     * @return Product
     */
    public function setDateUpdated($dateUpdated)
    {
        $this->dateUpdated = $dateUpdated;

        return $this;
    }

    /**
     * Get dateUpdated
     *
     * @return \DateTime 
     */
    public function getDateUpdated()
    {
        return $this->dateUpdated;
    }

    /**
     * Add favourites
     *
     * @param \Eshop\ShopBundle\Entity\Favourites $favourites
     * @return Product
     */
    public function addFavourite(\Eshop\ShopBundle\Entity\Favourites $favourites)
    {
        $this->favourites[] = $favourites;

        return $this;
    }

    /**
     * Remove favourites
     *
     * @param \Eshop\ShopBundle\Entity\Favourites $favourites
     */
    public function removeFavourite(\Eshop\ShopBundle\Entity\Favourites $favourites)
    {
        $this->favourites->removeElement($favourites);
    }

    /**
     * Get favourites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getFavourites()
    {
        return $this->favourites;
    }

    /**
     * Set featured
     *
     * @param \Eshop\ShopBundle\Entity\Featured $featured
     * @return Product
     */
    public function setFeatured(\Eshop\ShopBundle\Entity\Featured $featured = null)
    {
        $this->featured = $featured;

        return $this;
    }

    /**
     * Get featured
     *
     * @return \Eshop\ShopBundle\Entity\Featured 
     */
    public function getFeatured()
    {
        return $this->featured;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Product
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
     * @Assert\Callback
     */
    public function isOldPriceLessThanPrice(ExecutionContextInterface $context)
    {
        $price = $this->getPrice();
        $oldPrice = $this->getOldPrice();

        if ($oldPrice <= $price) {
            $context->buildViolation('Price should be more than old price!')
                ->atPath('oldPrice')
                ->addViolation()
            ;
        }
    }

    /**
     * @Assert\Callback
     */
    public function checkAgeFromLesstThenAgeTo(ExecutionContextInterface $context)
    {
        $ageFrom = $this->getAgeFrom();
        $ageTo = $this->getAgeTo();

        if (!empty($ageTo)
            && !empty($ageFrom)
            && $ageTo < $ageFrom
        ) {
            $context->buildViolation('Age to should be more than age from!')
                ->atPath('ageFrom')
                ->addViolation();
        }
    }
}
