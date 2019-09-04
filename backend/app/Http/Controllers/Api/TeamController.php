<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Response\ApiResponse;
use App\Http\Requests\Team\InviteTeamMemberHttpRequest;
use App\Http\Requests\Team\RemoveTeamMemberHttpRequest;
use App\Actions\Teams\InviteTeamMemberRequest;
use App\Actions\Teams\RemoveTeamMemberRequest;
use App\Actions\Teams\InviteTeamMemberAction;
use App\Actions\Teams\RemoveTeamMemberAction;

final class TeamController extends Controller
{
    private $inviteTeamMemberAction;
    private $removeTeamMemberAction;

    public function __construct(
        InviteTeamMemberAction $inviteTeamMemberAction,
        RemoveTeamMemberAction $removeTeamMemberAction
    ) {
        $this->inviteTeamMemberAction = $inviteTeamMemberAction;
        $this->removeTeamMemberAction = $removeTeamMemberAction;
    }

    public function inviteTeamMember(InviteTeamMemberHttpRequest $request): ApiResponse
    {
        $this->inviteTeamMemberAction->execute(
            InviteTeamMemberRequest::fromRequest($request)
        );
        return ApiResponse::emptySuccess()->setStatusCode(201);
    }

    public function removeTeamMember(int $id, RemoveTeamMemberHttpRequest $request): ApiResponse
    {
        $this->removeTeamMemberAction->execute(
            RemoveTeamMemberRequest::fromRequest($request, $id)
        );
        return ApiResponse::emptySuccess()->setStatusCode(204);
    }
}
