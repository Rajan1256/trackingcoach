<?php

namespace App\Exports\Sheets\BigData;

use App\Enum\Roles;

class CustomersSheet extends AbstractBigDataSheet
{
    public function query()
    {
        parent::query();
        return current_team()
            ->users()
            ->whereIn('users.id', $this->customerIds)
            ->where(function ($builder) {
                $builder->whereJsonContains('team_user.roles', Roles::CUSTOMER)
                    ->orWhereJsonContains('team_user.roles', 'client_archived');
            })
            ->isCustomer()
            ->withTrashed();
    }

    public function title(): string
    {
        return __('Customers');
    }
}
