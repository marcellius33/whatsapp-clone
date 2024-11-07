<?php

namespace App\Http\Controllers;

use App\Http\Helpers\RequestHelper;
use App\Http\Requests\StoreChatRoomRequest;
use App\Http\Requests\UpdateChatRoomRequest;
use App\Http\Resources\ChatRoomCollection;
use App\Http\Resources\ChatRoomResource;
use App\Models\ChatRoom;
use App\QueryBuilders\ChatRoomQueryBuilder;
use App\Services\ChatRoomService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group [User] Chat Room
 */
class ChatRoomController extends Controller
{
    public function __construct(
        private ChatRoomQueryBuilder $chatRoomQueryBuilder,
        private ChatRoomService $chatRoomService,
    ) {

    }

    /**
     * List
     *
     * @queryParam filter[name] string Example: Paul
     * @queryParam sort string Example: created_at
     */
    public function index(Request $request): ChatRoomCollection
    {
        $data = $this->chatRoomQueryBuilder->getQueryBuilder();

        return (new ChatRoomCollection($data->paginate(RequestHelper::limit($request))))
            ->additional($this->chatRoomQueryBuilder->getResource($request));
    }

    /**
     * Detail
     *
     * @urlParam id string required Example: xxxxxxxx-xxxx-xxxx-xxxxxxxxxxxx
     */
    public function show(ChatRoom $chatRoom): ChatRoomResource
    {
        return new ChatRoomResource($chatRoom);
    }

    /**
     * Create
     *
     * @bodyParam name string required Example: paul
     * @bodyParam max_members integer required Example: 10
     * @throws Exception
     */
    public function store(StoreChatRoomRequest $request): JsonResponse
    {
        $input = $request->validated();

        DB::beginTransaction();
        try {
            $this->chatRoomService->storeChatRoom($input, auth()->user());
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return response()->json([
            'message' => __('success.store_chat_room_success'),
        ]);
    }

    /**
     * Update
     *
     * @urlParam id string required Example: xxxxxxxx-xxxx-xxxx-xxxxxxxxxxxx
     * @bodyParam name string required Example: paul
     * @bodyParam max_members integer required Example: 10
     * @throws Exception
     */
    public function update(ChatRoom $chatRoom, UpdateChatRoomRequest $request): JsonResponse
    {
        $input = $request->validated();

        DB::beginTransaction();
        try {
            $this->chatRoomService->updateChatRoom($chatRoom, $input, auth()->user());
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return response()->json([
            'message' => __('success.update_chat_room_success'),
        ]);
    }

    /**
     * Delete
     *
     * @urlParam id string required Example: xxxxxxxx-xxxx-xxxx-xxxxxxxxxxxx
     * @throws Exception
     */
    public function destroy(ChatRoom $chatRoom): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->chatRoomService->deleteChatRoom($chatRoom, auth()->user());
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return response()->json([
            'message' => __('success.delete_chat_room_success'),
        ]);
    }

    /**
     * Join / Leave
     *
     * @urlParam id string required Example: xxxxxxxx-xxxx-xxxx-xxxxxxxxxxxx
     * @urlParam action string required Example: leave / join
     * @throws Exception
     */
    public function action(ChatRoom $chatRoom, string $action): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->chatRoomService->actionChatRoom($chatRoom, $action, auth()->user());
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        return response()->json([
            'message' => __('success.action_chat_room_success', [
                'action' => ucfirst($action),
            ]),
        ]);
    }
}
