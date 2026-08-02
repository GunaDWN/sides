<?php

namespace App\Livewire\Components;

use Livewire\Attributes\Modelable;
use Livewire\Component;

class Select extends Component
{
    #[Modelable]
    public $value = '';

    public $label = null;
    public $placeholder = '-- Pilih --';
    public $options = [];
    public $optionValue = 'id';
    public $optionLabel = 'nama';
    public $searchable = false;
    public $search = '';
    public $required = false;

    public function selectOption($val)
    {
        $this->value = $val;
        $this->dispatch('updated-select', value: $val);
    }

    public function clearSelection()
    {
        $this->value = '';
        $this->search = '';
    }

    public function render()
    {
        $formattedOptions = [];

        if (is_iterable($this->options)) {
            foreach ($this->options as $key => $opt) {
                $val = is_object($opt) ? ($opt->{$this->optionValue} ?? $key) : (is_array($opt) ? ($opt[$this->optionValue] ?? $key) : $key);
                $lbl = is_object($opt) ? ($opt->{$this->optionLabel} ?? $opt) : (is_array($opt) ? ($opt[$this->optionLabel] ?? $opt) : $opt);

                if ($this->searchable && !empty(trim($this->search))) {
                    if (stripos($lbl, trim($this->search)) === false) {
                        continue;
                    }
                }

                $formattedOptions[] = [
                    'value' => $val,
                    'label' => $lbl,
                ];
            }
        }

        // Determine label for current selected value
        $selectedLabel = null;
        if ($this->value !== null && $this->value !== '') {
            foreach ($formattedOptions as $item) {
                if ($item['value'] == $this->value) {
                    $selectedLabel = $item['label'];
                    break;
                }
            }
        }

        return view('livewire.components.select', [
            'formattedOptions' => $formattedOptions,
            'selectedLabel' => $selectedLabel,
        ]);
    }
}
