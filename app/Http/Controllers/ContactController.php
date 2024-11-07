<?php

namespace App\Http\Controllers;

use App\Http\Helpers\RequestHelper;
use App\Http\Requests\StoreContactRequest;
use App\Http\Resources\ContactCollection;
use App\QueryBuilders\ContactQueryBuilder;
use App\Services\ContactService;
use Exception;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @group [User] Contact
 */
class ContactController extends Controller
{
    public function __construct(
        private ContactQueryBuilder $contactQueryBuilder,
        private ContactService $contactService,
    ) {

    }

    /**
     * List
     *
     * @queryParam filter[username] string Example: paul
     * @queryParam filter[phone_number] string Example: 08
     * @queryParam filter[name] string Example: Paul
     * @queryParam sort string Example: created_at
     */
    public function index(Request $request): ContactCollection
    {
        $data = $this->contactQueryBuilder->getQueryBuilder();

        return (new ContactCollection($data->paginate(RequestHelper::limit($request))))
            ->additional($this->contactQueryBuilder->getResource($request));
    }

    /**
     * Add
     *
     * @bodyParam username string required Example: paul
     * @bodyparam phone_number string required Example: 081231231
     * @throws Exception
     */
    public function store(StoreContactRequest $request): JsonResponse
    {
        $input = $request->validated();

        DB::beginTransaction();
        try {
            $this->contactService->storeContact($input, auth()->user());
            DB::commit();
        } catch (UniqueConstraintViolationException $exception) {
            DB::rollBack();
            throw new BadRequestHttpException(__('error.duplicate_contact'));
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return response()->json([
            'message' => __('success.store_contact_success'),
        ]);
    }
}
