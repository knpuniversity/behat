<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Tests\Fixtures\ContainerAwareFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFixtures extends ContainerAwareFixture
{
    public function load(ObjectManager $manager)
    {
        $defaultAuthor = $this->loadUsers($manager);
        $this->loadProducts($defaultAuthor, $manager);
    }

    private function loadProducts(User $defaultAuthor, EntityManager $em)
    {
        $em->createQuery('DELETE FROM AppBundle:Product');

        $product1 = new Product();
        $product1->setName('Kindle Fire HD 7');
        $product1->setAuthor($defaultAuthor);
        $product1->setDescription('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed posuere, neque quis pharetra tincidunt, enim libero pretium elit, in vehicula dolor lacus eget erat. Praesent sed justo nisl. Integer vel libero elit. Sed nulla quam, ornare et euismod sit amet, pellentesque vitae erat. Nullam rutrum metus vel magna molestie eget vulputate ligula fermentum. Cras eros nunc, semper sed scelerisque ultricies, condimentum sit amet orci. Suspendisse posuere pulvinar facilisis. Suspendisse gravida libero et sapien scelerisque ac feugiat erat mattis. Aliquam vel magna dolor, eu imperdiet enim. Proin id erat sollicitudin eros vulputate euismod. Nulla sed sem at lectus fringilla malesuada at in mi. Aenean porta metus et nisl accumsan rutrum. Nulla tincidunt enim a lacus tincidunt vitae ornare nisl pellentesque. Praesent id tempor velit.');
        $product1->setPrice('199.99');
        $product1->setCreatedAt(new \DateTime('-1 day -4 hours -3 minutes'));
        $product1->setIsPublished(true);

        $product2 = new Product();
        $product2->setName('Samsung Galaxy S II');
        $product2->setDescription('Sed et velit suscipit nisi porttitor rutrum. Aliquam at ante justo, sed consectetur lorem. Integer scelerisque nulla neque. Donec et felis non sem viverra adipiscing. Proin condimentum, risus sed imperdiet rhoncus, augue sem consectetur odio, in eleifend libero quam nec lectus. Aenean eget ipsum in nulla sodales aliquet. Pellentesque blandit, tortor eu tristique sagittis, arcu metus condimentum sapien, vel rutrum lorem elit porta odio. Etiam erat tortor, pellentesque eget tempus in, gravida eu sapien. Sed eu erat vitae neque fringilla fermentum sit amet id lectus. Etiam ligula ipsum, lobortis non mattis eu, laoreet eget urna. Aenean tellus nulla, pretium quis sodales et, eleifend vel tellus. Vivamus et eros ante, et varius sapien.');
        $product2->setPrice('434.99');
        $product2->setIsPublished(true);
        $product2->setCreatedAt(new \DateTime('-1 month'));

        $product3 = new Product();
        $product3->setName('Samsung 3D Slim LED');
        $product3->setDescription('Sed feugiat sem ac urna hendrerit ac sollicitudin est vulputate. Duis eleifend lacinia lacinia. Ut in justo sit amet lacus varius vehicula ac quis arcu. Aliquam eu tellus nisl, vitae volutpat dolor. Vivamus vitae massa et tortor ultrices imperdiet. Aliquam erat volutpat. Aenean ut justo at tortor feugiat dictum. Maecenas est metus, iaculis id iaculis sit amet, semper id nulla. Nunc lobortis purus sit amet eros pulvinar id feugiat enim dictum. Aenean arcu nisi, eleifend non rutrum non, rhoncus non magna. Donec quis mi non lorem commodo fringilla. Sed varius iaculis risus, quis commodo felis aliquam non. Maecenas elementum diam quis dui venenatis et volutpat elit malesuada.');
        $product3->setPrice('2497.99');
        $product3->setIsPublished(false);

        $em->persist($product1);
        $em->persist($product2);
        $em->persist($product3);
        $em->flush();
    }

    /**
     * Loads some dummy users
     */
    private function loadUsers(EntityManager $em)
    {
        $em->createQuery('DELETE FROM AppBundle:Product');

        $user = new User();
        $user->setUsername('admin');
        $user->setPlainPassword('admin');
        $user->setRoles(array('ROLE_ADMIN'));

        $em->persist($user);
        $em->flush();

        return $user;
    }
}