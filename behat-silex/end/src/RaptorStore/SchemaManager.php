<?php

namespace RaptorStore;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Table;

/**
 * Handles rebuilding our database tables
 */
class SchemaManager
{
    private $conn;

    private $productRepository;

    private $userRepository;

    public function __construct(Connection $conn, ProductRepository $productRepository, UserRepository $userRepository)
    {
        $this->conn = $conn;
        $this->productRepository = $productRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Drops the tables and rebuilds them
     */
    public function rebuildSchema()
    {
        $schemaManager = $this->conn->getSchemaManager();

        $productTable = new Table('product');
        $productTable->addColumn("id", "integer", array("unsigned" => true));
        $productTable->addColumn("name", "string", array("length" => 255));
        $productTable->addColumn('author_id', 'integer', array('notNull' => false));
        $productTable->addColumn("description", "text", array('notNull' => false));
        $productTable->addColumn("price", "decimal", array('scale' => 2, 'notNull' => false));
        $productTable->addColumn("is_published", "boolean");
        $productTable->addColumn('created_at', 'datetime');
        $productTable->setPrimaryKey(array("id"));
        $schemaManager->dropAndCreateTable($productTable);

        $userTable = new Table('user');
        $userTable->addColumn('id', 'integer', array('unsigned' => true, 'autoincrement' => true));
        $userTable->setPrimaryKey(array('id'));
        $userTable->addColumn('username', 'string', array('length' => 32));
        $userTable->addUniqueIndex(array('username'));
        $userTable->addColumn('password', 'string', array('length' => 255));
        $userTable->addColumn('roles', 'string', array('length' => 255));
        $userTable->addColumn('created_at', 'datetime');

        // add an author_id to product
        $productTable->addForeignKeyConstraint($userTable, array('author_id'), array('id'));

        $schemaManager->dropAndCreateTable($userTable);
    }

    public function loadFixtures()
    {
        $user = $this->loadUsers();
        $this->loadProducts($user);
    }

    private function loadProducts(User $defaultAuthor)
    {
        $this->productRepository->emptyTable();

        $product1 = new Product();
        $product1->name = 'Kindle Fire HD 7';
        $product1->author = $defaultAuthor;
        $product1->description = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed posuere, neque quis pharetra tincidunt, enim libero pretium elit, in vehicula dolor lacus eget erat. Praesent sed justo nisl. Integer vel libero elit. Sed nulla quam, ornare et euismod sit amet, pellentesque vitae erat. Nullam rutrum metus vel magna molestie eget vulputate ligula fermentum. Cras eros nunc, semper sed scelerisque ultricies, condimentum sit amet orci. Suspendisse posuere pulvinar facilisis. Suspendisse gravida libero et sapien scelerisque ac feugiat erat mattis. Aliquam vel magna dolor, eu imperdiet enim. Proin id erat sollicitudin eros vulputate euismod. Nulla sed sem at lectus fringilla malesuada at in mi. Aenean porta metus et nisl accumsan rutrum. Nulla tincidunt enim a lacus tincidunt vitae ornare nisl pellentesque. Praesent id tempor velit.';
        $product1->price = '199.99';
        $product1->createdAt = new \DateTime('-1 day -4 hours -3 minutes');
        $product1->isPublished = true;

        $product2 = new Product();
        $product2->name = 'Samsung Galaxy S II';
        $product2->description = 'Sed et velit suscipit nisi porttitor rutrum. Aliquam at ante justo, sed consectetur lorem. Integer scelerisque nulla neque. Donec et felis non sem viverra adipiscing. Proin condimentum, risus sed imperdiet rhoncus, augue sem consectetur odio, in eleifend libero quam nec lectus. Aenean eget ipsum in nulla sodales aliquet. Pellentesque blandit, tortor eu tristique sagittis, arcu metus condimentum sapien, vel rutrum lorem elit porta odio. Etiam erat tortor, pellentesque eget tempus in, gravida eu sapien. Sed eu erat vitae neque fringilla fermentum sit amet id lectus. Etiam ligula ipsum, lobortis non mattis eu, laoreet eget urna. Aenean tellus nulla, pretium quis sodales et, eleifend vel tellus. Vivamus et eros ante, et varius sapien.';
        $product2->price = '434.99';
        $product2->isPublished = true;
        $product2->createdAt = new \DateTime('-1 month');

        $product3 = new Product();
        $product3->name = 'Samsung 3D Slim LED';
        $product3->description = 'Sed feugiat sem ac urna hendrerit ac sollicitudin est vulputate. Duis eleifend lacinia lacinia. Ut in justo sit amet lacus varius vehicula ac quis arcu. Aliquam eu tellus nisl, vitae volutpat dolor. Vivamus vitae massa et tortor ultrices imperdiet. Aliquam erat volutpat. Aenean ut justo at tortor feugiat dictum. Maecenas est metus, iaculis id iaculis sit amet, semper id nulla. Nunc lobortis purus sit amet eros pulvinar id feugiat enim dictum. Aenean arcu nisi, eleifend non rutrum non, rhoncus non magna. Donec quis mi non lorem commodo fringilla. Sed varius iaculis risus, quis commodo felis aliquam non. Maecenas elementum diam quis dui venenatis et volutpat elit malesuada.';
        $product3->price = '2497.99';
        $product3->isPublished = false;

        $this->productRepository->insert($product1);
        $this->productRepository->insert($product2);
        $this->productRepository->insert($product3);
    }

    /**
     * Loads some dummy users
     */
    private function loadUsers()
    {
        $this->userRepository->emptyTable();

        $user = new User();
        $user->username = 'admin';
        $user->plainPassword = 'admin';
        $user->roles = array('ROLE_ADMIN');

        $this->userRepository->insert($user);

        return $user;
    }
}