<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\Agent\Persistence;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\UserTransfer;
use Orm\Zed\Customer\Persistence\Map\SpyCustomerTableMap;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\Agent\Persistence\AgentPersistenceFactory getFactory()
 */
class AgentRepository extends AbstractRepository implements AgentRepositoryInterface
{
    /**
     * @param string $username
     *
     * @return \Generated\Shared\Transfer\UserTransfer|null
     */
    public function findAgentByUsername(string $username): ?UserTransfer
    {
        $userEntity = $this->getFactory()
            ->getUserQuery()
            ->filterByIsAgent(true)
            ->filterByUsername($username)
            ->findOne();

        $userTransfer = new UserTransfer();

        if ($userEntity === null) {
            return null;
        }

        return $userTransfer->fromArray($userEntity->toArray(), true);
    }

    /**
     * @param string $query
     * @param int|null $limit
     *
     * @return \Generated\Shared\Transfer\CustomerTransfer[]
     */
    public function findCustomersByQuery(string $query, ?int $limit): array
    {
        $queryPattern = $query . '%';

        $customersQuery = $this->getFactory()
            ->getCustomerQuery()
            ->setIgnoreCase(true)
            ->select([
                SpyCustomerTableMap::COL_ID_CUSTOMER,
                SpyCustomerTableMap::COL_FIRST_NAME,
                SpyCustomerTableMap::COL_LAST_NAME,
                SpyCustomerTableMap::COL_EMAIL,
            ])
            ->filterByEmail_Like($queryPattern)
            ->_or()
            ->filterByLastName_Like($queryPattern)
            ->_or()
            ->filterByFirstName_Like($queryPattern);

        if ($limit !== null) {
            $customersQuery->limit($limit);
        }

        $customers = $customersQuery->find();

        $customerTransferList = [];

        foreach ($customers as $customer) {
            $customerTransfer = new CustomerTransfer();
            $customerTransfer->setIdCustomer($customer[SpyCustomerTableMap::COL_ID_CUSTOMER]);
            $customerTransfer->setFirstName($customer[SpyCustomerTableMap::COL_FIRST_NAME]);
            $customerTransfer->setLastName($customer[SpyCustomerTableMap::COL_LAST_NAME]);
            $customerTransfer->setEmail($customer[SpyCustomerTableMap::COL_EMAIL]);

            $customerTransferList[] = $customerTransfer;
        }

        return $customerTransferList;
    }
}
