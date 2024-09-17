<?php

namespace App\Http\Controllers\Api\V1;


use App\Services\UserService;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUSerRequest;
use App\Http\Requests\UpdateUSerRequest;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * @var UserService
     * The service instance to handle user-related logic.
     */
    protected UserService $userService;

    /**
     * UserController constructor.
     *
     * @param UserService $userService
     * The service that handles user operations.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Display a listing of users.
     *
     * This method retrieves a paginated list of users.
     *
     * @return \Illuminate\Http\JsonResponse JSON response containing user data and pagination details.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        $users = $this->userService->getUsers();

        return response()->json([
            'status' => 'success',
            'message' => 'Users retrieved successfully',
            'info' => UserResource::collection($users->items()),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'per_page' => $users->perPage(),
            'total' => $users->total(),
        ], 200); // OK
    }

    /**
     * Store a newly created user in storage.
     *
     * This method creates a new user and returns a response containing the user data and a token.
     *
     * @param StoreUSerRequest $request The validated request object containing the new user's data.
     * @throws ValidationException
     * @return \Illuminate\Http\JsonResponse JSON response containing the newly created user and token.
     */
    public function store(StoreUSerRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = $this->userService->registerUser($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => new UserResource($user['user']),
            'token' => $user['token'],
        ], 201); // Created
    }

    /**
     * Display the specified user.
     *
     * This method retrieves a single user by ID and returns a response containing the user data.
     *
     * @param string $id The ID of the user to retrieve.
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse JSON response containing the user data.
     */
    public function show(string $id)
    {
        $user = $this->userService->getUserById($id);

        return response()->json([
            'status' => 'success',
            'message' => 'User retrieved successfully',
            'user' => new UserResource($user),
        ], 200); // OK
    }

    /**
     * Update the specified user in storage.
     *
     * This method updates the details of a specific user by ID.
     *
     * @param UpdateUSerRequest $request The validated request object containing the updated user data.
     * @param string $id The ID of the user to update.
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse JSON response containing the updated user data and token.
     */
    public function update(UpdateUSerRequest $request, string $id)
    {
        $user = $this->userService->updateUser($request->validated(), $id);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'user' => new UserResource($user['user']),
            'token' => $user['token'],
        ], 200); // OK
    }

    /**
     * Remove the specified user from storage.
     *
     * This method deletes a specific user by ID.
     *
     * @param string $id The ID of the user to delete.
     * @throws \Exception
     * @return \Illuminate\Http\JsonResponse JSON response containing a success message.
     */
    public function destroy(string $id)
    {
        $message = $this->userService->deleteUser($id);

        return response()->json([
            'status' => $message['status'],
            'message' => $message['message'],
        ], 200); // OK
    }

    public function restoreUser($id): \Illuminate\Http\JsonResponse
    {
        $message= $this->userService->restoreUser($id);
        return response()->json([
            'status' => $message['status'],
            'message' => $message['message'],
            'user' => new UserResource($message['user']),
        ], 200); // OK  
    }
}
