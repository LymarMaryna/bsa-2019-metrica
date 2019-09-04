<?php
declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Entities\Website;
use App\Entities\User;

interface WebsiteRepository
{
    public function save(Website $website): Website;
    public function getById(int $id): Website;
    public function getByTrackNumber(int $id): ?Website;
    public function getCurrentWebsite(int $id): Website;
    public function getFirstExistingUserWebsite(): Website;
    public function setWebsiteOwner(User $user, int $websiteId): void;
    public function addTeamMemberToWebsite(User $user, int $websiteId): void;
    public function removeMemberFromWebsiteTeam(User $user, int $websiteId): void;
}
