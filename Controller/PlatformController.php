<?php namespace Controller;

use Data\Model\Platform;

class PlatformController
{
    private $platform;

    public function __construct()
    {
        $this->platform = new Platform();
    }

    /**
     * @param array $platform
     * @return string
     */
    public function createPlatform(array $platform)
    {
        return $this->platform->create($platform);
    }

    /***
     * This function updates a platform.
     * @param $data
     * @param $id
     * @return null
     */
    public function updatePlatform($data, $id)
    {
        return $this->platform->update($data, $id);
    }

    /**
     * This function removes a platform
     * @param $data
     * @param $id
     */
    public function removePlatform($id)
    {
        $this->platform->remove($id);
    }
    
    /**
     * @param $id
     * @return array
     */
    public function fetchPlatforms($id)
    {
        return $this->platform->find($id);
    }

    /**
     * This fetches all platforms.
     * @return array
     */
    public function fetchAllPlatforms()
    {
        return $this->platform->findAll();
    }

}
