<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

/**
 * @group Example management
 *
 * APIs for managing Example
 */
class PingController extends Controller
{
    /**
     * This is a successful example.
     *
     * @queryParam sort string Field to sort by. Defaults to 'id'.
     *
     * @urlParam id integer required The ID of the post.
     *
     * @bodyParam user_id int required The id of the user. Example: 9
     *
     * @response {
     *     "status": "success",
     *     "code": 200,
     *     "message": "Http ok",
     *     "data": [
     *             "This is a successful example."
     *         ],
     *     "error": {}
     * }
     */
    public function ping(Request $request)
    {
        if ($request->get('is_bad')) {
            return $this->errorBadRequest('This is a bad example.');
        }

        return $this->success('This is a successful example.');
    }
}
