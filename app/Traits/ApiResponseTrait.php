<?php

namespace App\Traits;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

trait ApiResponseTrait
{
    public function sendResponse($data, $message, $code = 200)
    {

        $respone = [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
        return response()->json($respone, $code);
    }


    public function sendError($errors, $message, $code = 404)
    {

        $respone = [
            'success' => false,
            'message' => $message,
            'errors' => $errors
        ];
        return response()->json($respone, $code);
    }


    /**
     * Generates a JSON response with paginated data.
     *
     * Transforms the paginated items using the provided resource class and
     * returns the transformed data along with pagination information.
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $paginator The paginator instance containing the items.
     * @param string $resourceClass The resource class used to transform the paginated items.
     * @param string $message Optional message to be included in the response.
     * @param int $status HTTP status code.
     *
     * @return \Illuminate\Http\JsonResponse A JSON response containing the transformed items and pagination details.
     */
    public static function paginated(LengthAwarePaginator $paginator, $resourceClass, $message = '', $status)
    {
        $transformedItems = $resourceClass::collection($paginator->items());

        return response()->json([
            'status' => 'success',
            'message' => trans($message),
            'data' => $transformedItems,
            'pagination' => [
                'total' => $paginator->total(),
                'count' => $paginator->count(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'total_pages' => $paginator->lastPage(),
            ],
        ], $status);
    }
}
