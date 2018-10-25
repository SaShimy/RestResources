<?php
namespace Simple\Bundle\RestResourcesBundle\Repository;

/**
 * Class ResourceRepositoryInterface
 * @package Simple\Bundle\RestResourcesBundle\Repository
 */
interface ResourceRepositoryInterface
{
    /**
     * @param array $filters
     */
    public function cget(array $filters);

    /**
     * @param $id
     */
    public function get($id);

}