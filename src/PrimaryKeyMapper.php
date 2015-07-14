<?php

namespace jtl\Connector\Example;

use jtl\Connector\Core\Logger\Logger;
use jtl\Connector\Drawing\ImageRelationType;
use jtl\Connector\Linker\IdentityLinker;
use jtl\Connector\Mapper\IPrimaryKeyMapper;

class PrimaryKeyMapper implements IPrimaryKeyMapper
{

    /**
     * Host ID getter
     *
     * @param string $endpointId
     * @param integer $type
     * @return integer|null
     */
    public function getHostId($endpointId, $type)
    {
        // TODO: Implement getHostId() method.
    }

    /**
     * Endpoint ID getter
     *
     * @param integer $hostId
     * @param integer $type
     * @return string|null
     */
    public function getEndpointId($hostId, $type)
    {
        // TODO: Implement getEndpointId() method.
    }

    /**
     * Save link to database
     *
     * @param string $endpointId
     * @param integer $hostId
     * @param integer $type
     * @return boolean
     */
    public function save($endpointId, $hostId, $type)
    {
        // TODO: Implement save() method.
    }

    /**
     * Delete link from database
     *
     * @param string $endpointId
     * @param integer $hostId
     * @param integer $type
     * @return boolean
     */
    public function delete($endpointId = null, $hostId = null, $type)
    {
        // TODO: Implement delete() method.
    }

    /**
     * Clears the entire link table
     *
     * @return boolean
     */
    public function clear()
    {
        // TODO: Implement clear() method.
    }

    /**
     * Garbage Collect the entire link table
     *
     * @return boolean
     */
    public function gc()
    {
        // TODO: Implement gc() method.
    }
}
