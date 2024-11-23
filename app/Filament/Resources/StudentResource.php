<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Section;
use App\Models\Student;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('class_id')->
                live()->
                relationship(name:'class',titleAttribute:'name'),

                Select::make('section_id')->label('section')->options(function(Get $get){
                    $classId = $get('class_id');
                    if($classId){
                        return Section::where('class_id',$classId)->pluck('name','id')->toArray();
                    }
                }),

                TextInput::make('name')->required()->autofocus(),
                TextInput::make('email')->required()->unique(),

            ]);
    }

    public static function table(Table $table): Table
    {
        $table
            ->columns([

                TextColumn::make('name')->
                    searchable()->
                    sortable(),
                TextColumn::make('email')->
                    searchable()->
                    sortable(),
                TextColumn::make('class.name')->badge(),
                TextColumn::make('section.name')->badge(),
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
            // dd($table);
            return $table;
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
        ];
    }
}
