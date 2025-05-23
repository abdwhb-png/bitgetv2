<?php

namespace App\Rules;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;


class ExistsInColumnsRule implements Rule
{
    protected $table;
    protected $columns;

    /**
     * Create a new rule instance.
     */
    public function __construct($table, array $columns)
    {
        $this->table = $table;
        $this->columns = $columns;
    }

    /**
     * Determine if the validation rule passes.
     */
    public function passes($attribute, $value)
    {
        foreach ($this->columns as $column) {
            if (DB::table($this->table)->where($column, $value)->exists()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the validation error message.
     */
    public function message()
    {
        return 'The selected :attribute is invalid.';
    }
}
