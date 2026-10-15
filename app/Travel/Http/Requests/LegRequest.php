<?php

namespace App\Travel\Http\Requests;

use App\Travel\Enums\LegType;
use App\Travel\Models\Leg;
use App\Travel\Models\Station;
use App\Travel\Models\TransitLine;
use App\Travel\Validation\LegInvariants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Stores a leg on the journey or accommodation in the route, or updates the leg in the route. The transit lines
 * always come along and replace the current ones. The position is set on create and changed by reordering.
 */
class LegRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(LegType::class)],
            'from_station_id' => ['nullable', 'integer', Rule::exists(Station::class, 'id')],
            'to_station_id' => ['nullable', 'integer', Rule::exists(Station::class, 'id')],
            'departure_at' => ['nullable', 'date'],
            'arrival_at' => ['nullable', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:0'],
            'number' => ['nullable', 'string', 'max:255'],
            'carriage' => ['nullable', 'string', 'max:255'],
            'seat' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
            'transit_lines' => ['array'],
            'transit_lines.*.id' => ['required', 'integer', 'distinct', Rule::exists(TransitLine::class, 'id')],
            'transit_lines.*.direction' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'type' => 'soort etappe',
            'from_station_id' => 'vertrekstation',
            'to_station_id' => 'aankomststation',
            'departure_at' => 'vertrektijd',
            'arrival_at' => 'aankomsttijd',
            'duration_minutes' => 'duur',
            'number' => 'nummer',
            'carriage' => 'rijtuig',
            'seat' => 'stoel',
            'note' => 'opmerking',
            'transit_lines' => 'lijnen',
            'transit_lines.*.id' => 'lijn',
            'transit_lines.*.direction' => 'richting',
        ];
    }

    /**
     * Check the leg as it would be saved, and its transit lines, against the invariants, once the fields are valid.
     *
     * @return list<callable(Validator): void>
     */
    public function after(): array
    {
        return [function (Validator $validator) {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $leg = $this->legAsSaved();
            $lines = TransitLine::findMany(array_column($this->transitLines(), 0));

            foreach ([...LegInvariants::check($leg), ...LegInvariants::checkTransitLines($leg->type, $lines)] as $invariant) {
                $validator->errors()->add($invariant->field(), $invariant->message());
            }
        }];
    }

    /**
     * The leg's own fields, without its transit lines.
     *
     * @return array<string, mixed>
     */
    public function legAttributes(): array
    {
        return Arr::except($this->validated(), 'transit_lines');
    }

    /**
     * @return list<array{int, string|null}> [transit_line_id, direction] pairs, in the order given.
     */
    public function transitLines(): array
    {
        return array_map(
            fn (array $line) => [(int) $line['id'], $line['direction'] ?? null],
            array_values($this->validated('transit_lines', [])),
        );
    }

    private function legAsSaved(): Leg
    {
        $leg = $this->route('leg');

        if ($leg instanceof Leg) {
            return (clone $leg)->fill($this->legAttributes());
        }

        return (new Leg($this->legAttributes()))
            ->legable()->associate($this->route('journey') ?? $this->route('accommodation'));
    }
}
