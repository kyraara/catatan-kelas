<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\CatatanHarian;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CatatanHarianResource\Pages;
use Filament\Tables\Actions\Action;
use App\Filament\Resources\CatatanHarianResource\RelationManagers;

class CatatanHarianResource extends Resource
{
    public static ?string $navigationGroup = 'Jadwal & Catatan';
    public static ?int $navigationSort = 10;
    protected static ?string $model = CatatanHarian::class;
    public static ?string $navigationLabel = 'Catatan Harian';
    public static ?string $pluralModelLabel = 'Daftar Catatan Harian';

    public static ?string $navigationIcon = 'heroicon-o-document-text';


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('user_id')
                ->default(fn() => auth()->id()),
            Forms\Components\Select::make('jadwal_id')
                ->label('Jadwal Kelas/Mapel/Guru')
                ->relationship(
                    'jadwal',
                    modifyQueryUsing: function (Builder $query) {
                        $user = auth()->user();
                        
                        // Admin bisa lihat semua jadwal
                        if ($user->hasRole('admin')) {
                            return $query;
                        }
                        
                        $isGuru = $user->hasRole('guru');
                        $isWaliKelas = $user->hasRole('wali_kelas');
                        
                        // Ambil kelas yang diwalikan (jika wali kelas)
                        $kelasWaliIds = $isWaliKelas 
                            ? \App\Models\Kelas::where('wali_kelas_id', $user->id)->pluck('id')->toArray()
                            : [];
                        
                        return $query->where(function ($q) use ($user, $isGuru, $isWaliKelas, $kelasWaliIds) {
                            // Guru: lihat jadwal yang dia ajar
                            if ($isGuru) {
                                $q->orWhere('guru_id', $user->id);
                            }
                            // Wali Kelas: lihat jadwal di kelas yang diwalikan
                            if ($isWaliKelas && !empty($kelasWaliIds)) {
                                $q->orWhereIn('kelas_id', $kelasWaliIds);
                            }
                        });
                    }
                )
                ->getOptionLabelFromRecordUsing(fn($record) => 
                    $record->kelas->nama . ' - ' . $record->mataPelajaran->nama . ' (' . $record->guru->name . ')'
                )
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(function ($state, callable $set) {
                    if (!$state) {
                        $set('presensis', []);
                        return;
                    }
                    
                    // Load siswa dari kelas jadwal yang dipilih
                    $jadwal = \App\Models\Jadwal::with('kelas.siswa')->find($state);
                    if (!$jadwal || !$jadwal->kelas) {
                        $set('presensis', []);
                        return;
                    }
                    
                    $presensis = $jadwal->kelas->siswa->map(fn($siswa) => [
                        'siswa_id' => $siswa->id,
                        'status' => 'hadir',
                        'keterangan' => null,
                    ])->toArray();
                    
                    $set('presensis', $presensis);
                }),
            Forms\Components\DatePicker::make('tanggal')->label('Tanggal')->required()->default(now()),
            Forms\Components\Textarea::make('materi')->label('Materi')->rows(2)->nullable(),
            
            // Section Presensi Siswa
            Forms\Components\Section::make('Presensi Siswa')
                ->description('Centang status kehadiran setiap siswa')
                ->schema([
                    Forms\Components\Repeater::make('presensis')
                        ->relationship()
                        ->label('')
                        ->schema([
                            Forms\Components\Hidden::make('siswa_id'),
                            Forms\Components\Placeholder::make('nama_siswa')
                                ->label('Nama Siswa')
                                ->content(fn($get) => \App\Models\Siswa::find($get('siswa_id'))?->nama ?? '-'),
                            Forms\Components\Select::make('status')
                                ->label('Status')
                                ->options([
                                    'hadir' => '✅ Hadir',
                                    'izin' => '📝 Izin',
                                    'sakit' => '🏥 Sakit',
                                    'alpa' => '❌ Alpa',
                                ])
                                ->default('hadir')
                                ->required(),
                            Forms\Components\TextInput::make('keterangan')
                                ->label('Keterangan')
                                ->placeholder('Opsional')
                                ->nullable(),
                        ])
                        ->columns(4)
                        ->defaultItems(0)
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false),
                ])
                ->visible(fn($get) => $get('jadwal_id') !== null),
            
            Forms\Components\Textarea::make('jam_kosong')->label('Jam Kosong')->rows(2)->nullable(),
            Forms\Components\Textarea::make('catatan')->label('Catatan')->rows(3)->nullable(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('tanggal')->label('Tanggal')->sortable()->searchable(),
            TextColumn::make('jadwal.kelas.nama')->label('Kelas')->searchable(),
            TextColumn::make('jadwal.hari')->label('Hari')->searchable(),
            TextColumn::make('jadwal.jam_ke')->searchable(),
            TextColumn::make('jadwal.mataPelajaran.nama')->label('Mapel')->searchable(),
            TextColumn::make('jadwal.guru.name')->label('Guru')->searchable(),
            TextColumn::make('materi')->label('Materi')->limit(40)->searchable(),
            TextColumn::make('catatan')->label('Catatan')->limit(30)->searchable(),
            TextColumn::make('approved_at')
                ->label('Status')
                ->formatStateUsing(fn($state) => $state ? '✔ Approved' : 'Pending')
                ->badge()
                ->color(fn($state) => $state ? 'success' : 'warning'),
        ])
            ->filters([
                SelectFilter::make('kelas')
                    ->label('Kelas')
                    ->options(function () {
                        $user = auth()->user();
                        $query = \App\Models\Kelas::query();
                        
                        if (!$user->hasRole('admin')) {
                            $isGuru = $user->hasRole('guru');
                            $isWaliKelas = $user->hasRole('wali_kelas');
                            
                            $kelasIds = collect();
                            
                            // Guru: kelas dari jadwal yang diajar
                            if ($isGuru) {
                                $kelasIds = $kelasIds->merge(
                                    \App\Models\Jadwal::where('guru_id', $user->id)->pluck('kelas_id')
                                );
                            }
                            
                            // Wali Kelas: kelas yang diwalikan
                            if ($isWaliKelas) {
                                $kelasIds = $kelasIds->merge(
                                    \App\Models\Kelas::where('wali_kelas_id', $user->id)->pluck('id')
                                );
                            }
                            
                            $query->whereIn('id', $kelasIds->unique());
                        }
                        
                        return $query->pluck('nama', 'id');
                    })
                    ->query(fn (Builder $query, array $data) => 
                        $query->when($data['value'], fn($q) => 
                            $q->whereHas('jadwal', fn($j) => $j->where('kelas_id', $data['value']))
                        )
                    ),

                SelectFilter::make('mapel')
                    ->label('Mapel')
                    ->options(function () {
                        $user = auth()->user();
                        
                        if ($user->hasRole('admin')) {
                            return \App\Models\MataPelajaran::pluck('nama', 'id');
                        }
                        
                        $isGuru = $user->hasRole('guru');
                        $isWaliKelas = $user->hasRole('wali_kelas');
                        
                        $mapelIds = collect();
                        
                        // Guru: mapel dari jadwal yang diajar
                        if ($isGuru) {
                            $mapelIds = $mapelIds->merge(
                                \App\Models\Jadwal::where('guru_id', $user->id)->pluck('mapel_id')
                            );
                        }
                        
                        // Wali Kelas: mapel di kelas yang diwalikan
                        if ($isWaliKelas) {
                            $kelasIds = \App\Models\Kelas::where('wali_kelas_id', $user->id)->pluck('id');
                            $mapelIds = $mapelIds->merge(
                                \App\Models\Jadwal::whereIn('kelas_id', $kelasIds)->pluck('mapel_id')
                            );
                        }
                        
                        return \App\Models\MataPelajaran::whereIn('id', $mapelIds->unique())->pluck('nama', 'id');
                    })
                    ->query(fn (Builder $query, array $data) => 
                        $query->when($data['value'], fn($q) => 
                            $q->whereHas('jadwal', fn($j) => $j->where('mapel_id', $data['value']))
                        )
                    ),

                SelectFilter::make('guru')
                    ->label('Guru')
                    ->options(function () {
                        $user = auth()->user();
                        
                        if ($user->hasRole('admin')) {
                            return \App\Models\User::role('guru')->pluck('name', 'id');
                        }
                        
                        $isGuru = $user->hasRole('guru');
                        $isWaliKelas = $user->hasRole('wali_kelas');
                        
                        $guruIds = collect();
                        
                        // Guru: diri sendiri
                        if ($isGuru) {
                            $guruIds->push($user->id);
                        }
                        
                        // Wali Kelas: guru yang mengajar di kelas yang diwalikan
                        if ($isWaliKelas) {
                            $kelasIds = \App\Models\Kelas::where('wali_kelas_id', $user->id)->pluck('id');
                            $guruIds = $guruIds->merge(
                                \App\Models\Jadwal::whereIn('kelas_id', $kelasIds)->pluck('guru_id')
                            );
                        }
                        
                        return \App\Models\User::whereIn('id', $guruIds->unique())->pluck('name', 'id');
                    })
                    ->query(fn (Builder $query, array $data) => 
                        $query->when($data['value'], fn($q) => 
                            $q->whereHas('jadwal', fn($j) => $j->where('guru_id', $data['value']))
                        )
                    ),

                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('from')->label('Mulai'),
                        DatePicker::make('to')->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['from'], fn($q) => $q->where('tanggal', '>=', $data['from']))
                            ->when($data['to'], fn($q) => $q->where('tanggal', '<=', $data['to']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->modalHeading('Detail Catatan Harian')
                    ->modalButton('Tutup')
                    ->record(
                        fn($record) => $record->load(['jadwal.kelas', 'jadwal.mataPelajaran', 'jadwal.guru', 'user'])
                    ),
                Tables\Actions\EditAction::make()
                    ->visible(fn($record) => auth()->user()->hasRole('admin')
                        || (auth()->user()->hasRole('guru') && $record->user_id == auth()->id())
                        || (auth()->user()->hasRole('wali_kelas') &&
                            $record->jadwal->kelas->wali_kelas_id == auth()->id())),
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(
                        fn($record) =>
                        auth()->user()->hasRole('admin') ||
                            (
                                auth()->user()->hasRole('wali_kelas')
                                && $record->jadwal->kelas->wali_kelas_id == auth()->id()
                            )
                            && !$record->approved_at
                    )
                    ->requiresConfirmation()
                    ->action(fn($record) => $record->update([
                        'approved_at' => now(),
                        'approved_by' => auth()->id(),
                    ]))
                    ->disabled(fn($record) => $record->approved_at !== null)

            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getViewRecordLayout(): array
    {
        return [
            Section::make('Detail Catatan Harian')
                ->description('Informasi lengkap setiap catatan harian.')
                ->schema([
                    Placeholder::make('jadwal.kelas.nama')->label('Kelas'),
                    Placeholder::make('jadwal.hari')->label('Hari'),
                    Placeholder::make('jadwal.jam_ke')->label('Jam Ke'),
                    Placeholder::make('jadwal.mataPelajaran.nama')->label('Mapel'),
                    Placeholder::make('jadwal.guru.name')->label('Guru'),
                    Placeholder::make('tanggal')->label('Tanggal'),

                    Textarea::make('materi')->label('Materi')->disabled(),
                    Textarea::make('catatan')->label('Catatan')->disabled(),
                    Textarea::make('jam_kosong')->label('Jam Kosong')->disabled(),

                    // Ringkasan Presensi
                    Placeholder::make('ringkasan_presensi')
                        ->label('Ringkasan Presensi')
                        ->content(function ($record) {
                            $ringkasan = $record->getRingkasanPresensi();
                            return "✅ Hadir: {$ringkasan['hadir']} | 📝 Izin: {$ringkasan['izin']} | 🏥 Sakit: {$ringkasan['sakit']} | ❌ Alpa: {$ringkasan['alpa']}";
                        }),
                    
                    Placeholder::make('siswa_tidak_hadir')
                        ->label('Siswa Tidak Hadir')
                        ->content(function ($record) {
                            $tidakHadir = collect();
                            foreach (['izin', 'sakit', 'alpa'] as $status) {
                                $siswa = $record->getSiswaByStatus($status);
                                foreach ($siswa as $nama) {
                                    $label = match($status) {
                                        'izin' => '📝',
                                        'sakit' => '🏥',
                                        'alpa' => '❌',
                                    };
                                    $tidakHadir->push("{$label} {$nama}");
                                }
                            }
                            return $tidakHadir->isEmpty() ? 'Semua hadir' : $tidakHadir->join(', ');
                        }),

                    Placeholder::make('user.name')->label('Diinput oleh'),
                    Placeholder::make('updated_at')->label('Update Terakhir')->content(fn($record) => $record->updated_at->format('d-M Y H:i')),
                ])->alignment(Alignment::Left),
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCatatanHarians::route('/'),
            'create' => Pages\CreateCatatanHarian::route('/create'),
            'edit' => Pages\EditCatatanHarian::route('/{record}/edit'),
        ];
    }

    // Role filtering is handled by global scope in CatatanHarian model
    // See: App\Models\CatatanHarian::booted() method
}
