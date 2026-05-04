<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // GET /profile
    // -------------------------------------------------------------------------

    public function test_guest_is_redirected_from_profile_page(): void
    {
        $this->get('/profile')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_profile_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertViewIs('profile.edit')
            ->assertViewHas('user', $user);
    }

    public function test_profile_page_shows_current_user_data(): void
    {
        $user = User::factory()->create([
            'name'      => 'João da Silva',
            'nickname'  => 'joao_silva',
            'city'      => 'São Paulo',
        ]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk()
            ->assertSee('João da Silva')
            ->assertSee('joao_silva')
            ->assertSee('São Paulo');
    }

    // -------------------------------------------------------------------------
    // PATCH /profile – basic data
    // -------------------------------------------------------------------------

    public function test_guest_cannot_update_profile(): void
    {
        $this->patch('/profile', ['name' => 'Hacker'])->assertRedirect('/login');
    }

    public function test_user_can_update_basic_profile_data(): void
    {
        $user = User::factory()->create(['name' => 'Antigo Nome', 'phone' => '11000000000']);

        $this->actingAs($user)
            ->patch('/profile', [
                'name'         => 'Novo Nome',
                'phone'        => '11999999999',
                'city'         => 'Curitiba',
                'neighborhood' => 'Batel',
            ])
            ->assertRedirect('/profile')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'           => $user->id,
            'name'         => 'Novo Nome',
            'phone'        => '11999999999',
            'city'         => 'Curitiba',
            'neighborhood' => 'Batel',
        ]);
    }

    public function test_email_is_not_changed_during_profile_update(): void
    {
        $user = User::factory()->create(['email' => 'original@example.com']);

        $this->actingAs($user)->patch('/profile', [
            'name'  => $user->name,
            'phone' => $user->phone,
        ]);

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'email' => 'original@example.com',
        ]);
    }

    public function test_name_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/profile', ['name' => '', 'phone' => '11999999999'])
            ->assertSessionHasErrors('name');
    }

    public function test_phone_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->patch('/profile', ['name' => 'Nome', 'phone' => ''])
            ->assertSessionHasErrors('phone');
    }

    // -------------------------------------------------------------------------
    // Nickname validation
    // -------------------------------------------------------------------------

    public function test_user_can_set_a_valid_nickname(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'name'     => $user->name,
            'phone'    => $user->phone,
            'nickname' => 'Guerreiro Oeste',
        ])->assertRedirect('/profile')->assertSessionHas('success');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'nickname' => 'Guerreiro Oeste']);
    }

    public function test_user_can_set_nickname_with_accents(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'name'     => $user->name,
            'phone'    => $user->phone,
            'nickname' => 'Leão Dourado',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'nickname' => 'Leão Dourado']);
    }

    public function test_nickname_can_be_empty(): void
    {
        $user = User::factory()->create(['nickname' => 'antigo']);

        $this->actingAs($user)->patch('/profile', [
            'name'     => $user->name,
            'phone'    => $user->phone,
            'nickname' => '',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['id' => $user->id, 'nickname' => null]);
    }

    public function test_nickname_too_short_fails(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'name'     => $user->name,
            'phone'    => $user->phone,
            'nickname' => 'ab',
        ])->assertSessionHasErrors('nickname');
    }

    public function test_nickname_too_long_fails(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'name'     => $user->name,
            'phone'    => $user->phone,
            'nickname' => str_repeat('a', 31),
        ])->assertSessionHasErrors('nickname');
    }

    public function test_duplicate_nickname_fails(): void
    {
        User::factory()->create(['nickname' => 'apelido_ja_usado']);
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'name'     => $user->name,
            'phone'    => $user->phone,
            'nickname' => 'apelido_ja_usado',
        ])->assertSessionHasErrors('nickname');
    }

    public function test_user_can_keep_their_own_nickname(): void
    {
        $user = User::factory()->create(['nickname' => 'meu_apelido']);

        $this->actingAs($user)->patch('/profile', [
            'name'     => $user->name,
            'phone'    => $user->phone,
            'nickname' => 'meu_apelido',
        ])->assertSessionHasNoErrors();
    }

    // -------------------------------------------------------------------------
    // Avatar – file upload
    // -------------------------------------------------------------------------

    public function test_user_can_upload_avatar_image(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $this->actingAs($user)->patch('/profile', [
            'name'        => $user->name,
            'phone'       => $user->phone,
            'avatar_file' => $file,
        ])->assertRedirect('/profile')->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_uploading_new_avatar_deletes_old_one(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        // Upload first avatar
        $first = UploadedFile::fake()->image('first.jpg');
        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name, 'phone' => $user->phone, 'avatar_file' => $first,
        ]);
        $user->refresh();
        $oldPath = $user->avatar_path;
        Storage::disk('public')->assertExists($oldPath);

        // Upload second avatar
        $second = UploadedFile::fake()->image('second.png');
        $this->actingAs($user)->patch('/profile', [
            'name' => $user->name, 'phone' => $user->phone, 'avatar_file' => $second,
        ]);
        $user->refresh();

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    public function test_invalid_avatar_mime_type_is_rejected(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('malware.exe', 100, 'application/octet-stream');

        $this->actingAs($user)->patch('/profile', [
            'name'        => $user->name,
            'phone'       => $user->phone,
            'avatar_file' => $file,
        ])->assertSessionHasErrors('avatar_file');
    }

    public function test_avatar_file_exceeding_2mb_is_rejected(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('big.jpg')->size(3000); // 3 MB

        $this->actingAs($user)->patch('/profile', [
            'name'        => $user->name,
            'phone'       => $user->phone,
            'avatar_file' => $file,
        ])->assertSessionHasErrors('avatar_file');
    }

    // -------------------------------------------------------------------------
    // Avatar – external URL
    // -------------------------------------------------------------------------

    public function test_user_can_set_external_avatar_url(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'name'       => $user->name,
            'phone'      => $user->phone,
            'avatar_url' => 'https://example.com/avatar.png',
        ])->assertRedirect('/profile')->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id'         => $user->id,
            'avatar_url' => 'https://example.com/avatar.png',
        ]);
    }

    public function test_invalid_avatar_url_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->patch('/profile', [
            'name'       => $user->name,
            'phone'      => $user->phone,
            'avatar_url' => 'not-a-url',
        ])->assertSessionHasErrors('avatar_url');
    }

    public function test_uploading_file_clears_avatar_url(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['avatar_url' => 'https://example.com/old.png']);

        $file = UploadedFile::fake()->image('new.jpg');

        $this->actingAs($user)->patch('/profile', [
            'name'        => $user->name,
            'phone'       => $user->phone,
            'avatar_file' => $file,
        ]);

        $user->refresh();
        $this->assertNull($user->avatar_url);
        $this->assertNotNull($user->avatar_path);
    }
}
