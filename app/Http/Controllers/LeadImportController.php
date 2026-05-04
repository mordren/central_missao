<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class LeadImportController extends Controller
{
    // Internal field names (order used in the import template)
    const HEADERS = [
        'name', 'email', 'phone', 'city', 'neighborhood', 'referred_by',
        'date_of_birth', 'religion', 'education_level', 'higher_course',
        'profession', 'how_known', 'first_spokesperson',
        'pauta1', 'pauta2', 'pauta3', 'political_ambition', 'current_status',
    ];

    // Portuguese labels (as used in the export file) → internal field names
    const HEADERS_PT_MAP = [
        'nome'             => 'name',
        'e-mail'           => 'email',
        'email'            => 'email',
        'telefone'         => 'phone',
        'cidade'           => 'city',
        'bairro'           => 'neighborhood',
        'indicação'        => 'referred_by',
        'indicacao'        => 'referred_by',
        'data de nasc.'    => 'date_of_birth',
        'data de nasc'     => 'date_of_birth',
        'religião'         => 'religion',
        'religiao'         => 'religion',
        'escolaridade'     => 'education_level',
        'curso superior'   => 'higher_course',
        'profissão'        => 'profession',
        'profissao'        => 'profession',
        'como conheceu'    => 'how_known',
        '1º porta-voz'     => 'first_spokesperson',
        '1o porta-voz'     => 'first_spokesperson',
        'pauta 1'          => 'pauta1',
        'pauta 2'          => 'pauta2',
        'pauta 3'          => 'pauta3',
        'ambição política' => 'political_ambition',
        'ambicao politica' => 'political_ambition',
        'status atual'     => 'current_status',
    ];

    // -------------------------------------------------------------------------
    // GET /leads/template  — download blank XLS template
    // -------------------------------------------------------------------------
    public function template()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header row (internal names — matches what import expects)
        $col = 'A';
        foreach (self::HEADERS as $header) {
            $sheet->setCellValue($col . '1', $header);
            $col++;
        }

        $lastCol = chr(ord('A') + count(self::HEADERS) - 1);
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        foreach (range('A', $lastCol) as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        // Example row
        $example = [
            'Maria da Silva', 'maria@email.com', '11987654321',
            'São Paulo', 'Centro', 'João Indicador',
            '1990-05-20', 'Católico', 'Ensino Superior Completo', 'Direito',
            'Advogada', 'Conheci pelo Instagram em 2023',
            'Kim Kataguiri', 'Segurança pública', 'Educação', '',
            'Vereadora', 'Ativo',
        ];
        $col = 'A';
        foreach ($example as $val) {
            $sheet->setCellValue($col . '2', $val);
            $col++;
        }

        $writer = new Xls($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'template_importacao_leads.xls', [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /leads/import  — show upload form
    // -------------------------------------------------------------------------
    public function showImport()
    {
        return view('admin.leads_import');
    }

    // -------------------------------------------------------------------------
    // POST /leads/import/preview  — parse + validate XLS, store in session
    // -------------------------------------------------------------------------
    public function preview(Request $request)
    {
        $request->validate([
            'spreadsheet' => ['required', 'file', 'mimes:xls,xlsx', 'max:5120'],
        ], [
            'spreadsheet.required' => 'Selecione um arquivo .xls ou .xlsx.',
            'spreadsheet.mimes'    => 'O arquivo deve ser .xls ou .xlsx.',
            'spreadsheet.max'      => 'O arquivo não pode ter mais de 5 MB.',
        ]);

        try {
            $spreadsheet = IOFactory::load($request->file('spreadsheet')->getPathname());
        } catch (\Throwable $e) {
            return back()->withErrors(['spreadsheet' => 'Não foi possível ler o arquivo Excel: ' . $e->getMessage()]);
        }

        $sheet = $spreadsheet->getActiveSheet();
        $allRows = $sheet->toArray(null, true, true, false);

        if (empty($allRows)) {
            return back()->withErrors(['spreadsheet' => 'A planilha está vazia.']);
        }

        // Normalize headers: accept both internal names and Portuguese labels,
        // and ignore extra columns (e.g. 'Cadastro' from export).
        $rawHeaders = array_map(fn($h) => trim((string) $h), $allRows[0]);
        $normalizedHeaders = array_map(function ($h) {
            $lower = mb_strtolower($h);
            return self::HEADERS_PT_MAP[$lower] ?? $h;
        }, $rawHeaders);

        $colMap = [];
        foreach ($normalizedHeaders as $idx => $field) {
            if (in_array($field, self::HEADERS, true)) {
                $colMap[$idx] = $field;
            }
        }

        $missingFields = array_diff(self::HEADERS, array_values($colMap));
        if (!empty($missingFields)) {
            $expected = implode(', ', self::HEADERS);
            $got      = implode(', ', $rawHeaders);
            return back()->withErrors([
                'spreadsheet' => "Cabeçalhos inválidos ou ausentes.\nEsperado: {$expected}\nRecebido: {$got}",
            ]);
        }

        // Process data rows
        $valid      = [];
        $duplicates = [];
        $errors     = [];

        foreach ($allRows as $i => $row) {
            if ($i === 0) continue;

            $rowNum = $i + 1;

            $data = array_fill_keys(self::HEADERS, '');
            foreach ($colMap as $idx => $field) {
                $data[$field] = trim((string) ($row[$idx] ?? ''));
            }

            // Blank row guard
            if (empty($data['name']) && empty($data['email']) && empty($data['phone'])) {
                continue;
            }

            // Duplicate email check
            if (!empty($data['email']) && User::where('email', $data['email'])->exists()) {
                $duplicates[] = ['row' => $rowNum, 'data' => $data, 'reason' => "E-mail '{$data['email']}' já cadastrado."];
                continue;
            }

            $validator = Validator::make($data, [
                'name'               => 'required|string|max:150',
                'email'              => 'required|email|max:255',
                'phone'              => 'required|string|max:20',
                'city'               => 'nullable|string|max:100',
                'neighborhood'       => 'nullable|string|max:100',
                'referred_by'        => 'nullable|string|max:150',
                'date_of_birth'      => 'nullable|date',
                'religion'           => 'nullable|string|max:120',
                'education_level'    => 'nullable|string|max:120',
                'higher_course'      => 'nullable|string|max:150',
                'profession'         => 'nullable|string|max:150',
                'how_known'          => 'nullable|string|max:1000',
                'first_spokesperson' => 'nullable|string|max:150',
                'pauta1'             => 'nullable|string|max:250',
                'pauta2'             => 'nullable|string|max:250',
                'pauta3'             => 'nullable|string|max:250',
                'political_ambition' => 'nullable|string|max:150',
                'current_status'     => 'nullable|string|max:255',
            ]);

            if ($validator->fails()) {
                $errors[] = ['row' => $rowNum, 'data' => $data, 'reason' => implode(' | ', $validator->errors()->all())];
                continue;
            }

            $digits = preg_replace('/\D/', '', $data['phone']);
            if (strlen($digits) < 6) {
                $errors[] = ['row' => $rowNum, 'data' => $data, 'reason' => 'Telefone deve ter ao menos 6 dígitos (para gerar a senha padrão).'];
                continue;
            }

            $valid[] = ['row' => $rowNum, 'data' => $data];
        }

        if (empty($valid) && empty($duplicates) && empty($errors)) {
            return back()->withErrors(['spreadsheet' => 'Nenhuma linha de dados encontrada na planilha.']);
        }

        session(['lead_import' => compact('valid', 'duplicates', 'errors')]);

        return view('admin.leads_import_preview', compact('valid', 'duplicates', 'errors'));
    }

    // -------------------------------------------------------------------------
    // POST /leads/import/confirm  — create users
    // -------------------------------------------------------------------------
    public function confirm(Request $request)
    {
        $import = session('lead_import');

        if (empty($import) || empty($import['valid'])) {
            return redirect()->route('leads.import')->withErrors(['spreadsheet' => 'Sessão expirada. Faça upload novamente.']);
        }

        $created = 0;
        $skipped = 0;

        foreach ($import['valid'] as $entry) {
            $data = $entry['data'];

            if (User::where('email', $data['email'])->exists()) {
                $skipped++;
                continue;
            }

            $digits  = preg_replace('/\D/', '', $data['phone']);
            $rawPass = substr($digits, 0, 6);

            User::create([
                'name'               => $data['name'],
                'email'              => $data['email'],
                'phone'              => $data['phone'],
                'password'           => Hash::make($rawPass),
                'role'               => 'participante',
                'city'               => $data['city']               ?: null,
                'neighborhood'       => $data['neighborhood']       ?: null,
                'referred_by'        => $data['referred_by']        ?: null,
                'date_of_birth'      => $data['date_of_birth']      ?: null,
                'religion'           => $data['religion']           ?: null,
                'education_level'    => $data['education_level']    ?: null,
                'higher_course'      => $data['higher_course']      ?: null,
                'profession'         => $data['profession']         ?: null,
                'how_known'          => $data['how_known']          ?: null,
                'first_spokesperson' => $data['first_spokesperson'] ?: null,
                'pauta1'             => $data['pauta1']             ?: null,
                'pauta2'             => $data['pauta2']             ?: null,
                'pauta3'             => $data['pauta3']             ?: null,
                'political_ambition' => $data['political_ambition'] ?: null,
                'current_status'     => $data['current_status']     ?: null,
                'referral_code'      => substr(md5($data['email'] . now()), 0, 12),
                'points'             => 0,
                'force_password_change' => true,
            ]);

            $created++;
        }

        session()->forget('lead_import');

        $duplicateCount = count($import['duplicates']) + $skipped;
        $errorCount     = count($import['errors']);

        $message = "{$created} usuário(s) importado(s) com sucesso.";
        if ($duplicateCount) $message .= " {$duplicateCount} linha(s) ignorada(s) (e-mail duplicado).";
        if ($errorCount)     $message .= " {$errorCount} linha(s) com erro não importada(s).";

        return redirect()->route('leads.index')->with('success', $message);
    }

    // -------------------------------------------------------------------------
    // GET /leads/export  — download all leads as XLS
    // -------------------------------------------------------------------------
    public function export()
    {
        $leads = User::query()
            ->select([
                'name', 'email', 'phone', 'city', 'neighborhood', 'referred_by',
                'date_of_birth', 'religion', 'education_level', 'higher_course',
                'profession', 'how_known', 'first_spokesperson',
                'pauta1', 'pauta2', 'pauta3', 'political_ambition', 'current_status',
                'created_at',
            ])
            ->orderBy('name')
            ->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Leads');

        $headers = [
            'Nome', 'E-mail', 'Telefone', 'Cidade', 'Bairro', 'Indicação',
            'Data de Nasc.', 'Religião', 'Escolaridade', 'Curso Superior',
            'Profissão', 'Como Conheceu', '1º Porta-voz',
            'Pauta 1', 'Pauta 2', 'Pauta 3', 'Ambição Política', 'Status Atual',
            'Cadastro',
        ];

        $col = 1;
        foreach ($headers as $header) {
            $sheet->setCellValue([$col++, 1], $header);
        }

        $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastColLetter . '1')->getFont()->setBold(true);

        $row = 2;
        foreach ($leads as $lead) {
            $values = [
                $lead->name,
                $lead->email,
                $lead->phone,
                $lead->city,
                $lead->neighborhood,
                $lead->referred_by,
                $lead->date_of_birth ? \Carbon\Carbon::parse($lead->date_of_birth)->format('d/m/Y') : '',
                $lead->religion,
                $lead->education_level,
                $lead->higher_course,
                $lead->profession,
                $lead->how_known,
                $lead->first_spokesperson,
                $lead->pauta1,
                $lead->pauta2,
                $lead->pauta3,
                $lead->political_ambition,
                $lead->current_status,
                $lead->created_at ? $lead->created_at->format('d/m/Y H:i') : '',
            ];
            foreach ($values as $c => $val) {
                $sheet->setCellValue([$c + 1, $row], $val);
            }
            $row++;
        }

        foreach (range(1, count($headers)) as $c) {
            $sheet->getColumnDimensionByColumn($c)->setAutoSize(true);
        }

        $sheet->freezePane('A2');

        $writer   = new Xls($spreadsheet);
        $filename = 'leads_' . now()->format('Y-m-d_His') . '.xls';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }
}