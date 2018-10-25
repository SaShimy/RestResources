<?php
namespace Components\Restresources\Repository;

/**
 * Class ResourceRepositoryInterface
 * @package Components\Restresources\Repository
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