<?php

namespace App\Livewire;

use App\Mapper\OfferDataMapper;
use App\Models\Offer;
use Livewire\Component;
use App\Models\Post;
use Carbon\Carbon;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Saade\FilamentAutograph\Forms\Components\SignaturePad;
use Filament\Forms;
use Filament\Forms\Components\Section;

class ViewOffer extends Component implements HasForms
{
    use InteractsWithForms;

    public $offer;
    public string $token;
    public ?array $data = [];

    public $provider;
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount($token)
    {

        $this->token = $token;
        $this->offer = Offer::withoutGlobalScopes()->where('token', $this->token)->where('general_access', 1)->first();
        $dataMapper = new OfferDataMapper();
        $this->provider = $dataMapper->getdata($this->offer)->toArray();
        // dd($this->provider);
        if (!$this->offer) {
            abort(404);
        }
        $this->form->fill();
    }


    public function form(Form $form)
    {
        if ($this->offer->signed === null) {
            $this->offer->signed = 0;
        }

        return match ($this->offer->signed) {
            1 =>  $form
                ->schema([
                    Forms\Components\Section::make(__('offer.section.title.signature'))
                        ->schema([
                            SignaturePad::make('signature')
                                ->label(__('offer.field.sign_here'))
                                ->disabled()
                                ->default($this->offer->signature)
                                ->dotSize(2.0)
                                ->lineMinWidth(0.5)
                                ->lineMaxWidth(2.5)
                                ->throttle(16)
                                ->minDistance(5)
                                ->velocityFilterWeight(0.7)
                                ->backgroundColor('rgba(0,0,0,0)')  // Background color on light mode
                                ->backgroundColorOnDark('#f0a')     // Background color on dark mode (defaults to backgroundColor)
                                ->exportBackgroundColor('#f00')     // Background color on export (defaults to backgroundColor)
                                ->penColor('#000')                  // Pen color on light mode
                                ->penColorOnDark('#fff')
                                ->required()            // Pen color on dark mode (defaults to penColor)
                                ->exportPenColor('#0f0')
                        ])
                        ->columnSpan(1),
                    Forms\Components\Section::make(__('offer.section.title.details'))
                        ->schema([
                            Forms\Components\Placeholder::make('signature_name')
                                ->content($this->offer->signature_name),
                            Forms\Components\Placeholder::make('signature_date')
                                ->content($this->offer->signature_date),
                        ])
                        ->columnSpan(1),
                ])->columns(2)
                ->statePath('data'),
            0 => $form
                ->schema([
                    Forms\Components\Section::make(__('offer.section.title.signature'))
                        ->description(__('offer.section.descr.signature'))
                        ->schema([
                            SignaturePad::make('signature')
                                ->label(__('offer.field.sign_here'))
                                ->default($this->offer->signature)
                                ->dotSize(2.0)
                                ->lineMinWidth(0.5)
                                ->lineMaxWidth(2.5)
                                ->throttle(16)
                                ->minDistance(5)
                                ->velocityFilterWeight(0.7)
                                ->backgroundColor('red')  // Background color on light mode
                                ->backgroundColorOnDark('red')     // Background color on dark mode (defaults to backgroundColor)
                                ->exportBackgroundColor('red')     // Background color on export (defaults to backgroundColor)
                                ->penColor('red')                  // Pen color on light mode
                                ->penColorOnDark('#fff')
                                ->required()            // Pen color on dark mode (defaults to penColor)
                                ->exportPenColor('#0f0')
                        ])
                        ->columnSpan(1),
                    Forms\Components\Section::make(__('offer.section.title.details'))

                        ->description(__('offer.section.descr.details'))
                        ->schema([
                            Forms\Components\TextInput::make('signature_name')
                                ->required(),
                            Forms\Components\DatePicker::make('signature_date')
                                ->default(Carbon::now()->format('d/m/Y')),
                        ])
                        ->columnSpan(1),
                ])->columns(2)
                ->statePath('data')
        };
    }

    public function create()
    {
        $this->offer->update(array_merge($this->form->getState(), ['signed' => 1]));
        return redirect("sign-contract/{$this->offer->token}");
    }
    public function trigger(): void
    {
        $this->dispatch('trigger');
    }


    public function render()
    {
        return view('livewire.offer.view-offer',['provider', $this->provider]);
    }
}
