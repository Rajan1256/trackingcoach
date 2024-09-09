<?php

namespace App\Models;

use App\Modules\Team\Contracts\NotTeamAware;
use App\Traits\DomainLogic;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laravel\Cashier\Billable;
use OzdemirBurak\Iris\Color\Hex;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Team extends Model implements NotTeamAware, HasMedia
{
    use Billable;
    use HasFactory;
    use DomainLogic;
    use SoftDeletes;
    use InteractsWithMedia;

    protected $casts = [
        'settings'      => AsArrayObject::class,
        'trial_ends_at' => 'datetime',
    ];

    protected $guarded = ['id'];

    protected $noPlanOptions = [
        'daily_tracker_app'    => true,
        'survey_and_interview' => true,
        'consent_procedure'    => true,
        'storage_notes'        => true,
        'storage_files'        => true,
        'data_export'          => true,
        'branding'             => true,
    ];

    public function colorIsLight()
    {
        return \App\Modules\Branding\Hex::isLight($this->getColor());
    }

    public function getColor()
    {
        if ($this->hasPlanOption('branding')) {
            return $this->settings['color'] ?? config('trackingcoach.color');
        }

        return config('trackingcoach.color');
    }

    public function hasPlanOption($option): bool
    {
        if ($this->isRoot()) {
            return true;
        }

        if ($this->stripePlan()[$option] ?? false) {
            return true;
        }

        if ($this->noPlanOptions[$option] ?? false) {
            return true;
        }

        return false;
    }

    public function isRoot()
    {
        return $this->root_access;
    }

    public function stripePlan()
    {
        foreach (config('cashier.plans') as $plan) {
            if ($this->subscribedToPrice($plan['monthly_id'])) {
                return $plan['options'];
            }
        }
        return [];
    }

    public function stripePlanName()
    {
        foreach (config('cashier.plans') as $plan) {
            if ($this->subscribedToPrice($plan['monthly_id'])) {
                return $plan['name'];
            }
        }
        return '-';
    }

    public function exports(): HasMany
    {
        return $this->hasMany(Export::class);
    }

    public function getColors()
    {
        $color = $this->getColor();
        return [
            $color,
            (string) (new Hex($color))->lighten(10)->toHex(),
        ];
    }

    public function getLogoAttribute()
    {
        if ($this->hasPlanOption('branding')) {
            $logo = $this->getFirstMediaUrl('logos');
            return !empty($logo) ? $logo : config('trackingcoach.logo');
        }

        return config('trackingcoach.logo');
    }

    public function getRgba($var)
    {
        $color = $this->getColor();

        $rgbA = (new Hex($color))->toRgba();
        $asString = (string) $rgbA;
        return str_replace('1)', "var({$var}))", $asString);
    }

    /**
     * Determine if the given user belongs to the team.
     *
     * @param  User  $user
     * @return bool
     */
    public function hasUser($user)
    {
        return $this->users->contains($user) || $user->ownsTeam($this);
    }

    /**
     * Determine if the given email address belongs to a user on the team.
     *
     * @param  string  $email
     * @return bool
     */
    public function hasUserWithEmail(string $email)
    {
        return $this->allUsers()->contains(function ($user) use ($email) {
            return $user->email === $email;
        });
    }

    /**
     * Get all of the team's users including its owner.
     *
     * @return Collection
     */
    public function allUsers()
    {
        return $this->users->merge([$this->owner]);
    }

    public function maxCustomers()
    {
        if ($this->isRoot()) {
            return 9999999; // Almost 10 million
        }

        if ($this->unlimited_members) {
            return 9999999; // Almost 10 million
        }

        return $this->stripePlan()['max_customers'] ?? 0;
    }

    /**
     * Purge all of the team's resources.
     *
     * @return void
     */
    public function purge()
    {
        $this->owner()->where('current_team_id', $this->id)
            ->update(['current_team_id' => null]);

        $this->users()->where('current_team_id', $this->id)
            ->update(['current_team_id' => null]);

        $this->users()->detach();

        $this->delete();
    }

    /**
     * Get the owner of the team.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all of the users that belong to the team.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot(['roles', 'company_name', 'paired_app_token', 'days_per_week', 'auto_invite_time', 'data'])
            ->withTimestamps()
            ->using(Membership::class)
            ->as('membership');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logos')
            ->singleFile();
    }

    /**
     * Remove the given user from the team.
     *
     * @param  User  $user
     * @return void
     */
    public function removeUser($user)
    {
        if ($user->current_team_id === $this->id) {
            $user->forceFill([
                'current_team_id' => null,
            ])->save();
        }

        $this->users()->detach($user);
    }

    /**
     * Get the address that should be synced to Stripe.
     *
     * @return array|null
     */
    public function stripeAddress()
    {
        return [
            'city'        => $this->billing_city,
            'country'     => $this->billing_country,
            'line1'       => $this->billing_address,
            'line2'       => $this->billing_address_line_2,
            'postal_code' => $this->billing_postal_code,
            'state'       => $this->billing_state,
        ];
    }

    public function stripeEmail()
    {
        return $this->owner->email;
    }

    /**
     * Determine if the given user has the given permission on the team.
     *
     * @param  User  $user
     * @param  string  $permission
     * @return bool
     */
    public function userHasPermission($user, $permission)
    {
        return $user->hasTeamPermission($this, $permission);
    }
}
