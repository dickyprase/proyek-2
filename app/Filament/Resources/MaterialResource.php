<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Filament\Resources\MaterialResource\RelationManagers;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Toggle;

class MaterialResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Material';

    public static function form(Form $form): Form
    {
        $isMandor = Auth::user()->hasRole('mandor');

        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->disabled($isMandor)
                    ->label('Nama Material'),
                Forms\Components\Select::make('project_id')
                    ->relationship('project', 'name')
                    ->required()
                    ->disabled($isMandor)
                    ->label('Proyek'),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->columnSpanFull()
                    ->disabled($isMandor)
                    ->label('Deskripsi'),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->disabled($isMandor),
                Forms\Components\TextInput::make('unit')
                    ->required()
                    ->maxLength(255)
                    ->disabled($isMandor),
                Toggle::make('is_delivered')
                    ->onColor('success')
                    ->offColor('danger')
                    ->label('Delivered'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $isMandor = Auth::user()->hasRole('mandor');
                if ($isMandor) {
                    $userId = Auth::user()->id;
            
                    // Menggunakan join untuk memfilter materials berdasarkan project_id
                    $query->whereHas('project', function (Builder $query) use ($userId) {
                        $query->where('user_id', $userId);
                    });
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->label('Nama Material'),
                Tables\Columns\TextColumn::make('project.name')
                    ->searchable()
                    ->label('Proyek'),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('unit')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_delivered')
                    ->boolean()
                    ->label('Delivered'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any'
        ];
    }
    
}
