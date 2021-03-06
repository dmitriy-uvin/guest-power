<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Action\ByIdRequest;
use App\Action\ByIdsRequest;
use App\Action\Platform\AddPlatformAction;
use App\Action\Platform\AddPlatformRequest;
use App\Action\Platform\DeletePlatformsByIdsAction;
use App\Action\Platform\GetPlatformByIdAction;
use App\Action\Platform\GetPlatformCollectionAction;
use App\Action\Platform\GetPlatformCollectionRequest;
use App\Action\Platform\GetPlatformsInTrashCollectionAction;
use App\Action\Platform\GetPlatformsInTrashCollectionRequest;
use App\Action\Platform\GetPlatformsNotInTrashAction;
use App\Action\Platform\GetPlatformsNotInTrashRequest;
use App\Action\Platform\MoveFromTrashByIdsAction;
use App\Action\Platform\MoveInTrashByIdsAction;
use App\Action\Platform\UpdateApiDataAllAction;
use App\Action\Platform\UpdateApiDataByIdsAction;
use App\Action\Platform\UpdatePlatformByIdAction;
use App\Action\Platform\UpdatePlatformByIdRequest;
use App\Http\Presenters\Platform\PlatformPresenter;
use App\Http\Requests\PaginatedHttpRequest;
use App\Http\Requests\Platform\AddPlatformHttpRequest;
use App\Http\Requests\Platform\IdsHttpRequest;
use App\Http\Requests\Platform\UpdatePlatformHttpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PlatformController extends ApiController
{
    private PlatformPresenter $platformPresenter;
    private AddPlatformAction $addPlatformAction;
    private GetPlatformCollectionAction $getPlatformCollectionAction;
    private MoveInTrashByIdsAction $moveInTrashByIdsAction;
    private MoveFromTrashByIdsAction $moveFromTrashByIdsAction;
    private DeletePlatformsByIdsAction $deletePlatformsByIdsAction;
    private GetPlatformsInTrashCollectionAction $getPlatformsInTrashAction;
    private UpdatePlatformByIdAction $updatePlatformByIdAction;
    private GetPlatformByIdAction $getPlatformByIdAction;
    private UpdateApiDataByIdsAction $updateApiDataByIdsAction;
    private UpdateApiDataAllAction $updateApiDataAction;
    private GetPlatformsNotInTrashAction $getPlatformsNotInTrashAction;

    public function __construct(
        AddPlatformAction $addPlatformAction,
        PlatformPresenter $platformPresenter,
        GetPlatformCollectionAction $getPlatformCollectionAction,
        MoveInTrashByIdsAction $moveInTrashByIdsAction,
        MoveFromTrashByIdsAction $moveFromTrashByIdsAction,
        DeletePlatformsByIdsAction $deletePlatformsByIdsAction,
        GetPlatformsInTrashCollectionAction $getPlatformsInTrashAction,
        UpdatePlatformByIdAction $updatePlatformByIdAction,
        GetPlatformByIdAction $getPlatformByIdAction,
        UpdateApiDataByIdsAction $updateApiDataByIdsAction,
        UpdateApiDataAllAction $updateApiDataAction,
        GetPlatformsNotInTrashAction $getPlatformsNotInTrashAction
    ) {
        $this->platformPresenter = $platformPresenter;
        $this->addPlatformAction = $addPlatformAction;
        $this->getPlatformCollectionAction = $getPlatformCollectionAction;
        $this->moveInTrashByIdsAction = $moveInTrashByIdsAction;
        $this->moveFromTrashByIdsAction = $moveFromTrashByIdsAction;
        $this->deletePlatformsByIdsAction = $deletePlatformsByIdsAction;
        $this->getPlatformsInTrashAction = $getPlatformsInTrashAction;
        $this->updatePlatformByIdAction = $updatePlatformByIdAction;
        $this->getPlatformByIdAction = $getPlatformByIdAction;
        $this->updateApiDataByIdsAction = $updateApiDataByIdsAction;
        $this->updateApiDataAction = $updateApiDataAction;
        $this->getPlatformsNotInTrashAction = $getPlatformsNotInTrashAction;
    }

    public function getPlatformsNotInTrash(PaginatedHttpRequest $request)
    {
        $response = $this->getPlatformsNotInTrashAction->execute(
            new GetPlatformsNotInTrashRequest(
                (int)$request->page,
                (int)$request->perPage,
                $request->sorting,
                $request->direction,
            )
        );

        return $this->createPaginatedResponse(
            $response->getPaginator(),
            $this->platformPresenter
        );
    }

    public function savePlatform(AddPlatformHttpRequest $request): JsonResponse
    {
        $response = $this->addPlatformAction->execute(
            new AddPlatformRequest(
                $request->website_url,
                $this->checkIfValueIsKnown($request->organic_traffic),
                (bool)$request->dofollow_active,
                (bool)$request->free_home_featured_active,
                (bool)$request->niche_edit_link_active,
                (float)$request->article_writing_price,
                (float)$request->niche_edit_link_price,
                $request->contacts,
                (float)$request->price,
                $request->email,
                $request->comment,
                $request->topics,
                $request->moz,
                $request->alexa,
                $request->semrush,
                $request->majestic,
                $request->ahrefs,
                $request->description,
                $request->article_requirements,
                (int)$request->deadline,
                $request->where_posted,
                $request->fb,
                $this->checkIfValueIsKnown($request->trust),
                $this->checkIfValueIsKnown($request->spam),
                $this->checkIfValueIsKnown($request->lrt_power_trust),
                (bool)$request->money_anchor
            )
        );

        return $this->successResponse($this->platformPresenter->present($response->getPlatform()));
    }

    public function updatePlatformById(UpdatePlatformHttpRequest $request, string $id)
    {
        $response = $this->updatePlatformByIdAction->execute(
            new UpdatePlatformByIdRequest(
                (int)$id,
                $request->website_url,
                $this->checkIfValueIsKnown($request->organic_traffic),
                (bool)$request->dofollow_active,
                (bool)$request->free_home_featured_active,
                (bool)$request->niche_edit_link_active,
                (float)$request->article_writing_price,
                (float)$request->niche_edit_link_price,
                $request->contacts,
                (float)$request->price,
                $request->email,
                $request->comment,
                $request->topics,
                $request->moz,
                $request->alexa,
                $request->semrush,
                $request->majestic,
                $request->ahrefs,
                $request->description,
                $request->article_requirements,
                (int)$request->deadline,
                $request->where_posted,
                $request->facebook,
                $this->checkIfValueIsKnown($request->trust),
                $this->checkIfValueIsKnown($request->spam),
                $this->checkIfValueIsKnown($request->power_trust),
                (bool)$request->money_anchor
            )
        );

        return $this->successResponse($this->platformPresenter->present($response->getPlatform()));
    }

    public function getPlatformById(string $id): JsonResponse
    {
        $response = $this->getPlatformByIdAction->execute(
            new ByIdRequest((int)$id)
        );

        return $this->successResponse($this->platformPresenter->present($response->getPlatform()));
    }

    public function getPlatformCollection(PaginatedHttpRequest $request)
    {
        $response = $this->getPlatformCollectionAction->execute(
            new GetPlatformCollectionRequest(
                (int)$request->query('page'),
                (int)$request->query('perPage'),
                $request->query('sorting'),
                $request->query('direction'),
                json_decode($request->query('filter'), true)
            )
        );

        return $this->createPaginatedResponse(
            $response->getPaginator(),
            $this->platformPresenter
        );
    }

    public function moveInTrashByIds(IdsHttpRequest $request): JsonResponse
    {
        $this->moveInTrashByIdsAction->execute(
            new ByIdsRequest($request->ids)
        );

        return $this->emptyResponse();
    }

    public function moveFromTrashByIds(IdsHttpRequest $request): JsonResponse
    {
        $this->moveFromTrashByIdsAction->execute(
            new ByIdsRequest($request->ids)
        );

        return $this->emptyResponse();
    }

    public function deletePlatformsByIds(Request $request)
    {
        $this->deletePlatformsByIdsAction->execute(
            new ByIdsRequest($request->ids)
        );
        return $this->emptyResponse();
    }

    public function getPlatformsInTrashCollection(PaginatedHttpRequest $request): JsonResponse
    {
        $response = $this->getPlatformsInTrashAction->execute(
            new GetPlatformsInTrashCollectionRequest(
                (int)$request->page,
                (int)$request->perPage,
                $request->sorting,
                $request->direction
            )
        );

        return $this->createPaginatedResponse($response->getPaginator(), $this->platformPresenter);
    }

    public function updateApiDataByIds(IdsHttpRequest $request)
    {
        $this->updateApiDataByIdsAction->execute(
            new ByIdsRequest($request->ids)
        );

        return $this->emptyResponse();
    }

    public function updateApiDataAll()
    {
        $this->updateApiDataAction->execute();

        return $this->emptyResponse();
    }

    private function checkIfValueIsKnown($value)
    {
        return $value === 'N/A' ? null : (string)$value;
    }
}
