<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Permission\Models\Role;

/** Admin: everything. Editor: content + menus. Sales: leads. Access rules live in app/Policies. */
class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?int $navigationSort = 2;

    public const DESCRIPTIONS = [
        'Admin' => 'Everything, including users, roles and settings.',
        'Editor' => 'Website content and menus.',
        'Sales' => 'Leads only: quote requests, contact messages, subscribers.',
    ];

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->unique(ignoreRecord: true)
                ->disabled(fn (?Role $record) => $record && array_key_exists($record->name, self::DESCRIPTIONS))
                ->helperText('Admin, Editor and Sales are used by the access rules and cannot be renamed.'),
            Forms\Components\Hidden::make('guard_name')->default('web'),
            Forms\Components\Select::make('users')->relationship('users', 'name')->multiple()->preload()
                ->label('Users with this role'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->description(fn (Role $record) => self::DESCRIPTIONS[$record->name] ?? null),
                Tables\Columns\TextColumn::make('users_count')->counts('users')->label('Users'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->hidden(fn (Role $record) => array_key_exists($record->name, self::DESCRIPTIONS)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
