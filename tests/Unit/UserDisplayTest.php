<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserDisplayTest extends TestCase
{
    // -------------------------------------------------------------------------
    // displayName()
    // -------------------------------------------------------------------------

    public function test_display_name_returns_nickname_when_set(): void
    {
        $user = new User(['name' => 'Maria Aparecida', 'nickname' => 'guerreira_oeste']);
        $this->assertSame('guerreira_oeste', $user->displayName());
    }

    public function test_display_name_returns_first_name_when_no_nickname(): void
    {
        $user = new User(['name' => 'Maria Aparecida', 'nickname' => null]);
        $this->assertSame('Maria', $user->displayName());
    }

    public function test_display_name_returns_first_name_when_nickname_is_empty_string(): void
    {
        $user = new User(['name' => 'João Pedro', 'nickname' => '']);
        $this->assertSame('João', $user->displayName());
    }

    public function test_display_name_returns_single_word_name_correctly(): void
    {
        $user = new User(['name' => 'Zélio', 'nickname' => null]);
        $this->assertSame('Zélio', $user->displayName());
    }

    // -------------------------------------------------------------------------
    // avatarSrc()
    // -------------------------------------------------------------------------

    public function test_avatar_src_returns_null_when_no_avatar(): void
    {
        $user = new User(['avatar_path' => null, 'avatar_url' => null]);
        $this->assertNull($user->avatarSrc());
    }

    public function test_avatar_src_returns_storage_url_when_path_is_set(): void
    {
        $user = new User(['avatar_path' => 'avatars/abc123.jpg', 'avatar_url' => null]);
        $src = $user->avatarSrc();
        $this->assertStringContainsString('avatars/abc123.jpg', $src);
        $this->assertStringContainsString('storage', $src);
    }

    public function test_avatar_src_returns_external_url_when_only_url_is_set(): void
    {
        $user = new User(['avatar_path' => null, 'avatar_url' => 'https://example.com/photo.png']);
        $this->assertSame('https://example.com/photo.png', $user->avatarSrc());
    }

    public function test_avatar_src_prefers_path_over_url(): void
    {
        $user = new User([
            'avatar_path' => 'avatars/local.jpg',
            'avatar_url'  => 'https://example.com/remote.png',
        ]);
        $src = $user->avatarSrc();
        $this->assertStringContainsString('avatars/local.jpg', $src);
        $this->assertStringNotContainsString('remote.png', $src);
    }
}
