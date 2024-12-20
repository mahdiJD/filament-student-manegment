<?php

namespace App\Filament\Resources;

use App\Exports\StudentsExport;
use App\Filament\Resources\StudentResource\Pages;
use App\Filament\Resources\StudentResource\RelationManagers;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Mechanisms\HandleComponents\Synthesizers\CollectionSynth;
use Maatwebsite\Excel\Facades\Excel;

class StudentResource extends Resource
{
    protected static ?string $model = Student::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';
    protected static ?string $navigationGroup = 'Academic Manegment';

    public static function getNavigationBadge(): ?string
    {
        return static::$model::count();
    }


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
        return $table
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
                Filter::make('class-section-filter')->form([
                    Select::make('class_id')->
                        label('Filter By Class')->placeholder('Select a Class')->options(Classes::pluck('name','id')->toArray()),
                    Select::make('section_id')->
                        label('Filter By Section')->placeholder('Select a Section')->options(function(Get $get){
                            $classId = $get('class_id');
                            if($classId){
                                return Section::where('class_id',$classId)->pluck('name','id')->toArray();
                            }
                        })
                ])
                ->query(function (Builder $query, array $data):Builder{
                    return $query->when($data['class_id'],function($query) use($data){
                        return $query->where('class_id', $data['class_id']);
                    })->when($data['section_id'],function($query) use($data){
                        return $query->where('section_id', $data['section_id']);
                    });
                }),
            ])
            ->actions([
                Action::make('downloadPdf')->url(function(Student $student){
                    return route('student.invo.generat', $student);
                }),
                Action::make('qrCode')->url(
                    function(Student $student){
                        return static::getUrl('qrCode', ['record' => $student]);
                    }
                ),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    BulkAction::make('export')->
                    label('Export to exel')->icon('heroicon-o-document-arrow-down')->action(function(Collection $recording){
                        return Excel::download(new StudentsExport($recording), 'student.xlsx');
                    })
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
            'index' => Pages\ListStudents::route('/'),
            'create' => Pages\CreateStudent::route('/create'),
            'edit' => Pages\EditStudent::route('/{record}/edit'),
            'qrCode' => Pages\GenerateQrCode::route('/{record}/qrcode'),
        ];
    }
}
