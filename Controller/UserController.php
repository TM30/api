<?php namespace Controller;

use Data\Model\User;

class UserController
{

    private $user;

    public function __construct()
    {
        $this->user = new User();
    }

    /**
     * @param array $user
     * @return string
     */
    public function createUser(array $user)
    {
        return $this->user->create($user);
    }

    /***
     * This function updates a user.
     * @param $data
     * @param $id
     * @return null
     */
    public function updateUser($data, $id)
    {
        return $this->user->update($data, $id);
    }

    /**
     * This function removes a user
     * @param $data
     * @param $id
     */
    public function removeUser($id)
    {
        $this->user->remove($id);
    }

    /**
     * @param $id
     * @return array
     */
    public function fetchUser($id)
    {
        return $this->user->find($id);
    }

    /**
     * THis returns all user
     * @return array
     */
    public function fetchAllUsers()
    {
        return $this->user->findAll();
    }

}
