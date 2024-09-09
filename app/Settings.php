<?php

namespace App;

use App\Models\Membership;
use App\Models\Team;
use App\Models\User;
use Crypt;
use Exception;
use Illuminate\Support\Collection;
use Throwable;

use function is_null;
use function json_decode;
use function json_encode;
use function throw_if;

/**
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $locale
 * @property string $timezone
 * @property string $date_format
 * @property array $roles
 * @property string $company_name
 * @property string $paired_app_token
 * @property string $phone
 * @property array $preferred_notification_methods
 * @property bool $skip_consent_checks
 * @property string $agreed_to_privacy_statement_at
 * @property string $agreed_to_personal_data_consent_at
 * @property string $agreed_to_process_general_data_anonymously_at
 * @property string $will_attend_physical_tests
 * @property string $agreed_to_physiological_data_consent_at
 * @property string $paired_app_on
 * @property string $auto_invite_time
 * @property string $filled_auto_invite_time
 * @property bool $use_own_timezone
 * @property integer $timezone_offset_days
 * @property integer $days_per_week
 *
 * @extends Collection
 */
class Settings
{
    public $defaults = [
        'roles'                          => [],
        'preferred_notification_methods' => [
            'daily_invites'   => 'app',
            'monthly_reports' => 'app',
            'weekly_reports'  => 'app',
        ],
    ];

    public User $user;

    public Team $team;

    private Collection $settings;

    private array $toUser = [
        'first_name',
        'last_name',
        'email',
        'locale',
        'timezone',
        'date_format',
    ];

    private array $toMembership = [
        'roles',
        'company_name',
        'paired_app_token',
        'days_per_week',
        'auto_invite_time',
    ];

    /**
     * @throws Throwable
     */
    public static function find(User $user, Team $team): Settings
    {
        $user = $team->users()->find($user->id);

        throw_if(is_null($user), Exception::class, 'User can not be found for team');

        $data = $user->membership->data->toArray();

        $settings = array_merge([
            'first_name'       => $user->first_name,
            'last_name'        => $user->last_name,
            'email'            => $user->email,
            'locale'           => $user->locale,
            'timezone'         => $user->timezone,
            'date_format'      => $user->date_format,
            'company_name'     => $user->membership->company_name,
            'paired_app_token' => $user->membership->paired_app_token,
            'roles'            => json_decode($user->membership->roles, true),
            'days_per_week'    => $user->membership->days_per_week,
            'auto_invite_time' => $user->membership->auto_invite_time,
        ], $data);

        return (new Settings)
            ->setUser($user)
            ->setTeam($team)
            ->set($settings);
    }

    /**
     * @param $settings
     * @return $this
     */
    private function set($settings): Settings
    {
        $this->settings = collect($settings);
        return $this;
    }

    private function setTeam(Team $team): Settings
    {
        $this->team = $team;
        return $this;
    }

    private function setUser(User $user): Settings
    {
        $this->user = $user;
        return $this;
    }

    public function __get($name)
    {
        if ($this->settings->has($name)) {
            return $this->settings[$name];
        }

        if (array_key_exists($name, $this->defaults)) {
            return $this->defaults[$name];
        }

        return null;
    }

    public function __set($name, $value)
    {
        $this->settings[$name] = $value;
    }

    public function __call(string $name, array $arguments)
    {
        return $this->settings->{$name}(...$arguments);
    }

    /**
     * @return bool
     */
    public function save($saveQuietly = false): bool
    {
        $this->settings->roles = json_encode($this->roles);

        $string = Crypt::encryptString(json_encode($this->settings->except(array_merge(
            $this->toUser,
            $this->toMembership
        ))->toArray()));

        Membership::where('user_id', $this->user->id)
            ->where('team_id', $this->team->id)
            ->update(array_merge(
                $this->settings->only($this->toMembership)->toArray(),
                [
                    'data' => $string,
                ],
            ));

        if ($saveQuietly) {
            return $this->user->updateQuietly($this->settings->only($this->toUser)->toArray());
        } else {
            return $this->user->update($this->settings->only($this->toUser)->toArray());
        }
    }
}
