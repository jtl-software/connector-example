<?php

namespace jtl\Connector\Example;

use jtl\Connector\Checksum\IChecksumLoader;

class ChecksumLoader implements IChecksumLoader
{

    /**
     * Loads the checksum
     *
     * @param string $endpointId
     * @param int $type
     * @return string
     */
    public function read($endpointId, $type)
    {
        // TODO: Implement read() method.
    }

    /**
     * Loads the checksum
     *
     * @param string $endpointId
     * @param int $type
     * @param string $checksum
     * @return boolean
     */
    public function write($endpointId, $type, $checksum)
    {
        // TODO: Implement write() method.
    }

    /**
     * Loads the checksum
     *
     * @param string $endpointId
     * @param int $type
     * @return boolean
     */
    public function delete($endpointId, $type)
    {
        // TODO: Implement delete() method.
    }
}
