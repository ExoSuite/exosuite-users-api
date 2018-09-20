<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 20/09/2018
 * Time: 16:33
 */

/**
 * @OA\Get(
 *      path="/user/me",
 *      operationId="getUser",
 *      tags={"User"},
 *      security={{"passport": {}}},
 *      summary="Get User",
 *      description="Bearer token is required",
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\JsonContent(ref="#/components/schemas/UserProfile")
 *       ),
 *       @OA\Response(
 *          response=401,
 *         description="Unauthorized",
 *       )
 *     )
 **/
