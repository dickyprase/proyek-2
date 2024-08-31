<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ComplaintResource\Pages;
use App\Filament\Resources\ComplaintResource\RelationManagers;
use App\Filament\Resources\ComplaintResource\RelationManagers\ResponseRelationManager;
use App\Models\Complaint;
use App\Models\Project;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Illuminate\Support\Facades\Auth;

class ComplaintResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Complaint::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Ticketing';
    
    protected static ?string $navigationLabel = 'Komplain';

    protected static ?string $breadcrumb = "Komplain";

    public static function form(Form $form): Form
    {

        $isDirektur = Auth::user()->hasRole('direktur');
        $userId = Auth::user()->id;
        return $form
        ->schema([   
            Forms\Components\TextInput::make('title')
                ->required()
                ->label('Judul')
                ->disabled($isDirektur),
            Forms\Components\Textarea::make('description')
                ->required()
                ->label('Deskripsi')
                ->disabled($isDirektur),
            Forms\Components\Select::make('project_id')
                ->relationship('project', 'name', modifyQueryUsing: fn (Builder $query) => $query->where('id', request()->route()->parameter('record')))
                ->required()
                ->label('Proyek')
                ->disabled($isDirektur),
        
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->modifyQueryUsing(function (Builder $query) {
                $isMandor = Auth::user()->hasRole('mandor');
                if ($isMandor) {
                    $userId = Auth::user()->id;
                    $query->where('user_id', $userId);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->label('Judul'),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->hidden(Auth::user()->hasRole('mandor')),
                Tables\Columns\TextColumn::make('project.name')
                    ->searchable()
                    ->label('Proyek'),
                Tables\Columns\TextColumn::make('description')
                    ->searchable()
                    ->label('Deskripsi'),
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
            ResponseRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComplaints::route('/'),
            'create' => Pages\CreateComplaint::route('/create'),
            'edit' => Pages\EditComplaint::route('/{record}/edit'),
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
