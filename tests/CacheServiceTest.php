<?php

use PHPUnit\Framework\TestCase;
use App\Services\CacheService;

class CacheServiceTest extends TestCase
{
    public function testCacheUnavailableReturnsNull(): void
    {
        putenv('REDIS_HOST=127.0.0.1');
        putenv('REDIS_PORT=9999');

        $cache = new CacheService();

        $this->assertFalse($cache->set('test-key', 'value', 1));
        $this->assertNull($cache->get('test-key'));
        $this->assertFalse($cache->delete('test-key'));
        $this->assertSame([], $cache->keys('test*'));
    }

    public function testRememberUsesCallbackWhenCacheUnavailable(): void
    {
        putenv('REDIS_HOST=127.0.0.1');
        putenv('REDIS_PORT=9999');

        $cache = new CacheService();
        $value = $cache->remember('remember-key', static function () {
            return ['result' => 42];
        }, 1);

        $this->assertSame(['result' => 42], $value);
    }
}
