<?php
namespace Simple\Component\RestResources\Repository;

/**
 * Class ResourceRepositoryInterface
 * @package Simple\Component\RestResources\Repository
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