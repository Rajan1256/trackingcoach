<?php

/*
 * This file is part of the hyn/multi-tenant package.
 *
 * (c) Daniël Klabbers <daniel@klabbers.email>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://laravel-tenancy.com
 * @see https://github.com/hyn/multi-tenant
 */

namespace App\Modules\Tenancy\Generators;

use Hyn\Tenancy\Contracts\Database\PasswordGenerator as PasswordGeneratorContract;
use Hyn\Tenancy\Contracts\Website;
use Illuminate\Contracts\Foundation\Application;

class PasswordGenerator implements PasswordGeneratorContract
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param Website $website
     * @return string
     */
    public function generate(Website $website) : string
    {
        return md5(sprintf(
            '%s.%s',
            $this->app['config']->get('app.key'),
            $website->uuid
        ));
    }
}
