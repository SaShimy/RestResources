<?php
namespace Simple\Bundle\RestResourcesBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * Class AbstractResourceRepository
 * @package Simple\Bundle\RestResourcesBundle\Repository
 */
class AbstractResourceRepository extends ServiceEntityRepository implements ResourceRepositoryInterface
{
    use EntityMetadataFilterTrait;

    /**
     * @param QueryBuilder $qb
     * @param              $sort
     */
    protected function addOrderBy(&$qb, $sort = array())
    {
        if ($sort)
        {
            foreach ($sort as $field => $type)
            {
                $qb->orderBy("{$qb->getRootAliases()[0]}.$field", $type);
            }
        }
    }

    /**
     * @param array        $filters
     * @param array|null   $sort
     * @param integer|null $limit
     * @param integer|null $offset
     *
     * @return array
     */
    public function cget(array $filters, array $sort = array(), $limit = null, $offset = null)
    {
        $qb = $this->filterByMetadataFieldNames($filters);
        $this->addOrderBy($qb, $sort);

        return $qb->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $id
     *
     * @return null|object
     */
    public function get($id)
    {
        return $this->find($id);
    }
}