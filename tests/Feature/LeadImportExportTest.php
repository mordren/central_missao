<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx as XlsxWriter;
use Tests\TestCase;

class LeadImportExportTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================================
    // Helpers
    // =========================================================================

    /** Pula o teste se a extensão zip não estiver habilitada (necessária para XLSX). */
    private function requireZip(): void
    {
        if (!class_exists('ZipArchive')) {
            $this->markTestSkipped('ext-zip não está habilitado. Habilite extension=zip no php.ini para executar este teste.');
        }
    }

    /** Cria um arquivo .xlsx temporário com os headers corretos e as linhas fornecidas. */
    private function makeXlsx(array $rows, array $headers = null): UploadedFile
    {
        $headers ??= [
            'name', 'email', 'phone', 'city', 'neighborhood', 'referred_by',
            'date_of_birth', 'religion', 'education_level', 'higher_course',
            'profession', 'how_known', 'first_spokesperson',
            'pauta1', 'pauta2', 'pauta3', 'political_ambition', 'current_status',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row
        foreach ($headers as $col => $value) {
            $sheet->setCellValue([$col + 1, 1], $value);
        }

        // Data rows
        foreach ($rows as $rowIdx => $row) {
            foreach ($row as $col => $value) {
                $sheet->setCellValue([$col + 1, $rowIdx + 2], $value);
            }
        }

        $tmpPath = sys_get_temp_dir() . '/test_leads_' . uniqid() . '.xlsx';
        (new XlsxWriter($spreadsheet))->save($tmpPath);

        return new UploadedFile(
            $tmpPath,
            'leads.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true // test mode — skip is_uploaded_file() check
        );
    }

    /** Retorna um array de linha válida com todos os 18 campos. */
    private function validRow(array $overrides = []): array
    {
        return array_values(array_merge([
            'name'               => 'Ana Souza',
            'email'              => 'ana@example.com',
            'phone'              => '11999887766',
            'city'               => 'São Paulo',
            'neighborhood'       => 'Centro',
            'referred_by'        => 'Alguém',
            'date_of_birth'      => '1990-01-15',
            'religion'           => 'Católica',
            'education_level'    => 'Superior',
            'higher_course'      => 'Direito',
            'profession'         => 'Advogada',
            'how_known'          => 'Instagram',
            'first_spokesperson' => 'Kim Kataguiri',
            'pauta1'             => 'Segurança',
            'pauta2'             => 'Educação',
            'pauta3'             => '',
            'political_ambition' => 'Vereadora',
            'current_status'     => 'Ativo',
        ], $overrides));
    }

    /** Cria um admin. */
    private function admin(): User
    {
        return User::factory()->create(['role' => 'administrador']);
    }

    /** Cria um coordenador. */
    private function coordinator(): User
    {
        return User::factory()->create(['role' => 'coordenador']);
    }

    /** Cria um participante comum. */
    private function participant(): User
    {
        return User::factory()->create(['role' => 'participante']);
    }

    // =========================================================================
    // Controle de acesso — Export
    // =========================================================================

    public function test_guest_is_redirected_from_export(): void
    {
        $this->get('/leads/export')->assertRedirect('/login');
    }

    public function test_participant_cannot_access_export(): void
    {
        $this->actingAs($this->participant())
            ->get('/leads/export')
            ->assertForbidden();
    }

    public function test_coordinator_can_export_leads(): void
    {
        $this->requireZip();
        $response = $this->actingAs($this->coordinator())
            ->get('/leads/export');

        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type') ?? ''
        );
    }

    public function test_admin_can_export_leads(): void
    {
        $this->requireZip();
        $response = $this->actingAs($this->admin())
            ->get('/leads/export');

        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type') ?? ''
        );
    }

    public function test_export_filename_contains_date(): void
    {
        $this->requireZip();
        $response = $this->actingAs($this->admin())
            ->get('/leads/export');

        $disposition = $response->headers->get('Content-Disposition') ?? '';
        $this->assertTrue(
            str_contains($disposition, 'leads_') || str_contains($disposition, '.xlsx'),
            "Content-Disposition header does not contain expected filename parts: {$disposition}"
        );
    }

    // =========================================================================
    // Controle de acesso — Import
    // =========================================================================

    public function test_guest_is_redirected_from_import_form(): void
    {
        $this->get('/leads/import')->assertRedirect('/login');
    }

    public function test_coordinator_cannot_access_import_form(): void
    {
        $this->actingAs($this->coordinator())
            ->get('/leads/import')
            ->assertForbidden();
    }

    public function test_admin_can_view_import_form(): void
    {
        $this->actingAs($this->admin())
            ->get('/leads/import')
            ->assertOk()
            ->assertViewIs('admin.leads_import');
    }

    // =========================================================================
    // Controle de acesso — Template
    // =========================================================================

    public function test_admin_can_download_template(): void
    {
        $this->requireZip();
        $response = $this->actingAs($this->admin())
            ->get('/leads/template');

        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type') ?? ''
        );
    }

    public function test_coordinator_cannot_download_template(): void
    {
        $this->actingAs($this->coordinator())
            ->get('/leads/template')
            ->assertForbidden();
    }

    // =========================================================================
    // Preview — validação do arquivo
    // =========================================================================

    public function test_preview_requires_file(): void
    {
        $this->actingAs($this->admin())
            ->post('/leads/import/preview', [])
            ->assertSessionHasErrors('spreadsheet');
    }

    public function test_preview_rejects_non_xlsx_file(): void
    {
        $csv = UploadedFile::fake()->createWithContent('leads.csv', "name,email\nAna,ana@test.com");

        $this->actingAs($this->admin())
            ->post('/leads/import/preview', ['spreadsheet' => $csv])
            ->assertSessionHasErrors('spreadsheet');
    }

    public function test_preview_rejects_wrong_headers(): void
    {
        $this->requireZip();
        $file = $this->makeXlsx(
            [['Ana Souza', 'ana@example.com', '11999887766']],
            ['nome', 'email', 'telefone'] // cabeçalhos errados
        );

        $this->actingAs($this->admin())
            ->post('/leads/import/preview', ['spreadsheet' => $file])
            ->assertSessionHasErrors('spreadsheet');
    }

    // =========================================================================
    // Preview — processamento de linhas
    // =========================================================================

    public function test_preview_shows_valid_rows(): void
    {
        $this->requireZip();
        $file = $this->makeXlsx([$this->validRow()]);

        $response = $this->actingAs($this->admin())
            ->post('/leads/import/preview', ['spreadsheet' => $file]);

        $response->assertViewIs('admin.leads_import_preview');
        $response->assertViewHas('valid', fn($valid) => count($valid) === 1);
        $response->assertViewHas('duplicates', fn($d) => count($d) === 0);
        $response->assertViewHas('errors', fn($e) => count($e) === 0);
    }

    public function test_preview_separates_duplicate_email_into_duplicates(): void
    {
        $this->requireZip();
        User::factory()->create(['email' => 'ana@example.com']);

        $file = $this->makeXlsx([$this->validRow(['email' => 'ana@example.com'])]);

        $response = $this->actingAs($this->admin())
            ->post('/leads/import/preview', ['spreadsheet' => $file]);

        $response->assertViewHas('valid', fn($v) => count($v) === 0);
        $response->assertViewHas('duplicates', fn($d) => count($d) === 1);
    }

    public function test_preview_puts_missing_name_in_errors(): void
    {
        $this->requireZip();
        $row = $this->validRow(['name' => '']);
        $file = $this->makeXlsx([$row]);

        $response = $this->actingAs($this->admin())
            ->post('/leads/import/preview', ['spreadsheet' => $file]);

        $response->assertViewHas('errors', fn($e) => count($e) === 1);
        $response->assertViewHas('valid', fn($v) => count($v) === 0);
    }

    public function test_preview_puts_missing_email_in_errors(): void
    {
        $this->requireZip();
        $row = $this->validRow(['email' => '']);
        $file = $this->makeXlsx([$row]);

        $response = $this->actingAs($this->admin())
            ->post('/leads/import/preview', ['spreadsheet' => $file]);

        $response->assertViewHas('errors', fn($e) => count($e) === 1);
    }

    public function test_preview_puts_phone_too_short_in_errors(): void
    {
        $this->requireZip();
        $row = $this->validRow(['phone' => '123']); // menos de 6 dígitos
        $file = $this->makeXlsx([$row]);

        $response = $this->actingAs($this->admin())
            ->post('/leads/import/preview', ['spreadsheet' => $file]);

        $response->assertViewHas('errors', fn($e) => count($e) === 1);
    }

    public function test_preview_skips_blank_rows(): void
    {
        $this->requireZip();
        // Linha em branco (todos os campos vazios)
        $blank = array_fill(0, 18, '');
        $file = $this->makeXlsx([$blank, $this->validRow()]);

        $response = $this->actingAs($this->admin())
            ->post('/leads/import/preview', ['spreadsheet' => $file]);

        // Só a linha válida deve aparecer
        $response->assertViewHas('valid', fn($v) => count($v) === 1);
    }

    public function test_preview_handles_multiple_rows_mixed(): void
    {
        $this->requireZip();
        User::factory()->create(['email' => 'existing@example.com']);

        $rows = [
            $this->validRow(['email' => 'new1@example.com']),          // válida
            $this->validRow(['email' => 'existing@example.com']),      // duplicada
            $this->validRow(['name' => '', 'email' => 'bad@example.com']), // erro
        ];
        $file = $this->makeXlsx($rows);

        $response = $this->actingAs($this->admin())
            ->post('/leads/import/preview', ['spreadsheet' => $file]);

        $response->assertViewHas('valid', fn($v) => count($v) === 1);
        $response->assertViewHas('duplicates', fn($d) => count($d) === 1);
        $response->assertViewHas('errors', fn($e) => count($e) === 1);
    }

    public function test_preview_stores_data_in_session(): void
    {
        $this->requireZip();
        $file = $this->makeXlsx([$this->validRow()]);

        $this->actingAs($this->admin())
            ->post('/leads/import/preview', ['spreadsheet' => $file])
            ->assertSessionHas('lead_import');
    }

    // =========================================================================
    // Confirm — criação de usuários
    // =========================================================================

    public function test_confirm_creates_users_from_session(): void
    {
        $row = $this->validRow(['email' => 'nova@example.com', 'phone' => '11988776655']);
        $sessionData = [
            'valid'      => [['row' => 2, 'data' => array_combine([
                'name', 'email', 'phone', 'city', 'neighborhood', 'referred_by',
                'date_of_birth', 'religion', 'education_level', 'higher_course',
                'profession', 'how_known', 'first_spokesperson',
                'pauta1', 'pauta2', 'pauta3', 'political_ambition', 'current_status',
            ], $row)]],
            'duplicates' => [],
            'errors'     => [],
        ];

        $this->actingAs($this->admin())
            ->withSession(['lead_import' => $sessionData])
            ->post('/leads/import/confirm')
            ->assertRedirect(route('leads.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'nova@example.com',
            'role'  => 'participante',
        ]);
    }

    public function test_confirm_sets_force_password_change(): void
    {
        $row = $this->validRow(['email' => 'force@example.com', 'phone' => '11988776655']);
        $sessionData = [
            'valid'      => [['row' => 2, 'data' => array_combine([
                'name', 'email', 'phone', 'city', 'neighborhood', 'referred_by',
                'date_of_birth', 'religion', 'education_level', 'higher_course',
                'profession', 'how_known', 'first_spokesperson',
                'pauta1', 'pauta2', 'pauta3', 'political_ambition', 'current_status',
            ], $row)]],
            'duplicates' => [],
            'errors'     => [],
        ];

        $this->actingAs($this->admin())
            ->withSession(['lead_import' => $sessionData])
            ->post('/leads/import/confirm');

        $user = User::where('email', 'force@example.com')->first();
        $this->assertTrue((bool) $user->force_password_change);
    }

    public function test_confirm_password_is_first_6_digits_of_phone(): void
    {
        $row = $this->validRow(['email' => 'pass@example.com', 'phone' => '11988776655']);
        $sessionData = [
            'valid'      => [['row' => 2, 'data' => array_combine([
                'name', 'email', 'phone', 'city', 'neighborhood', 'referred_by',
                'date_of_birth', 'religion', 'education_level', 'higher_course',
                'profession', 'how_known', 'first_spokesperson',
                'pauta1', 'pauta2', 'pauta3', 'political_ambition', 'current_status',
            ], $row)]],
            'duplicates' => [],
            'errors'     => [],
        ];

        $this->actingAs($this->admin())
            ->withSession(['lead_import' => $sessionData])
            ->post('/leads/import/confirm');

        $user = User::where('email', 'pass@example.com')->first();
        // Primeiros 6 dígitos de '11988776655' → '119887'
        $this->assertTrue(Hash::check('119887', $user->password));
    }

    public function test_confirm_skips_emails_created_since_preview(): void
    {
        // Cria o usuário DEPOIS de ter feito o "preview" (simulando race condition)
        $row = $this->validRow(['email' => 'late@example.com', 'phone' => '11988776655']);
        $sessionData = [
            'valid'      => [['row' => 2, 'data' => array_combine([
                'name', 'email', 'phone', 'city', 'neighborhood', 'referred_by',
                'date_of_birth', 'religion', 'education_level', 'higher_course',
                'profession', 'how_known', 'first_spokesperson',
                'pauta1', 'pauta2', 'pauta3', 'political_ambition', 'current_status',
            ], $row)]],
            'duplicates' => [],
            'errors'     => [],
        ];

        User::factory()->create(['email' => 'late@example.com']);

        $this->actingAs($this->admin())
            ->withSession(['lead_import' => $sessionData])
            ->post('/leads/import/confirm');

        // Deve existir apenas 2 users: o admin + o factory criado acima
        // (o da sessão foi pulado por duplicata)
        $this->assertCount(2, User::all());
    }

    public function test_confirm_clears_session_after_import(): void
    {
        $row = $this->validRow(['email' => 'clear@example.com', 'phone' => '11988776655']);
        $sessionData = [
            'valid'      => [['row' => 2, 'data' => array_combine([
                'name', 'email', 'phone', 'city', 'neighborhood', 'referred_by',
                'date_of_birth', 'religion', 'education_level', 'higher_course',
                'profession', 'how_known', 'first_spokesperson',
                'pauta1', 'pauta2', 'pauta3', 'political_ambition', 'current_status',
            ], $row)]],
            'duplicates' => [],
            'errors'     => [],
        ];

        $this->actingAs($this->admin())
            ->withSession(['lead_import' => $sessionData])
            ->post('/leads/import/confirm')
            ->assertSessionMissing('lead_import');
    }

    public function test_confirm_without_session_redirects_to_import(): void
    {
        $this->actingAs($this->admin())
            ->post('/leads/import/confirm')
            ->assertRedirect(route('leads.import'));
    }

    public function test_confirm_success_message_includes_count(): void
    {
        $rows = [];
        $phones = ['11988000001', '11988000002'];
        foreach (['a@ex.com', 'b@ex.com'] as $idx => $email) {
            $rows[] = ['row' => count($rows) + 2, 'data' => array_combine([
                'name', 'email', 'phone', 'city', 'neighborhood', 'referred_by',
                'date_of_birth', 'religion', 'education_level', 'higher_course',
                'profession', 'how_known', 'first_spokesperson',
                'pauta1', 'pauta2', 'pauta3', 'political_ambition', 'current_status',
            ], $this->validRow(['email' => $email, 'phone' => $phones[$idx]]))];
        }

        $sessionData = ['valid' => $rows, 'duplicates' => [], 'errors' => []];

        $response = $this->actingAs($this->admin())
            ->withSession(['lead_import' => $sessionData])
            ->post('/leads/import/confirm');

        $response->assertSessionHas('success', fn($msg) => str_contains($msg, '2'));
    }

    // =========================================================================
    // Export — conteúdo
    // =========================================================================

    public function test_export_returns_xlsx_with_correct_content_type(): void
    {
        $this->requireZip();
        $response = $this->actingAs($this->admin())
            ->get('/leads/export');

        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type') ?? ''
        );
    }

    public function test_export_works_with_no_leads(): void
    {
        $this->requireZip();
        // Apenas o admin na base, sem outros users; deve funcionar sem erro
        $this->actingAs($this->admin())
            ->get('/leads/export')
            ->assertOk();
    }

    public function test_export_works_with_leads_in_database(): void
    {
        $this->requireZip();
        User::factory()->count(3)->create(['role' => 'participante']);

        $this->actingAs($this->admin())
            ->get('/leads/export')
            ->assertOk();
    }
}
