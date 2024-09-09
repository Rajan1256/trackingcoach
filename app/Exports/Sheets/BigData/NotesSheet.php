<?php

namespace App\Exports\Sheets\BigData;

use App\Models\Note;

class NotesSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        $query = Note::whereYear('created_at', $this->year);

        if ($this->customer) {
            $query = $query->where('user_id', $this->customer->id);
        }

        return $query;
    }

    public function title(): string
    {
        return __('Notes');
    }
}
