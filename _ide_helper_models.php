<?php

// @formatter:off
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models\Base{
/**
 * Class Calendar
 *
 * @property int $id
 * @property Carbon $date_start
 * @property int $hours
 * @property int|null $project_id
 * @property string|null $title
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $date_end
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar query()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereUpdatedAt($value)
 */
	class Calendar extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class Changelog
 *
 * @property int $id
 * @property int $version_id
 * @property string $type
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereVersionId($value)
 */
	class Changelog extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class ChangelogVersion
 *
 * @property int $id
 * @property string $version
 * @property string|null $description
 * @property int $published
 * @property Carbon $published_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion whereVersion($value)
 */
	class ChangelogVersion extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class City
 *
 * @property int $id
 * @property string $name
 * @property string $country_code
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 */
	class City extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class Composer
 *
 * @property int $id
 * @property string $title
 * @property string|null $title_alternatives
 * @property string $compose_filename
 * @property string $orig_url
 * @property string $orig_compose
 * @property Carbon $orig_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Composer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Composer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Composer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereComposeFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereOrigCompose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereOrigDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereOrigUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereTitleAlternatives($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereUpdatedAt($value)
 */
	class Composer extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class ComposerContainerRel
 *
 * @property int $id
 * @property int $composer_id
 * @property int $container_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel whereComposerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel whereContainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel whereUpdatedAt($value)
 */
	class ComposerContainerRel extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class Container
 *
 * @property int $id
 * @property string $title
 * @property string|null $content
 * @property string $content_orig
 * @property Carbon $content_orig_date
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Container newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Container newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Container query()
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereContentOrig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereContentOrigDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereUpdatedAt($value)
 */
	class Container extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class Credential
 *
 * @property int $id
 * @property int $customer_id
 * @property string $username
 * @property string $password
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $user_id
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Credential newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Credential newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Credential query()
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereUsername($value)
 */
	class Credential extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class Customer
 *
 * @property int $id
 * @property int $user_id
 * @property int $short_no
 * @property string $sap_no
 * @property string $dynamics_no
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $city_id
 * @property string|null $intermediate_cert_raw
 * @property string|null $root_cert_raw
 * @property string|null $private_key_raw
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDynamicsNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereIntermediateCertRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePrivateKeyRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereRootCertRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSapNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereShortNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUserId($value)
 */
	class Customer extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class CustomerContact
 *
 * @property int $id
 * @property int $customer_id
 * @property string|null $prefix
 * @property string|null $name
 * @property string $familyname
 * @property string|null $phone_mobile
 * @property string|null $phone_office
 * @property string|null $email
 * @property string|null $comments
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereFamilyname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact wherePhoneMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact wherePhoneOffice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereUpdatedAt($value)
 */
	class CustomerContact extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class Env
 *
 * @property int $id
 * @property int $server_id
 * @property string $key
 * @property string $value
 * @property int $needed
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Env newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Env newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Env query()
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereNeeded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereValue($value)
 */
	class Env extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class FailedJob
 *
 * @property int $id
 * @property string $uuid
 * @property string $connection
 * @property string $queue
 * @property string $payload
 * @property string $exception
 * @property Carbon $failed_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob query()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereConnection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereQueue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereUuid($value)
 */
	class FailedJob extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class Log
 *
 * @property int $id
 * @property int $user_id
 * @property string $section
 * @property string $type
 * @property string $msg
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $content_id
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log query()
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereContentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUserId($value)
 */
	class Log extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class PasswordReset
 *
 * @property string $email
 * @property string $token
 * @property Carbon|null $created_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereToken($value)
 */
	class PasswordReset extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class PersonalAccessToken
 *
 * @property int $id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property string $name
 * @property string $token
 * @property string|null $abilities
 * @property Carbon|null $last_used_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereAbilities($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereTokenableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereTokenableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereUpdatedAt($value)
 */
	class PersonalAccessToken extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class Project
 *
 * @property int $id
 * @property string $dynamics_id
 * @property string $name
 * @property int $customer_id
 * @property int $user_id
 * @property int $status_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDynamicsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUserId($value)
 */
	class Project extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class Remark
 *
 * @property int $id
 * @property int $type
 * @property int $relation_id
 * @property string|null $remark
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Remark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Remark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Remark query()
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereUpdatedAt($value)
 */
	class Remark extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class Server
 *
 * @property int $id
 * @property string|null $servername
 * @property string|null $fqdn
 * @property string|null $ext_ip
 * @property string|null $int_ip
 * @property string|null $db_sid
 * @property string|null $db_server
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $customer_id
 * @property int $user_id
 * @property string|null $docker_compose_raw
 * @property string|null $env_raw
 * @property string|null $server_cert_raw
 * @property string|null $type
 * @property string|null $private_key_raw
 * @property int $cert_server_ok
 * @property int $cert_intermediate_ok
 * @property int $cert_root_ok
 * @property int $cert_key_ok
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server query()
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCertIntermediateOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCertKeyOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCertRootOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCertServerOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereDbServer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereDbSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereDockerComposeRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereEnvRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereExtIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereFqdn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereIntIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePrivateKeyRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereServerCertRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereServername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereUserId($value)
 */
	class Server extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class ServersComposersRel
 *
 * @property int $id
 * @property int $composer_id
 * @property int $server_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel whereComposerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel whereUpdatedAt($value)
 */
	class ServersComposersRel extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class Status
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereUpdatedAt($value)
 */
	class Status extends \Eloquent {}
}

namespace App\Models\Base{
/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @package App\Models\Base
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Calendar
 *
 * @property int $id
 * @property \Illuminate\Support\Carbon $date_start
 * @property int $hours
 * @property int|null $project_id
 * @property string|null $title
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $date_end
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar query()
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Calendar whereUpdatedAt($value)
 */
	class Calendar extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Changelog
 *
 * @property int $id
 * @property int $version_id
 * @property string $type
 * @property string $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog query()
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Changelog whereVersionId($value)
 */
	class Changelog extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ChangelogVersion
 *
 * @property int $id
 * @property string $version
 * @property string|null $description
 * @property int $published
 * @property \Illuminate\Support\Carbon $published_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion wherePublished($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChangelogVersion whereVersion($value)
 */
	class ChangelogVersion extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\City
 *
 * @property int $id
 * @property string $name
 * @property string $country_code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|City query()
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCountryCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|City whereUpdatedAt($value)
 */
	class City extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Composer
 *
 * @property int $id
 * @property string $title
 * @property string|null $title_alternatives
 * @property string $compose_filename
 * @property string $orig_url
 * @property string $orig_compose
 * @property \Illuminate\Support\Carbon $orig_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ComposerContainerRel> $rel
 * @property-read int|null $rel_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ServersComposersRel> $server_rel
 * @property-read int|null $server_rel_count
 * @method static \Illuminate\Database\Eloquent\Builder|Composer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Composer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Composer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereComposeFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereOrigCompose($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereOrigDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereOrigUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereTitleAlternatives($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Composer whereUpdatedAt($value)
 */
	class Composer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ComposerContainerRel
 *
 * @property int $id
 * @property int $composer_id
 * @property int $container_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Composer|null $composer
 * @property-read \App\Models\Container|null $container
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel whereComposerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel whereContainerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ComposerContainerRel whereUpdatedAt($value)
 */
	class ComposerContainerRel extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Container
 *
 * @property int $id
 * @property string $title
 * @property string|null $content
 * @property string $content_orig
 * @property \Illuminate\Support\Carbon $content_orig_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ComposerContainerRel> $rel
 * @property-read int|null $rel_count
 * @method static \Illuminate\Database\Eloquent\Builder|Container newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Container newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Container query()
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereContentOrig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereContentOrigDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Container whereUpdatedAt($value)
 */
	class Container extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Credential
 *
 * @property int $id
 * @property int $customer_id
 * @property string $username
 * @property string $password
 * @property string $type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $user_id
 * @method static \Illuminate\Database\Eloquent\Builder|Credential newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Credential newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Credential query()
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Credential whereUsername($value)
 */
	class Credential extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Customer
 *
 * @property int $id
 * @property int $user_id
 * @property int $short_no
 * @property string $sap_no
 * @property string $dynamics_no
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 * @property int|null $city_id
 * @property string|null $intermediate_cert_raw
 * @property string|null $root_cert_raw
 * @property string|null $private_key_raw
 * @property-read \App\Models\City|null $city
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CustomerContact> $contacts
 * @property-read int|null $contacts_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Credential> $credentials
 * @property-read int|null $credentials_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Project> $projects
 * @property-read int|null $projects_count
 * @property-read \App\Models\Remark|null $remark
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Server> $servers
 * @property-read int|null $servers_count
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereDynamicsNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereIntermediateCertRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer wherePrivateKeyRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereRootCertRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereSapNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereShortNo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Customer whereUserId($value)
 */
	class Customer extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\CustomerContact
 *
 * @property int $id
 * @property int $customer_id
 * @property string|null $prefix
 * @property string|null $name
 * @property string $familyname
 * @property string|null $phone_mobile
 * @property string|null $phone_office
 * @property string|null $email
 * @property string|null $comments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact query()
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereComments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereFamilyname($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact wherePhoneMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact wherePhoneOffice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact wherePrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CustomerContact whereUpdatedAt($value)
 */
	class CustomerContact extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Env
 *
 * @property int $id
 * @property int $server_id
 * @property string $key
 * @property string $value
 * @property int $needed
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Env newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Env newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Env query()
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereNeeded($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Env whereValue($value)
 */
	class Env extends \Eloquent {}
}

namespace App\Models{
/**
 * Class FailedJob
 *
 * @property int $id
 * @property string $uuid
 * @property string $connection
 * @property string $queue
 * @property string $payload
 * @property string $exception
 * @property Carbon $failed_at
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob query()
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereConnection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereException($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereFailedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob wherePayload($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereQueue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FailedJob whereUuid($value)
 */
	class FailedJob extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Log
 *
 * @property int $id
 * @property int $user_id
 * @property string $section
 * @property string $type
 * @property string $msg
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int|null $content_id
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Log query()
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereContentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereMsg($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereSection($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Log whereUserId($value)
 */
	class Log extends \Eloquent {}
}

namespace App\Models{
/**
 * Class PasswordReset
 *
 * @property string $email
 * @property string $token
 * @property Carbon|null $created_at
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset query()
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PasswordReset whereToken($value)
 */
	class PasswordReset extends \Eloquent {}
}

namespace App\Models{
/**
 * Class PersonalAccessToken
 *
 * @property int $id
 * @property string $tokenable_type
 * @property int $tokenable_id
 * @property string $name
 * @property string $token
 * @property string|null $abilities
 * @property Carbon|null $last_used_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken query()
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereAbilities($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereLastUsedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereTokenableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereTokenableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PersonalAccessToken whereUpdatedAt($value)
 */
	class PersonalAccessToken extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Project
 *
 * @property int $id
 * @property string $dynamics_id
 * @property string $name
 * @property int $customer_id
 * @property int $user_id
 * @property int $status_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 * @property string|null $start_date
 * @property string|null $end_date
 * @property-read \App\Models\Customer|null $customer
 * @property-read \App\Models\Status|null $status
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDynamicsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereStatusId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUserId($value)
 */
	class Project extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Remark
 *
 * @property int $id
 * @property int $type
 * @property int $relation_id
 * @property string|null $remark
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Remark newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Remark newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Remark query()
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereRelationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Remark whereUpdatedAt($value)
 */
	class Remark extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\Server
 *
 * @property int $id
 * @property string|null $servername
 * @property string|null $fqdn
 * @property string|null $ext_ip
 * @property string|null $int_ip
 * @property string|null $db_sid
 * @property string|null $db_server
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $customer_id
 * @property int $user_id
 * @property string|null $docker_compose_raw
 * @property string|null $env_raw
 * @property string|null $server_cert_raw
 * @property string|null $type
 * @property string|null $private_key_raw
 * @property int $cert_server_ok
 * @property int $cert_intermediate_ok
 * @property int $cert_root_ok
 * @property int $cert_key_ok
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ServersComposersRel> $composer_rel
 * @property-read int|null $composer_rel_count
 * @property-read \App\Models\Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Server query()
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCertIntermediateOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCertKeyOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCertRootOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCertServerOk($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereDbServer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereDbSid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereDockerComposeRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereEnvRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereExtIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereFqdn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereIntIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server wherePrivateKeyRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereServerCertRaw($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereServername($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Server whereUserId($value)
 */
	class Server extends \Eloquent {}
}

namespace App\Models{
/**
 * App\Models\ServersComposersRel
 *
 * @property int $id
 * @property int $composer_id
 * @property int $server_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Composer|null $composer
 * @property-read \App\Models\Server|null $server
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel query()
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel whereComposerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ServersComposersRel whereUpdatedAt($value)
 */
	class ServersComposersRel extends \Eloquent {}
}

namespace App\Models{
/**
 * Class Status
 *
 * @property int $id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|Status newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Status query()
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Status whereUpdatedAt($value)
 */
	class Status extends \Eloquent {}
}

namespace App\Models{
/**
 * Class User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @package App\Models
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

