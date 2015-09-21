<?php

namespace AppBundle\Doctrine;

use AppBundle\DataFixtures\ORM\LoadFixtures;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Handles rebuilding our database tables
 */
class SchemaManager
{
    private $em;

    private $container;

    public function __construct(EntityManager $em, ContainerInterface $container)
    {
        $this->em = $em;
        $this->container = $container;
    }

    public function rebuildSchema()
    {
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        $tool = new SchemaTool($this->em);
        $tool->dropSchema($metadatas);
        $tool->updateSchema($metadatas, false);
    }

    public function loadFixtures()
    {
        $loader = new ContainerAwareLoader($this->container);
        $loader->loadFromDirectory(__DIR__.'/../DataFixtures');
        $purger = new ORMPurger();
        $executor = new ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());
    }
}
