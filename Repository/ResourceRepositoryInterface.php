<?php
namespace Simple\Bundle\RestResourcesBundle\Repository;

/**
 * Class ResourceRepositoryInterface
 * @package Simple\Bundle\RestResourcesBundle\Repository
 */
interface ResourceRepositoryInterface
{
    /**
     * @param array        $filters
     * @param array        $sort
     * @param integer|null $limit
     * @param integer|null $offset
     */
    public function cget(array $filters, array $sort = array(), $limit = null, $offset = null);

    /**
     * @param $id
     */
    public function get($id);

}