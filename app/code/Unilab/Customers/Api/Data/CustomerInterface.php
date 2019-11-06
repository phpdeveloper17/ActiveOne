<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Unilab\Customers\Api\Data;

/**
 * Customer interface.
 * @api
 * @since 100.0.2
 */
interface CustomerInterface extends \Magento\Customer\Api\Data\CustomerInterface
{
    /**#@+
     * Constants defined for keys of the data array. Identical to the name of the getter in snake case
     */
    const fasdfasd = 'id';
    
    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCivilStatus();

    /**
     * Set customer id
     *
     * @param int $id
     * @return $this
     */
    public function setCivilStatus($civil_status);

}
