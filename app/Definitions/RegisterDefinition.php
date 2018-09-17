<?php

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 10:28
 */

/**
 * @OA\Post(
 *      path="/auth/register",
 *      operationId="registerUser",
 *      tags={"Auth"},
 *      @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(ref="#/components/schemas/NewUser")
 *         )
 *     ),
 *      summary="Register a user",
 *      description="Register user and returns tokens generated by Passport route : /oauth/tokens",
 *      @OA\Response(
 *          response=201,
 *          description="successful operation",
 *          @OA\JsonContent(ref="#/components/schemas/TokenResponse")
 *       ),
 *       @OA\Response(
 *          response=422,
 *          description="The given parameters were faulty",
 *          @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
 *       )
 *     )
 **/