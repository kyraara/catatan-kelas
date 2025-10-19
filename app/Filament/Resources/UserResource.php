<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    public static ?string $navigationGroup = 'Manajemen User';

    public static ?int $navigationSort = 20;

    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => bcrypt($state))
                    ->required(fn($livewire) => $livewire instanceof CreateRecord)
                    ->label('Password'),

                // Field untuk mengatur role user
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name') // Spatie Permission
                    ->multiple()
                    ->preload()
                    ->label('Role')
                    ->required(),

                Forms\Components\TextInput::make('kode_guru')
                    ->label('Kode Guru/NIP')
                    ->helperText('Isi hanya jika user adalah guru')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nama')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->label('Email')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Role')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('kode_guru')->label('Kode Guru/NIP')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Dibuat Pada')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->label('Diubah Pada')->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
