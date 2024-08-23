<?php

namespace App\Filament\Resources\ComplaintResource\RelationManagers;

use App\Models\Response;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class ResponseRelationManager extends RelationManager
{
    protected static string $relationship = 'Response';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('complaint_id')
                    ->relationship('complaint', 'title')
                    ->required(),
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::user()->id),
                Forms\Components\Textarea::make('response')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('response')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('response')
                    ->description(fn (Response $record): string => $record->created_at, position: 'above')
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort(fn ($query) => $query->orderBy('created_at', 'desc'));
    }
}
