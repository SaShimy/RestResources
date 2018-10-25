<?php
namespace Simple\Bundle\RestResourcesBundle\Model;

/**
 * Class ResourceInterface
 * @package Simple\Bundle\RestResourcesBundle\Model
 */
interface ResourceInterface
{
    const CAN_LIST = 'list';
    const CAN_RETRIEVE = 'retrieve';
    const CAN_CREATE = 'create';
    const CAN_UPDATE = 'update';
    const CAN_DELETE = 'delete';
}