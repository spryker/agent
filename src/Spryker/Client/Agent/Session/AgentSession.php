<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Client\Agent\Session;

use Generated\Shared\Transfer\UserTransfer;
use Spryker\Client\Agent\Dependency\Client\AgentToSessionClientInterface;

class AgentSession implements AgentSessionInterface
{
    /**
     * @var int
     */
    protected const DEFAULT_INVALIDATE_TIME = 1;

    /**
     * @var \Spryker\Client\Agent\Dependency\Client\AgentToSessionClientInterface
     */
    protected $sessionClient;

    /**
     * @var string
     */
    protected const SESSION_KEY = 'agent-session';

    /**
     * @param \Spryker\Client\Agent\Dependency\Client\AgentToSessionClientInterface $sessionClient
     */
    public function __construct(AgentToSessionClientInterface $sessionClient)
    {
        $this->sessionClient = $sessionClient;
    }

    /**
     * @return bool
     */
    public function isLoggedIn(): bool
    {
        return $this->getSessionClient()->has(static::SESSION_KEY);
    }

    /**
     * @return \Generated\Shared\Transfer\UserTransfer
     */
    public function getAgent(): UserTransfer
    {
        return $this->getSessionClient()->get(static::SESSION_KEY);
    }

    /**
     * @param \Generated\Shared\Transfer\UserTransfer $userTransfer
     *
     * @return void
     */
    public function setAgent(UserTransfer $userTransfer): void
    {
        $this->getSessionClient()->set(static::SESSION_KEY, $userTransfer);
    }

    /**
     * @return void
     */
    public function invalidateAgent(): void
    {
        $this->getSessionClient()->invalidate(static::DEFAULT_INVALIDATE_TIME);
    }

    /**
     * @return \Spryker\Client\Agent\Dependency\Client\AgentToSessionClientInterface
     */
    protected function getSessionClient(): AgentToSessionClientInterface
    {
        return $this->sessionClient;
    }
}
