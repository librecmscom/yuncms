<?php
/**
 * This is NOT a freeware, use is subject to license terms.
 *
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 */

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\CheckAccountRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * 用户接口
 *
 * @author Tongle Xu <xutongle@gmail.com>
 */
class UserController extends Controller
{
    /**
     * @var UserService
     */
    protected UserService $userService;

    /**
     * UserController Constructor.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
        $this->middleware('auth:api');
    }

    /**
     * 用户账户是否存在
     *
     * @param CheckAccountRequest $request
     * @return array
     */
    public function exists(CheckAccountRequest $request): array
    {
        if (!empty($request->email)) {
            $exists = User::withTrashed()->where('email', $request->email)->exists();
        } elseif (!empty($request->phone)) {
            $exists = User::withTrashed()->where('phone', $request->phone)->exists();
        } else {
            $exists = User::withTrashed()->where('name', $request->name)->exists();
        }
        return ['exists' => $exists];
    }

    /**
     * 获取基本资料
     *
     * @param Request $request
     * @return UserResource
     */
    public function baseProfile(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    /**
     * 注销并删除自己的账户
     *
     * @param Request $request
     * @return Response
     */
    public function destroy(Request $request): Response
    {
        $request->user()->delete();
        return response('', 204);
    }
}
