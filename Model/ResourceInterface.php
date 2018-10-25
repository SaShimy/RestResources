<?php
namespace Components\Restresources\Model;

/**
 * Class ResourceInterface
 * @package Components\Restresources\Model
 */
interface ResourceInterface
{
    const CAN_LIST = 'list';
    const CAN_RETRIEVE = 'retrieve';
    const CAN_CREATE = 'create';
    const CAN_UPDATE = 'update';
    const CAN_DELETE = 'delete';
}