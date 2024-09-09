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

use Hyn\Tenancy\Contracts\Website;
use Hyn\Tenancy\Contracts\Website\UuidGenerator as Contract;
use Ramsey\Uuid\Uuid;

class UuidGenerator implements Contract
{
    /**
     * @param Website $website
     * @return string
     */
    public function generate(Website $website) : string
    {
        $uuid = Uuid::uuid4()->toString();

        return substr(str_replace('-', null, $uuid), 0, 12);
    }
}
