<?php

namespace RaptorStore;

use Doctrine\DBAL\Connection;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;

class UserRepository extends BaseRepository implements UserProviderInterface
{
    private $passwordEncoder;

    public function __construct(Connection $conn, PasswordEncoderInterface $passwordEncoder)
    {
        $this->conn = $conn;
        $this->passwordEncoder = $passwordEncoder;

        parent::__construct($conn);
    }

    public function getTableName()
    {
        return 'user';
    }

    public function createAdminUser($username, $password)
    {
        $user = new User();
        $user->username = $username;
        $user->plainPassword = $password;
        $user->roles = array('ROLE_ADMIN');

        $this->insert($user);

        return $user;
    }

    public function insert($user)
    {
        $this->encodePassword($user);

        parent::insert($user);
    }

    public function update($user)
    {
        $this->encodePassword($user);

        parent::insert($user);
    }

    /**
     * Turns a user into a flat array
     *
     * @todo - should really be its own service
     *
     * @param User $user
     * @return array
     */
    public function objectToArray($user)
    {
        return array(
            'id' => $user->id,
            'username' => $user->username,
            'password' => $user->password,
            'roles' => implode(',', $user->roles),
            'created_at' => $user->createdAt->format(self::DATE_FORMAT),
        );
    }

    /**
     * Turns an array of data into a User object
     *
     * @param array $userArr
     * @param User $user
     * @return User
     */
    public function arrayToObject(array $userArr, $user = null)
    {
        // create a User, unless one is given
        if (!$user) {
            $user = new User();

            // only hydrate in the id if we're creating a new User
            // this is used when we're grabbing something out of the database, for example
            // we should *not* do this otherwise, because we already have an id, and are just updating its data
            $user->id = isset($userArr['id']) ? $userArr['id'] : null;
        }

        $username = isset($userArr['username']) ? $userArr['username'] : null;
        $password = isset($userArr['password']) ? $userArr['password'] : null;
        $roles = isset($userArr['roles']) ? explode(',', $userArr['roles']) : array();
        $createdAt = isset($userArr['created_at']) ? \DateTime::createFromFormat(self::DATE_FORMAT, $userArr['created_at']) : null;

        if ($username) {
            $user->username = $username;
        }

        if ($password) {
            $user->password = $password;
        }

        if ($roles) {
            $user->roles = $roles;
        }

        if ($createdAt) {
            $user->createdAt = $createdAt;
        }

        return $user;
    }

    public function loadUserByUsername($username)
    {
        $stmt = $this->conn->executeQuery('SELECT * FROM user WHERE username = ?', array(strtolower($username)));

        if (!$user = $stmt->fetch()) {
            throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
        }

        return $this->arrayToObject($user);
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass($class)
    {
        return $class === 'RaptorStore\User';
    }

    /**
     * Encodes the user's password if necessary
     *
     * @param User $user
     */
    private function encodePassword(User $user)
    {
        if ($user->plainPassword) {
            $user->password = $this->passwordEncoder->encodePassword($user->plainPassword, $user->getSalt());
        }
    }
}