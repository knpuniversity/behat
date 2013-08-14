<?php

namespace RaptorStore;

use Doctrine\DBAL\Connection;

class ProductRepository extends BaseRepository
{
    private $userRepository;

    public function __construct(Connection $conn, UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;

        parent::__construct($conn);
    }

    public function createProduct($name, $price)
    {
        $product = new Product();
        $product->name = $name;
        $product->price = $price;

        $this->insert($product);

        return $product;
    }

    /**
     * @return Product[]
     */
    public function findAllPublished()
    {
        return $this->findBy(array(
            'is_published' => true
        ));
    }

    public function getTableName()
    {
        return 'product';
    }

    /**
     * Turns a product into an array
     *
     * @todo - should really be its own service
     *
     * @param mixed $product
     * @return array
     */
    public function objectToArray($product)
    {
        /** @var $product Product */

        return array(
            'id' => $product->id,
            'name' => $product->name,
            'author_id' => $product->author ? $product->author->id : null,
            'description' => $product->description,
            'price' => $product->price,
            'is_published' => $product->isPublished,
            'created_at' => $product->createdAt->format(self::DATE_FORMAT),
        );
    }

    /**
     * Turns an array of data into a Product object
     *
     * @param array $productArr
     * @param null $product
     * @return null|Product
     * @throws \Exception
     */
    public function arrayToObject(array $productArr, $product = null)
    {
        // create a Product, unless one is given
        if (!$product) {
            $product = new Product();

            // only hydrate in the id if we're creating a new Product
            // this is used when we're grabbing something out of the database, for example
            // we should *not* do this otherwise, because we already have an id, and are just updating its data
            $product->id = isset($productArr['id']) ? $productArr['id'] : null;
        }

        $name = isset($productArr['name']) ? $productArr['name'] : null;
        $authorId = isset($productArr['author_id']) ? $productArr['author_id'] : null;
        $description = isset($productArr['description']) ? $productArr['description'] : null;
        $price = isset($productArr['price']) ? $productArr['price'] : null;
        $isPublished = isset($productArr['is_published']) ? $productArr['is_published'] : null;
        $createdAt = isset($productArr['created_at']) ? \DateTime::createFromFormat(self::DATE_FORMAT, $productArr['created_at']) : null;

        if ($name) {
            $product->name = $name;
        }

        if ($authorId) {
            $author = $this->userRepository->find($authorId);
            if (!$author) {
                throw new \Exception(sprintf('Hydration of the author_id "%s" failed!', $authorId));
            }

            $product->author = $author;
        }

        if ($description) {
            $product->description = $description;
        }

        if ($price) {
            $product->price = $price;
        }

        if ($isPublished !== null) {
            $product->isPublished = $isPublished;
        }

        if ($createdAt) {
            $product->createdAt = $createdAt;
        }

        return $product;
    }
}