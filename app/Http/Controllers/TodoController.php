<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\Todo;

class TodoController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/todos",
     *     operationId="todosIndex",
     *     tags={"Todos"},
     *     summary="Get a list of todos with priority filtering",
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Filter todos by priority (low, medium, high, highest, or all for all priorities)",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             enum={"all", "low", "medium", "high", "highest"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Todo")
     *         )
     *     )
     * )
     */
    public function todosIndex()
    {
        // Initialize the query
        $query = Todo::query();

        // Check 'priority' parameter
        if ($request->has('priority') && $request->input('priority') !== 'all') {
            $query->where('priority', $request->input('priority'));
        }

        // Execute the query
        $todos = $query->get();

        return response()->json([
            'data' => $todos
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/todos",
     *     operationId="todosCreate",
     *     tags={"Todos"},
     *     summary="Create a new todo",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="description", type="string", example="Laravel Assessment"),
     *             @OA\Property(property="priority", type="string", example="high"),
     *             @OA\Property(property="due_date", type="string", format="date-time", example="2024-11-05T12:00:00Z"),
     *             @OA\Property(property="user_id", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Todo created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Todo")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="error",
     *                 type="string",
     *                 example="Validation failed"
     *             ),
     *             @OA\Property(
     *                 property="details",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */
    public function todosCreate(Request $request)
    {
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'description' => 'required|string|max:255',
                'priority' => 'required|string|in:low,medium,high,highest',
                'due_date' => 'required|date',
                'user_id' => 'required|uuid|exists:users,id'
            ]);

            // Create a new todo
            $todo = Todo::create($validatedData);

            // Return the new todo with successful response
            return response()->json(['data' => $todo], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred while creating the todo.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/todos/{todoId}",
     *     operationId="todoRead",
     *     tags={"Todos"},
     *     summary="Get a specific todo by ID",
     *     @OA\Parameter(
     *         name="todoId",
     *         in="path",
     *         required=true,
     *         description="UUID of the todo to retrieve",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Todo retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Todo")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Todo not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Todo not found")
     *         )
     *     )
     * )
     */
    public function todoRead($todoId)
    {
        $todo = Todo::find($todoId);
        if ($todo) {
            return response()->json(['data' => $todo], 200);
        } else {
            return response()->json(['error' => 'Todo not found'], 404);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/v1/todos/{todoId}",
     *     operationId="todoUpdate",
     *     tags={"Todos"},
     *     summary="Update a specific todo by ID",
     *     @OA\Parameter(
     *         name="todoId",
     *         in="path",
     *         required=true,
     *         description="UUID of the todo to update",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="description", type="string", example="Update the project description"),
     *             @OA\Property(property="priority", type="string", example="medium"),
     *             @OA\Property(property="due_date", type="string", format="date-time", example="2024-12-01T12:00:00Z"),
     *             @OA\Property(property="completed_at", type="string", format="date-time", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Todo updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Todo")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Todo not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Todo not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Validation failed"),
     *             @OA\Property(property="details", type="object")
     *         )
     *     )
     * )
     */
    public function todoUpdate(Request $request, $todoId)
    {
        try {
            $todo = Todo::find($todoId);

            if (todo) {
                $validatedData = $request->validate([
                    'description' => 'sometimes|required|string|max:255',
                    'priority' => 'sometimes|required|string|in:low,medium,high,highest',
                    'due_date' => 'sometimes|required|date',
                    'completed_at' => 'nullable|date'
                ]);

                $todo->update($validatedData);

                return response()->json(['data' => $todo], 200);
            } else {
                return response()->json(['error' => 'Todo not found'], 404);
            }

        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/todos/{todoId}",
     *     operationId="todoDelete",
     *     tags={"Todos"},
     *     summary="Delete a specific todo by ID",
     *     @OA\Parameter(
     *         name="todoId",
     *         in="path",
     *         required=true,
     *         description="UUID of the todo to delete",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Todo deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Todo not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="error", type="string", example="Todo not found")
     *         )
     *     )
     * )
     */
    public function todoDelete($todoId)
    {
        try {
            $todo = Todo::find($todoId);
            if ($todo) {
                $todo->delete();
                return response()->json([], 204); 
            } else {
                return response()->json(['error' => 'Todo not found'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.', 'message' => $e->getMessage()], 500);
        }
    }
}
