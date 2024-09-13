<?php

namespace App\Services;

use Exception;
use App\Models\User;

/**
 * Class UserService
 *
 * Handles operations related to users including CRUD operations, registration, and updates.
 */
class UserService
{
    /**
     * Retrieve all users with pagination.
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator|array
     * Returns a paginated list of users with their roles or an error response.
     */
    public function getUsers(): \Illuminate\Pagination\LengthAwarePaginator|array
    {
        try {
            // Retrieve users along with their roles, paginated by 5 per page
            $users = User::paginate(5);
            return $users;
        } catch (Exception $e) {
            // Handle any exceptions that may occur
            return [
                'status' => 'error',
                'message' => 'An error occurred while retrieving users.',
                'errors' => $e->getMessage(),
            ];
        }
    }

    /**
     * Register a new user.
     *
     * @param array $data
     * The array containing user registration data including 'name', 'email', 'password', and 'role'.
     *
     * @return array
     * An array containing the user resource, a JWT token, or an error response.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function registerUser(array $data): array
    {
        try {
            
            $user= User::create($data);
            // Generate a JWT token for the user
            $token = auth()->login($user);

            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'An error occurred during registration.',
                'errors' => $e->getMessage(),
            ];
        }
    }


    /**
     * Update an existing user.
     *
     * @param array $data
     * The array containing updated user data. The 'password' field, if present, will be hashed.
     * @param int $id
     * The ID of the user to be updated.
     *
     * @return User
     * The updated user instance.
     *
     * @throws \Exception
     * Throws an exception if the user is not found or if an error occurs during the update.
     */
    public function updateUser(array $data, string $id): array
    {
        try {

            //Find the user by ID or throw a 404 exception
            //  $user = User::where('user_id', $id)->first();
            $user = User::findOrFail($id);


            if (!$user) {
                throw new Exception('User not found!');
            }

            // if (isset($data['role'])) {
            //     $user->system_role = $data['role'];
            // }
            $user->update(array_filter($data));
            $token = auth()->login($user);

            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (Exception $e) {
            // Handle any other exceptions
            throw new Exception('An error occurred during updating: ' . $e->getMessage());
        }
    }


    /**
     * Retrieve a user by ID.
     *
     * @param int $id
     * The ID of the user to be retrieved.
     *
     * @return User
     * The user instance if found.
     *
     * @throws \Exception
     * Throws an exception if the user is not found or if an error occurs during retrieval.
     */
    public function getUserById($id): User
    {
        try {
            // Find the user by ID or throw a 404 exception
            $user = User::find($id);
            if (!$user) {
                throw new Exception('User not found!');
            }
            return $user;
        } catch (Exception $e) {
            // Handle any other exceptions
            throw new Exception('An error occurred while retrieving the user: ' . $e->getMessage() );
        }
    }

    /**
     * Delete a user by ID.
     *
     * @param int $id
     * The ID of the user to be deleted.
     *
     * @return array
     * An array containing a success message or an error response.
     *
     * @throws \Exception
     * Throws an exception if the user is not found or if an error occurs during deletion.
     */
    public function deleteUser($id): array
    {
        try {
            // Find the user by ID or throw a 404 exception
            $user = User::find($id);
            if (!$user) {
                throw new Exception('User not found!');
            }
            // Delete the user
            $user->delete();

            // Return a success message
            return [
                'status' => 'success',
                'message' => 'User deleted successfully.',
            ];
        } catch (Exception $e) {
            // Handle any other exceptions
            return [
                'status' => 'error',
                'message' => 'An error occurred during deletion.',
                'errors' => $e->getMessage(),
            ];
        }
    }
}