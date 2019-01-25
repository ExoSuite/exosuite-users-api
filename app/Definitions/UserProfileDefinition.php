<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 20/09/2018
 * Time: 15:11
 */

/**
 * @OA\Post(
 *      path="/user/me/profile",
 *      operationId="createUserProfile",
 *      tags={"UserProfile"},
 *      security={{"passport": {}}},
 *      @OA\RequestBody(
 *         required=false,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/UserProfile")
 *         )
 *     ),
 *      summary="Create User Profile",
 *      description="Request Body is not mandatory but Bearer token is required",
 *      @OA\Response(
 *          response=201,
 *          description="successful operation",
 *       ),
 *       @OA\Response(
 *          response=422,
 *          description="The given parameters were faulty",
 *          @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *       ),
 *       @OA\Response(
 *          response=401,
 *         description="Unauthorized",
 *       )
 *     )
 **/

/**
 * @OA\Patch(
 *      path="/user/me/profile",
 *      operationId="patchUserProfile",
 *      tags={"UserProfile"},
 *      security={{"passport": {"*"}}},
 *      @OA\RequestBody(
 *         required=false,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/UserProfile")
 *         )
 *     ),
 *      summary="Update User Profile",
 *      description="Request Body is not mandatory but Bearer token is required",
 *      @OA\Response(
 *          response=204,
 *          description="successful operation",
 *       ),
 *       @OA\Response(
 *          response=422,
 *          description="The given parameters were faulty",
 *          @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *       ),
 *      @OA\Response(
 *          response=401,
 *         description="Unauthorized",
 *       )
 *     )
 **/

/**
 * @OA\Get(
 *      path="/user/me/profile",
 *      operationId="getUserProfile",
 *      tags={"UserProfile"},
 *      security={{"passport": {}}},
 *      summary="Get User Profile",
 *      description="Bearer token is required",
 *      @OA\Response(
 *          response=200,
 *          description="successful operation",
 *          @OA\JsonContent(ref="#/components/schemas/User")
 *       ),
 *       @OA\Response(
 *          response=401,
 *          description="Unauthorized"
 *       )
 *     )
 **/
