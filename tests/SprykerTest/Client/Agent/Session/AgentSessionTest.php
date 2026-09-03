<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Client\Agent\Session;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\UserTransfer;
use Spryker\Client\Agent\Dependency\Client\AgentToSessionClientInterface;
use Spryker\Client\Agent\Session\AgentSession;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Client
 * @group Agent
 * @group Session
 * @group AgentSessionTest
 * Add your own group annotations below this line
 */
class AgentSessionTest extends Unit
{
    protected const string PASSWORD_HASH = '$2y$10$examplehashvalue1234567890';

    public function testSetAgentStoresUserWithoutPassword(): void
    {
        // Arrange
        $storedValue = null;
        $sessionClientMock = $this->createMock(AgentToSessionClientInterface::class);
        $sessionClientMock->method('set')->willReturnCallback(function (string $key, mixed $value) use (&$storedValue): void {
            $storedValue = $value;
        });
        $agentSession = new AgentSession($sessionClientMock);
        $userTransfer = (new UserTransfer())
            ->setUsername('agent@spryker.com')
            ->setPassword(static::PASSWORD_HASH);

        // Act
        $agentSession->setAgent($userTransfer);

        // Assert
        $this->assertNull($storedValue->getPassword());
        // The key must be absent entirely: an explicit null would count as a modified field.
        $this->assertArrayNotHasKey(UserTransfer::PASSWORD, $storedValue->modifiedToArray());
        $this->assertSame('agent@spryker.com', $storedValue->getUsername());
        // The original transfer stays untouched for the caller.
        $this->assertSame(static::PASSWORD_HASH, $userTransfer->getPassword());
    }

    public function testInvalidateAgentRemovesOnlyTheAgentSessionKey(): void
    {
        // Arrange
        $sessionClientMock = $this->createMock(AgentToSessionClientInterface::class);
        $sessionClientMock->expects($this->once())
            ->method('remove')
            ->with('agent-session');
        $sessionClientMock->expects($this->never())
            ->method('invalidate');
        $agentSession = new AgentSession($sessionClientMock);

        // Act
        $agentSession->invalidateAgent();
    }
}
